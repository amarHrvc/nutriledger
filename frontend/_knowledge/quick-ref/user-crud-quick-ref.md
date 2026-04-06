# User CRUD Quick Reference

**Epic:** kb-joc.2.1, kb-joc.2.2, kb-joc.2.3  
**Status:** Complete  
**Last Updated:** 2026-05-04

## Quick Answers

### Q: How to display a list of users with search, filters, and pagination?
**A:** Use `UserListTable` from `src/views/apps/user/list/UserListTable.tsx`
- Accepts `tableData: UsersType[]`
- Provides: fuzzy search, role/plan/status filters, row selection, pagination (10/25/50)
- Returns: rendered table with all features
- See: [users-list.md](../pages/users-list.md)

### Q: How to create/add a new user?
**A:** Use `AddUserDrawer` from `src/views/apps/user/list/AddUserDrawer.tsx`
- Opens as right-side drawer modal
- Validates: fullName, username, email, role, plan, status (required)
- Optional: company, country, contact
- Creates new user object with auto-generated avatar and ID
- See: [add-user-drawer.md](../components/add-user-drawer.md)

### Q: How to view detailed user profile with tabs?
**A:** Use `UserViewTab` from `src/views/apps/user/view/index.tsx`
- Async server component (fetches pricing data)
- Two-column layout: left (profile), right (tabbed settings)
- Tabs: Overview, Security, Billing & Plans, Notifications, Connections
- Dynamic imports for code splitting
- See: [user-detail.md](../pages/user-detail.md)

### Q: How to filter users by role, plan, or status?
**A:** Use `TableFilters` from `src/views/apps/user/list/TableFilters.tsx`
- Dropdown filters: role (5 options), plan (4 options), status (3 options)
- Combines with AND logic
- Triggers `setFilteredData()` on change
- Independent state per filter

---

## Type Definitions

### UsersType (Core User Object)

```typescript
type UsersType = {
  id: number
  role: string                 // 'admin' | 'author' | 'editor' | 'maintainer' | 'subscriber'
  email: string
  status: string              // 'active' | 'pending' | 'inactive'
  avatar: string              // Image path
  company: string
  country: string
  contact: string
  fullName: string
  username: string
  currentPlan: string         // 'basic' | 'company' | 'enterprise' | 'team'
  avatarColor?: ThemeColor
  billing: string             // e.g. 'Auto Debit'
}
```

### RoleType (User Role Options)

```typescript
type RoleType = 'admin' | 'author' | 'editor' | 'maintainer' | 'subscriber'

// Role Mapping (from UserListTable)
const userRoleObj = {
  admin: { icon: 'tabler-crown', color: 'error' },
  author: { icon: 'tabler-device-desktop', color: 'warning' },
  editor: { icon: 'tabler-edit', color: 'info' },
  maintainer: { icon: 'tabler-chart-pie', color: 'success' },
  subscriber: { icon: 'tabler-user', color: 'primary' }
}
```

### PlanType (Subscription Plans)

```typescript
type PlanType = 'basic' | 'company' | 'enterprise' | 'team'
```

### StatusType (User Status Options)

```typescript
type StatusType = 'active' | 'pending' | 'inactive'

// Status Color Mapping
const userStatusObj = {
  active: 'success',
  pending: 'warning',
  inactive: 'secondary'
}
```

---

## Component Architecture & Patterns

### Pattern Map: User CRUD Flow

