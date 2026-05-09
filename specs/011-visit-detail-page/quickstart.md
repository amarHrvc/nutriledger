# Quickstart: Visit Detail Page

**Branch**: `011-visit-detail-page`

## Running the feature

```bash
# Start full dev stack (Laravel API + queue + Vite + Next.js)
composer run dev          # starts backend at http://localhost:8000
npm run dev               # starts frontend at http://localhost:3000 (run in /frontend)
```

## Navigating to the visit detail page

1. Log in as a doctor or admin
2. Go to `/dashboard/visits`
3. Click the **View** button on any visit row
4. The URL will be `/dashboard/visits/[visitId]?patient=[patientId]`

Alternatively, from a patient profile:
1. Go to `/dashboard/patients/[id]`
2. Open the **Visits** tab
3. Click **View** on any visit row

## Testing the feature

```bash
# Run all backend tests (auth, policy, API) for visits
php artisan test --filter=Visit

# Run the full test suite to check for regressions
php artisan test
```

## Files involved

### New files
```
frontend/src/app/(dashboard)/dashboard/visits/[id]/page.tsx
frontend/src/views/visits/VisitDetail.tsx
```

### Modified files
```
frontend/src/views/visits/index.tsx                             (add View link to each row)
frontend/src/views/patients/patient-right/visits/index.tsx      (add View link to each row)
```

### Unchanged files (referenced, not modified)
```
frontend/src/api/generated/visit/visit.ts          (patientsVisitsShow used)
frontend/src/api/generated/nutriBaseAPI.schemas.ts  (VisitResource type used)
frontend/src/views/visits/VisitEditForm.tsx         (reused in edit dialog)
frontend/src/context/AuthContext.tsx               (useAuth() for role checks)
```
