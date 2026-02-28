# Phase 1: Code Quality & Architecture Review

## Code Quality Findings

### Critical
_(none in this category — see Architecture for Critical #1)_

### High
| # | File | Issue |
|---|------|-------|
| CQ-1 | `ManageSocioeconomic.php` + `StoreSocioeconomicRequest.php` | Validation rules duplicated verbatim across 17 fields — guaranteed drift |
| CQ-2 | All enum fields | Magic string `in:` rules duplicated across validation, factory, and Blade views — no type safety |
| CQ-3 | `routes/web.php` | `edit` route has no `->can()` middleware; `save()` does not re-authorize before write |
| CQ-4 | `ManageSocioeconomic::save()` | No re-authorization before write — `mount()` auth does not protect Livewire action calls |

### Medium
| # | File | Issue |
|---|------|-------|
| CQ-5 | `ManageSocioeconomic.php` | `toArray()` hydration silently maps all model keys including `id`, `patient_id`, `created_at` to component properties |
| CQ-6 | `ManageSocioeconomic.php` | `bool $has_health_insurance = false` defaults mask null (unknown) — semantically different from "answered No" |
| CQ-7 | `ViewSocioeconomic.php` | Falls back to `PatientPolicy::view` when no record exists — wrong policy boundary |
| CQ-8 | `ManageSocioeconomic.php` | 17 loose public properties on component (god component) — Livewire Form Object is the idiomatic fix |
| CQ-9 | `PatientSocioeconomicPolicy.php` | No doctor-patient assignment scoping — any doctor can modify any patient's data |
| CQ-10 | `routes/web.php` | Route named `patients.socioeconomic.delete` on a GET endpoint — misleading |

### Low
| # | File | Issue |
|---|------|-------|
| CQ-11 | `ManageSocioeconomic.php` | `save()` return type is `mixed` — should be `void` |
| CQ-12 | `app/helpers.php` | `format_enum_label()` edge cases (double underscore) — moot if enums adopted |
| CQ-13 | `StoreSocioeconomicRequest.php` | Entirely dead code — never referenced anywhere |

---

## Architecture Findings

### Critical
| # | File | Issue |
|---|------|-------|
| AR-1 | `routes/web.php` | `edit` and `delete` routes have no `->can()` middleware — violates project's defense-in-depth pattern (create route has it, the others don't) |

### High
| # | File | Issue |
|---|------|-------|
| AR-2 | `StoreSocioeconomicRequest.php` + `ManageSocioeconomic.php` | FormRequest created per project convention but unused — duplicated inline rules violate DRY and project pattern |
| AR-3 | `ManageSocioeconomic.php` | 14 loose public form properties expose full wire surface unnecessarily — Livewire 3 canonical pattern is a Form Object |
| AR-4 | `database/migrations/` | Two-migration SQLite workaround: Migration 2 is irreversible (`down()` is no-op), uses raw SQL + `PRAGMA foreign_keys = OFF`, breaks `migrate:refresh` and `migrate:rollback` |

### Medium
| # | File | Issue |
|---|------|-------|
| AR-5 | `PatientSocioeconomic.php` | `patient_id` in `$fillable` — FK should never be mass-assignable |
| AR-6 | `ViewSocioeconomic.php` | Auth fallback to `PatientPolicy` when no record — mixes two authorization boundaries |
| AR-7 | `routes/web.php` | Dedicated GET `/socioeconomic/delete` route for a destructive action — browser prefetch / `wire:navigate` preload could trigger unintended component mount |
| AR-8 | All enum fields | No PHP backed enums — same magic strings appear in migration, validation, factory, and Blade selects |

### Low
| # | File | Issue |
|---|------|-------|
| AR-9 | `app/helpers.php` | Global function for presentation-layer concern — should be Blade directive or support class method |
| AR-10 | `ManageSocioeconomic::mount()` | `toArray()` implicit hydration — fragile coupling to column names |

---

## Critical Issues for Phase 2 Context

1. **Authorization gap on edit/delete routes** (AR-1 / CQ-3/4) — route-level auth missing, action-level auth missing in `save()`. Security review should assess exploitability.
2. **`patient_id` in `$fillable`** (AR-5) — if `patient_id` ever appears in request data (e.g., from a manipulated form), it could reassign the record to a different patient.
3. **Two-migration raw SQL workaround** (AR-4) — `PRAGMA foreign_keys = OFF` during table recreation creates a window where FK integrity is not enforced. Security/data integrity impact to assess.
4. **No Enums** (AR-8 / CQ-2) — validation rules are the only enforcement layer; any bypass of validation (e.g., direct DB write, future API without validation) passes silently.
5. **Doctor scoping** (CQ-9) — any doctor can modify any patient's socioeconomic data regardless of assignment. Performance review should note this affects query patterns if doctor-patient scoping is added later.
