# Research: AI Diet Plan Generator (014)

## D1 — SDK Installation & Provider

**Decision**: `composer require laravel/ai` + Anthropic provider (`claude-haiku-*` model)  
**Rationale**: `laravel/ai` is the official Laravel AI SDK. Anthropic Haiku is the cheapest capable model for structured generation (~$0.0003/plan at ~1,400 tokens total). No alternative SDK considered — this is the first agent and the SDK's structured output pattern is the key learning objective.  
**Alternatives considered**: PrismPHP (predecessor, API differs, migration cost); direct Anthropic SDK (no structured output or retry abstractions).  
**Install commands**:
```bash
composer require laravel/ai
php artisan vendor:publish --provider="Laravel\Ai\AiServiceProvider"
php artisan migrate   # creates agent_conversations + agent_conversation_messages tables
```
**Config**: `config/ai.php` — add `anthropic` driver with `ANTHROPIC_API_KEY` env var.

---

## D2 — Custom Job vs SDK Queue

**Decision**: Custom `GenerateDietPlanJob` (standard Laravel queued job), calling `$agent->prompt()` synchronously inside `handle()`.  
**Rationale**: The SDK's `->queue()->then()->catch()` fluent API does not give enough control over the manual retry-on-validation-failure pattern (attempt 1 → validate → if fail → attempt 2 → if fail → store failed status). A standard job's `handle()` method gives full control over the retry loop, status transitions, and failure reason storage.  
**Alternatives considered**: SDK's `->queue()` with `->then()/->catch()` — rejected because status transitions (pending → completed/failed) and the validation-retry loop require imperative control flow.

---

## D3 — Retry Strategy

**Decision**: Manual retry loop inside `handle()` — up to 2 attempts, no Laravel auto-retry.  
**Rationale**: Laravel's `$this->tries` auto-retry would re-dispatch the whole job and bypass the status-transition logic. The single manual retry keeps the plan record in "pending" status throughout both attempts, only updating to "completed" or "failed" at the end.  
**Implementation sketch**:
```php
$attempts = 0;
$lastError = null;
while ($attempts < 2) {
    try {
        $response = (new DietPlanAgent($this->patient))->prompt('Generate the plan.');
        $validated = $this->validate($response->toArray());
        $this->plan->update(['status' => 'completed', ...$validated]);
        return;
    } catch (\Illuminate\Validation\ValidationException $e) {
        $lastError = $e->getMessage();
        $attempts++;
    }
}
$this->plan->update(['status' => 'failed', 'failure_reason' => $lastError]);
```

---

## D4 — Queue Driver

**Decision**: Database queue (already configured as default).  
**Rationale**: `config/queue.php` already has `default => 'database'` and a `jobs` table target. No Redis or additional infra needed.  
**Prerequisite**: Verify `jobs` table exists (`php artisan queue:table && php artisan migrate` if not).

---

## D5 — Policy Design

**Decision**: New `DietPlanPolicy` class with three methods: `generate(User, Patient)`, `viewAny(User, Patient)`, `view(User, PatientDietPlan)`.  
**Rationale**: `PatientPolicy` already exists and governs patient record access. Diet plan authorization is a separate concern (different model, different actor constraint: patients explicitly excluded). A dedicated policy is cleaner than adding 3 methods to PatientPolicy.  
**All three methods**: return `$user->isAdmin() || $user->isDoctor()` — patients are explicitly excluded per FR-012.

---

## D6 — Structured Output Schema API

**Decision**: Use `JsonSchema $schema` parameter helpers (`$schema->string()`, `$schema->integer()`, `$schema->array()`, `$schema->object()`).  
**Rationale**: This is the correct SDK API signature — `schema(JsonSchema $schema): array`. The plain PHP array approach from the POC brainstorm was a sketch; the actual SDK uses typed builder methods.  
**Critical pitfall**: The SDK does NOT validate structured output. `Validator::make($response->toArray(), [...])` is mandatory after every `->prompt()` call.

---

## D7 — Polling vs WebSocket

**Decision**: Frontend polls `GET /api/patients/{patient}/diet-plans?limit=1` until status changes from "pending".  
**Rationale**: Spec explicitly scopes out Reverb/WebSocket for POC. Polling on a 2–3 second interval is acceptable for a ~15–30 second generation window.  
**Future upgrade path**: Replace polling with `$agent->broadcastOnQueue(channel: 'patient.'.$patient->id)` once Reverb is configured.

---

## D8 — Route Scoping

**Decision**: Manual check `if ($dietPlan->patient_id !== $patient->id) abort(404)` in `show()`, identical to `VisitController` pattern.  
**Rationale**: Consistent with established codebase convention. Ensures 404 (not 403) on cross-patient access before authorization runs.

---

## D9 — Frontend Polling Pattern

**Decision**: React `useEffect` with `setInterval` (or TanStack Query's `refetchInterval`) polling `GET /api/patients/{patient}/diet-plans` while the latest plan has `status: pending`. Stop polling on `completed` or `failed`.  
**Rationale**: No WebSocket dependency, simple to implement and test.
