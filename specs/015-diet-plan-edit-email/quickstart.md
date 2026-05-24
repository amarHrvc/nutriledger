# Quickstart: Diet Plan Edit and Email Delivery (015)

## Prerequisites

1. Feature 014 (`014-ai-diet-plan`) must be fully deployed — this feature extends it.

2. **Configure mail** in `backend/.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=mailpit          # or your SMTP host
   MAIL_PORT=1025
   MAIL_FROM_ADDRESS="noreply@nutriledger.local"
   MAIL_FROM_NAME="NutriLedger"
   ```
   For local development, [Mailpit](https://github.com/axllent/mailpit) is recommended — it captures all outgoing mail.

3. **Run migrations** (adds edit columns to `patient_diet_plans`, creates `diet_plan_deliveries`):
   ```bash
   cd backend && php artisan migrate
   ```

---

## Running the Feature Locally

```bash
# Terminal 1 — API server
cd backend && php artisan serve

# Terminal 2 — Queue worker (required for async email dispatch)
cd backend && php artisan queue:work

# Terminal 3 — Frontend
cd frontend && pnpm run dev

# Terminal 4 — Mailpit UI (optional, view captured emails)
mailpit   # then visit http://localhost:8025
```

---

## Smoke Test (curl)

```bash
# 1. Login and get token
TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"doctor@example.com","password":"password"}' \
  | jq -r '.data.token')

# 2. Edit a completed diet plan (replace {patient} and {plan} with real IDs)
curl -X PATCH http://localhost:8000/api/patients/1/diet-plans/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"rationale": "Clinician reviewed and approved."}'

# 3. Send the plan to the patient (email field is required in the request body)
curl -X POST http://localhost:8000/api/patients/1/diet-plans/1/send \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email": "patient@example.com"}'

# 4. Verify delivery record in DB
php artisan tinker --execute="App\Models\DietPlanDelivery::latest()->first();"
```

> **Note:** The `POST /send` endpoint requires an `email` field in the request body
> (`SendDietPlanRequest` validates `email` as required string). Omitting it returns 422.

---

## Testing

```bash
# Run all diet-plan tests (014 + 015 combined)
cd backend && php artisan test tests/Feature/diet-plans/

# Run only new 015 tests
cd backend && php artisan test --filter=DietPlanUpdate
cd backend && php artisan test --filter=DietPlanSend

# Full suite
cd backend && php artisan test
```

---

## Faking Mail in Tests

The Mailable class is `App\Mail\DietPlanMail` (not `DietPlanMailable`).

The job (`SendDietPlanEmailJob`) uses `Mail::to(...)->send()` synchronously inside
the job — it does **not** queue the mailable itself. The job is the queued unit.

To assert mail in tests, fake Mail **and** run the job synchronously:

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\DietPlanMail;

Mail::fake();

// ... trigger send endpoint ...
// The job is dispatched to the queue; in tests the queue is synchronous by default.

Mail::assertSent(DietPlanMail::class, function ($mail) use ($recipientEmail) {
    return $mail->hasTo($recipientEmail);
});
```

> **Gotcha:** `Mail::assertSent()` requires the queue to run synchronously in the test
> environment. Add `QUEUE_CONNECTION=sync` to `backend/.env.testing`, or use
> `Queue::fake()` + `Queue::assertPushed(SendDietPlanEmailJob::class)` instead to
> assert the job was queued without running it. The shipped tests use `Mail::fake()`
> with `Mail::assertSent()` commented out pending queue configuration.

---

## `latestDelivery` Eager-Loading Gotcha

`DietPlanResource` does **not** automatically include `latestDelivery`. The show
and update controller methods load `['doctor', 'editor']` but not deliveries:

```php
$dietPlan->load(['doctor', 'editor']);   // latestDelivery NOT included
```

As a result, `latestDelivery` is **not** returned by `GET /diet-plans/{id}` or
`PATCH /diet-plans/{id}`. The frontend re-fetches delivery state by calling
`GET /diet-plans/{id}` after the send endpoint returns the delivery object in its
own response envelope (`data.delivery`).

If you need `latestDelivery` in the show/update response in future, add to
the controller loads:

```php
$dietPlan->load(['doctor', 'editor', 'latestDelivery']);
```

and add to `DietPlanResource::toArray()`:

```php
'latestDelivery' => $this->whenLoaded('latestDelivery',
    fn () => new DietPlanDeliveryResource($this->latestDelivery)),
```

---

## Key Files Created or Modified by This Feature

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   └── DietPlanController.php          ← modified (add update, send methods)
│   │   ├── Requests/
│   │   │   ├── UpdateDietPlanRequest.php        ← new
│   │   │   └── SendDietPlanRequest.php          ← new (validates required email field)
│   │   └── Resources/Api/
│   │       ├── DietPlanResource.php             ← modified (add edit fields: isEdited, editedAt, editedBy)
│   │       └── DietPlanDeliveryResource.php     ← new
│   ├── Jobs/
│   │   └── SendDietPlanEmailJob.php             ← new (ShouldQueue, calls Mail::to()->send() inside handle())
│   ├── Mail/
│   │   └── DietPlanMail.php                     ← new (class name: DietPlanMail, not DietPlanMailable)
│   ├── Models/
│   │   ├── PatientDietPlan.php                  ← modified (edit columns, deliveries relation)
│   │   └── DietPlanDelivery.php                 ← new
│   ├── Policies/
│   │   └── DietPlanPolicy.php                   ← modified (add update, send methods)
│   └── Providers/
│       └── AppServiceProvider.php               ← modified (register DietPlanPolicy)
├── database/
│   ├── factories/
│   │   └── DietPlanDeliveryFactory.php          ← new
│   └── migrations/
│       ├── xxxx_add_edit_fields_to_patient_diet_plans_table.php ← new
│       └── xxxx_create_diet_plan_deliveries_table.php           ← new
├── resources/views/emails/
│   └── diet-plan.blade.php                      ← new (email template)
└── tests/Feature/diet-plans/
    ├── DietPlanUpdateTest.php                   ← new
    └── DietPlanSendTest.php                     ← new

frontend/
└── src/views/patients/diet-plans/
    ├── types.ts                                 ← extended (EditedBy, DeliveryRecord, isEdited, editedAt, editedBy, latestDelivery)
    ├── DietPlanDeliveryBadge.tsx                ← new (MUI Chip: sent/pending/failed)
    ├── DietPlanEditForm.tsx                     ← new (PATCH form with 7-day meal grid, isDirty guard, 422 errors)
    ├── DietPlanCard.tsx                         ← modified (Edit + Send buttons, Edited chip, DeliveryBadge, Snackbar)
    └── DietPlanSection.tsx                      ← modified (pass patientId/onUpdate, re-fetch after update)

frontend/src/app/api/patients/[id]/diet-plans/
    ├── [planId]/route.ts                        ← extended (add PATCH handler)
    └── [planId]/send/route.ts                   ← new (POST proxy for /send endpoint)
```
