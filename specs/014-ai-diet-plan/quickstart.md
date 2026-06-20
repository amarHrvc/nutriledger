# Quickstart: AI Diet Plan Generator (014)

## Prerequisites

1. **Install the Laravel AI SDK** (from `backend/`):
   ```bash
   composer require laravel/ai
   php artisan vendor:publish --provider="Laravel\Ai\AiServiceProvider"
   php artisan migrate
   ```

2. **Add Anthropic API key** to `backend/.env`:
   ```env
   ANTHROPIC_API_KEY=sk-ant-...
   AI_DEFAULT_PROVIDER=anthropic
   ```

3. **Verify the `jobs` table exists** (database queue driver is already configured):
   ```bash
   php artisan queue:table    # only if jobs table doesn't exist
   php artisan migrate
   ```

---

## Running the Feature Locally

```bash
# Terminal 1 — API server
cd backend && php artisan serve

# Terminal 2 — Queue worker (required — generation is async)
cd backend && php artisan queue:work

# Terminal 3 — Frontend
cd frontend && pnpm run dev
```

---

## Smoke Test (curl)

```bash
# 1. Login and get token
TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"doctor@example.com","password":"password"}' \
  | jq -r '.data.token')

# 2. Trigger generation for patient ID 1
curl -X POST http://localhost:8000/api/patients/1/diet-plans \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"

# 3. Poll for status (repeat until status != "pending")
curl http://localhost:8000/api/patients/1/diet-plans \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"

# 4. Fetch full plan detail (use ID from step 3)
curl http://localhost:8000/api/patients/1/diet-plans/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

---

## Testing

```bash
# Run diet plan feature tests only
cd backend && php artisan test tests/Feature/diet-plans/

# Run a specific test
cd backend && php artisan test --filter=DietPlanGenerateTest

# Full suite
cd backend && php artisan test
```

---

## Key Files Created by This Feature

```
backend/
├── app/
│   ├── Ai/Agents/DietPlanAgent.php
│   ├── Http/
│   │   ├── Controllers/Api/DietPlanController.php
│   │   ├── Requests/StoreDietPlanRequest.php
│   │   └── Resources/Api/
│   │       ├── DietPlanResource.php
│   │       └── DietPlanSummaryResource.php
│   ├── Jobs/GenerateDietPlanJob.php
│   ├── Models/PatientDietPlan.php
│   └── Policies/DietPlanPolicy.php
├── database/migrations/xxxx_create_patient_diet_plans_table.php
├── config/ai.php                  (published by SDK)
└── tests/Feature/diet-plans/
    ├── DietPlanGenerateTest.php
    ├── DietPlanListTest.php
    └── DietPlanShowTest.php

frontend/
└── src/
    └── pages/patients/
        └── components/DietPlanSection/
            ├── DietPlanSection.jsx    (container, polling logic)
            ├── DietPlanCard.jsx       (renders completed plan)
            └── DietPlanHistory.jsx    (list of past plans)
```

---

## Faking AI Calls in Tests

The SDK provides `Agent::fake()` to avoid real API calls in tests:

```php
use Laravel\Ai\Facades\Agent;

Agent::fake([
    DietPlanAgent::class => [
        'rationale'          => 'Test plan rationale.',
        'daily_calories'     => 1800,
        'nutritional_goals'  => ['protein_g' => 90, 'carbs_g' => 220, 'fat_g' => 60],
        'days'               => array_fill(0, 7, [
            'day'       => 'Monday',
            'breakfast' => 'Oatmeal',
            'lunch'     => 'Salad',
            'dinner'    => 'Chicken',
            'snack'     => 'Apple',
        ]),
        'warnings'           => [],
    ],
]);
```

Use `Queue::fake()` alongside to assert jobs were dispatched without running the queue worker.
