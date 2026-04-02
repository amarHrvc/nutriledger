# research.md

## Decisions

1. JSON contract: Adopt strict JSON:API v1 compliance (data/type/id/attributes/relationships/included). Rationale: aligns with project resources and simplifies standardized testing and client expectations. Alternatives: custom envelopes (rejected due to inconsistency risk).

2. Socioeconomic fields: Use enums for key fields with lists sourced from spec prompt. Rationale: predictable validation and consistent resource contracts.

3. Soft-delete lifecycle: Soft-delete Patient and PatientSocioeconomic together (use SoftDeletes trait on both). On patient restore, restore socioeconomic. Hard-delete of User cascades and permanently removes related models.

4. Tests: Use Pest HTTP tests with factories. Aim for 35+ tests: covering listing, creation, view, update, delete, restore, validation, authorization, and response formats.

## Alternatives considered
- Using free-form socioeconomic fields: simpler but reduces validation and introduces inconsistent data (rejected).
- Leaving socioeconomic records untouched on patient delete: could keep historical data but complicates restore semantics (rejected).

## Actionable outcomes
- Data model will include explicit enum lists.
- Resources will follow JSON:API v1, with `included` for related socioeconomic when requested.
- Migration and models updated to soft-delete socioeconomic and ensure cascading behavior on user hard-delete.
