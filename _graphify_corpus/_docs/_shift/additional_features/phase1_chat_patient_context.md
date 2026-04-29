# Phase 1 — Doctor Assistant: Chat with Patient Context

## What This Is

A conversational AI panel embedded in the patient profile page. When a doctor views a patient, a chat sidebar lets them ask natural language questions about that specific patient. The AI has real-time access to the patient's data via tools it can invoke during the conversation.

This is **not** a simple prompt/response — it is a proper AI agent with tool use. The agent decides which data to fetch based on the question, fetches it from the real database, and uses it to answer.

---

## How It Works End-to-End

```
Doctor opens patient profile → chat panel is visible
Doctor types: "What were the main topics in the last 2 visits?"

→ React sends POST /api/assistant/chat
    { patient_id: 42, message: "...", conversation_id: null }

→ PatientAssistant agent receives the message
→ Laravel AI SDK sends to Claude with:
    - System prompt (who the doctor is, which patient, today's date)
    - Available tools: list_visits, get_visit, get_patient, get_risk_score
    - The doctor's message

→ Claude decides: "I need visit data" → calls list_visits(patient_id=42, limit=2)
→ Laravel executes ListVisitsTool.handle() → real Eloquent query → returns JSON
→ Claude reads the visit data → generates answer
→ Response streamed back word-by-word via SSE
→ React displays streaming text in chat panel
→ Conversation saved to DB (thread ID returned for follow-up messages)
```

---

## Laravel AI SDK — Key Concepts

The `laravel/ai` package (Laravel 12+) centers on three primitives:

### Agent
A PHP class implementing `Agent` with the `Promptable` trait. Holds:
- `instructions()` — the system prompt
- `tools()` — which tools the agent can call
- Optional: `Conversational` interface for persistent conversation threads

### Tool
A PHP class implementing `Tool`. The SDK auto-invokes it when Claude decides to call it. Has:
- `description()` — plain English for the LLM ("what does this tool do")
- `schema()` — JSON schema of the input parameters
- `handle(Request $request)` — the actual PHP code that runs (Eloquent query, computation, etc.)

### Streaming
Instead of `.prompt()` you call `.stream()` — Laravel returns an SSE response automatically. The frontend reads it token-by-token with `EventSource` or `fetch` + `ReadableStream`.

---

## What Needs to Be Built

### 1. Install & Configure Laravel AI SDK

```bash
composer require laravel/ai
php artisan vendor:publish --provider="Laravel\Ai\AiServiceProvider"
php artisan migrate
```

`.env` additions:
```env
ANTHROPIC_API_KEY=sk-ant-...
AI_DEFAULT_PROVIDER=anthropic
AI_DEFAULT_MODEL=claude-sonnet-4-5
```

`config/ai.php` — published by the package, configure default provider and model.

---

### 2. The Agent — `PatientAssistant`

```php
// app/Ai/Agents/PatientAssistant.php

use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;

class PatientAssistant implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    public function __construct(
        private readonly Patient $patient,
        private readonly User $doctor,
    ) {}

    public function instructions(): string
    {
        return <<<PROMPT
        You are a clinical assistant for {$this->doctor->name} at NutriLedger.
        Today's date is {$today}.
        You have access to medical records for patient: {$this->patient->full_name}
        (ID: {$this->patient->id}, DOB: {$this->patient->date_of_birth}).

        Answer questions about this patient's profile, medical history, visits,
        and nutritional risk. Be clinical, concise, and factual.
        Only reference data returned by your tools — never invent information.
        When asked to summarize, structure your response clearly.
        PROMPT;
    }

    public function tools(): iterable
    {
        return [
            new GetPatientTool($this->patient->id),
            new ListVisitsTool($this->patient->id),
            new GetVisitTool($this->patient->id),
            new GetRiskScoreTool($this->patient->id),
        ];
    }
}
```

**Key design decision:** the patient ID is injected at construction and locked into all tools. The agent cannot query other patients. Scope is enforced at the PHP level, not just the prompt level.

---

### 3. The Four Tools (Phase 1)

#### `GetPatientTool`
Returns full patient profile including socioeconomic data.

