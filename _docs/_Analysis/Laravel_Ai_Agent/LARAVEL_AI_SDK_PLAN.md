# Laravel AI SDK — Knowledge Index Plan + nutri-ledger Integration

> **Purpose**: Research synthesis + implementation plan.  
> Sources: LaraCasts transcript (4 episodes), Google NotebookLM index (5 sections), 6 web resources.  
> Do not implement without explicit go-ahead.

---

## Research Summary

### LaraCasts Transcript (4 Episodes)

**Episode 1 — Structured Output as Foundation**  
Demonstrates the canonical triage agent. Install `laravel/ai`, run migrations, create an Agent class implementing `HasStructuredOutput`. Define `schema()` returning a `JsonSchema` array (priority int 1-5, department string, sentiment enum, tags string[], summary string). Call via `$agent->prompt($text)`. Response is array-accessible with guaranteed keys — store directly to DB.

**Episode 2 — Logging AI Usage**  
Introduces `AiRun` + `AiUsage` Eloquent models. `AiRun` fields: `team_id, user_id, entity_id, feature, provider, model, status, started_at, finished_at`. `AiUsage` fields: `prompt_tokens, completion_tokens, total_tokens, cost_usd`. Controller wraps agent call in try/catch, persisting `success` or `failure`. Full observability + cost attribution.

**Episode 3 — Conversation Architecture & Context**  
Adds `ai_conversation_id` to the entity table. `TicketAssistant` uses `RemembersConversations` trait. Context built from structured entity data. Controller uses `->continue($conversationId)` to resume or start a conversation. Conversations scoped per entity AND per user — no cross-contamination.

**Episode 4 — Frontend Demo (partial)**  
Alpine.js + Livewire for streaming chat UI. CSRF token handling, `alpine:init` event listener. Streaming response from `$agent->stream()` feeds into Livewire component reactive properties.

---

### NotebookLM Index (5 Sections)

**1. Core Architecture**  
Agent class with PHP attributes: `#[Temperature(0.0)]` (deterministic), `#[MaxTokens(1024)]` (cost cap), `#[UseCheapestModel]` (auto-select cheapest model), `#[Timeout(120)]` (production safety). `prompt()` is the primary method. Provider config in `config/ai.php`, per-call override via `->using('provider')`.

**2. Multi-Agent Workflow Patterns**
- **Prompt Chaining**: PHP `Pipeline` — sequential agents, each output feeds next
- **Routing**: Classifier agent returns `{ specialist }`, controller dispatches to named agent
- **Parallelization**: `Concurrency::run([...])` — run multiple agents simultaneously
- **Orchestrator-Workers**: Lead agent has worker agents registered as Tools, decides which to invoke
- **Evaluator-Optimizer**: Agent A generates, Agent B scores, loop until threshold met

**3. Data Intelligence & RAG**  
pgvector (1,536 dimensions). Generate: `Str::of($text)->toEmbeddings()` or `Embeddings::for([])` (batch). Query: `whereVectorSimilarTo('embedding', $vector, limit: 5)`. Built-in `SimilaritySearch` tool queries any Eloquent model with an embedding column. `FileSearch` auto-vectorises documents. `Reranking::rerank($query, $docs)` for post-search relevance.

**4. Production Resilience**  
Failover: `->using(['openai', 'anthropic'])`. Queue: `$agent->queue()`. Stream: `$agent->stream()` → SSE / Vercel Data Protocol. Broadcast: `$agent->broadcastOnQueue(channel: '...')`. Middleware: `make:agent-middleware`. **Critical**: SDK does NOT validate structured output — always `Validator::make($result, [...])` before persisting.

**5. Ecosystem**  
`laravel/mcp` — exposes app as tool to external AI agents (Claude Desktop, Cursor). `laravel/boost` — injects live DB schema/routes into coding agent context (dev only). `AI Skills` (`SKILL.md`) — progressive context disclosure for large agent systems. Testing: `Agent::fake()`, `Image::fake()`, `Audio::fake()`.

---

### Web Resources (6 Sources)

