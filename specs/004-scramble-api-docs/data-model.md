# Data Model: Setup Scramble API Documentation

**Feature**: 004-scramble-api-docs
**Date**: 2026-04-07

---

## No New Database Entities

This feature does not introduce any new Eloquent models or database migrations. Scramble reads existing routes, Form Requests, and Resources at runtime — it does not persist anything.

---

## Configuration Entities

These are file-based configuration objects, not database entities.

### Scramble Config (`config/scramble.php`)

Published from the package. Key fields used:

| Key | Value | Purpose |
|---|---|---|
| `api_path` | `api` | Prefix for routes Scramble scans |
| `info.title` | `NutriBase API` | Displayed in Swagger UI |
| `info.version` | `1.0.0` | Displayed in Swagger UI |
| `middleware` | `[]` | No extra middleware on docs routes (dev-only restriction is in AppServiceProvider) |

### Orval Config (`frontend/orval.config.ts`)

| Key | Value | Purpose |
|---|---|---|
| `input.target` | `http://localhost:8000/docs/api.json` | Live spec URL from Scramble |
| `output.target` | `./src/api/generated` | Where generated files land |
| `output.client` | `fetch` | Native fetch wrappers (no Axios) |
| `output.mode` | `tags-split` | One file per tag (patients, users, auth) |

---

## Generated Output Structure

Files produced by Orval under `frontend/src/api/generated/` (not committed to repo — in `.gitignore`):

```
frontend/src/api/generated/
├── patients.ts       ← typed fetch wrappers for /api/patients endpoints
├── users.ts          ← typed fetch wrappers for /api/users endpoints
├── auth.ts           ← typed fetch wrappers for login/logout/me
└── model/
    ├── patientResource.ts
    ├── userResource.ts
    └── ...            ← TypeScript interfaces per Resource shape
```
