# Research: SE API Foundation

**Feature**: `001-se-api-foundation` | **Date**: 2026-03-15 | **Plan**: [plan.md](plan.md)

## Decisions

### 1. API Versioning Strategy

**Decision**: Level 0 — no `/v1/` prefix
**Rationale**: LARAVEL_API_RULES.md §4 states "start at Level 0 for internal/MVP APIs; add versioning only when external consumers require it." NutriLedger has no external consumers at this stage — the only consumer is the React SPA we control.
**Alternatives considered**:
- Level 1 (`/api/v1/`): Adds URL complexity with zero current benefit; adds migration cost when we eventually drop it
- Header-based versioning: Overkill for a single-consumer MVP

---

### 2. Token Abilities (Sanctum)

**Decision**: Not used — roles enforced via `RoleMiddleware`, not token abilities
**Rationale**: `RoleMiddleware` is already implemented, JSON-aware, and registered in `bootstrap/app.php`. Token abilities would duplicate role enforcement without adding anything meaningful for MVP. LARAVEL_API_RULES.md §5 does not mandate abilities.
**Alternatives considered**:
- Token abilities per role: Adds complexity at issuance time; every login would need to know which abilities to assign; RoleMiddleware already covers the same access decisions

---

### 3. Response Envelope Format

**Decision**: `{ message, status, data }` for success; `{ message, errors }` for 422 validation
**Rationale**: Matches LARAVEL_API_RULES.md §2 exactly. Consistent shape across all response types allows client-side error handling to be written once. `data` holds the payload; `message` is human-readable; `errors` is field-keyed for validation failures; `status` mirrors the HTTP status code for clients that prefer it in the body.
**Alternatives considered**:
- Nested `{ success: bool, payload: {} }`: Non-standard; diverges from LARAVEL_API_RULES.md
- No envelope (raw resource): Breaks FR-007; client must handle shape differences per endpoint

---

### 4. CORS Implementation

**Decision**: Laravel built-in `config/cors.php` — add `http://localhost:5173` to `allowed_origins`
**Rationale**: Zero additional dependencies. Handles preflight OPTIONS automatically. Sufficient for both development (`localhost:5173`) and production (environment-specific origin). LARAVEL_API_RULES.md does not prescribe a specific CORS package.
**Alternatives considered**:
- `fruitcake/laravel-cors`: Deprecated — functionality merged into Laravel core in v8
- Custom middleware: Unnecessary given built-in support

---

### 5. React SPA Scaffold Tool

**Decision**: `npm create vite@latest frontend -- --template react`
**Rationale**: Official Vite scaffolding; React 19 compatible; produces minimal JavaScript (no TypeScript) project matching constitution v2.0.1 SE track requirement. No additional boilerplate beyond what this feature needs (no routing, no state management — those belong to 002-users FE).
**Alternatives considered**:
- Create React App: Deprecated
- Next.js: SSR framework — overkill for a bare connectivity scaffold; adds complexity we'd need to strip out for 002+
- Manual setup: More error-prone than official scaffolding

---

### 6. Token Expiry

**Decision**: 1 month — `Carbon::now()->addMonth()`
**Rationale**: LARAVEL_API_RULES.md §5 — "always set expiry; never issue non-expiring tokens." 1 month is the recommended MVP default: long enough that normal usage doesn't hit expiry mid-session, short enough to limit exposure from a leaked token.
**Alternatives considered**:
- 24 hours: Causes constant re-login friction in development; unacceptable for MVP UX
- Never expires: Explicitly prohibited by LARAVEL_API_RULES.md §5
- 7 days: Reasonable alternative; 1 month chosen as lower-friction MVP default

---

### 7. Global Exception Handling Location

**Decision**: `bootstrap/app.php` `withExceptions()` closure
**Rationale**: Laravel 12 streamlined structure — no `app/Exceptions/Handler.php`. `bootstrap/app.php` is the canonical place for exception registration. Two intercepts needed:
1. `AuthenticationException` → 401 JSON (prevents Fortify's redirect from firing on API routes)
2. `ValidationException` → 422 with `{ message, errors }` envelope (ensures FR-007 holds outside controllers)

**Alternatives considered**:
- Per-controller try/catch: Would need to be duplicated everywhere; global handler is DRY
- Custom exception classes: Adds abstraction without benefit at this scope
