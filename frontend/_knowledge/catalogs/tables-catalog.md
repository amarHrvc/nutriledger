# Tables Catalog - CRUD Domain Reference

**Catalog:** Application Tables  
**Epic:** kb-joc.3 - Tables & Invoice Artifacts  
**Status:** Complete

## Overview

This catalog documents all TanStack React Table implementations across the application domains, providing a standardized reference for table patterns, column definitions, and filtering strategies.

## Table Implementations Summary

### 1. User Domain Tables

#### UserListTable
- **Location:** `src/views/apps/user/list/UserListTable.tsx`
- **Type:** Client Component
- **Features:** Multi-filter, search, pagination, row selection
- **Columns:** 8 (select, user, role, plan, billing, status, action)
- **Filters:** 3 (role, plan, status via TableFilters)
- **Search:** Global fuzzy search with 500ms debounce
- **Pagination:** 10, 25, 50 rows per page
- **Row Selection:** Multi-select with select-all checkbox
- **Actions:** Delete, view, menu (download, edit)

**State Management:**
```typescript
- addUserOpen: boolean
- rowSelection: Record<>
- data: UsersType[]
- filteredData: UsersType[]
- globalFilter: string
```

**Column Types:**
1. Select checkbox
2. User (avatar + name + username)
3. Role (icon + role name)
4. Plan (text)
5. Billing (text)
6. Status (chip)
7. Action (buttons + menu)

**Custom Patterns:**
- DebouncedInput component for search
- fuzzyFilter function
- getAvatar() helper

---

### 2. Roles & Permissions Domain Tables

#### RolesTable
- **Location:** `src/views/apps/roles/RolesTable.tsx`
- **Type:** Client Component
- **Features:** Role filter, search, pagination, row selection
- **Columns:** 7 (select, user, role, plan, billing, status, action)
- **Filters:** 1 (role via local state + useEffect)
- **Search:** Global fuzzy search with 500ms debounce
- **Pagination:** 10, 25, 50 rows per page
- **Row Selection:** Multi-select with select-all checkbox
- **Actions:** Delete, view, menu (download, edit)

**State Management:**
```typescript
- role: UsersType['role']
- rowSelection: Record<>
- data: UsersType[]
- filteredData: UsersType[]
- globalFilter: string
```

**Unique Features:**
- Avatar skin='light' variant
- Different role icon colors than UserListTable
- Integrated role filter (not in separate component)

**Column Types:** Same as UserListTable but with different role color mapping

#### Permissions Table
- **Location:** `src/views/apps/permissions/index.tsx`
- **Type:** Client Component
- **Features:** Search, pagination, row selection, dialog editing
- **Columns:** 4 (name, assignedTo, createdDate, action)
- **Filters:** 1 (global search only)
- **Search:** Global fuzzy search with 500ms debounce
- **Pagination:** 5, 7, 9 rows per page (different from others)
- **Row Selection:** Multi-select with select-all checkbox
- **Actions:** Edit button + menu button

**State Management:**
```typescript
- open: boolean (dialog)
- rowSelection: Record<>
- editValue: string
- data: PermissionRowType[]
- globalFilter: string
```

**Unique Features:**
- Dialog-based editing (not inline)
- assignedTo can be single string or string[]
- Color-coded by role assignment
- Edit mode tracking via editValue

---

### 3. Invoice Domain Tables

#### InvoiceListTable
- **Location:** `src/views/apps/invoice/list/InvoiceListTable.tsx`
- **Type:** Client Component
- **Features:** Status filter, search, pagination, row selection
- **Columns:** 8 (select, id, status, client, total, issuedDate, balance, action)
- **Filters:** 1 (status via local state + useEffect)
- **Search:** Global fuzzy search with 500ms debounce
- **Pagination:** 10, 25, 50 rows per page
- **Row Selection:** Multi-select with select-all checkbox
- **Actions:** Delete, preview, menu (download, edit, duplicate)

**State Management:**
```typescript
- status: InvoiceType['invoiceStatus']
- rowSelection: Record<>
- data: InvoiceType[]
- filteredData: InvoiceType[]
- globalFilter: string
```

**Unique Features:**
- ID column links to preview page
- Status with tooltip (shows balance + due date)
- Balance shows as "Paid" chip when === 0
- 6 different invoice statuses with icon mapping
- Edit action links to edit page
- Duplicate action available

**Status Icons:**
```typescript
{
  'Sent': { color: 'secondary', icon: 'tabler-send-2' },
  'Paid': { color: 'success', icon: 'tabler-check' },
  'Draft': { color: 'primary', icon: 'tabler-mail' },
  'Partial Payment': { color: 'warning', icon: 'tabler-chart-pie-2' },
  'Past Due': { color: 'error', icon: 'tabler-alert-circle' },
  'Downloaded': { color: 'info', icon: 'tabler-arrow-down' }
}
```

---

## Comparison Matrix

