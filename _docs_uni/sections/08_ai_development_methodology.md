# 8. AI-ASSISTED SPECIFICATION-DRIVEN DEVELOPMENT

## 8.1. Motivation and Problem Statement

The emergence of large language model (LLM)-powered coding assistants has introduced a new risk in software engineering practice: *vibe coding* — the habit of issuing a loosely worded prompt to an AI and accepting whatever code is produced, without first defining requirements, validating assumptions, or establishing any traceability between the stated need and the resulting implementation. While this approach can produce rapid initial output, it tends to accumulate undocumented decisions, unclear scope boundaries, and untestable behaviour. The resulting system may function in demonstration conditions while being structurally unsound for production use.

NutriBase adopts the opposite position. AI tools are used extensively throughout the development process — for generating boilerplate, resolving ambiguities, researching SDK patterns, and producing first drafts of specification documents — but they operate within a structured, human-directed workflow. The human developer remains the architect: defining what is needed, approving design decisions, and reviewing every artifact before it advances to the next phase. The AI assists in executing those decisions efficiently and consistently.

This methodology is formalised in the project using a tool called **speckit**, a specification workflow system integrated with Claude Code. The workflow enforces a strict sequence: a feature cannot be implemented until it has been formally specified, and it cannot be planned until the specification has been reviewed and all unknowns have been resolved. The sections below describe the workflow stages and illustrate each with a concrete example drawn from Feature 014: the AI Diet Plan Generator.

---

## 8.2. Workflow Overview

The speckit workflow consists of four sequential phases. Each phase produces a named artifact stored in the feature's specification directory (`specs/[NNN]-[feature-name]/`). No phase may begin until all artifacts from the preceding phase are complete.

| Phase | Command | Primary Artifact | Purpose |
|---|---|---|---|
| 1 — Specification | `/speckit.specify` | `spec.md` | Define what the feature must do in technology-agnostic terms |
| 2 — Planning | `/speckit.plan` | `plan.md`, `research.md`, `data-model.md`, `contracts/` | Resolve all technical unknowns; define data model and API contracts |
| 3 — Task Generation | `/speckit.tasks` | `tasks.md` | Break the plan into developer-ready, independently verifiable tasks |
| 4 — Implementation | `/speckit.implement` | Source code | Execute tasks in dependency order with verification at each step |

The workflow is designed so that the artifact produced by each phase contains enough information to allow a developer (human or AI) to execute the next phase without requiring verbal explanation. This property — that all decisions are written down before implementation begins — is what distinguishes specification-driven development from ad-hoc AI-assisted coding.

---

## 8.3. Phase 1: Feature Specification (`/speckit.specify`)

The specification phase begins with a natural language description of the feature. The developer provides this description, which becomes the authoritative input to the specification process. The tool produces a `spec.md` file organised into mandatory sections: User Scenarios (with acceptance criteria), Functional Requirements, Key Entities, Success Criteria, and Assumptions.

### Principles enforced by the specification phase

**Technology agnosticism.** The specification describes what users need, not how the system will deliver it. References to specific frameworks, languages, or database engines are prohibited. This constraint ensures that the specification remains stable even if implementation choices change.

**Testability of requirements.** Every functional requirement is written in the form "System MUST [specific, observable behaviour]" and every user scenario includes acceptance criteria in the Given/When/Then format. A requirement that cannot be expressed this way is a sign that it is not yet clear enough to implement.

**Bounded unknowns.** If a critical design decision cannot be resolved from the feature description alone, it is marked with a `[NEEDS CLARIFICATION: ...]` tag. The specification phase enforces a maximum of three such markers; all must be resolved before planning begins. This prevents the planner from inheriting ambiguity.

### Example: Feature 014 specification

The diet plan feature was specified from the following developer-provided description:

> A doctor can generate a 7-day AI-powered diet plan for a patient. The system returns an acknowledgement immediately, generates the plan in a background job, and stores it in a history table. Only doctors and admins can access diet plan endpoints.

The `/speckit.specify` command expanded this into a `spec.md` containing twelve functional requirements (FR-001 through FR-012), four user stories covering generation, history browsing, failure handling, and access control, and six measurable success criteria. No `[NEEDS CLARIFICATION]` markers were needed because the developer's description was precise enough to resolve all design decisions through reasonable defaults and documented assumptions.

