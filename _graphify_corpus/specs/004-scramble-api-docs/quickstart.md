# Quickstart: Scramble API Docs + Orval

**Feature**: 004-scramble-api-docs

---

## Backend — View Live Docs

1. Start the backend:
   ```bash
   cd backend
   php artisan serve
   ```

2. Open in browser:
   - Interactive UI: `http://localhost:8000/docs/api`
   - Raw JSON spec: `http://localhost:8000/docs/api.json`

> Docs are only available in `local` and `testing` environments. Production returns 404.

---

## Frontend — Regenerate API Client

Run this whenever backend routes, Form Requests, or Resources change:

```bash
cd frontend
pnpm run api:generate
```

**Prerequisites**: Backend must be running (`php artisan serve`).

If the backend is offline, Orval exits with a network error — start the backend first.

Generated files land in `frontend/src/api/generated/` (git-ignored). Commit `orval.config.ts` but not the generated output.

---

## First-time Setup (after installing this feature)

```bash
# Backend
cd backend
composer require dedoc/scramble
php artisan vendor:publish --provider="Dedoc\Scramble\ScrambleServiceProvider" --tag=scramble-config

# Frontend
cd frontend
pnpm add -D orval
```

Then add to `frontend/package.json` scripts:
```json
"api:generate": "orval"
```