| Feature | UserListTable | RolesTable | Permissions | InvoiceListTable |
|---------|---------------|-----------|-------------|-----------------|
| **Columns** | 8 | 7 | 4 | 8 |
| **Global Search** | ✓ | ✓ | ✓ | ✓ |
| **Filter Dropdowns** | 3 (separate) | 1 (integrated) | 0 | 1 (integrated) |
| **Pagination Options** | 10,25,50 | 10,25,50 | 5,7,9 | 10,25,50 |
| **Row Selection** | ✓ | ✓ | ✓ | ✓ |
| **Delete Action** | ✓ | ✓ | ✗ | ✓ |
| **Edit Action** | Menu option | Menu option | Dialog | Menu link |
| **Avatar Column** | ✓ | ✓ | ✗ | ✓ |
| **Status Chip** | ✓ | ✓ | ✗ | ✓ (special) |
| **Tooltip Info** | ✗ | ✗ | ✗ | ✓ |
| **Code Splitting** | ✗ | ✗ | ✗ | ✗ |

## Common Patterns

### 1. Fuzzy Filter Implementation
```typescript
const fuzzyFilter: FilterFn<any> = (row, columnId, value, addMeta) => {
  const itemRank = rankItem(row.getValue(columnId), value)
  addMeta({ itemRank })
  return itemRank.passed
}
```
**Used in:** All tables with search
**Source:** @tanstack/match-sorter-utils

### 2. Debounced Input Pattern
```typescript
const DebouncedInput = ({
  value: initialValue,
  onChange,
  debounce = 500,
  ...props
}) => {
  const [value, setValue] = useState(initialValue)
  useEffect(() => { setValue(initialValue) }, [initialValue])
  useEffect(() => {
    const timeout = setTimeout(() => onChange(value), debounce)
    return () => clearTimeout(timeout)
  }, [value])
  return <CustomTextField {...props} value={value} onChange={e => setValue(e.target.value)} />
}
```
**Used in:** All search inputs
**Debounce:** 500ms standard

### 3. Filter State + useEffect Pattern
```typescript
const [filterValue, setFilterValue] = useState('')

useEffect(() => {
  const filtered = data?.filter(item => {
    if (filterValue && item.field !== filterValue) return false
    return true
  })
  setFilteredData(filtered)
}, [filterValue, data])
```
**Used in:** All filter dropdowns
**Logic:** AND combination of all filters

### 4. Column Definition Pattern
```typescript
const columnHelper = createColumnHelper<DataType>()

const columns = useMemo<ColumnDef<DataType, any>[]>(
  () => [
    columnHelper.accessor('fieldName', {
      header: 'Header',
      cell: ({ row }) => <Component>{row.original.fieldName}</Component>
    })
  ],
  [dependencies]
)
```
**Used in:** All table column definitions
**Memoization:** Prevents unnecessary re-renders

### 5. Avatar Display Pattern
```typescript
const getAvatar = (params: Pick<Type, 'avatar' | 'name'>) => {
  if (avatar) {
    return <CustomAvatar src={avatar} size={34} />
  } else {
    return <CustomAvatar size={34}>{getInitials(name)}</CustomAvatar>
  }
}
```
**Used in:** User, Roles, Invoice tables
**Fallback:** getInitials() for missing avatars

---

## TanStack Configuration Patterns

### Standard Configuration
```typescript
const table = useReactTable({
  data,
  columns,
  filterFns: { fuzzy: fuzzyFilter },
  state: { rowSelection, globalFilter },
  initialState: { pagination: { pageSize: 10 } },
  enableRowSelection: true,
  globalFilterFn: fuzzyFilter,
  onRowSelectionChange: setRowSelection,
  getCoreRowModel: getCoreRowModel(),
  onGlobalFilterChange: setGlobalFilter,
  getFilteredRowModel: getFilteredRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getFacetedRowModel: getFacetedRowModel(),
  getFacetedUniqueValues: getFacetedUniqueValues(),
  getFacetedMinMaxValues: getFacetedMinMaxValues()
})
```

### Row Models Used
1. **getCoreRowModel** - Base data
2. **getFilteredRowModel** - After global & column filters
3. **getSortedRowModel** - After sorting
4. **getPaginationRowModel** - After pagination
5. **getFacetedRowModel** - For filter options
6. **getFacetedUniqueValues** - For unique values
7. **getFacetedMinMaxValues** - For min/max values

---

## Filter Strategies Comparison

### Strategy 1: Separate Filter Component (UserListTable)
```
TableFilters (separate component)
  ├── Role dropdown
  ├── Plan dropdown
  └── Status dropdown
  
UserListTable
  └── Integrates filters via setFilteredData callback
```
**Pros:** Clean separation, reusable
**Cons:** More components, more complexity

### Strategy 2: Integrated Filter (RolesTable)
```
RolesTable
  ├── Role dropdown (inline)
  └── useEffect watches role state
```
**Pros:** Simple, self-contained
**Cons:** Less reusable, harder to extend

