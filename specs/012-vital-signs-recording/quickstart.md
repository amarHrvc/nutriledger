# Quickstart: Vital Signs Recording & History (012)

## Prerequisites

- Branch `012-vital-signs-recording` checked out
- Backend dependencies installed: `composer install` (from `backend/`)
- Frontend dependencies installed: `pnpm install` (from `frontend/`)
- Backend `.env` configured (copy from `.env.example`; SQLite is the default for dev)

---

## Backend Setup

```bash
cd backend

# Run migrations (adds vital_signs table)
php artisan migrate

# Seed test data (optional — creates users, patients, visits)
php artisan db:seed

# Start backend
composer run dev
```

Verify routes are registered:
```bash
php artisan route:list | grep vitals
```

Expected output:
```
GET     api/patients/{patient}/vitals                   VitalSignController@history
GET     api/patients/{patient}/visits/{visit}/vitals    VitalSignController@show
POST    api/patients/{patient}/visits/{visit}/vitals    VitalSignController@store
PATCH   api/patients/{patient}/visits/{visit}/vitals    VitalSignController@update
DELETE  api/patients/{patient}/visits/{visit}/vitals    VitalSignController@destroy
```

---

## Backend Tests

```bash
cd backend

# Run all vital sign tests
php artisan test tests/Feature/vitals/

# Run a specific test file
php artisan test tests/Feature/vitals/VitalSignStoreTest.php

# Run a specific test by name
php artisan test --filter="doctor can record vitals on own visit"

# Run full suite to check for regressions
php artisan test
```

All tests should pass before moving to the frontend phase.

---

## Frontend Setup

After backend routes are registered and the backend is running:

```bash
cd frontend

# Regenerate Orval API client from OpenAPI spec
pnpm run api:generate

# Verify types — zero errors expected
npx tsc --noEmit

# Start dev server
pnpm run dev
```

---

## Manual End-to-End Test Flow

### Record vitals (as doctor)

1. Log in as a doctor account
2. Navigate to any patient → open a visit (or use the global Visits page)
3. Click "Record vital signs"
4. Fill in weight (e.g. 74.5) and height (e.g. 175) — observe live BMI preview (~24.3)
5. Submit — vitals card appears with BMI chip and no flags
6. Repeat with systolic BP = 155 — flag alert should appear after save

### View vitals history (as doctor)

1. Open any patient's profile → Vitals tab
2. Verify table with columns: Date, BP, Heart Rate, Temperature, Weight, Height, BMI, Category, Flags
3. With two+ visits recorded: BMI delta chip should appear on second row

### Patient access (as patient)

1. Log in as a patient account
2. Verify no "Record" / "Edit" / "Delete" controls anywhere
3. Verify only own vitals are visible

### Admin delete

1. Log in as admin
2. Open any visit with vitals → click Delete on the vitals card
3. Confirm dialog → vitals removed, visit returns to empty state

---

## Quick Reference

| Role | Record | Edit | Delete | View own | View all |
|---|---|---|---|---|---|
| admin | ✅ | ✅ | ✅ | ✅ | ✅ |
| doktor | ✅ own visits | ✅ own visits | ❌ | ✅ | ✅ all patients |
| pacijent | ❌ | ❌ | ❌ | ✅ | ❌ |

| Measurement | Unit | Abnormal if |
|---|---|---|
| Systolic BP | mmHg | < 90 or > 140 |
| Diastolic BP | mmHg | < 60 or > 90 |
| Heart rate | bpm | < 50 or > 100 |
| Temperature | °C | < 36.0 or > 37.5 |
| BMI | — | < 18.5 (underweight), ≥ 25 (overweight), ≥ 30 (obese) |
