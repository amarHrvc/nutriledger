# TableFilters Component Snapshot

**Component:** TableFilters  
**Location:** `src/views/apps/user/list/TableFilters.tsx`  
**Epic:** kb-joc.2.4  
**Task:** T019 - Write user forms and types component files  
**Status:** Complete

## Component Overview

**Type:** Stateful React Component (Client)  
**Purpose:** Provide filter controls for user table filtering

**Architecture Pattern:** Independent filter state with reactive updating

## Implementation

```typescript
// React Imports
import { useState, useEffect } from 'react'

// MUI Imports
import CardContent from '@mui/material/CardContent'
import Grid from '@mui/material/Grid'
import MenuItem from '@mui/material/MenuItem'

// Type Imports
import type { UsersType } from '@/types/apps/userTypes'

// Component Imports
import CustomTextField from '@core/components/mui/TextField'

const TableFilters = ({ setData, tableData }: { setData: (data: UsersType[]) => void; tableData?: UsersType[] }) => {
  // States
  const [role, setRole] = useState<UsersType['role']>('')
  const [plan, setPlan] = useState<UsersType['currentPlan']>('')
  const [status, setStatus] = useState<UsersType['status']>('')

  useEffect(() => {
    const filteredData = tableData?.filter(user => {
      if (role && user.role !== role) return false
      if (plan && user.currentPlan !== plan) return false
      if (status && user.status !== status) return false

      return true
    })

    setData(filteredData || [])
  }, [role, plan, status, tableData, setData])

  return (
    <CardContent>
      <Grid container spacing={6}>
        <Grid size={{ xs: 12, sm: 4 }}>
          <CustomTextField
            select
            fullWidth
            id='select-role'
            value={role}
            onChange={e => setRole(e.target.value)}
            slotProps={{
              select: { displayEmpty: true }
            }}
          >
            <MenuItem value=''>Select Role</MenuItem>
            <MenuItem value='admin'>Admin</MenuItem>
            <MenuItem value='author'>Author</MenuItem>
            <MenuItem value='editor'>Editor</MenuItem>
            <MenuItem value='maintainer'>Maintainer</MenuItem>
            <MenuItem value='subscriber'>Subscriber</MenuItem>
          </CustomTextField>
        </Grid>
        <Grid size={{ xs: 12, sm: 4 }}>
          <CustomTextField
            select
            fullWidth
            id='select-plan'
            value={plan}
            onChange={e => setPlan(e.target.value)}
            slotProps={{
              select: { displayEmpty: true }
            }}
          >
            <MenuItem value=''>Select Plan</MenuItem>
            <MenuItem value='basic'>Basic</MenuItem>
            <MenuItem value='company'>Company</MenuItem>
            <MenuItem value='enterprise'>Enterprise</MenuItem>
            <MenuItem value='team'>Team</MenuItem>
          </CustomTextField>
        </Grid>
        <Grid size={{ xs: 12, sm: 4 }}>
          <CustomTextField
            select
            fullWidth
            id='select-status'
            value={status}
            onChange={e => setStatus(e.target.value)}
            slotProps={{
              select: { displayEmpty: true }
            }}
          >
            <MenuItem value=''>Select Status</MenuItem>
            <MenuItem value='pending'>Pending</MenuItem>
            <MenuItem value='active'>Active</MenuItem>
            <MenuItem value='inactive'>Inactive</MenuItem>
          </CustomTextField>
        </Grid>
      </Grid>
    </CardContent>
  )
}

export default TableFilters
```

## Props Interface

```typescript
interface Props {
  setData: (data: UsersType[]) => void    // Callback to update filtered data
  tableData?: UsersType[]                 // Source data to filter
}
```

## Filter State Management

### Local State Variables

```typescript
const [role, setRole] = useState<UsersType['role']>('')
const [plan, setPlan] = useState<UsersType['currentPlan']>('')
const [status, setStatus] = useState<UsersType['status']>('')
```

**State Characteristics:**
- All start as empty strings
- Type-safe via UsersType properties
- Independent of each other
- Updated via onChange handlers

## Filter Options

### 1. Role Filter
**Field ID:** `select-role`

**Options:**
- Empty (clears filter)
- admin
- author
- editor
- maintainer
- subscriber

**Purpose:** Filter users by their assigned role

### 2. Plan Filter
**Field ID:** `select-plan`