The specification was validated against a quality checklist covering content quality, requirement completeness, and feature readiness before advancing to the planning phase.

---

## 8.4. Phase 2: Implementation Planning (`/speckit.plan`)

The planning phase reads the `spec.md` and produces four artifacts: `research.md`, `data-model.md`, a `contracts/` directory, and `plan.md`. Each addresses a different aspect of the transition from requirement to design.

### research.md — Resolving technical unknowns

`research.md` documents every significant technical decision made during planning as a numbered entry. Each entry follows a fixed structure: the decision itself, the rationale, and the alternatives considered and rejected. This ensures that future developers (and future AI tools) can understand why each choice was made and can revisit those decisions if the context changes.

For Feature 014, nine decisions were recorded (D1 through D9), covering topics including:

- **D1 — SDK installation**: `laravel/ai` with the Anthropic provider was chosen; PrismPHP (the predecessor package) and the direct Anthropic SDK were rejected with documented reasons.
- **D2 — Job architecture**: A custom `GenerateDietPlanJob` was chosen over the SDK's built-in `->queue()` fluent API, because the manual retry-and-status-transition logic required imperative control flow that the SDK's callback-based API does not provide cleanly.
- **D3 — Retry strategy**: Manual retry loop inside `handle()` (maximum two attempts) rather than Laravel's automatic job retry mechanism, to keep the `pending` status consistent throughout both attempts.
- **D6 — Structured output API**: The correct SDK schema signature (`schema(JsonSchema $schema): array`) was confirmed through the project's internal research corpus, which contained a full SDK knowledge index compiled in an earlier research session.

### data-model.md — Data model and component design

`data-model.md` defines the new database table, the new Eloquent model with its casts and relationships, the agent class with its attributes and schema, the queued job with its validation rules, and the policy class with its method signatures. This document is the single source of truth for the data layer — it is written before any migration or model file is created, and it is consulted during task generation to ensure every implementation step is consistent with the approved design.

For Feature 014, the `patient_diet_plans` table was designed with eleven columns including `status` (an enum with values `pending`, `completed`, `failed`), JSON columns for `nutritional_goals`, `days`, and `warnings`, and a `failure_reason` text column. The decision to use JSON columns rather than a fully normalised schema was documented: for a POC context where meal plan content is opaque to the relational layer, JSON storage avoids the complexity of a seven-table meal normalisation without practical disadvantage.

### contracts/api-endpoints.md — API contract

The contracts directory specifies the complete request and response shape for every endpoint the feature exposes. Response bodies are given as annotated JSON examples covering success cases, partial success (pending status), and failure cases. This document serves as the acceptance definition for the API layer — an implementation that does not match these contracts does not satisfy the feature's requirements.

Three endpoints were specified for Feature 014: `POST /api/patients/{patient}/diet-plans` (trigger generation, returns 202), `GET /api/patients/{patient}/diet-plans` (paginated history), and `GET /api/patients/{patient}/diet-plans/{dietPlan}` (full plan detail). The response shape for the list endpoint was distinguished from the detail endpoint to avoid transmitting the full 7-day meal data in list responses — a performance consideration documented in the contract.

### plan.md — Integrated implementation plan

`plan.md` consolidates all planning decisions into a single document that also contains the Constitution Check — a gate that verifies compliance with the project's non-negotiable architectural principles before implementation is authorised to begin.

For Feature 014, the Constitution Check confirmed:

- **Principle II (Authorization at Every Layer)**: All three authorization layers are applied — `auth:sanctum` on routes, a `DietPlanPolicy` enforced via `FormRequest::authorize()`, and explicit patient exclusion per FR-012.
- **Principle III (Test-First)**: Three Pest test files were planned before implementation, covering generation, listing, and detail retrieval, all using `Agent::fake()` to prevent real API calls.
- **Principle IV (Code Quality Gates)**: Laravel Pint, Larastan level 5, and the Pest test suite must pass before any task is considered complete.

