# Doctor-Facing AI Assistant

## Overview

A conversational assistant embedded in the doctor's UI. Two modes, one interface:

- **Conversational (RAG):** Doctor asks natural language questions about patients and visits
- **Report Generation:** Doctor requests structured summaries or exportable PDFs

Same chatbox — intent detection and tool routing handled by the LLM.

---

## Example Interactions

**Conversational**
- *"What did I discuss with Amira last visit?"*
- *"Which of my patients are high nutritional risk?"*
- *"Any patients I haven't seen in over 30 days?"*
- *"Does Amira have any known allergies?"*

**Report Generation**
- *"Generate a visit summary for Amira's last 3 visits"*
- *"Give me a monthly overview of all my patients"*
- *"Export a nutritional risk report for all food-insecure patients"*

---

## Architecture

### Option A — Laravel AI SDK + Tool Use (Recommended for live chat)

```
Doctor types message
    → Laravel API receives it
    → AI SDK sends to Claude with registered tools
    → Claude decides which tools to call
    → Laravel executes tools (real Eloquent queries)
    → Claude receives results, generates response
    → Streamed back to React via SSE
```

This is fully agentic — Claude decides what data to fetch. No hardcoded intent-to-query mapping needed.

### Option B — n8n Workflow (Best for scheduled/async reports)

```
Doctor triggers report (or scheduled cron)
    → Laravel fires webhook to n8n
    → n8n fetches data via Laravel API calls
    → n8n calls LLM node
    → Returns formatted result or sends email
```

**Both options are used together:**
- Laravel AI SDK → live chat and on-demand reports
- n8n → scheduled digests, async PDF generation, email delivery

---

## Tool Definitions

These are the tools exposed to the AI agent (via Laravel AI SDK tool use or Laravel MCP):

| Tool | Returns | Notes |
|---|---|---|
| `get_patient(id)` | Full profile + socioeconomic data | |
| `list_patients(doctor_id, filters)` | Doctor's patient list + risk scores | Supports filters: risk level, last visit |
| `list_visits(patient_id, limit)` | Visit history for a patient | |
| `get_visit(id)` | Single visit detail | date, doctor, notes |
| `get_upcoming_visits(doctor_id, days)` | Visits in next N days | |
| `get_risk_score(patient_id)` | Computed nutritional risk score + breakdown | Rule-based |
| `search_patients(query, filters)` | Filtered patient search | name, risk level, inactivity |

Each tool is a thin wrapper over an existing service/repository — no new business logic.

---

## Report Generation Flow

```
Doctor: "Generate a visit summary report for Amira"
    → Claude calls get_patient(amira_id)
    → Claude calls list_visits(amira_id, limit=10)
    → Claude synthesizes narrative summary
    → Laravel renders to PDF (DomPDF or Browsershot)
    → Returns signed download URL
```

PDF generation is a queued Laravel job — response returns immediately with a job ID, frontend polls or receives a push notification when ready.

---

## Streaming

Use SSE (Server-Sent Events) from the start — do not retrofit later.

- Laravel AI SDK supports streaming natively
- React consumes with `EventSource` or `fetch` with `ReadableStream`
- Words appear as Claude generates them — essential for perceived responsiveness

---

## Conversation History

- Store message threads per doctor (new `assistant_threads` table or JSON in cache)
- Inject last N messages as context on each request
- Thread scoped to: doctor + optionally a specific patient

---

## Phased Delivery

### Phase 1 — Single-patient chat
- Doctor selects a patient → opens chat panel
- Tools available: `get_patient`, `list_visits`, `get_visit`, `get_risk_score`
- Scope limited to that patient — simpler prompts, smaller payloads
- Streaming enabled from day one

### Phase 2 — Cross-patient queries
- Doctor can ask about their full patient list
- Adds: `list_patients`, `search_patients`, `get_upcoming_visits`
- Enables: *"Which of my patients are high risk?"*, *"Who haven't I seen in 30 days?"*

### Phase 3 — Report generation
- Same chatbox, doctor says "generate report"
- Adds: PDF rendering, queued job, download link returned in chat

### Phase 4 — Scheduled digests (n8n)
- Weekly email with pre-generated patient summaries
- No doctor action required — fully automated

---

## Complexity Summary

| Component | Complexity |
|---|---|
| Chatbox UI (React, streaming) | Low |
| Tool definitions in Laravel | Low |
| Laravel AI SDK + Claude tool use | Medium |
| SSE streaming to frontend | Medium |
| Conversation history storage | Medium |
| PDF report generation | Medium |
| Cross-patient aggregate queries | Medium-High |
| n8n scheduled digest | Medium |
| Multi-turn context management | Medium-High |

---

## Dependencies

- `laravel/ai` (Laravel AI SDK)
- `barryvdh/laravel-dompdf` or `spatie/browsershot` for PDF
- n8n instance (self-hosted or cloud) for scheduled workflows
- Claude API key (Anthropic)
