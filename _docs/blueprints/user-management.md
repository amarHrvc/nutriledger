# User Management — Implementation Blueprint

## Folder Structure

```
views/users/
├── index.tsx               ← page entry, mounts the list
├── UserList.tsx            ← list + search + create dialog
├── UserDetail.tsx          ← detail layout shell (left/right split)
├── UserForm.tsx            ← create/edit form, mode-discriminated
├── user-left/
│   ├── index.tsx           ← left column orchestrator
│   ├── UserDetailsCard.tsx ← identity, actions (suspend/restore/force)
│   └── UserPlanCard.tsx
├── user-right/
│   ├── index.tsx           ← tab orchestrator
│   ├── overview/
│   ├── security/
│   └── billing-plans/
└── shared/
    ├── ConfirmDialog.tsx
    └── UserStatusChip.tsx
```

**Rule:** Split detail views into `left` (identity + actions) and `right` (tabs). Each level has an `index.tsx` orchestrator that only composes — no data fetching, no logic.

---

## Data Flow

```
BFF route (/api/users/*)
    ↕ fetch (server-to-server, auth cookies stay server-side)
Orval generated client (user.ts)
```

**Rule:** Components never call the Laravel API directly. All fetches go to Next.js BFF routes (`/api/...`). BFF routes call Orval-generated clients. This keeps auth tokens off the browser.

---

## Refresh Mechanism — Single Contract

```
mutation happens
  → window.dispatchEvent(new CustomEvent('users:changed'))

UserList (if mounted)      → re-fetches list
[id]/page.tsx (if mounted) → re-fetches single user
```

**Rule:** Every mutation (create, update, delete, restore) dispatches `users:changed`. Every page/component that needs to stay fresh listens to it in a `useEffect` with cleanup. No `refreshKey`, no prop callbacks, no shared state.

---

## Action Pattern (destructive operations)

```
button click → openConfirm(action)
             → ConfirmDialog shown
             → onConfirm()
               → fetch BFF route
               → check res.ok → toast.error on failure
               → toast.success on success
               → dispatch users:changed  (or router.push if record destroyed)
```

**Rule:** All destructive actions go through `ConfirmDialog`. Always check `res.ok` before showing success. If the record no longer exists after the action (force delete), navigate away — don't dispatch the refresh event.

---

## Error & Loading States

| State | Handling |
|---|---|
| Loading | `CircularProgress` centered |
| Network/API error | `<Alert severity='error'>` with message from `json.message ?? fallback` |
| Empty list | Inline text, not an error |
| 404 on detail | Error state replaces the page (not infinite spinner) |

**Rule:** Every fetch has three states: loading, error, data. Error message always comes from `json.message` first, with a hardcoded fallback string.

---

## Form Pattern

```
UserForm receives: mode ('create' | 'edit'), user? (for edit pre-fill)
On success: dispatch users:changed → call onSuccess()
On 422: set field errors from json.errors
On other !ok: set formError banner
```

**Rule:** One form component handles both create and edit via `mode` prop. Field errors (`errors[field][0]`) bind to individual inputs. A top-level `<Alert>` handles non-validation errors.

---

## Component Rules

- `'use client'` on every interactive component
- No data fetching inside presentational components — fetch at the page level, pass data down as props
- Shared primitives (`ConfirmDialog`, `UserStatusChip`) live in `shared/`, receive only what they render — no fetch, no side effects
- Prop types always match the actual data shape (e.g. `deletedAt: string | null | undefined`, not `boolean`)