### Strategy 3: Dialog Editing (Permissions)
```
PermissionsTable
  ├── Search only (no filter dropdowns)
  └── PermissionDialog (external editing)
```
**Pros:** Clean modal pattern, good for complex forms
**Cons:** Can't do quick edits inline

---

## Accessibility & UX Patterns

### 1. Column Sorting Indicators
```typescript
{{
  asc: <i className='tabler-chevron-up text-xl' />,
  desc: <i className='tabler-chevron-down text-xl' />
}[header.column.getIsSorted() as 'asc' | 'desc'] ?? null}
```
- Icon shows sort direction
- Cursor pointer when sortable
- Only shows when sorted

### 2. Select All Checkbox
```typescript
<Checkbox
  checked={table.getIsAllRowsSelected()}
  indeterminate={table.getIsSomeRowsSelected()}
  onChange={table.getToggleAllRowsSelectedHandler()}
/>
```
- Three states: all, some, none
- Indeterminate for partial selection

### 3. Empty State Handling
```typescript
{table.getFilteredRowModel().rows.length === 0 ? (
  <tbody>
    <tr>
      <td colSpan={table.getVisibleFlatColumns().length} className='text-center'>
        No data available
      </td>
    </tr>
  </tbody>
) : (
  // Table rows
)}
```

---

## Extension & Customization Guide

### Adding a New Column
```typescript
columnHelper.accessor('newField', {
  header: 'New Field Header',
  cell: ({ row }) => <div>{row.original.newField}</div>,
  enableSorting: true,
  size: 100  // Optional: column width
})
```

### Adding a New Filter
```typescript
// 1. Add state
const [newFilter, setNewFilter] = useState('')

// 2. Add to filter logic
if (newFilter && item.field !== newFilter) return false

// 3. Add UI element
<CustomTextField
  select
  value={newFilter}
  onChange={e => setNewFilter(e.target.value)}
>
  <MenuItem value=''>Clear</MenuItem>
  {/* Options */}
</CustomTextField>
```

### Changing Pagination Options
```typescript
// In pagination selector
<MenuItem value='5'>5</MenuItem>
<MenuItem value='10'>10</MenuItem>
<MenuItem value='25'>25</MenuItem>
```

---

## Performance Optimization Opportunities

### 1. Memoization
```typescript
// Already done for columns
const columns = useMemo(() => [...], [data, filteredData])

// Could add for filtered data
const filteredData = useMemo(() => {
  return data?.filter(item => /* logic */)
}, [data, filters])
```

### 2. Virtual Scrolling
- No current implementation
- Could use `@tanstack/react-virtual` for large lists
- Would improve performance with 1000+ rows

### 3. Code Splitting
- No dynamic imports for tables
- Could be split by route for faster page load

### 4. Search Debouncing
- Already implemented: 500ms
- Prevents excessive re-renders

---

## Testing Checklist

### Unit Tests
- [ ] Columns render correctly
- [ ] Sorting works
- [ ] Filtering works
- [ ] Pagination works
- [ ] Row selection works
- [ ] Empty state displays
- [ ] Search filters data
- [ ] All dropdown options work

### Integration Tests
- [ ] Filters combine correctly
- [ ] Search + filters work together
- [ ] Pagination updates correctly
- [ ] Row selection persists
- [ ] Actions trigger callbacks
- [ ] Parent updates from callbacks

### E2E Tests
- [ ] User can filter and sort
- [ ] User can search
- [ ] User can select rows
- [ ] User can paginate
- [ ] User can perform actions
- [ ] Responsive on mobile

---

## Dependencies

### NPM Packages
- `@tanstack/react-table`: Table core
- `@tanstack/match-sorter-utils`: Fuzzy search
- `@mui/material`: UI components
- `react-hook-form`: Form handling (AddUserDrawer)
- `classnames`: Conditional CSS

### Custom Components
- `CustomTextField`: MUI wrapper
- `CustomAvatar`: Avatar with fallback
- `TablePaginationComponent`: Pagination UI
- `OptionMenu`: Action menu
- `DebouncedInput`: Search input

---

## Version History & Standards

**Current TanStack React Table Version:** v8

**Standards Applied:**
- Fuzzy search for all global filters
- 500ms debounce for search
- AND combination for filter dropdowns
- Memoized column definitions
- Responsive grid layouts
- Native checkbox selection
- Pagination at table level (not URL-based)

---

## Summary

This catalog documents:
1. **4 main table implementations** across user, roles, invoice domains
2. **Consistent patterns** for filtering, searching, pagination
3. **Type-safe column definitions** with TypeScript
4. **Reusable components** like DebouncedInput and getAvatar
5. **Performance optimizations** with useMemo and debouncing
6. **Accessibility features** with native controls
7. **Extension points** for customization

**Key Takeaway:** The application follows a standardized TanStack React Table pattern with filters, search, sorting, pagination, and row selection - with variations in complexity and data model per domain.