# Users List Page Snapshot

**Page:** `/apps/user` or `/apps/user/list`  
**Epic:** kb-joc.2.1  
**Task:** T016 - Write users-list page snapshot  
**Status:** Complete

## File Location

```
src/views/apps/user/list/index.tsx
```

## Component Implementation

### Page Structure
```typescript
// MUI Imports
import Grid from '@mui/material/Grid'

// Type Imports
import type { UsersType } from '@/types/apps/userTypes'

// Component Imports
import UserListTable from './UserListTable'
import UserListCards from './UserListCards'

const UserList = ({ userData }: { userData?: UsersType[] }) => {
  return (
    <Grid container spacing={6}>
      <Grid size={{ xs: 12 }}>
        <UserListCards />
      </Grid>
      <Grid size={{ xs: 12 }}>
        <UserListTable tableData={userData} />
      </Grid>
    </Grid>
  )
}

export default UserList
```

## Page Architecture

### Purpose
Container component that orchestrates user list display with:
1. Summary statistics cards
2. Detailed user table with filtering/search/management

### Layout
- **Grid-based:** MUI Grid container with spacing-6
- **Responsive:** Stacks on mobile, full width on all sizes
- **Two-section:** Cards above, table below

### Data Flow
```
userData (prop)
  ↓
UserListTable (passes tableData)
  ├── Manages: selection, filtering, pagination, adding
  └── UI: Search, filters, table, add drawer

UserListCards (no props)
  └── Static statistics display
```

## Props Interface

```typescript
interface Props {
  userData?: UsersType[]  // Array of users to display
}
```

**userData Source:**
- Passed from parent page/layout
- Optional (? indicates nullable)
- Type: Array of UsersType objects

## Component Composition

### 1. UserListCards (Upper Section)
- **Grid Size:** `xs: 12` (full width)
- **Purpose:** Display user statistics
- **Content:** Summary cards showing counts by role/status

### 2. UserListTable (Lower Section)
- **Grid Size:** `xs: 12` (full width)
- **Purpose:** Full user management interface
- **Features:**
  - Advanced search with debouncing
  - Multi-filter system (role, plan, status)
  - Column sorting and pagination
  - Row selection and bulk actions
  - Add/edit/delete operations

## Styling & Spacing

- **Container Spacing:** `spacing={6}` = 24px gaps
- **Grid Items:** Full width on all breakpoints
- **No Custom Styles:** Uses MUI default styling

## Navigation & Routing

### Page Access
- Route: `/apps/user` or `/apps/user/list`
- Locale-aware: Supports i18n routing

### Internal Navigation
- View user details: `/apps/user/view`
- Links available in UserListTable action column

## State Management Pattern

```
Page Level: Props-based (userData)
↓
UserListTable Level:
  ├── Local state: data, filteredData, rowSelection, globalFilter, addUserOpen
  └── Local state: formData (in AddUserDrawer)
↓
Child Components:
  ├── TableFilters: role, plan, status
  └── DebouncedInput: globalFilter
```

### Key Insight
- Page is **stateless** (no hooks)
- All state managed in child components
- Props drilling pattern for data

## Integration Points

### Parent Integration
The page expects a parent layout/route handler to provide:
- `userData`: Array of user objects
- Locale context (via useParams in child components)

### Example Usage
```typescript
// In app/layout or route handler
import UserList from '@views/apps/user/list'
import { getUsersData } from '@/data/users'

export default function UserListPage() {
  const userData = getUsersData()
  return <UserList userData={userData} />
}
```

## Performance Characteristics

- **Render:** Quick (no heavy computations)
- **Re-renders:** Only when userData prop changes
- **Memory:** Light (simple container)
- **Child Optimization:** Handled in UserListTable (useMemo for columns)

## Accessibility Features

- **Semantic Structure:** Grid-based layout
- **Component Responsibility:** Delegated to children
- **Keyboard Navigation:** Supported by child components

## Known Limitations

1. **No Error Boundary:** Parent should handle userData errors
2. **No Loading State:** Assumes data is ready
3. **No Empty State Message:** Handled in table ("No data available")
4. **No Pagination at Page Level:** Pagination at table level

## Testing Considerations

### Unit Tests
```typescript
describe('UserList Component', () => {
  test('renders UserListCards and UserListTable', () => {
    render(<UserList userData={mockUsers} />)
    expect(screen.getByComponent(UserListCards)).toBeInTheDocument()
    expect(screen.getByComponent(UserListTable)).toBeInTheDocument()
  })

  test('passes userData to UserListTable', () => {
    const mockUsers = [{ id: 1, name: 'John' }]
    render(<UserList userData={mockUsers} />)
    expect(screen.getByComponent(UserListTable)).toHaveProperty('tableData', mockUsers)
  })
})
```

### Integration Tests
```typescript
describe('UserList Page Integration', () => {
  test('displays user statistics in cards', () => {
    // Test UserListCards functionality
  })

  test('displays users in searchable table', () => {
    // Test UserListTable search/filter/sort
  })

  test('allows adding new user via drawer', () => {
    // Test AddUserDrawer functionality
  })
})
```

## Usage Examples

### Basic Usage
```typescript
import UserList from '@views/apps/user/list'

const users = [
  {
    id: 1,
    fullName: 'John Doe',
    email: 'john@example.com',
    role: 'admin',
    status: 'active',
    // ... other fields
  }
]

export function MyPage() {
  return <UserList userData={users} />
}
```

### With Loading State (Parent Component)
```typescript
'use client'
import { useEffect, useState } from 'react'
import UserList from '@views/apps/user/list'
import type { UsersType } from '@/types/apps/userTypes'

export function UserListPageWrapper() {
  const [users, setUsers] = useState<UsersType[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    fetchUsers().then(data => {
      setUsers(data)
      setLoading(false)
    })
  }, [])

  if (loading) return <div>Loading...</div>
  
  return <UserList userData={users} />
}
```

## Related Components

### Direct Children
- **UserListCards:** Summary statistics
- **UserListTable:** Main data table
  - **TableFilters:** Filter controls
  - **AddUserDrawer:** User creation form
  - **DebouncedInput:** Search functionality

### Type Dependencies
- **UsersType:** User data structure

## Summary

The UserList page is a **lightweight container component** that:
1. Accepts user data as props
2. Renders summary statistics (UserListCards)
3. Renders detailed table (UserListTable)
4. Delegates all interactivity to child components
5. Maintains simple, composable structure

**Best Practices Demonstrated:**
- ✅ Single responsibility (composition)
- ✅ Props-based data flow
- ✅ Child component delegation
- ✅ Responsive grid layout
- ✅ Type safety with TypeScript