**Options:**
- Empty (clears filter)
- basic
- company
- enterprise
- team

**Purpose:** Filter users by subscription plan

### 3. Status Filter
**Field ID:** `select-status`

**Options:**
- Empty (clears filter)
- pending
- active
- inactive

**Purpose:** Filter users by account status

## Filtering Logic

### Reactive Filtering via useEffect
```typescript
useEffect(() => {
  const filteredData = tableData?.filter(user => {
    if (role && user.role !== role) return false
    if (plan && user.currentPlan !== plan) return false
    if (status && user.status !== status) return false

    return true
  })

  setData(filteredData || [])
}, [role, plan, status, tableData, setData])
```

### Filter Combination
- **Logic:** AND combination
- **All empty:** Shows all users
- **All filled:** Shows users matching ALL filters
- **Partial:** Shows users matching selected filters
- **Empty value:** Treated as "no filter" for that dimension

### Filter Chain Example
```
Users: [User1(admin, basic, active), User2(admin, company, pending), User3(editor, basic, active)]

Filter: role='admin'
Result: [User1, User2]

Filter: role='admin' AND plan='basic'
Result: [User1]

Filter: role='admin' AND plan='basic' AND status='active'
Result: [User1]

Filter: role='admin' AND plan='basic' AND status='pending'
Result: [] (no match)
```

## UI Layout

### Grid Structure
```
CardContent
└── Grid container (spacing={6})
    ├── Grid size={{ xs: 12, sm: 4 }}  → Role filter
    ├── Grid size={{ xs: 12, sm: 4 }}  → Plan filter
    └── Grid size={{ xs: 12, sm: 4 }}  → Status filter
```

### Responsive Behavior
- **Mobile (xs):** All filters stack full-width (size 12)
- **Tablet+ (sm):** Three-column layout (size 4 each = 33%)
- **Desktop:** Maintains three-column layout

### Spacing
- **Gap:** spacing={6} = 24px between filters
- **Padding:** CardContent provides container padding

## Filter Styling

### CustomTextField Props
```typescript
{
  select: true,              // Converts to dropdown
  fullWidth: true,           // Takes full container width
  value: filterState,        // Current filter value
  onChange: handler,         // Update filter state
  slotProps: {
    select: {
      displayEmpty: true     // Shows placeholder when empty
    }
  }
}
```

### MenuItem Structure
```typescript
<MenuItem value=''>Select [Filter Name]</MenuItem>  // Placeholder
<MenuItem value='option1'>Option 1</MenuItem>
<MenuItem value='option2'>Option 2</MenuItem>
// ... more options
```

## Data Flow

### Filter Update Flow
```
User changes filter dropdown
  ↓
onChange triggered
  ↓
Filter state updated (setRole, setPlan, setStatus)
  ↓
useEffect dependency change detected
  ↓
Filter function runs
  ↓
filteredData computed
  ↓
setData callback triggered
  ↓
Parent component updates (UserListTable.setFilteredData)
  ↓
Table re-renders with filtered results
```

### Parent Integration
```typescript
// In UserListTable
<TableFilters 
  setData={setFilteredData}     // Callback function
  tableData={data}              // Source data
/>

// TableFilters calls:
setData(filteredData || [])
// Which updates UserListTable's filteredData state
```

## Type Safety

### Type Inference
```typescript
// Using UsersType properties for type safety
setRole: useState<UsersType['role']>('')
setPlan: useState<UsersType['currentPlan']>('')
setStatus: useState<UsersType['status']>('')
```

**Benefits:**
- Filter values match actual data types
- TypeScript ensures filter values are valid
- Refactoring type changes propagates to filters

## Performance Characteristics

### Optimization Patterns
1. **Filter runs on state changes:** Only when filters change
2. **Optional chaining:** `tableData?.filter()`
3. **Fallback:** `filteredData || []` prevents undefined
4. **Dependency array:** Comprehensive dependencies for effect

### Potential Issues
- **Re-renders:** Effect runs for each filter change
- **No debouncing:** Filters apply immediately
- **No memoization:** Recalculates on every change
- **Simple logic:** Linear time complexity O(n)

### Optimization Opportunities
```typescript
// Could be optimized with useMemo:
const filteredData = useMemo(() => {
  return tableData?.filter(user => {
    // ... filter logic
  }) || []
}, [role, plan, status, tableData])
```

