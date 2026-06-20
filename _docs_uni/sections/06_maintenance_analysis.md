# 6. MAINTENANCE ANALYSIS

## 6.1. Deployment Architecture

NutriBase is deployed on Railway, a managed Platform-as-a-Service (PaaS) provider. The deployment consists of three Railway services running within a single project:

- **API Service** — the Laravel 12 backend, auto-built by Railway's Nixpacks builder from the `backend/` directory. Nixpacks detects the PHP environment, installs Composer dependencies, and starts the application using the built-in PHP server configured via `APP_ENV=production`.
- **Queue Worker Service** — a separate Railway service running the same Laravel codebase with a different start command (`php artisan queue:work --sleep=3 --tries=3`). This service processes all asynchronous jobs: `GenerateDietPlanJob` (AI diet plan generation via the Claude API) and `SendDietPlanEmailJob` (diet plan email delivery). Separating the worker from the API service ensures that long-running AI inference jobs do not block HTTP request handling.
- **Frontend Service** — the Next.js 15 application, built and served from the `frontend/` directory. Railway builds the Next.js project and serves it via the built-in Node.js server.

Railway's PostgreSQL plugin provides the production database. The `QUEUE_CONNECTION=database` configuration means jobs are stored in the `jobs` table of the same PostgreSQL instance, keeping the infrastructure simple without requiring a dedicated Redis service.

All inter-service communication uses Railway's private networking. The `INTERNAL_API_URL` environment variable on the frontend service points to the API service's private Railway URL, ensuring API calls between the Next.js server and Laravel do not leave Railway's internal network.

## 6.2. Application Administration

### Environment Variables

All configuration is managed through Railway's environment variable dashboard. No secrets are committed to the repository. The following variables must be set per environment:

| Variable | Purpose |
|---|---|
| `APP_KEY` | Laravel encryption key — generated once via `php artisan key:generate` |
| `APP_ENV` | Set to `production` on Railway |
| `APP_DEBUG` | Set to `false` in production |
| `DATABASE_URL` | Injected automatically by Railway's PostgreSQL plugin |
| `SANCTUM_TOKEN_EXPIRATION` | Token lifetime in minutes (default 480 — 8 hours) |
| `ANTHROPIC_API_KEY` | Claude API key for AI diet plan generation |
| `MAIL_MAILER` | SMTP mailer (e.g. Resend or Mailgun) for diet plan email delivery |
| `MAIL_FROM_ADDRESS` | Sender address for outgoing diet plan emails |
| `FRONTEND_URL` | Next.js public URL — used in CORS and email links |

### Database Migrations

Railway runs `php artisan migrate --force` as part of the build step on every deployment. This ensures the production schema is always consistent with the deployed code. Migrations are written as incremental, non-destructive changes — columns are added, never silently dropped — so a failed deployment can be rolled back to the previous Railway release without a schema mismatch.

### Monitoring and Logs

Railway streams stdout and stderr logs from all three services in real time through its dashboard. Laravel writes structured log entries for security-sensitive events — failed login attempts (`security.login_failed`) and token revocations (`security.token_revoked`) — using the `LOG_CHANNEL=stack` configuration. In production, these logs should be forwarded to a persistent log aggregator (e.g. Papertrail or Logtail) since Railway's in-dashboard log retention is limited.

## 6.3. Data Maintenance and Backup

### Soft Deletes

The `users` and `patients` tables use Laravel's `SoftDeletes` trait, adding a `deleted_at` timestamp column. Deleting a user or patient via the API sets this timestamp rather than issuing a `DELETE` statement. Standard Eloquent queries automatically exclude soft-deleted records via a global scope. Restoring a record clears the timestamp and makes the record visible again.

When a patient is soft-deleted, a model `booted()` hook cascades the soft delete to the associated `patient_socioeconomic` record. Restoring the patient reverses this cascade. Visit records and diet plan history are preserved in full throughout the lifecycle of a patient record, maintaining a complete audit trail.

Permanent deletion (`forceDelete`) is restricted to the `admin` role and physically removes the record and all associated data from the database.

### Database Backups

Railway's PostgreSQL plugin provides automated daily backups with a seven-day retention window. Point-in-time recovery is available through the Railway dashboard, allowing the database to be restored to any state within the retention window. For a clinical application where data loss is unacceptable, Railway's backup schedule should be supplemented with a scheduled `pg_dump` export stored in external object storage (e.g. AWS S3 or Cloudflare R2).

### Queue Job Persistence

Pending and failed jobs are stored in the `jobs` and `failed_jobs` tables in PostgreSQL. If the queue worker crashes, jobs remain in the database and are re-processed when the worker restarts. Failed jobs — those that have exceeded the `--tries=3` limit — are written to `failed_jobs` and can be retried manually via `php artisan queue:retry all` or inspected and discarded via `php artisan queue:flush`.

## 6.4. Crash Recovery

Railway monitors all services and automatically restarts any service that exits with a non-zero code. The typical recovery sequence for a crash is:

