# Research: Vital Signs Recording & History (012)

## Decision 1 — One-to-One Relationship Model

**Decision**: `vital_signs.visit_id` with a UNIQUE constraint. `VitalSign` belongs to one `Visit`; `Visit` has one `VitalSign`.

**Rationale**: The spec is explicit: one vitals record per visit maximum. A UNIQUE constraint on `visit_id` enforces this at the database level regardless of application-layer bugs. The controller adds a 409 guard for readability, but the DB constraint is the authoritative safety net.

**Alternatives considered**:
- Soft-deleting old records and creating new ones — rejected: over-complicates history and makes the uniqueness guarantee implicit.
- Storing vitals as a JSON column on `visits` — rejected: prevents indexing, querying, and the AI history endpoint would need to parse unstructured data.

---

## Decision 2 — At-Least-One-Field Validation

**Decision**: Use `withValidator()` in `StoreVitalSignRequest` to add a custom `after` rule that checks at least one of the six measurement fields is non-null.

**Rationale**: Laravel has no built-in `required_without_all` for this pattern. The `withValidator()` `after` hook runs after individual field rules pass, which is the correct place to apply cross-field validation. The error is attached to the key `'vitals'` so the frontend can display it as a form-level (not field-level) error.

**Alternatives considered**:
- Custom `Rule` class `AtLeastOneFilled` — equally valid but more ceremony for a single use.
- Allowing fully-empty records — rejected: a record with all nulls has no clinical value and pollutes history.

---

## Decision 3 — BMI Storage vs Computed-Only

**Decision**: BMI is computed server-side and **stored** in the `vital_signs` table. It is not recomputed on each read.

**Rationale**: If a doctor later corrects a patient's height (e.g., entered 175 instead of 170), recomputing historical BMI retroactively would corrupt the clinical record — the risk score and trend charts from past visits would silently change. Storing BMI freezes it at the time of recording. The `VitalSignService::update()` method recomputes BMI only for the record being updated, not for prior records.

**Alternatives considered**:
- Computed attribute (not stored) — rejected: changes to height after the fact would silently alter all historical BMI values.
- Stored + `updatedAt` audit trail — unnecessary; the `updated_at` timestamp on `vital_signs` already captures when the record was last changed.

---

## Decision 4 — Abnormal Flags: Computed Accessor, Not Stored

**Decision**: Flags are computed by a model accessor (`getComputedFlagsAttribute`) at read time and included in `VitalSignResource`. They are not persisted.

**Rationale**: Thresholds are fixed system-wide constants that never require querying. Storing flags would create a secondary truth that could drift from the raw measurements if thresholds are ever adjusted. Since the resource always recomputes from the raw values, the flag output is always consistent.

**Alternatives considered**:
- Storing flags as a JSON column — rejected: adds a write cost and a potential stale-data bug with no benefit.
- Computing flags only in the frontend — rejected: AI services consuming the API would need to re-implement the same threshold logic.

---

## Decision 5 — Routes: Singular Resource at Visit Level

**Decision**: Register vitals as a singular-style resource: `GET/POST/PATCH/DELETE /patients/{patient}/visits/{visit}/vitals` (no `{vital}` ID in the URL). The patient-level history is `GET /patients/{patient}/vitals`.

**Rationale**: Since there is exactly one vitals record per visit, the resource is naturally singular — the visit ID is sufficient to identify it. Using `/vitals/{id}` would require the client to know the vitals record's primary key, which has no UI value. Registering outside role middleware (same pattern as existing visit routes) allows patients to reach the `show` and `history` endpoints with policy handling the authorization check.

**Alternatives considered**:
- `Route::apiResource('vitals', ...)` nested under visits — rejected: generates `index` and `show/{vital}` routes that don't match the singular model.
- Single `/vitals` route with patient-scoped query — rejected: breaks RESTful nesting pattern established by the visits feature.

---

## Decision 6 — Previous-Visit Delta in Single-Visit Show

**Decision**: `VitalSignController::show()` loads the previous visit's vitals via a single additional query and attaches the result as `$vitalSign->previousVitals`. `VitalSignResource` includes a `previousVisit` block when this property is set.

**Rationale**: FR-016 requires that a visit-level AI narrative can be generated without a second request from the client. The controller (not the model) resolves this because the previous-visit lookup is contextual to a single-visit show — it should not run for every record in the paginated history list.

**Alternatives considered**:
- Returning previous vitals in every history record — expensive N+1 risk; the history list is designed for trend analysis where deltas can be computed from adjacent rows.
- Letting the AI client make a second call — rejected by FR-016 explicitly.

---

## Decision 7 — Date-Range Filter on History Endpoint

**Decision**: Accept `?from=YYYY-MM-DD&to=YYYY-MM-DD` query parameters on `GET /patients/{patient}/vitals`. Filter applies to the related visit's `date` column.

**Rationale**: FR-012 requires date-range filtering so future AI services can isolate the period during which a specific dietary plan was active. Using visit date (not `vital_signs.created_at`) is consistent with how all other visit-level queries in this project are scoped.

**Alternatives considered**:
- Cursor-based or offset pagination only — insufficient for AI use case which needs a fixed time window.
- Filtering on `vital_signs.created_at` — rejected: a record created today for a visit from last week would be mis-filtered; the visit date is the clinically meaningful date.

---

## Decision 8 — Admin Delete is Permanent (No Soft Delete)

**Decision**: `VitalSignService::delete()` calls `$vitalSign->delete()` — a hard delete. The `VitalSign` model does not use `SoftDeletes`.

**Rationale**: Soft-deletes were added to `users` and `patients` because those records are referenced across the system and accidental deletion needs to be recoverable without data loss. Vitals records are leaf-level data with no downstream foreign key references. Hard delete is simpler, cannot produce orphaned flags in the AI risk score, and matches the spec assumption ("admin delete is permanent").

**Alternatives considered**:
- SoftDeletes on `VitalSign` — adds a `deleted_at` column, changes all queries to scope out soft-deleted records, and adds a restore endpoint — unnecessary complexity for this use case.

---

## Decision 9 — BFF Pattern: Orval-Generated Functions Only

**Decision**: All BFF route handlers import and call Orval-generated functions. No raw `customFetchMutator` calls in route handlers.

**Rationale**: This is an established project rule (from CLAUDE.md and corrected in session 011). Orval-generated functions handle cookie-based Bearer token forwarding internally. Using them keeps BFF handlers consistent and prevents the "wrong auth header" bug that affected earlier routes.

**Alternatives considered**:
- Raw `customFetchMutator` — rejected: was the source of auth bugs in prior features; project rules explicitly prohibit it in BFF handlers.

---

## Existing Assets (no changes needed)

- `VisitPolicy`, `VisitController`, `VisitResource` — unchanged; `VitalSign` is a separate domain object.
- `Visit` model — add only `hasOne(VitalSign::class)` relationship.
- `PatientRightTabs` — add Vitals tab entry only.
- `VisitDetail.tsx` — replace placeholder callout section only; all other sections untouched.
