# Data Model: Admin User Management

**Branch**: `008-admin-user-management` | **Date**: 2026-05-01

## Core Entity: UserResource

Source: `frontend/src/api/generated/nutriBaseAPI.schemas.ts`

```ts
interface UserResource {
  type: 'users'
  id: number
  attributes: {
    name: string
    email: string
    role: 'admin' | 'doktor' | 'pacijent'   // string at runtime, narrowed for display
    createdAt: string                         // ISO 8601
    updatedAt: string                         // ISO 8601
    deletedAt: string | null                  // null when active
    isDeleted: boolean                        // derived from deletedAt
  }
  relationships: {
    patient?: Patient                         // present when role === 'pacijent'
  }
  links: {
    self: string
  }
}
```

## Account State Machine

```
         ┌──────────────┐
         │    ACTIVE    │──── deactivate (DELETE /users/{id}) ────┐
         │ isDeleted=false│                                         │
         └──────────────┘                                         ▼
                ▲                                        ┌──────────────────┐
                │                                        │   DEACTIVATED    │
                └──── restore (POST /users/{id}/restore) │  isDeleted=true  │
                                                         └──────────────────┘
                                                                  │
                                                    force-delete  │
                                                    (DELETE /users/{id}/force)
                                                                  │
                                                                  ▼
                                                          ┌──────────────┐
                                                          │   DELETED    │
                                                          │ (gone from DB)│
                                                          └──────────────┘
```

**Rules enforced by backend policy**:
- Only ACTIVE users can be deactivated
- Only DEACTIVATED users can be restored
- Only DEACTIVATED users can be force-deleted
- Admins see both ACTIVE and DEACTIVATED users in the list
- Doctors see only ACTIVE users

## Frontend State Shape

```ts
// View orchestrator state (views/users/index.tsx)
type UserView = 'list' | 'detail' | 'create' | 'edit'

interface UsersPageState {
  view: UserView
  selectedUser: UserResource | null   // set when view === 'detail' | 'edit'
}
```

## Paginated List Response (BFF wraps backend)

The BFF `GET /api/users` returns:
```ts
{
  users: UserResource[]   // current page
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}
```

Note: `UsersIndex200.data` and `.meta` are typed as `string` in Orval schemas but are objects at runtime. Access via `as any` cast in the BFF handler.

## Form Data Shapes

**Create** (`POST /api/users` → `StoreUserRequest`):
```ts
{
  name: string              // required
  email: string             // required, unique
  password: string          // required, min 8 chars
  password_confirmation: string  // required, must match password
  role: 'admin' | 'doktor' | 'pacijent'  // required
}
```

**Update** (`PATCH /api/users/{id}` → `UpdateUserRequest`):
```ts
{
  name?: string
  email?: string            // unique excluding own
  password?: string         // min 8 chars if provided
  role?: 'admin' | 'doktor' | 'pacijent'
}
```

## Validation Error Shape (Laravel 422)

```ts
// ValidationExceptionResponse from Orval schemas
{
  message: string
  errors: Record<string, string[]>   // e.g. { email: ['The email has already been taken.'] }
}
```

The `UserForm` component reads `errors` from the 422 response and maps field names to inline error messages below each input.
