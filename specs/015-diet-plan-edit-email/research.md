# Research: Diet Plan Edit and Email Delivery (015)

## Decision 1: Email Sending Strategy — Queued vs Synchronous

**Decision**: Queued mail via `SendDietPlanEmailJob` (mirrors `GenerateDietPlanJob` from 014).

**Rationale**: The `send` endpoint must return 202 Accepted immediately without blocking on SMTP. A queued job handles the actual dispatch asynchronously. The delivery record is created with `status=pending` before the job is dispatched; the job updates it to `sent` or `failed` on completion. This matches the existing queue infrastructure (already configured in 014).

**Alternatives considered**:
- **Synchronous `Mail::send()`**: Simpler but blocks the HTTP response on SMTP round-trip. Rejected — violates SC-003 and mirrors the same problem solved in 014.
- **Fire-and-forget `Mail::queue()` without a job**: Would work but provides no hook for recording failure outcomes. Rejected — FR-011 requires failure to be surfaced.

---

## Decision 2: Edit Strategy — In-Place Update vs New Version Record

**Decision**: In-place PATCH — the existing `patient_diet_plans` row is updated. Three columns are added to the table: `is_edited` (boolean), `edited_by` (FK → users), `edited_at` (timestamp).

**Rationale**: The spec explicitly states editing updates the plan record in-place. Creating new version records would grow the history in a confusing way (two `completed` records for the same generation event). The `is_edited` flag cleanly distinguishes raw AI output from doctor-reviewed content in the UI and API response. The `DietPlanHistory` view already scopes history per patient, so in-place editing does not disrupt history display.

**Alternatives considered**:
- **New version record on every save**: Would preserve the original AI output unmodified. Rejected per spec Assumptions — overwrite is the documented design choice.
- **Separate `DietPlanEdit` table**: Adds join complexity without benefit given the in-place approach. Rejected.

---

## Decision 3: Email Template Format

**Decision**: Blade HTML email template (`resources/views/emails/diet-plan.blade.php`). Plain-text fallback via `->text()` method on the Mailable.

**Rationale**: Laravel's Mailable class renders Blade views natively. HTML provides a readable 7-day meal grid for the patient. A plain-text alternative is included for email clients that block HTML. PDF export is explicitly out of scope (spec Assumptions).

**Alternatives considered**:
- **Markdown mail (Laravel default)**: Generates acceptable HTML but limited layout control for the 7-day table. Plain Blade is equally simple and gives more formatting flexibility.
- **PDF attachment**: Out of scope per spec.

---

## Decision 4: Delivery Record Lifecycle

**Decision**: One `diet_plan_deliveries` row per send attempt. Created with `status=pending` when the doctor triggers a send. The `SendDietPlanEmailJob` updates to `status=sent` on success or `status=failed` with `failure_reason` on exception.

**Rationale**: This gives a complete audit trail (FR-008, FR-009) and ensures no silent failures (FR-011). The API `show` endpoint can include the latest delivery record so the FE can display "Last sent: [date]" without a separate request.

**Alternatives considered**:
- **Single `last_sent_at` column on `patient_diet_plans`**: Does not support FR-009 (multiple delivery records). Rejected.
- **Storing outcome only on success**: Would leave failed attempts unrecorded. Rejected — FR-011 is explicit.

---

## Decision 5: Authorization for Edit and Send

**Decision**: Extend `DietPlanPolicy` with two new methods: `update(User $user, PatientDietPlan $plan): bool` and `send(User $user, PatientDietPlan $plan): bool`. Both return `$user->isAdmin() || $user->isDoctor()`. Route scoping (abort 404 if plan doesn't belong to patient) is applied before policy check, matching the existing `show()` pattern.

**Rationale**: Consistent with existing 014 policy structure. Patients are excluded at the policy level (FR-012). The `DietPlanPolicy` is already the correct place for all diet-plan-level authorization.

**Alternatives considered**:
- **Gate::define() instead of Policy method**: Would bypass the existing Policy structure. Rejected for consistency.

---

## Decision 6: Validation of Edit Payload

**Decision**: `UpdateDietPlanRequest` validates all submitted fields with `sometimes` (partial update allowed). Required when present: `rationale` (string), `daily_calories` (integer, between 1000–4000), `nutritional_goals.protein_g/carbs_g/fat_g` (integer, min 0), `days` (array, size:7 when present), each day's meal fields (string, required). `warnings` (array of strings, nullable).

**Rationale**: Doctors may want to fix a single field (e.g. one meal). Making all fields `sometimes` allows partial saves. The `days` field is all-or-nothing — if sent, all 7 days must be present to prevent partial data corruption. This mirrors the validation rules in `GenerateDietPlanJob`.

**Alternatives considered**:
- **All fields required**: Forces doctor to re-submit the entire plan on every save. Rejected — unnecessarily restrictive for single-field corrections.

---

## Decision 7: `DietPlanPolicy` Registration

**Decision**: Register `DietPlanPolicy` in `AppServiceProvider::boot()` via `Gate::policy(PatientDietPlan::class, DietPlanPolicy::class)`. This was missing from 014 implementation — this feature must add it.

**Rationale**: Without explicit registration, Laravel may attempt autodiscovery. Explicit registration matches the pattern used for `VitalSignPolicy` and is deterministic. The missing registration from 014 is a gap that this feature must close to make `update` and `send` policy checks work correctly.
