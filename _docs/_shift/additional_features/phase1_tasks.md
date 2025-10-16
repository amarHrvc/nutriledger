# Phase 1 Task List — Doctor Assistant (Patient Context Chat)

Status legend: `[ ]` todo · `[~]` in progress · `[x]` done

---

## TASK GROUP A — Setup & Infrastructure

### A1 — Install Laravel AI SDK
- [ ] `composer require laravel/ai`
- [ ] `php artisan vendor:publish --provider="Laravel\Ai\AiServiceProvider"`
- [ ] `php artisan migrate` (SDK creates `agent_conversations` and `agent_conversation_messages` tables)
- [ ] Add `ANTHROPIC_API_KEY` to `.env` and `.env.example`
- [ ] Set default provider + model in `config/ai.php`
- **Expand later:** add OpenAI as fallback provider for automatic failover

### A2 — Directory Structure
- [ ] Scaffold agent: `php artisan make:agent PatientAssistant`
- [ ] Scaffold tools: `php artisan make:tool GetPatientTool`, `ListVisitsTool`, `GetVisitTool`, `GetRiskScoreTool`
- [ ] Create `app/Services/NutritionalRiskCalculator.php` stub
- **Expand later:** add `app/Ai/Prompts/` for extracting prompt strings

---

## TASK GROUP B — Tools

### B1 — `GetPatientTool`
- [ ] Create `app/Ai/Tools/GetPatientTool.php`
- [ ] Implement `description()`, `schema()` (no inputs), `handle()`
- [ ] Eager-load `socioeconomic` relation
- [ ] Return JSON with: id, full_name, dob, gender, blood_type, allergies, medical_notes, socioeconomic fields
- [ ] Unit test: returns correct data for a given patient ID
- **Expand later:** include BMI if height/weight fields are added (Groups 4+)

### B2 — `ListVisitsTool`
- [ ] Create `app/Ai/Tools/ListVisitsTool.php`
- [ ] Implement `schema()` with optional `limit` (integer, 1-20, default 5)
- [ ] Eager-load `doctor` relation on visits
- [ ] Return JSON array ordered by `date` descending
- [ ] Unit test: respects limit, only returns visits for injected patient ID
- **Expand later:** add `from_date` / `to_date` filter params (Phase 2)

### B3 — `GetVisitTool`
- [ ] Create `app/Ai/Tools/GetVisitTool.php`
- [ ] Implement `schema()` with required `visit_id` (integer)
- [ ] Scope query with `where('patient_id', $this->patientId)` — prevents cross-patient access
- [ ] Return JSON with: id, date, doctor name, notes
- [ ] Unit test: throws 404 if visit_id belongs to different patient
- **Expand later:** include vitals/meds/labs when Groups 4-5 are built

### B4 — `GetRiskScoreTool`
- [ ] Create `app/Ai/Tools/GetRiskScoreTool.php`
- [ ] Implement `schema()` (no inputs)
- [ ] Delegate to `NutritionalRiskCalculator::calculate($patient)`
- [ ] Return JSON with: score (0-100), level (low/medium/high/critical), contributing_factors[]
- **Expand later:** add trend (compare to previous score over time)

---

## TASK GROUP C — Risk Calculator Service

### C1 — `NutritionalRiskCalculator`
- [ ] Create `app/Services/NutritionalRiskCalculator.php`
- [ ] Define scoring weights for each socioeconomic field (see `phase1_chat_patient_context.md`)
- [ ] Implement `calculate(Patient $patient): array` — returns score, level, factors
- [ ] Handle null socioeconomic (patient may not have it filled in)
- [ ] Unit test: covers all risk levels, null socioeconomic case, max score case
- **Expand later:** make weights configurable via `config/nutrition.php`

---

## TASK GROUP D — Agent

### D1 — `PatientAssistant` Agent
- [ ] Create `app/Ai/Agents/PatientAssistant.php`
- [ ] Implement `Agent` + `Conversational` interfaces
- [ ] Use `Promptable` + `RemembersConversations` traits
- [ ] Constructor receives `Patient $patient` and `User $doctor`
- [ ] `instructions()` — system prompt with doctor name, patient name, today's date
- [ ] `tools()` — returns all 4 Phase 1 tools with patient ID locked in
- [ ] Unit test: agent can be instantiated, tools list is correct
- **Expand later:** inject additional context (upcoming visits, last visit date) into system prompt