```php
// app/Ai/Tools/GetPatientTool.php

class GetPatientTool implements Tool
{
    public function __construct(private readonly int $patientId) {}

    public function description(): string
    {
        return 'Get the full profile of the patient including personal details,
                medical metadata (blood type, allergies, notes),
                and socioeconomic data (employment, income, food security, lifestyle).';
    }

    public function schema(JsonSchema $schema): array
    {
        return []; // no inputs — patient ID is locked at construction
    }

    public function handle(Request $request): string
    {
        $patient = Patient::with('socioeconomic')
            ->findOrFail($this->patientId);

        return json_encode([
            'id'                  => $patient->id,
            'full_name'           => $patient->full_name,
            'date_of_birth'       => $patient->date_of_birth->format('Y-m-d'),
            'gender'              => $patient->gender,
            'blood_type'          => $patient->blood_type,
            'allergies'           => $patient->allergies,
            'medical_notes'       => $patient->medical_notes,
            'socioeconomic'       => $patient->socioeconomic,
        ]);
    }
}
```

#### `ListVisitsTool`
Returns visit history, most recent first. Accepts optional limit.

```php
// app/Ai/Tools/ListVisitsTool.php

public function description(): string
{
    return 'List the visit history for this patient, ordered by most recent first.
            Returns date, attending doctor, and notes for each visit.';
}

public function schema(JsonSchema $schema): array
{
    return [
        'limit' => $schema->integer()->min(1)->max(20)->default(5),
    ];
}

public function handle(Request $request): string
{
    $visits = Visit::with('doctor')
        ->where('patient_id', $this->patientId)
        ->latest('date')
        ->limit($request['limit'] ?? 5)
        ->get()
        ->map(fn ($v) => [
            'id'     => $v->id,
            'date'   => $v->date,
            'doctor' => $v->doctor->name,
            'notes'  => $v->notes,
        ]);

    return json_encode($visits);
}
```

#### `GetVisitTool`
Returns a single visit by ID. Validates the visit belongs to this patient.

```php
// app/Ai/Tools/GetVisitTool.php

public function description(): string
{
    return 'Get the full details of a specific visit by its ID.
            Use list_visits first to find the visit ID.';
}

public function schema(JsonSchema $schema): array
{
    return [
        'visit_id' => $schema->integer()->required(),
    ];
}

public function handle(Request $request): string
{
    $visit = Visit::with('doctor')
        ->where('patient_id', $this->patientId) // scope enforced
        ->findOrFail($request['visit_id']);

    return json_encode([
        'id'     => $visit->id,
        'date'   => $visit->date,
        'doctor' => $visit->doctor->name,
        'notes'  => $visit->notes,
    ]);
}
```

#### `GetRiskScoreTool`
Computes nutritional risk score from socioeconomic data and returns breakdown.

```php
// app/Ai/Tools/GetRiskScoreTool.php

public function description(): string
{
    return 'Calculate the nutritional risk score for this patient based on
            their socioeconomic profile. Returns a score (0-100), a risk level
            (low/medium/high/critical), and a breakdown of contributing factors.';
}

public function schema(JsonSchema $schema): array
{
    return []; // no inputs
}

public function handle(Request $request): string
{
    $patient = Patient::with('socioeconomic')->findOrFail($this->patientId);
    $score   = NutritionalRiskCalculator::calculate($patient); // service to be built

    return json_encode($score);
}
```

---

### 4. The Controller

```php
// app/Http/Controllers/Api/AssistantController.php

class AssistantController extends Controller
{
    public function chat(ChatRequest $request): JsonResponse
    {
        $patient = Patient::findOrFail($request->patient_id);
        $this->authorize('view', $patient);  // existing PatientPolicy

        $agent = (new PatientAssistant($patient, $request->user()))
            ->forUser($request->user());

        if ($request->conversation_id) {
            $agent = $agent->continue($request->conversation_id, as: $request->user());
        }

        $response = $agent->prompt($request->message);

        return response()->json([
            'message'         => (string) $response,
            'conversation_id' => $response->conversationId,
        ]);
    }

    public function stream(StreamChatRequest $request): StreamedResponse
    {
        $patient = Patient::findOrFail($request->patient_id);
        $this->authorize('view', $patient);

        $agent = (new PatientAssistant($patient, $request->user()))
            ->forUser($request->user());

        if ($request->conversation_id) {
            $agent = $agent->continue($request->conversation_id, as: $request->user());
        }

        return $agent->stream($request->message);
        // Laravel AI SDK returns StreamedResponse with text/event-stream automatically
    }
}
```