| # | Resource | What It Covers |
|---|----------|----------------|
| 1 | [Official Docs 13.x](https://laravel.com/docs/13.x/ai-sdk) | Complete API surface: 10+ providers, full schemas, multimodal, all features |
| 2 | [Laravel Blog: Intro](https://laravel.com/blog/introducing-the-laravel-ai-sdk) | Design philosophy: agents as the core abstraction, provider-agnostic API |
| 3 | [Laravel Blog: Multi-Agent](https://laravel.com/blog/building-multi-agent-workflows-with-the-laravel-ai-sdk) | Orchestrator-worker end-to-end, structured handoff, `Concurrency::run()` |
| 4 | [Laravel Daily Course](https://laraveldaily.com/course/laravel-ai-sdk) | 6 practical examples: images, TTS, STT, chatbot with docs, PDF Q&A |
| 5 | [Laravel News Series](https://laravel-news.com/ship-ai-with-laravel-building-your-first-agent-with-laravel-13s-ai-sdk) | Full-stack: pgvector + Livewire + Alpine + SSE streaming, step-by-step |
| 6 | [PrismPHP Tools Docs](https://prismphp.com/core-concepts/tools-function-calling/) | Tool/function-calling patterns (predecessor pkg, influenced SDK design) |

---

## Knowledge Base DB Index — Design Plan

### What It Is

A structured, queryable knowledge store that gives any AI agent complete working knowledge of the Laravel AI SDK. When loaded as context, the agent can answer any capability question, generate correct boilerplate, pick the right pattern, and avoid known pitfalls — without external docs access.

### Storage Options

**Option A — Portable Markdown** (zero infra, recommended first)  
10 structured `.md` files + `MANIFEST.json` index in `_docs/_Analysis/Laravel_Ai_Agent/knowledge_base/`. Can be fed as raw context to any LLM.

**Option B — PostgreSQL + pgvector** (queryable, scalable)  
Embed each entry's `summary + code_example`, store in `ai_sdk_knowledge` table. Any agent with `SimilaritySearch` tool can query it semantically.

### Database Schema

```sql
-- Core knowledge entries
CREATE TABLE ai_sdk_knowledge (
    id            BIGSERIAL PRIMARY KEY,
    category      VARCHAR(64)  NOT NULL,  -- CAT-1 through CAT-10
    topic         VARCHAR(128) NOT NULL,  -- e.g. "Structured Output"
    subtopic      VARCHAR(128),           -- e.g. "Schema Definition"
    summary       TEXT NOT NULL,          -- 3-5 sentence plain-language description
    code_example  TEXT,                   -- canonical, runnable PHP snippet
    pitfalls      TEXT,                   -- known gotchas (null if none)
    keywords      TEXT[]   NOT NULL,      -- for keyword/tag search
    providers     TEXT[],                 -- which providers support this (null = all)
    source_url    TEXT,
    sdk_version   VARCHAR(16) DEFAULT 'laravel/ai ^1.0',
    embedding     VECTOR(1536),           -- for semantic similarity search
    created_at    TIMESTAMP DEFAULT NOW()
);

-- Provider capability matrix (machine-readable)
CREATE TABLE ai_sdk_providers (
    id                  BIGSERIAL PRIMARY KEY,
    provider_slug       VARCHAR(64) UNIQUE NOT NULL,
    display_name        VARCHAR(64) NOT NULL,
    env_key             VARCHAR(64) NOT NULL,
    default_model       VARCHAR(128),
    supports_tools      BOOLEAN DEFAULT TRUE,
    supports_vision     BOOLEAN DEFAULT FALSE,
    supports_audio_in   BOOLEAN DEFAULT FALSE,
    supports_tts        BOOLEAN DEFAULT FALSE,
    supports_image_gen  BOOLEAN DEFAULT FALSE,
    supports_embeddings BOOLEAN DEFAULT FALSE,
    notes               TEXT
);

-- Reusable workflow pattern catalog
CREATE TABLE ai_sdk_patterns (
    id            BIGSERIAL PRIMARY KEY,
    pattern_name  VARCHAR(128) NOT NULL,
    category      VARCHAR(64)  NOT NULL,
    problem       TEXT NOT NULL,      -- when to use
    solution      TEXT NOT NULL,      -- how it works
    code_skeleton TEXT,               -- structural outline
    tradeoffs     TEXT,
    knowledge_ids BIGINT[],           -- linked ai_sdk_knowledge entries
    embedding     VECTOR(1536)
);
```

### Topic Taxonomy (10 Categories, 48 Entries Total)

#### CAT-1: SETUP (5 entries)
1. **Installation** — `composer require laravel/ai`, migrations, vendor:publish
2. **Provider Configuration** — `config/ai.php`, `default`, `->using()`
3. **Environment Variables** — per-provider `.env` keys, Azure special case
4. **Artisan Generators** — `make:agent`, `make:tool`, `make:agent-middleware`
5. **Supported Providers Matrix** — 10+ providers, which supports what

#### CAT-2: CORE AGENT (6 entries)
1. **Agent Class Structure** — extends Agent, PHP attributes, `$instructions`
2. **PHP Attributes** — `#[Temperature]`, `#[MaxTokens]`, `#[UseCheapestModel]`, `#[Timeout]`
3. **Basic Prompting** — `prompt()`, `->using()`, `->withFile()`
4. **Conversation Memory** — `RemembersConversations`, `->continue()`, scoping
5. **Structured Output** — `HasStructuredOutput`, `JsonSchema`, array access, **MUST VALIDATE**
6. **File Attachments** — `->withFile()`, `->withImage()`, PDF/image input

#### CAT-3: TOOLS (5 entries)
1. **Tool Creation** — `make:tool`, `handle()`, parameter schema
2. **Registering Tools** — `$tools` property or `->withTools([])`
3. **Built-in Tools** — `SimilaritySearch`, `WebSearch`, `WebFetch`, `FileSearch`
4. **Orchestrator-Workers** — agents as tools via `AgentTool`
5. **Tool Security** — validate all args, never trust LLM input, scope to authenticated user

#### CAT-4: RAG & EMBEDDINGS (5 entries)
1. **Generating Embeddings** — `Str::of()->toEmbeddings()`, `Embeddings::for([])`
2. **Vector Storage** — `VECTOR(1536)` migration, pgvector extension required
3. **Similarity Search Query** — `whereVectorSimilarTo()`, Eloquent scopes
4. **SimilaritySearch Tool** — built-in tool, `model`, `column`, `limit`, `labelColumn`
5. **Reranking** — `Reranking::rerank($query, $docs)`, when to use vs. not

#### CAT-5: MULTI-AGENT PATTERNS (5 entries)
1. **Prompt Chaining** — `Pipeline`, sequential, clear input/output contracts
2. **Routing** — classifier → specialist dispatch, structured output for handoff
3. **Parallelization** — `Concurrency::run([])`, keyed results, synthesis step
4. **Orchestrator-Workers** — autonomous task delegation via tools
5. **Evaluator-Optimizer** — quality loop, score threshold, max iterations

#### CAT-6: MULTIMODAL (4 entries)
1. **Image Generation** — `Image::generate()`, OpenAI/Gemini, queue for async
2. **Text-to-Speech** — `Audio::synthesize()`, voice/speed options, store to disk
3. **Speech-to-Text** — `Audio::transcribe()`, voice note → structured agent pipeline
4. **Vision Input** — `->withImage()`, OpenAI/Anthropic/Gemini, analysis use cases

#### CAT-7: PRODUCTION (6 entries)
1. **Provider Failover** — `->using(['openai', 'anthropic'])`, automatic fallback
2. **Queueing** — `->queue()`, `onQueue()`, `PendingDispatch`
3. **Streaming (SSE)** — `->stream()`, `StreamedResponse`, Vercel Data Protocol
4. **Broadcasting** — `broadcastOnQueue(channel:)`, Laravel Echo, WebSocket
5. **Agent Middleware** — `before()`, `after()`, `#[Middleware(...)]` attribute
6. **Structured Output Validation** ⚠️ — SDK does NOT validate, always use `Validator::make()`

#### CAT-8: OBSERVABILITY (4 entries)
1. **AiRun Model** — per-invocation log: user, entity, feature, provider, model, status
2. **AiUsage Model** — token counts + cost_usd, linked to AiRun
3. **Feature-Level Cost Attribution** — groupBy feature, sum cost_usd
4. **Error Handling** — `AiException`, `StructuredOutputException`, queue retry backoff

#### CAT-9: TESTING (4 entries)
1. **Agent::fake()** — prevent real API calls, configure structured responses
2. **Image/Audio::fake()** — multimodal test doubles, pass instanceof checks
3. **Tool Call Assertions** — `assertToolCalled('name', $args)`
4. **Conversation Persistence Testing** — multi-prompt test, assert `ai_conversations` table

#### CAT-10: ECOSYSTEM (4 entries)
1. **Laravel MCP** — `laravel/mcp`, expose app as tool to Claude Desktop / Cursor
2. **Laravel Boost** — dev tool, injects live DB schema into coding agent context
3. **AI Skills (SKILL.md)** — progressive disclosure, prevent context window overflow
4. **Testing Integration** — fakes + RefreshDatabase + actingAs(), auto-reset between tests

---

### Example Knowledge Entry (JSON format)

```json
{
  "category": "CORE_AGENT",
  "topic": "Structured Output",
  "subtopic": "Schema Definition",
  "summary": "Implement HasStructuredOutput on an agent and define a schema() method returning a JsonSchema array. The agent is forced to respond in JSON matching the schema. The response is array-accessible with guaranteed keys. This enables reliable DB storage without parsing guesswork.",
  "code_example": "class VisitSummaryAgent extends Agent implements HasStructuredOutput\n{\n    public function schema(): array\n    {\n        return JsonSchema::object([\n            'summary'            => JsonSchema::string(),\n            'flags'              => JsonSchema::array(JsonSchema::string()),\n            'follow_up_required' => JsonSchema::boolean(),\n            'risk_level'         => JsonSchema::string()->enum(['low', 'medium', 'high']),\n        ]);\n    }\n}",
  "pitfalls": "SDK does NOT validate the response against the schema. Always run Validator::make($result, [...]) before persisting. Silent data corruption otherwise.",
  "keywords": ["structured output", "HasStructuredOutput", "JsonSchema", "schema", "array response"],
  "source_url": "https://laravel.com/docs/13.x/ai-sdk#structured-output",
  "sdk_version": "laravel/ai ^1.0"
}
```

### Agent Context Prompt Template

When giving an AI agent access to this index:
```
You have access to a Laravel AI SDK knowledge base tool: search_sdk_knowledge(query: string, category?: string).
Query it before answering any question about laravel/ai capabilities, configuration, or patterns.
Entries include summaries, runnable code examples, pitfalls, and source URLs.
```

### Population Strategy

1. Parse each of the 10 markdown files → insert one row per entry
2. Run `Embeddings::for($summaries)` in a seeder → store vector per row
3. Index `keywords` column as GIN index for fast keyword search
4. Expose `SearchAiSdkKnowledge` tool to any agent that needs SDK guidance

---

## Provider Capability Matrix

| Provider    | Tools | Vision | STT | TTS | Image Gen | Embeddings | Best For               |
|-------------|-------|--------|-----|-----|-----------|------------|------------------------|
| OpenAI      | ✓     | ✓      | ✓   | ✓   | ✓ DALL-E  | ✓          | General, most features |
| Anthropic   | ✓     | ✓      | —   | —   | —         | —          | Best reasoning         |
| Gemini      | ✓     | ✓      | ✓   | ✓   | ✓         | ✓          | Best multimodal        |
| Azure OAI   | ✓     | ✓      | —   | ✓   | ✓         | ✓          | Enterprise/compliance  |
| Groq        | ✓     | —      | ✓   | —   | —         | —          | Fastest inference      |
| xAI (Grok)  | ✓     | ✓      | —   | —   | —         | —          | Real-time web data     |
| DeepSeek    | ✓     | —      | —   | —   | —         | —          | Cost-effective         |
| Mistral     | ✓     | —      | —   | —   | —         | ✓          | EU data residency      |
| Ollama      | ⚠️    | ⚠️     | —   | —   | —         | ✓          | Local/private data     |

---

## nutri-ledger — AI Integration Plan

### Feature 1 — Clinical Visit Note Summarizer ★ RECOMMENDED FOR M3

**What**: On `Visit` save, agent generates a structured summary with risk flags and follow-up indicator.

**Pattern**: `HasStructuredOutput` + Observer → Queued Job

**Output schema**:
```php
JsonSchema::object([
    'summary'            => JsonSchema::string()->maxLength(500),
    'clinical_flags'     => JsonSchema::array(JsonSchema::string()),
    'follow_up_required' => JsonSchema::boolean(),
    'risk_level'         => JsonSchema::string()->enum(['low', 'medium', 'high']),
])
```

**Integration**:
- `VisitObserver::created/updated` → dispatch `ProcessVisitSummary` queued job
- New `visits` columns: `ai_summary TEXT`, `ai_flags JSON`, `ai_risk_level VARCHAR(16)`, `ai_follow_up BOOLEAN`
- `VisitResource` exposes AI fields
- Doctor/admin sees AI summary in visit detail

**Provider**: Anthropic (best clinical text reasoning)  
**Test**: `Agent::fake()` → verify columns populated, resource includes AI fields

---

### Feature 2 — AI Usage Observability ★ RECOMMENDED FOR M3

**What**: Track every AI invocation with cost and performance metrics.

**Pattern**: `AiRun` + `AiUsage` models (exact pattern from LaraCasts Episode 2)

**Migrations**:
```
ai_runs:   id, user_id, patient_id (nullable), feature, provider, model, status, started_at, finished_at
ai_usages: id, ai_run_id, prompt_tokens, completion_tokens, total_tokens, cost_usd
```

**API**: `GET /api/admin/ai-stats` → cost by feature, success rate, total calls

**Why for M3**: Shows production-grade AI thinking beyond hello-world prompting — strong for grading.

---

### Feature 3 — Patient Chat Assistant with RAG (M3 stretch goal)

**What**: Doctor asks natural-language questions about a patient's visit history.

**Pattern**: `RemembersConversations` + `SimilaritySearch` tool

**Setup steps**:
1. Add `embedding VECTOR(1536)` to `visits` table
2. `VisitObserver` embeds `notes` on create/update
3. `PatientAssistant` agent with `SimilaritySearch` scoped to `patient_id`
4. `POST /api/patients/{patient}/chat` → continues or starts conversation
5. `ai_conversation_id` on `patients` table

**Example queries the system would handle**:
- "What medications has this patient taken in the last 6 months?"
- "Are there any recurring symptoms?"
- "Has the patient's blood pressure trend improved?"

**Prerequisite**: pgvector extension installed on PostgreSQL server.

---

### Feature 4 — Dietary Risk Flag Agent (post-M3, SD scope)

**What**: Agent reads `Patient` allergies + blood type + `PatientSocioeconomic` → structured dietary risk assessment.

```php
JsonSchema::object([
    'risk_level'             => JsonSchema::string()->enum(['low', 'moderate', 'high', 'critical']),
    'dietary_restrictions'   => JsonSchema::array(JsonSchema::string()),
    'supplement_suggestions' => JsonSchema::array(JsonSchema::string()),
    'contraindications'      => JsonSchema::array(JsonSchema::string()),
])
```

---

### M3 Implementation Timeline

| Week | Dates | Work |
|------|-------|------|
| 1 | Apr 19 – Apr 26 | Feature 2: `AiRun` + `AiUsage` migrations + models |
| 2 | Apr 27 – May 3  | Feature 1 RED: failing tests for visit summarizer |
| 3 | May 4 – May 17  | Feature 1 GREEN: `ClinicalSummaryAgent` + `ProcessVisitSummary` job |
| 4 | May 18 – May 31 | Feature 3 setup: pgvector + embeddings observer/seeder |
| 5 | Jun 1 – Jun 7   | Feature 3: `PatientAssistant` + `/chat` endpoint + polish |

---

## Deliverables (when ready to implement)

### Knowledge Base Files
```
_docs/_Analysis/Laravel_Ai_Agent/knowledge_base/
├── MANIFEST.json
├── 01_setup.md          — Installation, providers, env vars, generators
├── 02_core_agent.md     — Agent class, attributes, prompting, memory, structured output, attachments
├── 03_tools.md          — Tool creation, built-ins, orchestrator, security
├── 04_rag_embeddings.md — Embeddings, pgvector, similarity search, reranking
├── 05_multi_agent.md    — Chaining, routing, parallelization, orchestrator-workers, evaluator
├── 06_multimodal.md     — Image gen, TTS, STT, vision input
├── 07_production.md     — Failover, queueing, streaming, broadcasting, middleware, validation
├── 08_observability.md  — Run logging, token/cost tracking, feature attribution, errors
├── 09_testing.md        — Agent::fake(), assertions, conversation testing
└── 10_ecosystem.md      — MCP, Boost, AI Skills
```

### nutri-ledger App Files
```
app/AI/
├── Agents/
│   ├── ClinicalSummaryAgent.php
│   └── PatientAssistant.php
├── Tools/
│   └── GetPatientVisitHistory.php
└── Jobs/
    └── ProcessVisitSummary.php

database/migrations/
├── xxxx_add_ai_fields_to_visits_table.php
├── xxxx_create_ai_runs_table.php
└── xxxx_create_ai_usages_table.php

app/Models/
├── AiRun.php
└── AiUsage.php

tests/Feature/AI/
├── ClinicalSummaryTest.php
└── AiObservabilityTest.php
```

### DB Index Files (if building the queryable index)
```
database/migrations/xxxx_create_ai_sdk_knowledge_table.php
database/seeders/AiSdkKnowledgeSeeder.php
app/AI/Tools/SearchAiSdkKnowledge.php
```

---

## Verification Plan

1. **Knowledge base**: Load `01_setup.md` as agent context → ask "configure Anthropic" → answer matches entry
2. **Visit Summarizer**: `php artisan test tests/Feature/AI/ClinicalSummaryTest.php` → green with `Agent::fake()`
3. **Observability**: POST a visit → check `ai_runs` + `ai_usages` rows exist with correct data
4. **RAG**: Seed 5 visits with embeddings → POST to `/api/patients/1/chat` → response references visit data
5. **Regression**: `php artisan test` → no existing tests broken