---

## TASK GROUP E — API Layer

### E1 — Form Requests
- [ ] Create `app/Http/Requests/ChatRequest.php`
  - Fields: `patient_id` (required, integer, exists:patients,id), `message` (required, string, max:2000), `conversation_id` (nullable, string)
- [ ] Create `app/Http/Requests/StreamChatRequest.php` (same rules, used for GET/stream)
- **Expand later:** add rate limiting rule per doctor

### E2 — `AssistantController`
- [ ] Create `app/Http/Controllers/Api/AssistantController.php`
- [ ] `chat()` method: authorize → build agent → prompt → return JSON response + conversation_id
- [ ] `stream()` method: authorize → build agent → stream → return SSE response
- [ ] Use `PatientPolicy::view()` for authorization (no new policy needed)
- **Expand later:** add `history()` method to return conversation thread for a given conversation_id

### E3 — Routes
- [ ] Add to `routes/api.php` under `auth:sanctum` + `role:doktor,admin` middleware:
  - `POST /api/assistant/chat`
  - `GET /api/assistant/stream`
- **Expand later:** `GET /api/assistant/history/{conversationId}`

### E4 — Feature Tests
- [ ] Test: unauthenticated request returns 401
- [ ] Test: patient role cannot access assistant (403)
- [ ] Test: doctor can chat about their patient
- [ ] Test: doctor cannot chat about a patient they cannot view (403 via policy)
- [ ] Test: invalid `patient_id` returns 422
- [ ] Test: conversation_id is returned and can be used to continue
- **Expand later:** test tool invocation side effects, streaming response format

---

## TASK GROUP F — Frontend (React)

### F1 — `ChatPanel` Component
- [ ] Sliding/collapsible panel on patient profile page
- [ ] Message thread: user messages right, assistant messages left
- [ ] Input field + send button
- [ ] Disabled state while waiting for response
- **Expand later:** markdown rendering for assistant responses (bold, lists, etc.)

### F2 — SSE Integration
- [ ] Implement streaming consumer using `fetch` + `ReadableStream` (preferred over `EventSource` for POST support)
- [ ] Append tokens to current message as they arrive
- [ ] Handle stream end event
- [ ] Handle stream error (show error state, allow retry)
- **Expand later:** abort controller to cancel in-flight stream

### F3 — Conversation State
- [ ] Store `conversation_id` in component state after first message
- [ ] Send `conversation_id` with all subsequent messages in same session
- [ ] Clear on patient navigation (new patient = new conversation)
- **Expand later:** persist conversation_id in localStorage so doctor can resume after page reload

---

## TASK GROUP G — Observability & Safety

### G1 — Error Handling
- [ ] Wrap agent calls in try/catch — return 503 with user-friendly message if AI provider is down
- [ ] Log failed AI calls via Laravel's logger (never log patient data in messages)
- **Expand later:** implement provider failover (OpenAI as backup to Anthropic)

### G2 — Rate Limiting
- [ ] Add `throttle:20,1` (20 requests per minute per user) to assistant routes
- **Expand later:** configurable rate limit per role via config file

### G3 — Input Sanitization
- [ ] Strip HTML from `message` input before sending to agent
- [ ] Max message length enforced in FormRequest (2000 chars)
- **Expand later:** content moderation layer if needed for compliance

---

## Summary — Build Order

```
A1 → A2 (setup)
    ↓
C1 (risk calculator — pure PHP, testable standalone)
    ↓
B1 → B2 → B3 → B4 (tools — depend on C1 for B4)
    ↓
D1 (agent — depends on all tools)
    ↓
E1 → E2 → E3 → E4 (API layer — depends on agent)
    ↓
F1 → F2 → F3 (frontend — depends on API)
    ↓
G1 → G2 → G3 (hardening — can be done in parallel with F)
```

---

## Estimated Task Counts

| Group | Tasks | Notes |
|---|---|---|
| A — Setup | 7 | One-time, sequential |
| B — Tools | 16 | Parallelizable after A |
| C — Risk Calculator | 5 | Independent, do early |
| D — Agent | 5 | Depends on B+C |
| E — API | 12 | Depends on D |
| F — Frontend | 9 | Depends on E |
| G — Safety | 6 | Parallel with F |
| **Total** | **60** | |