The implementation sequence in `plan.md` lists twelve tasks in dependency order (BE tasks 1–9 followed by FE tasks 10–12), each at a level of granularity appropriate for direct task generation in the next phase.

---

## 8.5. Artifact Summary

The table below shows the complete set of artifacts produced by the specify and plan phases for Feature 014, stored under `specs/014-ai-diet-plan/`.

| Artifact | Produced by | Contains |
|---|---|---|
| `spec.md` | `/speckit.specify` | 4 user stories, 12 FRs, 6 success criteria, assumptions, edge cases |
| `checklists/requirements.md` | `/speckit.specify` | Quality validation checklist — all items passed |
| `research.md` | `/speckit.plan` | 9 technical decisions with rationale and alternatives |
| `data-model.md` | `/speckit.plan` | Table schema, model, agent, job, policy definitions |
| `contracts/api-endpoints.md` | `/speckit.plan` | 3 endpoint contracts with annotated JSON examples |
| `quickstart.md` | `/speckit.plan` | Install steps, smoke test commands, Agent::fake() pattern |
| `plan.md` | `/speckit.plan` | Technical context, Constitution Check, implementation sequence, risks |

No source code file was created during these two phases. The entire output is structured documentation. Implementation (Phase 3 and 4) consumes these artifacts as its input — the developer's and the AI's instructions at implementation time are drawn from `data-model.md` and `contracts/`, not from a live verbal conversation.

---

## 8.6. Phase 3: Task Generation (`/speckit.tasks`)

The task generation phase reads all planning artifacts — `plan.md`, `spec.md`, `data-model.md`, `contracts/`, and `research.md` — and produces a `tasks.md` file that decomposes the implementation into individually executable, independently verifiable steps. Each task specifies a unique identifier, a parallelisability flag, a user story label, a concrete action, and an exact file path. This format is designed so that any task can be handed to a developer or an AI coding agent without requiring additional context.

### Structure enforced by the task generation phase

**Organisation by user story.** Tasks are grouped into phases that correspond directly to the user stories defined in `spec.md`. Each user story phase is independently completable: a developer can stop after any story phase and have a working, demonstrable increment of the feature. This contrasts with a flat task list, where the feature is only testable once all tasks are complete.

**TDD commit discipline.** For every backend user story, the task list enforces three sequential commit phases — RED, GREEN, and REFACTOR — as separate atomic commits. The RED phase consists solely of the test file; all tests must be confirmed failing before implementation begins. The GREEN phase contains the implementation code; all tests must pass. The REFACTOR phase handles route registration, code style enforcement, and static analysis. This three-phase commit structure is a non-negotiable architectural principle of the project, ensuring that the test suite accurately reflects intent rather than post-hoc rationalising existing code.

**Prerequisite gating.** The task list opens with a Setup phase (external dependency installation) and a Foundational phase (database migration, model, factory, policy). These two phases are explicitly marked as blocking: no user story work may begin until both are complete and verified. This prevents the common failure mode where implementation begins before the infrastructure that supports it is in place.

**Explicit parallelism.** Tasks that touch different files and have no shared dependency on an incomplete task are marked `[P]`. This annotation serves as a signal to both human developers and AI agents that these tasks can be executed concurrently. Within Phase 2 of Feature 014, for example, the model, factory, and policy files can be created simultaneously because they have no mutual dependency.

### Example: Feature 014 task generation

The `/speckit.tasks` command produced 36 tasks across seven phases for Feature 014:

| Phase | Scope | Tasks | TDD phases |
|---|---|---|---|
| 1 — Setup | Install `laravel/ai`, configure Anthropic, verify queue table | T001–T003 | — |
| 2 — Foundational | Migration, `PatientDietPlan` model, factory, policy | T004–T009 | — |
| 3 — US1 (P1) | Generate endpoint + agent + job | T010–T017 | RED / GREEN / REFACTOR |
| 4 — US2 (P2) | List and detail endpoints + resources | T018–T025 | RED / GREEN / REFACTOR |
| 5 — US3 (P3) | Failure path verification | T026–T028 | RED / GREEN / REFACTOR |
| 6 — FE | React `DietPlanSection`, `DietPlanCard`, `DietPlanHistory` | T029–T032 | — |
| 7 — Polish | Pint, Larastan, build, documentation updates | T033–T036 | — |

