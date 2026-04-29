# 10 — Ecosystem

## Laravel MCP (`laravel/mcp`)

Expose your Laravel application as a tool that external AI agents (Claude Desktop, Cursor, etc.) can call.

```bash
composer require laravel/mcp
php artisan vendor:publish --provider="Laravel\Mcp\McpServiceProvider"
```

**What it does:** Registers your app's routes/actions as MCP tools. External agents can discover and invoke them over the Model Context Protocol.

**Use case:** Let Claude Desktop query your app's DB, trigger workflows, or retrieve data without a custom integration.

**Config:** `config/mcp.php` — define which tools/resources to expose.

---

## Laravel Boost (`laravel/boost`)

⚠️ **Development-only tool** — do not install in production.

Injects live application context (DB schema, routes, models) directly into a coding agent's context window, enabling accurate code generation without manual schema lookup.

```bash
composer require laravel/boost --dev
```

**What it does:**
- Reads current DB schema and injects it as agent context
- Reads routes, model relationships, and config
- Dramatically reduces hallucinations in code generation tasks

**In `nutri-ledger`:** Already installed as an MCP server (port 64342). Available tools: `search-docs`, `tinker`, `database-query`, `browser-logs`, `list-artisan-commands`, `get-absolute-url`.

---

## AI Skills (`SKILL.md`)

Progressive context disclosure for large multi-agent systems.

**Problem:** Large agent systems have too much context to load at once — exceeds context windows, wastes tokens.

**Solution:** Each sub-agent has a `SKILL.md` file describing what it does and when to call it. The orchestrator loads `SKILL.md` files (small) to decide which agent to invoke, then loads the full agent only when needed.

```
app/Ai/Agents/
├── ClinicalSummaryAgent.php
├── ClinicalSummaryAgent.SKILL.md    ← "Summarizes visit notes. Call when: visit saved."
├── PatientAssistant.php
└── PatientAssistant.SKILL.md        ← "Answers questions about a patient. Call when: doctor asks."
```

**SKILL.md format** (keep under 100 words):
```markdown
## ClinicalSummaryAgent

Generates structured clinical summaries from visit notes.

**Call when:** A visit is created or updated and `ai_summary` is null.
**Input:** Raw visit notes (string).
**Output:** Structured object: summary, clinical_flags[], risk_level, follow_up_required.
**Provider:** Anthropic (reasoning quality matters here).
**Avg tokens:** ~800 prompt / ~200 completion.
```

---

## Summary

| Tool | Install env | Purpose |
|---|---|---|
| `laravel/mcp` | Production | Expose app as MCP tool to external agents |
| `laravel/boost` | Dev only | Inject live DB schema into agent context |
| `SKILL.md` convention | Any | Progressive disclosure — orchestrators pick agents efficiently |
