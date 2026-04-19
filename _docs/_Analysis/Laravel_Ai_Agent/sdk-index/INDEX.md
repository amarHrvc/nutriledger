# Laravel AI SDK — Agent Navigation Index

> Load this file first. Find your task, load only the files you need.

---

## Quick-Switch Guide

| Task | Load |
|---|---|
| Set up SDK / add a provider | `01-setup.md` |
| Build a basic agent | `02-agents.md` |
| Return structured data / store AI response to DB | `03-structured-output.md` |
| Add tools / function calling to an agent | `04-tools.md` |
| Chat with memory / conversation history | `02-agents.md` §Conversations |
| RAG / semantic search over your own data | `05-rag.md` + `04-tools.md` §SimilaritySearch |
| Multi-agent: routing, parallelization, orchestrator | `06-multi-agent.md` |
| Image generation | `07-multimodal.md` §Images |
| Text-to-speech / speech-to-text | `07-multimodal.md` §Audio |
| Vision input (image → agent) | `02-agents.md` §Attachments |
| Provider failover | `08-production.md` §Failover |
| Queue / async agent execution | `08-production.md` §Queuing |
| Streaming SSE responses | `08-production.md` §Streaming |
| Cost tracking / observability | `09-testing.md` §Observability |
| Test without real API calls | `09-testing.md` |
| Upload files / vector stores for RAG | `05-rag.md` §Files |
| Rerank search results | `05-rag.md` §Reranking |
| Expose app as MCP tool (Claude Desktop / Cursor) | `10-ecosystem.md` |

---

## Provider Capability Matrix

| Provider | Text | Tools | Vision | STT | TTS | Image Gen | Embeddings | Reranking | Best For |
|---|---|---|---|---|---|---|---|---|---|
| OpenAI | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | — | General, most features |
| Anthropic | ✓ | ✓ | ✓ | — | — | — | — | — | Best reasoning |
| Gemini | ✓ | ✓ | ✓ | ✓ | — | ✓ | ✓ | — | Best multimodal |
| Azure OAI | ✓ | ✓ | ✓ | — | — | — | ✓ | — | Enterprise/compliance |
| Groq | ✓ | ✓ | — | — | — | — | — | — | Fastest inference |
| xAI (Grok) | ✓ | ✓ | — | — | — | ✓ | — | — | Real-time data |
| DeepSeek | ✓ | ✓ | — | — | — | — | — | — | Cost-effective |
| Mistral | ✓ | ✓ | — | ✓ | — | — | ✓ | — | EU data residency |
| Ollama | ✓ | ⚠️ | ⚠️ | — | — | — | ✓ | — | Local/private |
| ElevenLabs | — | — | — | ✓ | ✓ | — | — | — | High-quality voice |
| Cohere | — | — | — | — | — | — | ✓ | ✓ | Reranking |
| Jina | — | — | — | — | — | — | ✓ | ✓ | Reranking |
| VoyageAI | — | — | — | — | — | — | ✓ | — | Embeddings |

---

## SDK-Wide Critical Pitfalls

⚠️ **Structured output NOT validated by SDK** — `schema()` forces shape but not value validity. Always `Validator::make($response->toArray(), [...])` before persisting. → `03-structured-output.md`

⚠️ **`MaxSteps` default may block tool use** — Tool invocation requires ≥ 2 steps (1 LLM call + 1 tool call). If tools never fire, add `#[MaxSteps(10)]`. → `04-tools.md`

⚠️ **`migrate` required after install** — SDK creates `agent_conversations` + `agent_conversation_messages` tables. Skip migrate = broken conversations. → `01-setup.md`

⚠️ **pgvector extension must exist before vector migrations** — Run `CREATE EXTENSION IF NOT EXISTS vector;` on PostgreSQL first. → `05-rag.md`

⚠️ **Tool args come from LLM — treat as untrusted** — Validate `$request` in `handle()`. Never pass LLM-supplied values directly to DB queries. → `04-tools.md`

⚠️ **`->stream()` returns `StreamedResponse`, not string** — Must be returned directly from route/controller, not assigned to a variable. → `08-production.md`

---

## Package Quick Reference

```bash
composer require laravel/ai
php artisan vendor:publish --provider="Laravel\Ai\AiServiceProvider"
php artisan migrate

php artisan make:agent AgentName
php artisan make:agent AgentName --structured
php artisan make:tool ToolName
php artisan make:agent-middleware MiddlewareName
```

| Key | Value |
|---|---|
| Config file | `config/ai.php` |
| Provider enum | `Laravel\Ai\Enums\Lab` |
| Agent namespace | `App\Ai\Agents\` |
| Tool namespace | `App\Ai\Tools\` |
| Key config keys | `providers`, `models.text`, `models.image`, `models.audio`, `models.transcription`, `models.embedding`, `caching.embeddings` |
| DB tables | `agent_conversations`, `agent_conversation_messages` |

---

## Source References

| Source | URL | Used For |
|---|---|---|
| Official Docs 13.x | https://laravel.com/docs/13.x/ai-sdk | Primary — all API signatures |
| Laravel Blog: Intro | https://laravel.com/blog/introducing-the-laravel-ai-sdk | Design philosophy |
| Laravel Blog: Multi-Agent | https://laravel.com/blog/building-multi-agent-workflows-with-the-laravel-ai-sdk | Multi-agent patterns |
| Laravel News Series | https://laravel-news.com/ship-ai-with-laravel-building-your-first-agent-with-laravel-13s-ai-sdk | Full-stack patterns |
| PrismPHP Tools Docs | https://prismphp.com/core-concepts/tools-function-calling/ | Tool/function-calling patterns |
| Laravel Daily Course | https://laraveldaily.com/course/laravel-ai-sdk | Use-case catalog |