The MVP scope is Phases 1 through 3 (T001–T017): enough to demonstrate that a doctor can trigger generation, receive an immediate 202 acknowledgement, and have the background job produce a structured 7-day plan stored in the database. Each subsequent phase adds a verifiable increment without modifying what the previous phase delivered.

Access control (User Story 4 in the spec) was not assigned a separate phase. Instead, the 401 and 403 assertions for every endpoint are embedded in the RED phase test file of whichever story introduces that endpoint. This avoids duplicating infrastructure while ensuring that access control is tested from the moment the endpoint is registered.

### The role of the task list at implementation time

`tasks.md` is the primary input to Phase 4. When the developer (or AI agent) begins implementation, they execute tasks in the order specified, committing after each TDD phase and running the verification command listed at each checkpoint. The task descriptions reference exact file paths, specific class and method names from `data-model.md`, and validation rules from `research.md`, so there is no need to re-read or re-interpret earlier artifacts during implementation. The knowledge captured in the specification and planning phases has been distilled into actionable steps.

---

## 8.7. Artifact Summary

The table below shows the complete set of artifacts produced by the three planning phases for Feature 014, stored under `specs/014-ai-diet-plan/`.

| Artifact | Produced by | Contains |
|---|---|---|
| `spec.md` | `/speckit.specify` | 4 user stories, 12 FRs, 6 success criteria, assumptions, edge cases |
| `checklists/requirements.md` | `/speckit.specify` | Quality validation checklist — all items passed |
| `research.md` | `/speckit.plan` | 9 technical decisions with rationale and alternatives |
| `data-model.md` | `/speckit.plan` | Table schema, model, agent, job, policy definitions |
| `contracts/api-endpoints.md` | `/speckit.plan` | 3 endpoint contracts with annotated JSON examples |
| `quickstart.md` | `/speckit.plan` | Install steps, smoke test commands, `Agent::fake()` pattern |
| `plan.md` | `/speckit.plan` | Technical context, Constitution Check, implementation sequence, risks |
| `tasks.md` | `/speckit.tasks` | 36 tasks across 7 phases, TDD commit sequence, dependency graph, parallel map |

No source code file was created during these three phases. The entire output is structured documentation. Phase 4 (`/speckit.implement`) consumes these artifacts as its input — the developer's and the AI's instructions at implementation time are drawn from `tasks.md`, `data-model.md`, and `contracts/`, not from a live verbal conversation.

---

## 8.8. Phase 4: Implementation (`/speckit.implement`)

The implementation phase consumes `tasks.md` as its primary input and produces source code as its output. Unlike the preceding phases, which are entirely document-producing, Phase 4 is iterative: each task is executed, verified, and committed before the next begins. The AI coding agent operates strictly within the boundaries established by `data-model.md` and `contracts/` — it is executing a specification, not making design decisions.

### Structure enforced by the implementation phase

**Task-by-task execution.** The developer (or AI agent) works through `tasks.md` in the order specified, treating each task as an atomic unit of work. A task is not considered complete until its verification command passes. This prevents the common failure mode of implementing several tasks simultaneously and encountering cascading failures that are difficult to attribute to a specific change.

**TDD commit sequence in practice.** For each backend user story, the three-phase commit discipline enforced in `tasks.md` plays out as follows. The RED commit contains only the Pest test file, with all tests confirmed failing — this establishes what "done" means before a single line of implementation code is written. The GREEN commit introduces the implementation code (migration, model, controller, service, resource, route registration) and brings all tests to passing. The REFACTOR commit runs Laravel Pint for code style, Larastan for static analysis, and confirms the full test suite still passes. Only after all three commits are complete does the next user story begin.

**Deviation handling.** When a task cannot be completed as specified — for example, if an SDK API differs from what `research.md` documented — the deviation is recorded in a brief note and the affected `research.md` entry is updated before proceeding. This keeps the planning artifacts accurate as a record of what was actually built, not only what was planned.

### Example: Feature 014 implementation

