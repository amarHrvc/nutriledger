Feature name: SE API Foundation (shift)
Branch: feature/se-api-foundation
Type: BE/FE — backend API baseline + bare-bones React SPA scaffold

Parent context: NutriLedger pivot from Livewire monolith to Laravel REST API + React SPA.
This feature delivers the minimum infrastructure that all child features depend on.
It is not user-facing; it proves the full stack is wired correctly end-to-end.

Existing domain layer (already implemented — do not re-spec):
- Models, migrations, factories: User, Patient, PatientSocioeconomic, Visit
- Policies: PatientPolicy, VisitPolicy
- Form requests: StorePatientRequest, UpdatePatientRequest
- RoleMiddleware (registered in bootstrap/app.php)
- All Livewire routes and components are archived — must not be modified or removed

Reference documents (cite in plan, do not duplicate here):
- _docs/_shift/LARAVEL_API_RULES.md — API design rules, response format, versioning
- _docs/_shift/DB_SCHEMA_FINAL.md — authoritative schema
- _docs/_shift/IMPLEMENTATION_STATUS.md — what is and is not yet built

BE deliverables:
1. Sanctum token auth — POST /api/login returns Bearer token; POST /api/logout revokes
   it; GET /api/user returns authenticated user; unauthenticated requests return 401 JSON.

2. API route structure — routes/api.php with route groups protected by auth:sanctum.
   Role-based groups using RoleMiddleware for admin/doktor/pacijent. A public /api/ping
   health-check route returns { data: { status: "ok" } } for CORS smoke testing.

3. Standardised JSON responses — ApiResponses trait used by all controllers.
   Envelope: { data, message, errors }. HTTP 422 for validation, 401 for unauthenticated,
   403 for unauthorized, 404 for not found — all with consistent JSON shape.

4. CORS — localhost:5173 (dev) whitelisted. Preflight OPTIONS returns correct headers.

FE deliverables:
1. Vite + React + JavaScript project scaffold in /frontend folder.
   No TypeScript — SE track uses JavaScript.

2. Axios instance configured with VITE_API_URL env variable and a Bearer token
   interceptor stub (no auth state yet — that comes with 001-users).

3. A single smoke-test component or script that calls GET /api/ping and logs the
   response to the console — proves CORS works from a real browser request.

4. .env.example with VITE_API_URL=http://localhost:8000

Success criteria:
- POST /api/login with valid credentials → HTTP 200, { data: { token }, message }
- POST /api/login with invalid credentials → HTTP 401, { errors, message }
- GET /api/ping without auth → HTTP 200, { data: { status: "ok" } }
- Any auth:sanctum route without Bearer token → HTTP 401 JSON (not a redirect)
- Any role-protected route with wrong role → HTTP 403 JSON
- Browser fetch from localhost:5173 to localhost:8000/api/ping → no CORS error,
  correct Access-Control-Allow-Origin header present
- vendor/bin/pint --dirty passes, composer run analyse passes, affected Pest tests pass
- React SPA scaffold starts with npm run dev without errors
