# Data Model: Auth & User Management API

**Branch**: `002-user-mgmt-api` | **Date**: 2026-03-19

---

## Entity: User

**Table**: `users` (existing — no schema changes required)

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| `id` | bigint unsigned | PK, auto-increment | |
| `name` | string(255) | NOT NULL | Display name |
| `email` | string(255) | NOT NULL, UNIQUE | Case-insensitive identifier |
| `email_verified_at` | timestamp | NULL | Managed by Fortify — not exposed in API |
| `password` | string(255) | NOT NULL | Hashed (bcrypt); never in responses |
| `remember_token` | string(100) | NULL | Web auth "remember me" token — not used by Sanctum; not exposed in API |
| `role` | string | NOT NULL | Enum-like: `admin`, `doktor`, `pacijent`. No DB-level default — application sets `pacijent` during registration |
| `deleted_at` | timestamp | NULL | Soft-delete — NULL = active, set = deactivated |
| `created_at` | timestamp | NOT NULL | |
| `updated_at` | timestamp | NOT NULL | |

**Fillable**: `name`, `email`, `password`, `role`
**Hidden**: `password`, `remember_token`
**Casts**: `email_verified_at` → datetime, `password` → hashed

### State Machine

```
active (deleted_at = NULL)
  │
  ├─── deactivate (soft delete) ──→  deactivated (deleted_at = timestamp)
  │                                         │
  │                                         ├─── restore ──→  active
  │                                         │
  │                                         └─── force delete ──→  [gone]
  │
  └─── force delete ──→  [gone]
```

- **active → deactivated**: `$user->delete()` — sets `deleted_at`, preserves all data
- **deactivated → active**: `$user->restore()` — clears `deleted_at`
- **any → gone**: `$user->forceDelete()` — hard deletes row; releases email for re-registration
- **blocked from login**: deactivated users are excluded from auth queries by `SoftDeletes` global scope

### Validation Rules

**Create (StoreUserRequest)**:
- `name`: required, string, max:255
- `email`: required, email, unique:users,email
- `password`: required, string, min:8
- `role`: required, in:admin,doktor,pacijent

**Update (UpdateUserRequest)**:
- `name`: sometimes, string, max:255
- `email`: sometimes, email, `Rule::unique('users', 'email')->ignore($this->route('user'))` — excludes the current user from the uniqueness check
- `password`: sometimes, string, min:8
- `role`: sometimes, in:admin,doktor,pacijent

**Registration (RegisterRequest)**:
- `name`: required, string, max:255
- `email`: required, email, unique:users,email
- `password`: required, string, min:8, confirmed (the `confirmed` rule automatically validates the `password_confirmation` request field — no separate rule needed)
- `role`: sometimes, in:admin,doktor,pacijent (application defaults to `pacijent` if omitted)

**Login (LoginRequest — existing)**:
- `email`: required, email
- `password`: required, string

---

## Entity: Authentication Token (PersonalAccessToken)

**Table**: `personal_access_tokens` (Sanctum — existing, no changes)

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint unsigned | PK |
| `tokenable_type` | string | Polymorphic: `App\Models\User` |
| `tokenable_id` | bigint unsigned | Foreign key to `users.id` |
| `name` | string | Token name (e.g., `api-token`) |
| `token` | string(64) | SHA-256 hash of plaintext token |
| `abilities` | text | JSON array (e.g., `["*"]`) |
| `last_used_at` | timestamp | NULL |
| `expires_at` | timestamp | NULL — set at creation to enforce absolute expiry |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Expiry**: `expires_at = created_at + config('sanctum.expiration') minutes`
- `config/sanctum.php` `expiration` must be set to `env('SANCTUM_EXPIRATION', 1440)` (default 24h)
- Sanctum automatically rejects tokens where `expires_at < now()`
- Logout: current token row is deleted → cannot be reused

---

## Entity: User Profile (API Resource Shape)

**Source**: `UserResource` — read-only projection of `User` for API responses

```json
{
  "data": {
    "type": "user",
    "id": 1,
    "attributes": {
      "name": "Ana Kovač",
      "email": "ana@nutriledger.com",
      "role": "doktor",
      "createdAt": "2026-01-01T00:00:00Z",
      "updatedAt": "2026-01-01T00:00:00Z",
      "deletedAt": null
    },
    "relationships": {
      "patient": {
        "data": { "type": "patient", "id": 5 }
      }
    },
    "links": {
      "self": "http://localhost/api/users/1"
    }
  }
}
```

**Rules**:
- `type` is always `"user"`
- `id` is the integer primary key
- All attribute keys are camelCase
- `relationships.patient` is included only when the `patient` relationship is eager-loaded (`$user->load('patient')`). When loaded and the user has no linked patient (e.g., role is `admin` or `doktor`), the value is `{ "data": null }`. When not loaded at all (e.g., index listing), the key is absent entirely via `whenLoaded()`
- `password`, `remember_token`, `email_verified_at` are NEVER included
- `deletedAt` is included (may be null for active users) — allows admin to identify deactivated accounts

---

## Entity: Patient (related — no changes)

**Table**: `patients` (existing)
- User `hasOne` Patient; Patient `belongsTo` User via `user_id`
- Referenced in `UserResource` relationships when loaded
- No schema changes for this feature

---

## Response Envelope

All controller responses use the standard envelope via `ApiResponses` trait (updated):

```json
{
  "message": "Human-readable status description",
  "status": 200,
  "data": { }
}
```

**Status codes used by this feature**:
| Code | When |
|------|------|
| 200 | Successful read or update |
| 201 | New resource created (register, store) |
| 204 | Successful deletion or logout (no body) |
| 401 | Unauthenticated (no token, invalid token, expired token, deactivated user login, wrong credentials) |
| 403 | Authenticated but forbidden (wrong role, self-deactivation attempt) |
| 404 | Resource not found |
| 422 | Validation failed (Laravel auto-formats with `errors` key) |
| 429 | Rate limit exceeded (login throttle) |