The implementation of Feature 014 proceeded across all seven phases defined in `tasks.md`. The Setup phase (T001–T003) installed the `laravel/ai` package with the Anthropic provider, added the `ANTHROPIC_API_KEY` environment variable to the Railway deployment configuration, and verified that the `jobs` queue table was present from a prior migration.

The Foundational phase (T004–T009) created the `patient_diet_plans` migration with all eleven columns as specified in `data-model.md`, the `PatientDietPlan` Eloquent model with its JSON casts and `belongsTo` relationships, a factory with named states, and the `DietPlanPolicy` with the doctor/admin permission rules and the explicit patient exclusion required by FR-012.

User Story 1 (T010–T017) was the most complex phase, introducing three new classes that had not existed in the codebase: `DietPlanAgent` (the structured-output AI agent), `GenerateDietPlanJob` (the queued job with manual retry logic), and `DietPlanController` with its `store` action. The RED commit established the test file covering the 202 acknowledgement response, the background job dispatch assertion using `Queue::fake()`, and the 401/403 access control cases. The GREEN commit introduced all three classes and registered the `POST` route. The REFACTOR commit resolved two Larastan type warnings introduced by the dynamic JSON schema construction in `DietPlanAgent`.

User Stories 2 and 3 (T018–T028) added the list and detail endpoints with their `DietPlanResource` and `DietPlanCollection` resources, and verified the failure path by asserting that a job that exhausts its retry budget sets `status` to `failed` and persists the `failure_reason` column.

The Frontend phase (T029–T032) added three React components to the patient profile page: `DietPlanSection` (the trigger button and status display), `DietPlanCard` (a single plan rendered as a collapsible day-by-day view), and `DietPlanHistory` (the paginated list of past plans). These components consume the same three endpoints specified in `contracts/api-endpoints.md` and use the same API client pattern established in the existing frontend codebase.

The Polish phase (T033–T036) ran the full Pint formatter across all modified files, confirmed Larastan at level 5 with no errors, executed the complete Pest suite to verify no regressions, and updated the project's OpenAPI documentation to include the three new endpoints.

---

## 8.9. Relationship to "Vibe Coding"

The contrast with ad-hoc AI-assisted development is most visible at the moment of implementation. In a vibe-coding workflow, a developer would prompt an AI with something like "implement a diet plan generator for my Laravel app" and review the produced code for obvious errors before committing it. The AI makes dozens of implicit decisions — table names, column types, authorization approach, retry behaviour, response shape — without those decisions being documented or reviewed.

In the speckit workflow, every one of those decisions appears in a named research entry, a data-model definition, or an API contract, reviewed and approved by the developer before implementation begins. When the AI generates source code in Phase 4, it is executing an explicit specification — not making creative choices. This has several practical consequences:

1. **Traceability**: every implementation decision traces back to a numbered requirement (FR-NNN) or a research decision (D-N). Reviewers and future maintainers can understand why the code is the way it is.
2. **Reviewability**: because the design was documented before implementation, a code review can check conformance with the approved design rather than trying to reverse-engineer intent from the code.
3. **Reproducibility**: if the implementation is discarded and rewritten, the specification and plan artifacts survive. A new implementation from the same artifacts will produce equivalent behaviour.
4. **Testability**: acceptance criteria defined in `spec.md` become the test assertions in the Pest test files. There is no ambiguity about what "done" means.

---

## 8.10. Limitations

The approach described here is not without cost. The specify, plan, and task generation phases add front-loaded effort that would not exist in a direct implementation workflow. For features where the design space is well understood and the implementation is straightforward, this overhead may not be justified.

The AI Diet Plan Generator is a non-trivial case: it introduces a new external dependency (the Laravel AI SDK), a new architectural pattern (asynchronous agent execution with status polling), and a new security boundary (excluding patients from all AI endpoints). These properties make the upfront investment worthwhile. For simpler CRUD additions to existing resources, a lighter process is appropriate.

A second limitation is that the speckit workflow relies on the AI agent accurately executing the task list without introducing unspecified behaviour. In practice, static analysis (Larastan) and the Pest test suite catch most deviations, but they cannot verify that the implementation fully captures the intent of the original specification. Human review of the GREEN commit against `contracts/api-endpoints.md` remains a necessary step that the tooling does not automate.
