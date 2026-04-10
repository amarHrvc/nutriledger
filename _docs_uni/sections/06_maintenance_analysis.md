# 6. MAINTENANCE ANALYSIS

## 6.1. Current State

The NutriBase API is deployed to a managed PaaS provider (Railway or Fly.io), which handles infrastructure provisioning, automatic restarts on failure, and environment variable management. The deployment pipeline runs database migrations automatically on each release, ensuring the schema is always consistent with the deployed code.

Authentication is handled by Laravel Sanctum [1], which issues opaque tokens stored in the `personal_access_tokens` table. Token expiration is configurable via environment variable (`SANCTUM_TOKEN_EXPIRATION`), allowing administrators to enforce session limits without code changes. All API routes require the `auth:sanctum` middleware, so unauthenticated requests are rejected at the routing layer before reaching any controller logic.

Data integrity is preserved through soft deletes on the `users` and `patients` tables. Deactivated users and archived patient records are not physically removed from the database; they are flagged with a `deleted_at` timestamp and excluded from standard queries. This ensures that historical visit records and audit trails remain intact even when accounts are deactivated. Permanent deletion is available to administrators only and requires an explicit force-delete action.

The automated Pest test suite [2] covers all API endpoints, including authentication flows, role-based access control, validation rejection, and soft-delete behaviour. Tests run against an in-memory SQLite database, providing fast, isolated, repeatable verification. The test suite is executed before each deployment to confirm no regression has been introduced.

## 6.2. Future Improvements

**Refresh token support.** The current implementation issues long-lived Sanctum tokens. Introducing short-lived access tokens paired with rotating refresh tokens would reduce the risk of token compromise, particularly in a medical context where data sensitivity is high. This improvement would require updates to the authentication endpoints and the React SPA token storage strategy.

**Redis caching layer.** Patient list and visit history queries are executed against the database on every request. As the patient count grows, these queries will increase in cost. Introducing a Redis caching layer for frequently accessed, rarely changed data — such as paginated patient lists — would reduce database load and improve response times under concurrent usage.

**Microservices decomposition.** The current monolithic Laravel API is appropriate for the MVP scale. As the platform grows to include Groups 4 through 9 (vital signs, medications, lab results, recommendations, reminders, and dashboards), the domain will become complex enough to justify splitting into independently deployable services. A phased migration using the strangler fig pattern would allow gradual decomposition without disrupting existing functionality.

**Extended clinical record (Groups 4–9).** The current MVP captures visits with free-text notes. Future releases should introduce structured clinical data: vital signs (blood pressure, pulse, weight, BMI) per visit, active and discontinued medication lists, laboratory result logs, dietary recommendations with expiry tracking, and automated follow-up reminders. Each adds clinically significant value and brings the platform closer to a complete electronic health record.

**AI-assisted clinical tools.** A rule-based nutritional risk scoring engine could be implemented as a Laravel service class, computing a risk level from existing patient data (BMI, dietary restrictions, food security score). A more advanced direction involves a doctor assistant chatbox powered by a large language model — a retrieval-augmented generation approach using the Laravel AI SDK with Claude as the model, exposing patient data as tool calls and streaming responses to the React frontend via server-sent events. This would allow doctors to query a patient's full history in natural language without leaving the application.