| **Operation** | **Component** | **Location** | **Pattern** | **Key Features** |
|---|---|---|---|---|
| List Users | UserListTable | `src/views/apps/user/list/UserListTable.tsx` | Client Component + TanStack Table | Search, filters, pagination, row select, delete, view |
| Add User | AddUserDrawer | `src/views/apps/user/list/AddUserDrawer.tsx` | Drawer Modal + React Hook Form | Form validation, auto-generated fields, drawer animation |
| Filter Users | TableFilters | `src/views/apps/user/list/TableFilters.tsx` | Client Component | Role, plan, status dropdowns with AND logic |
| Search Users | DebouncedInput | `src/views/apps/user/list/UserListTable.tsx` | Custom Hook + Fuzzy Filter | 500ms debounce, fuzzy matching via @tanstack/match-sorter-utils |
| View Detail | UserViewTab | `src/views/apps/user/view/index.tsx` | Async Server Component | Tabbed interface, dynamic imports, profile + settings |
| User Statistics | UserListCards | `src/views/apps/user/list/UserListCards.tsx` | Presentation Component | Summary cards with counts |

### Data Flow

```
UserList (Page Container)
  ├── UserListCards (Statistics)
  └── UserListTable (Main Table)
      ├── TableFilters (role, plan, status selects)
      ├── DebouncedInput (search)
      ├── Table (TanStack with columns)
      └── AddUserDrawer (form modal)
```

### Table Features

#### Search (Fuzzy)
- Input: `DebouncedInput` with 500ms debounce
- Logic: `fuzzyFilter` using `rankItem` from @tanstack/match-sorter-utils
- Applied to: fullName, email, username, company

#### Filters
- **Role:** admin, author, editor, maintainer, subscriber
- **Plan:** basic, company, enterprise, team
- **Status:** pending, active, inactive
- **Logic:** AND combination of selected filters

#### Columns (TanStack Table)
1. Select (checkbox)
2. User (avatar + fullName + username)
3. Role (icon-coded badge)
4. Plan (text display)
5. Billing (text display)
6. Status (color chip)
7. Actions (view, delete, menu)

#### Pagination
- Page sizes: 10, 25, 50
- Custom pagination component
- TanStack pagination handlers

---

## Code Snippets

### Using UserListTable with Data

```typescript
// src/views/apps/user/list/index.tsx (Page Component)
import UserListTable from './UserListTable'
import type { UsersType } from '@/types/apps/userTypes'

export default function UserList({ userData }: { userData?: UsersType[] }) {
  return (
    <Grid container spacing={6}>
      <Grid size={{ xs: 12 }}>
        <UserListTable tableData={userData} />
      </Grid>
    </Grid>
  )
}
```

### Opening AddUserDrawer from Parent

```typescript
// Inside UserListTable component
const [addUserOpen, setAddUserOpen] = useState(false)
const [data, setData] = useState<UsersType[]>(tableData || [])

return (
  <>
    <Button onClick={() => setAddUserOpen(true)}>Add User</Button>
    <AddUserDrawer
      open={addUserOpen}
      handleClose={() => setAddUserOpen(false)}
      userData={data}
      setData={setData}
    />
  </>
)
```

### Form Validation Pattern (React Hook Form)

```typescript
// Inside AddUserDrawer
const { control, handleSubmit, formState: { errors } } = useForm<FormValidateType>({
  defaultValues: {
    fullName: '',
    username: '',
    email: '',
    role: '',
    plan: '',
    status: ''
  }
})

// Field with validation
<Controller
  name='email'
  control={control}
  rules={{ required: true }}
  render={({ field }) => (
    <CustomTextField
      {...field}
      type='email'
      label='Email'
      {...(errors.email && { error: true, helperText: 'This field is required.' })}
    />
  )}
/>
```

### Accessing User Detail Page

```typescript
// From UserListTable actions column
<Link href={`/apps/user/view`}>
  <IconButton size='small'>View</IconButton>
</Link>
```

### Filter State Management

```typescript
// Inside UserListTable
const [roleFilter, setRoleFilter] = useState('')
const [planFilter, setPlanFilter] = useState('')
const [statusFilter, setStatusFilter] = useState('')

useEffect(() => {
  // Apply filters with AND logic
  let filtered = data.filter(user => {
    const matchRole = !roleFilter || user.role === roleFilter
    const matchPlan = !planFilter || user.currentPlan === planFilter
    const matchStatus = !statusFilter || user.status === statusFilter
    return matchRole && matchPlan && matchStatus
  })
  setFilteredData(filtered)
}, [roleFilter, planFilter, statusFilter, data])
```

