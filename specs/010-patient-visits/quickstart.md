# Quickstart: Patient Visits Feature (010)

## Prerequisites

- Branch `010-patient-visits` checked out ✓
- `composer install` and `npm install` already done
- Backend running: `composer run dev`
- Frontend running: `npm run dev` (inside `frontend/`)

## Running the Feature

1. Run the new migration:
   ```bash
   cd backend && php artisan migrate
   ```

2. Verify the `visits` table has the new `time` column:
   ```bash
   php artisan tinker --execute="Schema::getColumnListing('visits')"
   ```

3. Seed test data (if using existing seeders):
   ```bash
   php artisan db:seed
   ```

4. Open `http://localhost:3000/dashboard/visits` — log in as a doctor or admin.

## Running Tests

```bash
# Backend — all visit-related tests
cd backend && php artisan test --filter=Visit

# Backend — full suite
cd backend && php artisan test
```

## Code Quality Gates (must pass before any PR)

```bash
cd backend
vendor/bin/pint --dirty
composer run analyse
php artisan test --filter=Visit
```

## Key Files Reference

| What | Where |
|---|---|
| Visit model | `backend/app/Models/Visit.php` |
| VisitPolicy | `backend/app/Policies/VisitPolicy.php` |
| VisitController | `backend/app/Http/Controllers/Api/VisitController.php` |
| StoreVisitRequest | `backend/app/Http/Requests/StoreVisitRequest.php` |
| UpdateVisitRequest | `backend/app/Http/Requests/UpdateVisitRequest.php` |
| VisitResource | `backend/app/Http/Resources/Api/VisitResource.php` |
| API routes | `backend/routes/api.php` |
| VisitResource TS types | `frontend/src/api/generated/nutriBaseAPI.schemas.ts` |
| BFF — global visits | `frontend/src/app/api/visits/route.ts` |
| BFF — patient visits | `frontend/src/app/api/patients/[id]/visits/route.ts` |
| Visits page | `frontend/src/views/visits/index.tsx` |
| Create visit form | `frontend/src/views/visits/VisitForm.tsx` |
| Edit visit form | `frontend/src/views/visits/VisitEditForm.tsx` |
| Patient profile tab | `frontend/src/views/patients/patient-right/visits/index.tsx` |
