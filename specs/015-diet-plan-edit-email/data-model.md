# Data Model: Diet Plan Edit and Email Delivery (015)

## Modified Table: `patient_diet_plans`

Three columns added via migration (existing columns unchanged):

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `is_edited` | boolean | NO | Default `false`. Set to `true` on first doctor save. |
| `edited_by` | bigint unsigned | YES | FK → `users.id`, set null on user delete. Null = unedited AI output. |
| `edited_at` | timestamp | YES | Timestamp of most recent doctor save. |

---

## New Table: `diet_plan_deliveries`

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id` | bigint unsigned | NO | PK, auto-increment |
| `diet_plan_id` | bigint unsigned | NO | FK → `patient_diet_plans.id`, cascade delete |
| `sent_by` | bigint unsigned | NO | FK → `users.id`, restrict delete |
| `recipient_email` | varchar(255) | NO | Snapshot of patient email at send time |
| `status` | enum('pending','sent','failed') | NO | Default `pending` |
| `failure_reason` | varchar(500) | YES | Set only when status = failed |
| `created_at` | timestamp | NO | |
| `updated_at` | timestamp | NO | |

---

## Modified Model: `PatientDietPlan`

Add to `$fillable`:
```
'is_edited', 'edited_by', 'edited_at'
```

Add to `casts()`:
```php
'is_edited'  => 'boolean',
'edited_at'  => 'datetime',
```

Add relationship:
```php
public function editor(): BelongsTo
{
    return $this->belongsTo(User::class, 'edited_by');
}

public function deliveries(): HasMany
{
    return $this->hasMany(DietPlanDelivery::class, 'diet_plan_id');
}

public function latestDelivery(): HasOne
{
    return $this->hasOne(DietPlanDelivery::class, 'diet_plan_id')->latestOfMany();
}
```

---

## New Model: `DietPlanDelivery`

**Namespace**: `App\Models\DietPlanDelivery`
**Table**: `diet_plan_deliveries`

**Fillable**: `diet_plan_id`, `sent_by`, `recipient_email`, `status`, `failure_reason`

**Relationships**:
```php
public function dietPlan(): BelongsTo
{
    return $this->belongsTo(PatientDietPlan::class, 'diet_plan_id');
}

public function sender(): BelongsTo
{
    return $this->belongsTo(User::class, 'sent_by');
}
```

---

## Modified Model: `PatientDietPlan` — policy registration

`DietPlanPolicy` is registered in `AppServiceProvider::boot()`:
```php
Gate::policy(PatientDietPlan::class, DietPlanPolicy::class);
```
This was absent from 014 and must be added in this feature.

---

## Modified Policy: `DietPlanPolicy`

Add two methods:

| Method | Signature | Returns |
|---|---|---|
| `update` | `(User $user, PatientDietPlan $plan): bool` | `$user->isAdmin() \|\| $user->isDoctor()` |
| `send` | `(User $user, PatientDietPlan $plan): bool` | `$user->isAdmin() \|\| $user->isDoctor()` |

---

## New Mailable: `DietPlanMailable`

**Namespace**: `App\Mail\DietPlanMailable`
**Constructor**: `public function __construct(public PatientDietPlan $plan) {}`
**build()**: renders `resources/views/emails/diet-plan.blade.php`, subject "Your Personalised Diet Plan"
**Note**: recipient email is already stored on `DietPlanDelivery->recipient_email` and passed to `Mail::to()` by the job — no need to carry it in the Mailable.

---

## New Job: `SendDietPlanEmailJob`

**Namespace**: `App\Jobs\SendDietPlanEmailJob`
**Implements**: `ShouldQueue`
**Constructor**: `public function __construct(public DietPlanDelivery $delivery) {}`

**`handle()` flow**:
1. Load `$plan = $this->delivery->dietPlan`
2. `Mail::to($this->delivery->recipient_email)->send(new DietPlanMailable($plan))`
3. On success: `$this->delivery->update(['status' => 'sent'])`
4. `catch (Throwable $e)`: `$this->delivery->update(['status' => 'failed', 'failure_reason' => $e->getMessage()])`

---

## Validation Rules for `UpdateDietPlanRequest`

```php
'rationale'                   => ['sometimes', 'required', 'string'],
'daily_calories'              => ['sometimes', 'required', 'integer', 'between:1000,4000'],
'nutritional_goals'           => ['sometimes', 'required', 'array'],
'nutritional_goals.protein_g' => ['required_with:nutritional_goals', 'integer', 'min:0'],
'nutritional_goals.carbs_g'   => ['required_with:nutritional_goals', 'integer', 'min:0'],
'nutritional_goals.fat_g'     => ['required_with:nutritional_goals', 'integer', 'min:0'],
'days'                        => ['sometimes', 'required', 'array', 'size:7'],
'days.*.day'                  => ['required_with:days', 'string'],
'days.*.breakfast'            => ['required_with:days', 'string'],
'days.*.lunch'                => ['required_with:days', 'string'],
'days.*.dinner'               => ['required_with:days', 'string'],
'days.*.snack'                => ['required_with:days', 'string'],
'warnings'                    => ['sometimes', 'nullable', 'array'],
'warnings.*'                  => ['string'],
```

---

## Existing Tables (unchanged structure)

| Table | Role in this feature |
|---|---|
| `patients` | Source of `user.email` for delivery recipient |
| `users` | `edited_by`, `sent_by` FK targets; auth actor |
| `patient_diet_plans` | Updated with edit fields; source of plan content for email |