## Accessibility Features

- **Labels:** Dropdown labels via id attributes
- **Display Empty:** Shows placeholder when no selection
- **Keyboard Navigation:** Native select keyboard support
- **ARIA:** Inherited from MUI components

## Known Patterns

### Pattern: Independent Dropdown Filters
- Each filter manages its own state
- Filters combine with AND logic
- Empty value means "no filter"
- Synchronized via useEffect

### Pattern: Reactive Data Flow
- Filters are inputs
- Callback is output
- Parent owns filtered data state
- Child owns filter state

## Testing Considerations

### Unit Tests
```typescript
describe('TableFilters', () => {
  test('renders three filter dropdowns', () => {
    render(<TableFilters setData={mockSetData} tableData={mockUsers} />)
    expect(screen.getByRole('combobox', { name: /role/i })).toBeInTheDocument()
    expect(screen.getByRole('combobox', { name: /plan/i })).toBeInTheDocument()
    expect(screen.getByRole('combobox', { name: /status/i })).toBeInTheDocument()
  })

  test('calls setData with filtered data when role changes', () => {
    const mockSetData = jest.fn()
    const mockUsers = [
      { role: 'admin', currentPlan: 'basic', status: 'active' },
      { role: 'editor', currentPlan: 'company', status: 'pending' }
    ]
    
    render(<TableFilters setData={mockSetData} tableData={mockUsers} />)
    
    userEvent.click(screen.getByRole('combobox', { name: /role/i }))
    userEvent.click(screen.getByRole('option', { name: 'Admin' }))
    
    // Check that setData was called with only admin users
  })
})
```

### Integration Tests
```typescript
describe('TableFilters Integration', () => {
  test('filters combine with AND logic', () => {
    // Test multiple filters together
  })

  test('empty filter shows all users', () => {
    // Test no filters applied
  })
})
```

## Usage Examples

### Basic Usage
```typescript
import TableFilters from '@views/apps/user/list/TableFilters'
import { useState } from 'react'

function UserListView() {
  const [filteredData, setFilteredData] = useState(users)
  
  return (
    <TableFilters 
      tableData={users}
      setData={setFilteredData}
    />
  )
}
```

### With Initial Data
```typescript
function UserManagement({ userData }) {
  const [filtered, setFiltered] = useState(userData)
  
  return (
    <>
      <TableFilters tableData={userData} setData={setFiltered} />
      {/* Display filtered data */}
    </>
  )
}
```

## Extension Points

### Adding New Filter
```typescript
// 1. Add new state
const [newFilter, setNewFilter] = useState<UsersType['newField']>('')

// 2. Add to filter logic
if (newFilter && user.newField !== newFilter) return false

// 3. Add UI control
<Grid size={{ xs: 12, sm: 4 }}>
  <CustomTextField
    select
    fullWidth
    value={newFilter}
    onChange={e => setNewFilter(e.target.value)}
    // ... MenuItem options
  />
</Grid>
```

### Changing Filter Logic from AND to OR
```typescript
// Current: AND logic
if (role && user.role !== role) return false
if (plan && user.currentPlan !== plan) return false

// To OR logic:
const roleMatch = !role || user.role === role
const planMatch = !plan || user.currentPlan === plan
return roleMatch || planMatch
```

## Related Components

### Parent Component
- **UserListTable:** Owns filtered data, displays table

### Sibling Components
- **DebouncedInput:** Search functionality
- **Pagination:** Page size selector

### Data Flow Diagram
```
TableFilters
  ├── State: role, plan, status
  ├── Callback: setData (to parent)
  └── Input: tableData (from parent)

UserListTable
  ├── Owns: filteredData state
  ├── Receives: TableFilters updates via setData
  └── Displays: Table with filtered data
```

## Summary

TableFilters is a **filter component** that:
- **Manages three independent filters** (role, plan, status)
- **Provides dropdown selections** with clear options
- **Combines filters with AND logic** for precise results
- **Reactively updates parent** via callback
- **Maintains responsive layout** for all screen sizes

**Best Practices:**
- ✅ Single responsibility (filtering only)
- ✅ Independent filter state
- ✅ Reactive updates via useEffect
- ✅ Clear callback pattern
- ✅ Type-safe filter values
- ✅ Responsive grid layout
- ✅ Reusable with different data sources