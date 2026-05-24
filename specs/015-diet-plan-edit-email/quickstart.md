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

# 3. Send the plan to the patient
curl -X POST http://localhost:8000/api/patients/1/diet-plans/1/send \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"

# 4. Verify delivery record in DB
php artisan tinker --execute="App\Models\DietPlanDelivery::latest()->first();"
```

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

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\DietPlanMailable;

Mail::fake();

// ... trigger send endpoint ...

Mail::assertSent(DietPlanMailable::class, function ($mail) use ($patient) {
    return $mail->hasTo($patient->user->email);
});
```

Use `Queue::fake()` alongside to assert jobs dispatched without running the worker.

---

## Key Files Created or Modified by This Feature

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   └── DietPlanController.php          ← modified (add update, send methods)
│   │   ├── Requests/
│   │   │   └── UpdateDietPlanRequest.php        ← new
│   │   └── Resources/Api/
│   │       ├── DietPlanResource.php             ← modified (add edit fields + latestDelivery)
│   │       └── DietPlanDeliveryResource.php     ← new
│   ├── Jobs/
│   │   └── SendDietPlanEmailJob.php             ← new
│   ├── Mail/
│   │   └── DietPlanMailable.php                 ← new
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
    ├── DietPlanCard.tsx                         ← modified (Edit + Send buttons, Edited badge)
    └── DietPlanEditForm.tsx                     ← new (inline edit form)
```
