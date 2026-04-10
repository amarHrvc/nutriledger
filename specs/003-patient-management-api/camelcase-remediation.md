# Case Convention Remediation Plan
## Patient Management API — Constitution Principle VI

**Decision**: Request inputs use snake_case. Response outputs use camelCase (in Resource classes only).
**Rationale**: Aligns with Laravel's native conventions — `$fillable`, factories, `$request->validated()`,
and Eloquent all use snake_case. Eliminates `Str::snake()` / `mapWithKeys()` conversion in controllers.
**Governed by**: `.specify/memory/constitution.md` Principle VI (v2.1.0, amended 2026-04-04)

---

## State Assessment

### Correct — no changes needed

| File | Why correct |
|---|---|
| `app/Http/Resources/Api/PatientResource.php` | Output only — maps `$this->first_name → 'firstName'`. This IS the camelCase boundary. |
| `app/Http/Resources/Api/PatientSocioeconomicResource.php` | Same — output only, mapping is correct. |
| `app/Services/PatientService.php` | Convention-neutral — accepts `array $patientData` and passes directly to `Patient::create()`. |
| Test response assertions (`assertJsonPath('data.attributes.firstName', ...)`) | Checking camelCase response output — correct. |
| Test structure assertions (`assertJsonStructure(['attributes' => ['firstName', ...]])`) | Checking response envelope — correct. |

### Broken — violates Principle VI

| File | Problem |
|---|---|
| `app/Http/Requests/StorePatientRequest.php` | Validation rule keys are camelCase (`userId`, `firstName`, `dateOfBirth`, etc.) |
| `tests/Feature/Patient/PatientApiTest.php` | Request payloads sent as camelCase; `assertJsonValidationErrors` uses camelCase error keys |

---

## Remediation Task (already-implemented code)

**One task covers both files** — they are tightly coupled (rule key changes force test key changes).

### StorePatientRequest — rule key mapping

| Current (camelCase) | Correct (snake_case) |
|---|---|
| `userId` | `user_id` |
| `firstName` | `first_name` |
| `lastName` | `last_name` |
| `dateOfBirth` | `date_of_birth` |
| `postalCode` | `postal_code` |
| `emergencyContactName` | `emergency_contact_name` |
| `emergencyContactPhone` | `emergency_contact_phone` |
| `bloodType` | `blood_type` |
| `medicalNotes` | `medical_notes` |
| `socioeconomic.maritalStatus` | `socioeconomic.marital_status` |
| `socioeconomic.numberOfDependents` | `socioeconomic.number_of_dependents` |
| `socioeconomic.employmentStatus` | `socioeconomic.employment_status` |
| `socioeconomic.incomeLevel` | `socioeconomic.income_level` |
| `socioeconomic.hasHealthInsurance` | `socioeconomic.has_health_insurance` |
| `socioeconomic.smokingStatus` | `socioeconomic.smoking_status` |
| `socioeconomic.alcoholConsumption` | `socioeconomic.alcohol_consumption` |
| `socioeconomic.physicalActivityLevel` | `socioeconomic.physical_activity_level` |
| `socioeconomic.foodSecurityStatus` | `socioeconomic.food_security_status` |
| `socioeconomic.additionalNotes` | `socioeconomic.additional_notes` |

### PatientApiTest — what changes vs what stays

**Change** (request payloads and validation error keys):
- All `postJson`/`patchJson` payload keys → snake_case per table above
- `assertJsonValidationErrors(['firstName', 'lastName', 'dateOfBirth', 'gender'])` → `['first_name', 'last_name', 'date_of_birth', 'gender']`
- `assertJsonValidationErrors(['dateOfBirth'])` → `['date_of_birth']`
- `assertJsonValidationErrors(['gender'])` → `['gender']` (single-word, unchanged)

**Keep** (response assertions — camelCase is correct for output):
- `assertJsonPath('data.attributes.firstName', 'Updated')` — keep
- `assertJsonStructure(['attributes' => ['firstName', 'lastName', 'fullName', ...]])` — keep
- All `->assertJsonPath('data.type', ...)` etc. — keep

---

## Impact on Upcoming Tasks

### T006 — UpdatePatientRequest (not yet implemented)

Write rules in snake_case from the start. All fields are `sometimes` (partial update).
`user_id` MUST be absent from the rules entirely (immutable after create — any `user_id` in payload is ignored).

Example structure:
```php
'first_name'   => ['sometimes', 'string', 'max:50'],
'last_name'    => ['sometimes', 'string', 'max:50'],
'date_of_birth'=> ['sometimes', 'date', 'before:today'],
// ...
'socioeconomic.marital_status' => ['sometimes', 'nullable', 'in:single,married,...'],
```

### T021-T027 — PatientController implementation

`$request->validated()` can be passed directly to `PatientService`. No conversion layer.

Suggested split for store:
```php
$patientData = $request->except(['socioeconomic']);
$socioData   = $request->input('socioeconomic');         // null if not sent
$patient = $this->service->createPatient($patientData, $socioData);
```

For update, same pattern with `$request->safe()->except(['socioeconomic'])`.

### T039-T043 — US3 socioeconomic tests (not yet written)

Payloads must use snake_case nested keys: `socioeconomic.marital_status`, `socioeconomic.income_level`, etc.

---

## Verification

After remediation task is done:

```bash
php artisan test tests/Feature/Patient/PatientApiTest.php
vendor/bin/pint --dirty
composer run analyse
```

Tests should remain in the red phase (controllers not yet implemented) but validation and
authorization tests that don't need controller logic should pass or fail for the right reasons.
