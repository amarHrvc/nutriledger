# Data Model: AI Diet Plan Generator (014)

## New Table: `patient_diet_plans`

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id` | bigint unsigned | NO | PK, auto-increment |
| `patient_id` | bigint unsigned | NO | FK → `patients.id`, cascade delete |
| `generated_by` | bigint unsigned | NO | FK → `users.id`, restrict delete |
| `status` | enum('pending','completed','failed') | NO | Default: `pending` |
| `rationale` | text | YES | Overall plan rationale from AI |
| `daily_calories` | integer | YES | 1000–4000 range, validated |
| `nutritional_goals` | json | YES | `{protein_g, carbs_g, fat_g}` |
| `days` | json | YES | Array of 7 day objects |
| `warnings` | json | YES | Array of warning strings |
| `failure_reason` | varchar(500) | YES | Set only when status = failed |
| `created_at` | timestamp | NO | |
| `updated_at` | timestamp | NO | |

### `days` JSON shape (each element)

```json
{
  "day": "Monday",
  "breakfast": "Oatmeal with banana",
  "lunch": "Lentil soup with bread",
  "dinner": "Grilled chicken with roasted vegetables",
  "snack": "Apple"
}
```

### `nutritional_goals` JSON shape

```json
{ "protein_g": 90, "carbs_g": 220, "fat_g": 60 }
```

---

## New Model: `PatientDietPlan`

**Namespace**: `App\Models\PatientDietPlan`  
**Table**: `patient_diet_plans`

**Relationships**:
- `belongsTo(Patient::class)`
- `belongsTo(User::class, 'generated_by')` — exposed as `->doctor` relation

**Casts** (via `casts()` method):
- `nutritional_goals` → `array`
- `days` → `array`
- `warnings` → `array`
- `status` → string (plain string enum, no PHP Enum class per project convention)

**Scopes**:
- `scopeCompleted($query)` — `where('status', 'completed')`
- `scopeLatestCompleted($query)` — `completed()->latest()`

---

## Modified: `Patient` Model

Add relationship:
```php
public function dietPlans(): HasMany
{
    return $this->hasMany(PatientDietPlan::class);
}
```

---

## New Agent: `DietPlanAgent`

**Namespace**: `App\Ai\Agents\DietPlanAgent`  
**Attributes**: `#[Temperature(0.3)]`, `#[UseCheapestModel]`  
**Interfaces**: `Agent`, `HasStructuredOutput`  
**Trait**: `Promptable`

**Constructor injection**: `Patient $patient` (with `socioeconomic` relation eager-loaded)

**`instructions()` injects**:
- Age (computed from `date_of_birth`)
- Gender, blood type, allergies, medical notes
- Dietary restrictions, food security status, income level
- Activity level, smoking status, alcohol consumption

**`schema(JsonSchema $schema): array`** defines:
- `rationale` — `$schema->string()->required()`
- `daily_calories` — `$schema->integer()->required()`
- `nutritional_goals` — `$schema->object(['protein_g' => ..., 'carbs_g' => ..., 'fat_g' => ...])->required()`
- `days` — `$schema->array($schema->object([...]))->required()`
- `warnings` — `$schema->array($schema->string())->required()`

---

## New Job: `GenerateDietPlanJob`

**Namespace**: `App\Jobs\GenerateDietPlanJob`  
**Implements**: `ShouldQueue`  
**Constructor**: `PatientDietPlan $plan` (with `patient.socioeconomic` loaded)

**`handle()` flow**:
1. Load patient with socioeconomic data
2. Loop up to 2 attempts:
   - Call `(new DietPlanAgent($patient))->prompt('Generate the plan.')`
   - `Validator::make($response->toArray(), [...])` — throws `ValidationException` on failure
   - On success: update plan to `completed` with all fields → return
   - On `ValidationException`: increment attempt counter, continue loop
3. After 2 failed attempts: update plan to `failed` with last error message

**Validation rules**:
```php
'rationale'                  => ['required', 'string'],
'daily_calories'             => ['required', 'integer', 'between:1000,4000'],
'nutritional_goals'          => ['required', 'array'],
'nutritional_goals.protein_g'=> ['required', 'integer', 'min:0'],
'nutritional_goals.carbs_g'  => ['required', 'integer', 'min:0'],
'nutritional_goals.fat_g'    => ['required', 'integer', 'min:0'],
'days'                       => ['required', 'array', 'size:7'],
'days.*.day'                 => ['required', 'string'],
'days.*.breakfast'           => ['required', 'string'],
'days.*.lunch'               => ['required', 'string'],
'days.*.dinner'              => ['required', 'string'],
'days.*.snack'               => ['required', 'string'],
'warnings'                   => ['required', 'array'],
'warnings.*'                 => ['string'],
```

---

## New Policy: `DietPlanPolicy`

**Namespace**: `App\Policies\DietPlanPolicy`

| Method | Signature | Returns |
|---|---|---|
| `generate` | `(User $user, Patient $patient): bool` | `$user->isAdmin() \|\| $user->isDoctor()` |
| `viewAny` | `(User $user, Patient $patient): bool` | `$user->isAdmin() \|\| $user->isDoctor()` |
| `view` | `(User $user, PatientDietPlan $dietPlan): bool` | `$user->isAdmin() \|\| $user->isDoctor()` |

---

## Existing Tables (unchanged)

| Table | Role in this feature |
|---|---|
| `patients` | Source of clinical data fed to agent prompt |
| `patient_socioeconomic` | Source of socioeconomic data fed to agent prompt |
| `users` | `generated_by` FK target; auth actor |
