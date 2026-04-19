# Laravel AI SDK — Knowledge Base Plan

> **Purpose**: Agent-oriented documentation index for `laravel/ai` SDK.
> Replaces `LARAVEL_AI_SDK_PLAN.md` — previous version was course-centric and included DB schema deliverables that are out of scope.

---

## What This Is

A flat-file, token-optimized reference index that an AI agent can load to answer any `laravel/ai` capability question, pick the right pattern, and avoid known pitfalls — without external docs access and without wasting tokens on prose.

**Not**: a tutorial, a course summary, or a DB seeder plan.

---

## Output Structure

```
_docs/_Analysis/Laravel_Ai_Agent/sdk-index/
├── INDEX.md                  ← load this first — task → file routing
├── 01-setup.md               ← install, providers, env, artisan generators
├── 02-agents.md              ← class structure, attributes, prompting, memory
├── 03-structured-output.md   ← JsonSchema, HasStructuredOutput, validation ⚠️
├── 04-tools.md               ← create, register, built-ins, orchestrator, security
├── 05-rag.md                 ← embeddings, pgvector, similarity search, reranking
├── 06-multi-agent.md         ← chaining, routing, parallel, orchestrator-workers
├── 07-multimodal.md          ← image gen, TTS, STT, vision input
├── 08-production.md          ← failover, queues, streaming, broadcasting, middleware
├── 09-testing.md             ← Agent::fake(), assertions, conversation testing
└── 10-ecosystem.md           ← MCP, Boost, AI Skills, provider matrix
```

---

## INDEX.md Design

INDEX.md serves as the agent's entry point. It contains:

1. **Quick-Switch Guide** — task → file(s) mapping table
   - "Implementing chat with memory" → `02-agents.md` + `06-multi-agent.md`
   - "Storing AI response to DB" → `03-structured-output.md`
   - "Adding cost tracking" → `09-testing.md` (AiRun/AiUsage observability)
   - etc.

2. **Provider Capability Matrix** — which provider supports what (tools, vision, STT, TTS, image gen, embeddings)

3. **Critical Pitfalls** — top 3-5 SDK-wide gotchas that affect any implementation

4. **SDK Package Reference** — install command, config file path, artisan commands list

---

## Section File Format (Token-Optimized)

Each file follows this structure — no prose intros, no padding:

```
# [Section Title]

## Quick Reference
API_SIGNATURE | WHEN | PITFALL

## Patterns
[pattern name]: [one-line description]
[minimal runnable code example]

## Pitfalls
⚠️ [pitfall]: [what happens] → [fix]
```

Target: 80–150 lines per file. Dense but navigable.

---

## What Changed vs Previous Plan

| Previous (`LARAVEL_AI_SDK_PLAN.md`) | This plan |
|---|---|
| LaraCasts episode summaries as primary source | Official docs as primary source |
| Course transcript content treated as SDK truth | Transcripts demoted to use-case examples only |
| SQL schema + DB migration deliverables | Removed — flat files only |
| nutri-ledger integration plan embedded | Separated — belongs in project memory |
| Prose research synthesis | Dense reference cards |
| 48 entries as flat list | 10 focused section files |

---

## Source Priority

| Priority | Source | Use For |
|---|---|---|
| 1 | [Official Docs 13.x](https://laravel.com/docs/13.x/ai-sdk) | All API signatures, canonical patterns |
| 2 | [Laravel Blog: Intro](https://laravel.com/blog/introducing-the-laravel-ai-sdk) | Design philosophy, core abstractions |
| 3 | [Laravel Blog: Multi-Agent](https://laravel.com/blog/building-multi-agent-workflows-with-the-laravel-ai-sdk) | Multi-agent patterns, orchestrator-worker |
| 4 | [Laravel News Series](https://laravel-news.com/ship-ai-with-laravel-building-your-first-agent-with-laravel-13s-ai-sdk) | Full-stack patterns, SSE streaming |
| 5 | [Laravel Daily Course](https://laraveldaily.com/course/laravel-ai-sdk) | Use-case catalog: images, TTS, STT, chatbot, PDF Q&A |
| 6 | [PrismPHP Tools Docs](https://prismphp.com/core-concepts/tools-function-calling/) | Tool/function-calling patterns (influenced SDK design) |
| 7 | LaraCasts transcripts (4 episodes) | Confirm patterns only — never as source of truth |

---

## Token Optimization Rules Applied

- No "In this section we cover..." intros
- No repeated explanations across files (cross-reference instead)
- Code examples: canonical pattern only, not tutorial variations
- Prose replaced with `API | WHEN | PITFALL` table format where possible
- Each file is independently loadable — agent loads only what it needs
