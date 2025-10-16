# Additional Features — NutriLedger

Post-SE scope. These features extend the core platform (Groups 1-3) with automation, AI-assisted workflows, and agentic capabilities.

## Documents

| File | Description |
|---|---|
| `ai_features_brainstorm.md` | Full brainstorm of AI/automation ideas with complexity ratings |
| `doctor_assistant.md` | Doctor-facing AI assistant — architecture, tool definitions, phased plan |
| `phase1_chat_patient_context.md` | Deep-dive: Phase 1 implementation — how it works, what to build, code examples |
| `phase1_tasks.md` | Expandable task list for Phase 1 — 60 tasks across 7 groups with build order |

## Context

The application is in architectural transition from a Laravel + Livewire monolith to a **Laravel REST API + React SPA**. All features in this folder are designed for the new architecture.

## Available Tools / Stack

- **Laravel AI SDK** — synchronous in-request AI calls, tool use, streaming
- **n8n** — async/scheduled workflows, webhooks, multi-step automation
- **Laravel MCP** — exposes domain entities as MCP tools for agentic use
- **Claude API** — LLM backbone (tool use, RAG, report generation)