**Routes** (`routes/api.php`):
```php
Route::middleware(['auth:sanctum', 'role:doktor,admin'])->group(function () {
    Route::post('/assistant/chat',   [AssistantController::class, 'chat']);
    Route::get('/assistant/stream',  [AssistantController::class, 'stream']);
});
```

---

### 5. `NutritionalRiskCalculator` Service

Pure PHP class, no LLM. Scores socioeconomic fields:

| Field | Risk weight |
|---|---|
| `food_security_status = severely_insecure` | +30 |
| `food_security_status = food_insecure` | +20 |
| `income_level = low` | +15 |
| `has_health_insurance = false` | +10 |
| `physical_activity_level = sedentary` | +10 |
| `employment_status = unemployed` | +10 |
| `smoking_status = current_heavy` | +10 |
| `alcohol_consumption = heavy` | +10 |
| `has_family_support = false` | +5 |

Score → risk level: 0-20 low / 21-40 medium / 41-65 high / 66+ critical

Returns score, level, and which factors contributed.

---

### 6. Conversation Persistence

The `RemembersConversations` trait + `Conversational` interface handles this via the SDK's migration (published in step 1). The DB table stores:
- Thread ID
- User (doctor) ID
- Messages (role + content)
- Timestamps

Thread is scoped per doctor (`forUser($user)`). The doctor can continue a conversation by passing the `conversation_id` returned in the first response.

---

### 7. Authorization

No new policy needed — `PatientPolicy::view()` already exists and is used. Tools are locked to the injected `$patientId` so even if someone tampers with `patient_id` in the request, the policy check at the controller level rejects it before the agent runs.

---

### 8. React Frontend (Phase 1 Scope)

Minimal — this is backend-focused phase:

- `ChatPanel` component alongside the patient profile page
- Message thread display (user messages right-aligned, assistant left-aligned)
- Input field + send button
- SSE consumer: `EventSource` or `fetch` with `ReadableStream`
- Store `conversation_id` in component state for follow-up messages
- Loading indicator while streaming

---

## Data Flow Diagram

```
Doctor (React)
    │
    │  POST /api/assistant/stream
    │  { patient_id, message, conversation_id? }
    ▼
AssistantController
    │  authorize('view', patient)  ← PatientPolicy
    │  new PatientAssistant($patient, $doctor)
    ▼
PatientAssistant (Agent)
    │  system prompt with patient + doctor context
    │  tools: [GetPatient, ListVisits, GetVisit, GetRiskScore]
    ▼
Laravel AI SDK → Claude (Anthropic)
    │  Claude decides tool calls
    │  ← GetPatientTool.handle() → Eloquent → DB
    │  ← ListVisitsTool.handle() → Eloquent → DB
    │  Claude generates response
    ▼
SSE stream → React
    │  tokens arrive word-by-word
    │  conversation_id saved in component state
    ▼
Doctor sees answer
```

---

## What Phase 1 Does NOT Include

- Cross-patient queries (Phase 2)
- Report/PDF generation (Phase 3)
- n8n workflows (Phase 4)
- Voice input
- Admin or patient role access to assistant
- Custom UI beyond a basic functional panel

---

## Open Questions Before Building

1. **Streaming or request/response first?** Streaming is better UX but slightly more complex frontend. Recommended to do streaming from day one.
2. **Which Claude model?** `claude-haiku-4-5` is fast and cheap for tool-heavy Q&A. `claude-sonnet-4-6` is stronger for synthesis. Start with Haiku, upgrade if quality is insufficient.
3. **Conversation thread scope:** Per patient (doctor has one thread per patient) or per session (fresh each time)? Per-patient threads give continuity; per-session is simpler.
4. **Risk calculator:** Should it be recomputed on every tool call or cached? Recommend computing live — it's cheap and always fresh.
