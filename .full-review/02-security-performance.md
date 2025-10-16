# Phase 2: Security & Performance Review

## Security Findings

### Critical (CVSS 8.0+)

| ID | File | Issue | CVSS | CWE |
|----|------|-------|------|-----|
| SEC-01 | `routes/web.php` | No `->can()` middleware on `edit` and `delete` routes — violates defense-in-depth; inconsistent with `create` route | 8.8 | CWE-862 |
| SEC-02 | `ManageSocioeconomic::save()` | No `$this->authorize()` before write — TOCTOU: mount() auth does not protect the action call | 8.1 | CWE-863 |
| SEC-03 | `PatientSocioeconomic::$fillable` | `patient_id` is mass-assignable — could allow FK reassignment to different patient | 8.1 | CWE-915 |

### High

| ID | File | Issue | CVSS | CWE |
|----|------|-------|------|-----|
| SEC-04 | `PatientSocioeconomicPolicy` | No doctor-patient scoping — any doctor can view/edit/delete any patient's PHI | 6.5 | CWE-639 |
| SEC-05 | `PatientSocioeconomicFactory` | Factory generates values outside validation whitelist (old enum values pre-refactor) — tests pass with invalid data | 5.9 | CWE-1286 |
| SEC-06 | Migration 2 | No DB-level constraints after enum→VARCHAR conversion; `down()` is a no-op; `PRAGMA foreign_keys = OFF` with no rollback | 5.9 | CWE-20 |

### Medium

| ID | File | Issue | CVSS |
|----|------|-------|------|
| SEC-07 | `ManageSocioeconomic.php` | `public bool $isEditing` tamper risk via Livewire wire protocol — needs `#[Locked]` | 5.4 |
| SEC-08 | `ViewSocioeconomic::mount()` | Authorization fallback to `PatientPolicy::view()` when no record exists — wrong policy boundary | 4.3 |
| SEC-09 | `StoreSocioeconomicRequest.php` | Dead `authorize(): bool { return true; }` — dangerous if ever wired in | 3.7 |

### Low

| ID | File | Issue |
|----|------|-------|
| SEC-10 | No audit logging | No tracking of who viewed/modified PHI — HIPAA/GDPR compliance gap |
| SEC-11 | `PatientSocioeconomic` model | Free-text fields (`dietary_restrictions_cultural`, `additional_notes`) not encrypted at rest |
| SEC-12 | `RoleMiddleware.php:20` | `Log::debug()` on every request logs role decisions — noise, potential info disclosure |
| SEC-13 | `routes/web.php` | `show` route also lacks `->can()` middleware |

**XSS:** Not a finding — Blade `{{ }}` escaping is used throughout, no raw `{!! !!}`.
**CSRF:** Not a finding — Livewire 3 handles CSRF on all wire requests natively.

---

## Performance Findings

### High

| ID | File | Issue | Impact |
|----|------|-------|--------|
| PERF-01 | `PatientSocioeconomicPolicy::view():27` | `$socioeconomic->patient->user_id` fires an extra DB query for every `pacijent`-role page load — lazy-loads the `patient` relationship when `patient_id` is already on the model | 1 extra query per patient-role load |

### Medium

| ID | File | Issue | Impact |
|----|------|-------|--------|
| PERF-02 | `ViewPatient::mount()` | Loads full `PatientSocioeconomic` row (18 columns) but view only uses 3; held as public Livewire property causing re-hydration on any future action | Unnecessary data transfer; Livewire serialization |
| PERF-03 | `patients` table | Potential missing index on `patients.user_id` — used in policy checks and `PatientList` search | Full scan at 10k+ rows |
| PERF-04 | `PatientList` search | Leading `%` LIKE on `first_name`, `last_name`, subquery on `email` — can't use B-tree index | Measurable at 2k+ patients |
| PERF-05 | Schema divergence | ENUM in MySQL prod vs VARCHAR in SQLite test — adding enum values requires ALTER TABLE on prod | Deployment friction |

### Low

| ID | File | Issue |
|----|------|-------|
| PERF-06 | `ManageSocioeconomic` | 17 public properties add to wire payload; acceptable since `wire:model` is deferred (not live) |
| PERF-07 | `mount()` toArray() hydration | Iterates 20-key array with `property_exists()` — negligible at single-record scale |
| PERF-08 | Validation rules | Fresh array construction on every `save()` — opcode-cached, no measurable cost |

---

## Critical Issues for Phase 3 Context

1. **SEC-05 (Factory values mismatched)** — tests are currently passing with invalid data in factories. Test coverage may be masking validation edge cases.
2. **SEC-02 (No re-auth in save())** — test suite should verify that `save()` is callable without going through `mount()` (currently likely not tested).
3. **SEC-07 (`$isEditing` is public)** — tests should verify that `$isEditing` cannot be set client-side to trigger the wrong code path in `save()`.
4. **PERF-01 (Policy N+1)** — no test currently asserts query count; a test asserting `assertQueryCount(N)` would catch this regression.
5. **SEC-09 (Dead `StoreSocioeconomicRequest`)** — no test uses this class; documentation review should flag it as dead code.
