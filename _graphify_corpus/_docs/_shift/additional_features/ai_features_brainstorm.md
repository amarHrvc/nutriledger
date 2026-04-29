# AI & Automation Features — Brainstorm

## Origin Ideas (user-proposed)

### BMI Calculation
- Height + weight → standard formula → stored or computed on-the-fly
- Extend with: trend chart per patient, underweight/obese badge on patient list
- **Complexity:** Low
- **Tool:** Laravel (pure math, no AI needed)

### Dynamic Menu Suggestions Based on Socioeconomic Data
- Available inputs already in DB: food security status, income level, dietary restrictions, allergies, blood type, activity level, smoking/alcohol
- **v1:** Rule-based engine in a Laravel service — covers 80% of cases without LLM
- **v2:** LLM-generated meal plans with nutritional targets and plain-language rationale
- **Complexity:** Medium (rule-based) → Medium-High (LLM)
- **Tool:** Laravel service / Laravel AI SDK

### Doctor ChatBox — Summaries & Reports
- Doctor asks natural language questions about patients and visits
- System fetches relevant data, generates structured response
- See `doctor_assistant.md` for full architecture breakdown
- **Complexity:** Medium (basic RAG) → Medium-High (full agentic)
- **Tool:** Laravel AI SDK + Laravel MCP + n8n (for scheduled variant)

---

## Additional Ideas (proposed during brainstorm)

### Nutritional Risk Scoring
- Rule-based score computed from socioeconomic fields:
  - food insecurity + low income + sedentary + no health insurance → high risk
- Displayed as a badge/flag on patient list and profile
- No LLM, fully deterministic
- **Complexity:** Low
- **Tool:** Laravel service

### Automated Pre-Visit Briefing
- On visit creation (or on a scheduled trigger before the appointment), auto-generate a one-page briefing for the doctor:
  - Patient summary, last visit notes, BMI trend, flagged risk factors
- Doctor opens the visit form — briefing is already populated
- **Complexity:** Medium
- **Tool:** n8n (triggered by webhook on visit create) + LLM prompt

### Follow-up / Inactivity Alerts
- If a patient hasn't had a visit in X days AND has a high risk score → notify assigned doctor
- In-app notification or email
- No LLM required
- **Complexity:** Low-Medium
- **Tool:** n8n scheduled workflow → Laravel API → notification

### Voice-to-Notes Transcription
- Doctor speaks during the visit → browser mic button on visit form
- Whisper API transcribes → notes field auto-populated
- **Complexity:** Medium
- **Tool:** Whisper API + React frontend mic component

### Report Generation to PDF
- Doctor requests a report via chatbox or a dedicated UI action
- Laravel queued job renders data → LLM formats narrative → PDF output → download link
- **Complexity:** Medium-High
- **Tool:** Laravel AI SDK + DomPDF or Browsershot

### Scheduled Report Digest (Email)
- Weekly auto-generated email to each doctor:
  - Upcoming visits, high-risk patients, patients not seen recently
- No doctor action required
- **Complexity:** Medium
- **Tool:** n8n cron workflow + LLM node + email node

### Lab Result Interpretation Assist *(post-Groups 4-6)*
- When a lab result is entered, LLM flags values outside normal range with plain-language note
- Requires `labs` table — out of SE scope
- **Complexity:** High (data + clinical domain)

---

## Complexity Summary

| Feature | Complexity | Primary Tool |
|---|---|---|
| BMI calculation | Low | Laravel |
| Nutritional risk scoring | Low | Laravel service |
| Follow-up / inactivity alerts | Low-Medium | n8n |
| Pre-visit briefing | Medium | n8n + LLM |
| Menu suggestions (rule-based) | Medium | Laravel service |
| Doctor assistant chatbox (RAG) | Medium | Laravel AI SDK |
| Menu suggestions (LLM) | Medium-High | Laravel AI SDK |
| PDF report generation | Medium-High | Laravel + DomPDF |
| Scheduled report digest | Medium | n8n + email |
| Voice-to-notes | Medium | Whisper API |
| Lab result interpretation | High | Post-SE scope |

---

## Tool Roles Summary

| Tool | Best For |
|---|---|
| Laravel AI SDK | Synchronous in-request AI: chat, menu gen, risk explanation |
| n8n | Async/scheduled: alerts, digests, pre-visit briefings |
| Laravel MCP | Expose domain as agent tools (patients, visits, risk scores) |
| Claude API | LLM backbone for all AI features |