---

## File Structure

```
src/views/apps/user/
├── list/
│   ├── index.tsx              # Page container (UserList)
│   ├── UserListTable.tsx      # Main table component (1-427 lines)
│   ├── UserListCards.tsx      # Summary statistics
│   ├── AddUserDrawer.tsx      # User creation form (1-284 lines)
│   └── TableFilters.tsx       # Filter dropdowns (1-96 lines)
└── view/
    ├── index.tsx              # User detail page (async)
    └── user-left-overview/
        ├── index.tsx
        ├── UserDetails.tsx
        └── UserPlan.tsx
```

### Type Files

```
src/types/apps/
└── userTypes.ts               # UsersType, RoleType, PlanType, StatusType
```

---

## Key Patterns

### 1. Two-Column Detail Layout

```typescript
// UserViewTab component structure
<Grid container spacing={6}>
  <Grid size={{ xs: 12, lg: 4, md: 5 }}>
    <UserLeftOverview />        {/* Profile side */}
  </Grid>
  <Grid size={{ xs: 12, lg: 8, md: 7 }}>
    <UserRight tabContentList={tabs} />  {/* Tabbed settings */}
  </Grid>
</Grid>
```

### 2. Tabbed Interface with Dynamic Imports

```typescript
const OverViewTab = dynamic(() => import('./overview'))
const SecurityTab = dynamic(() => import('./security'))
const BillingPlans = dynamic(() => import('./billing-plans'))

const tabContentList = {
  'overview': <OverViewTab />,
  'security': <SecurityTab />,
  'billing-plans': <BillingPlans data={pricingData} />
  // ... more tabs
}
```

### 3. Form in Drawer Pattern

- Drawer: right-anchored, 300px (xs) / 400px (sm+)
- Form: two-part validation (required + optional fields)
- Submission: creates object, appends to array, closes drawer
- Reset: on close or after successful submit

---

## Dependencies & Imports

```typescript
// Core
import { useState, useEffect, useMemo } from 'react'
import { useForm, Controller } from 'react-hook-form'

// MUI
import Grid from '@mui/material/Grid'
import Button from '@mui/material/Button'
import Drawer from '@mui/material/Drawer'
import Table from '@mui/material/Table'
import TableBody from '@mui/material/TableBody'
import Chip from '@mui/material/Chip'

// TanStack
import { useReactTable, getCoreRowModel, getPaginationRowModel } from '@tanstack/react-table'
import { rankItem } from '@tanstack/match-sorter-utils'

// Types
import type { UsersType } from '@/types/apps/userTypes'
```

---

## Related Documentation

- [users-list.md](../pages/users-list.md) — Page structure and composition
- [user-detail.md](../pages/user-detail.md) — Detail page architecture
- [add-user-drawer.md](../components/add-user-drawer.md) — Form component details
- [user-domain.md](../source-analysis/user-domain.md) — Full source code analysis
- [table-filters.md](../components/table-filters.md) — Filter component patterns

---

## Performance Notes

- **Column memoization:** Column definitions memoized with [data, filteredData] deps
- **Debounced search:** 500ms to reduce re-renders during typing
- **TanStack optimizations:** Lazy pagination, efficient sorting
- **Dynamic imports:** Tab content split into separate bundles
- **Async server component:** UserViewTab fetches data server-side

---

## Known Limitations

1. No email format validation
2. No duplicate email/username prevention
3. No delete confirmation dialog
4. No success/error toast notifications
5. Auto-generated avatars (no upload)
6. Limited billing assignment logic
7. No optimistic UI updates
8. No edit user mode in AddUserDrawer