1. Service exits unexpectedly.
2. Railway detects the failure and restarts the container within seconds.
3. Laravel reconnects to PostgreSQL via the `DATABASE_URL` environment variable.
4. The queue worker resumes processing from the `jobs` table — no jobs are lost because they remained in the database during the downtime.
5. Any in-flight HTTP requests that were interrupted during the crash return a connection error to the client; the React frontend displays an error state and the user may retry.

For a full environment failure (Railway platform outage), the application can be redeployed to an alternative PaaS (Fly.io, Render) using the same codebase and environment variables. The PostgreSQL data can be restored from the most recent Railway backup. The stateless nature of the API (no server-side sessions — authentication is handled via Sanctum tokens in httpOnly cookies) means no session state is lost on restart.

## 6.5. Application Security

### Authentication and Token Management

Authentication is handled by Laravel Sanctum, which issues opaque tokens stored in the `personal_access_tokens` table. Tokens are returned to the Next.js API layer and stored in httpOnly, `sameSite=lax` cookies that are inaccessible to JavaScript running in the browser, mitigating cross-site scripting (XSS) token theft. Tokens expire after the period configured in `SANCTUM_TOKEN_EXPIRATION` and are explicitly revoked on logout by deleting the token record from the database.

### Role-Based Access Control

All API routes are protected by the `auth:sanctum` middleware. Role enforcement is applied at two levels: the `RoleMiddleware` restricts entire route groups by role (`admin`, `doktor`, `pacijent`), and Laravel Policies (`PatientPolicy`, `VisitPolicy`, `DietPlanPolicy`, `VitalSignPolicy`) enforce fine-grained ownership rules within those groups. The combination ensures that a patient cannot access another patient's records even if they construct a valid request URL.

The `PatientRedirectGuard` component on the frontend provides a second layer of access control at the UI level, immediately redirecting patient-role users away from admin and doctor pages. This is a UX safeguard — the API policies remain the authoritative enforcement point.

### CORS Configuration

Cross-Origin Resource Sharing is configured in `config/cors.php` to allow requests only from the deployed frontend URL (`FRONTEND_URL`). All other origins are rejected at the CORS layer before reaching Laravel routing. In production this means only the Railway-hosted Next.js service can make authenticated API calls.

### Sensitive Data

Diet plan content and patient medical records are stored in plaintext in the PostgreSQL database. Railway encrypts data at rest on its managed PostgreSQL instances. All traffic between services uses Railway's private network (TLS internally) and all public-facing endpoints are served over HTTPS enforced by Railway's edge layer.

## 6.6. Data Integrity

The `PatientService` wraps the creation and update of patient records in a database transaction. Both the `Patient` record and the associated `PatientSocioeconomic` record are written atomically — if either insert fails, the transaction is rolled back and no partial record is persisted. This prevents orphaned patient records without a socioeconomic profile.

Visit records reference both a `patient_id` and a `doctor_id` via foreign keys with `cascadeOnDelete`. If a patient record is force-deleted, all associated visits are removed in the same operation at the database level, maintaining referential integrity without requiring application-level cleanup logic.

Diet plan delivery is tracked via the `DietPlanDelivery` model, which records the recipient email, the sending user, the status (`pending`, `sent`, `failed`), and a timestamp for each send attempt. This provides a full audit trail of when diet plans were sent and to whom, independent of the email provider's own logs.

## 6.7. Future Developments

**Refresh token rotation.** The current implementation issues long-lived Sanctum tokens. Introducing short-lived access tokens paired with rotating refresh tokens would reduce the risk of token compromise in a medical data context. This would require changes to the authentication endpoints and the Next.js token storage strategy.

**Redis queue backend.** The current queue driver uses the PostgreSQL database. Under high load — particularly if many diet plan generation jobs are dispatched concurrently — database-backed queues can create contention. Migrating to Redis (available as a Railway plugin) would provide faster job dispatch and better throughput.

**File storage for clinical documents.** Railway services do not provide persistent local disk storage between deployments. Adding support for clinical document uploads (e.g. scanned lab results, referral letters) would require integrating an object storage provider such as AWS S3 or Cloudflare R2 via Laravel's `Storage` facade with the S3 driver.

**Extended clinical record (Groups 4–9).** Future releases will introduce structured clinical data per visit: vital signs trend charts, active medication lists, laboratory result logs with normal-range flagging, dietary recommendations, and automated follow-up reminders. Each feature adds a new table and API resource following the same service-layer pattern established in the current codebase.

**AI-assisted clinical tools.** The nutritional risk scoring engine — computing a risk flag from existing socioeconomic fields such as food security status, income level, and physical activity — can be implemented as a deterministic Laravel service class with no external API dependency. A more advanced direction is a doctor assistant chatbox using retrieval-augmented generation: the Laravel AI SDK with Claude as the model, patient data exposed as tool definitions via Laravel MCP, and responses streamed to the React frontend via server-sent events.
