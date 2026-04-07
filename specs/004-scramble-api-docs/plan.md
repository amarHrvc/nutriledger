# Implementation Plan: Setup Scramble API Documentation

**Branch**: `004-scramble-api-docs` | **Date**: 2026-04-07 | **Spec**: [spec.md](spec.md)
**Input**: Feature specification from `/specs/004-scramble-api-docs/spec.md`

## Summary

Install and configure Scramble (`dedoc/scramble`) on the Laravel 12 backend to expose a live OpenAPI 3.1 spec and Swagger UI. Configure Orval on the Next.js 16 frontend to consume the live spec URL and generate typed fetch wrappers and TypeScript interfaces. Docs are restricted to non-production environments. No existing code requires annotation or modification.

## Technical Context

**Language/Version**: PHP 8.4 (backend), TypeScript 5.9 (frontend)
**Primary Dependencies**: `dedoc/scramble` (BE), `orval` devDependency (FE)
**Storage**: N/A — no database changes
**Testing**: Pest 4 (BE), TypeScript compiler (FE type check)
**Target Platform**: Laravel 12 local dev server
**Project Type**: Web service (REST API) + Next.js SPA
**Performance Goals**: Spec endpoint response time is not a concern — developer tooling only
**Constraints**: Docs endpoints MUST NOT be active in production; zero annotations on existing code
**Scale/Scope**: Covers all current API routes (auth, users, patients — ~15 endpoints)

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | Notes |
|---|---|---|
| I. Dual-Track Architecture | PASS | BE-only change; no model/migration changes; FE config file only |
| II. Authorization at Every Layer | PASS | Docs endpoints carry no protected data; dev-only restriction is environment gate, not auth gate |
| III. Test-First | PASS | One Pest test required: verify `/docs/api.json` returns 200 in local env |
| IV. Code Quality Gates | PASS | Pint + Larastan run on all changed PHP files; no new ignores |
| V. Tasks Are Developer-Ready Specs | PASS | Tasks will include exact commands, file paths, verification steps |
| VI. API Input/Output Case Convention | N/A | No new Form Requests or Resources |
| VII. API Response Envelope | N/A | Docs endpoints are served by Scramble, not ApiResponses trait |

## Project Structure

### Documentation (this feature)

```text
specs/004-scramble-api-docs/
├── plan.md              ← this file
├── research.md          ← Phase 0 output
├── data-model.md        ← Phase 1 output
├── quickstart.md        ← Phase 1 output
├── contracts/
│   └── docs-endpoints.md
└── tasks.md             ← Phase 2 output (/speckit.tasks — NOT created by /speckit.plan)
```

### Source Code Changes

```text
backend/
├── composer.json                          ← add dedoc/scramble
├── config/scramble.php                    ← published config (title, version, route exclusions)
└── app/Providers/AppServiceProvider.php  ← Scramble::routes() + extendOpenApi() + env gate

frontend/
├── package.json                           ← add api:generate script
├── orval.config.ts                        ← new file: Orval config pointing at live spec URL
└── src/api/generated/                     ← git-ignored; populated by pnpm run api:generate
    ├── patients.ts
    ├── users.ts
    ├── auth.ts
    └── model/
```

**Structure Decision**: Web application layout (separate `backend/` and `frontend/`). BE tasks and FE tasks are separated per Constitution Principle V.

## Complexity Tracking

No constitution violations — table omitted.
