# Quickstart: Patient Socioeconomic Profile (013)

**Branch**: `013-socioeconomic-profile`

---

## Prerequisites

- Backend running: `cd backend && composer run dev`
- Frontend running: `cd frontend && pnpm run dev`
- Database seeded: `cd backend && php artisan migrate:fresh --seed`

## Verify backend is returning socioeconomicData

After Task BE-1:

```bash
# Get a patient token first (or use existing session)
curl -s http://localhost:8000/api/patients/1 \
  -H "Authorization: Bearer <token>" | jq '.data.patient.attributes.socioeconomicData'
```

Expected: full socioeconomic attributes object or `null`.

## Run backend tests

```bash
cd backend
php artisan test --filter=SocioeconomicData     # new test after Task BE-1
php artisan test --filter=PatientShow            # existing test, must still pass
php artisan test                                  # full suite — must be green
```

## Lint & analyse backend

```bash
cd backend
vendor/bin/pint --dirty
composer run analyse
```

## Frontend dev verification

1. Open `http://localhost:3000/dashboard/patients/1`
2. Confirm "Socioeconomic" tab appears after "Vitals"
3. Click the tab — confirm 5 sections with all fields displayed
4. Click "Edit" — confirm dialog opens pre-populated
5. Change a field, save — confirm tab reflects the change without page reload
6. Open Add New Patient — confirm collapsed Socioeconomic accordion at the bottom
7. Expand accordion — confirm 17 fields appear
8. Submit without expanding — confirm patient creates successfully

## Key files to create/modify

```
backend/app/Http/Resources/Api/PatientResource.php    ← MODIFY (embed socioeconomicData)
backend/tests/Feature/Api/PatientSocioeconomicTest.php ← CREATE

frontend/src/views/patients/socioeconomic/
├── types.ts          ← CREATE
├── labels.ts         ← CREATE
├── SocioeconomicTab.tsx    ← CREATE
├── SocioeconomicSection.tsx ← CREATE
└── SocioeconomicFields.tsx  ← CREATE

frontend/src/views/patients/patient-right/index.tsx   ← MODIFY (add tab)
frontend/src/views/patients/PatientForm.tsx            ← MODIFY (add accordion)
frontend/src/views/patients/PatientEditForm.tsx        ← MODIFY (add accordion)
```
