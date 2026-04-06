# Tables Quick Reference - React 19 & TanStack

> **Quick Reference** for implementing data tables with **TanStack React Table v8**, fuzzy search, filtering, pagination, and row selection.

---

## Q&A - Common Table Patterns

### Q1: How to create a table with sorting?
**A:** Use `getSortedRowModel()` and column header click handlers:

```typescript
// Setup TanStack with sorting
const table = useReactTable({
  data,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getSortedRowModel: getSortedRowModel(), // Enable sorting
  state: { sorting }
})

// Column definition with click handler
columnHelper.accessor('fieldName', {
  header: ({ column }) => (
    <button onClick={() => column.toggleSorting()}>
      Name {column.getIsSorted() === 'asc' && '↑'}
      {column.getIsSorted() === 'desc' && '↓'}
    </button>
  ),
  cell: ({ row }) => row.original.fieldName
})
```

### Q2: How to add pagination?
**A:** Use `getPaginationRowModel()` and page size controls:

```typescript
// Setup pagination
const table = useReactTable({
  data,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  initialState: { pagination: { pageSize: 10 } }
})

// Pagination controls
<TablePagination
  rowsPerPageOptions={[5, 10, 25, 50]}
  component='div'
  count={table.getFilteredRowModel().rows.length}
  rowsPerPage={table.getState().pagination.pageSize}
  page={table.getState().pagination.pageIndex}
  onPageChange={(event, newPage) => table.setPageIndex(newPage)}
  onRowsPerPageChange={(event) => table.setPageSize(Number(event.target.value))}
/>
```

### Q3: How to add search/filter functionality?
**A:** Use `getFilteredRowModel()` with fuzzy filter:

```typescript
import { rankItem } from '@tanstack/match-sorter-utils'

// Fuzzy filter function
const fuzzyFilter = (row, columnId, value, addMeta) => {
  const itemRank = rankItem(row.getValue(columnId), value)
  addMeta({ itemRank })
  return itemRank.passed
}

// Setup table
const [globalFilter, setGlobalFilter] = useState('')
const table = useReactTable({
  data,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  globalFilterFn: fuzzyFilter,
  state: { globalFilter },
  onGlobalFilterChange: setGlobalFilter
})

// Debounced search input
const DebouncedInput = ({ value: initialValue, onChange, debounce = 500, ...props }) => {
  const [value, setValue] = useState(initialValue)
  useEffect(() => setValue(initialValue), [initialValue])
  useEffect(() => {
    const timeout = setTimeout(() => onChange(value), debounce)
    return () => clearTimeout(timeout)
  }, [value])
  return <input {...props} value={value} onChange={e => setValue(e.target.value)} />
}
```

### Q4: How to implement row selection?
**A:** Use row selection state with checkboxes:

```typescript
const [rowSelection, setRowSelection] = useState({})
const table = useReactTable({
  data,
  columns,
  getCoreRowModel: getCoreRowModel(),
  enableRowSelection: true,
  state: { rowSelection },
  onRowSelectionChange: setRowSelection
})

// Select column
columnHelper.display({
  id: 'select',
  header: ({ table }) => (
    <Checkbox
      checked={table.getIsAllRowsSelected()}
      indeterminate={table.getIsSomeRowsSelected()}
      onChange={table.getToggleAllRowsSelectedHandler()}
    />
  ),
  cell: ({ row }) => (
    <Checkbox
      checked={row.getIsSelected()}
      onChange={row.getToggleSelectedHandler()}
    />
  )
})
```

---

## Type Definitions

### ColumnType (Column Configuration)
```typescript
type ColumnDef<T, V = unknown> = {
  id?: string                              // Unique identifier
  header?: string | ReactNode             // Column header
  accessorKey?: keyof T                   // Data property key
  accessorFn?: (row: T) => any            // Custom value accessor
  cell?: (info: CellContext) => ReactNode // Cell renderer
  footer?: string | ReactNode             // Footer content
  enableSorting?: boolean                 // Allow sorting
  enableColumnFilter?: boolean            // Allow filtering
  enableGlobalFilter?: boolean            // Include in global search
  enableGrouping?: boolean                // Allow grouping
  size?: number                           // Column width
}
```

### TableStateType (Table Configuration State)
```typescript
type TableState = {
  // Visibility
  columnVisibility: VisibilityState
  columnOrder: string[]
  columnPinning: PinningState
  
  // Selection
  rowSelection: RowSelectionState           // { [rowId]: boolean }
  
  // Filtering
  columnFilters: ColumnFiltersState        // { id: string, value: any }[]
  globalFilter: any                        // Search query
  
  // Sorting
  sorting: SortingState                    // { id: string, desc: boolean }[]
  
  // Pagination
  pagination: PaginationState              // { pageIndex, pageSize }
  
  // Expansion
  expanded: ExpandedState
  grouping: string[]
}
```

---

## 5-Table Pattern Map

### 1️⃣ User Table Pattern (Role-Based)
**Domain:** User Management  
**Columns:** Select, User (avatar+name), Role (icon), Plan, Billing, Status, Action  
**Filters:** 3 (Role, Plan, Status) + Global Search

```typescript
interface UsersType {
  id: number
  fullName: string
  username: string
  email: string
  role: 'admin' | 'author' | 'editor' | 'maintainer' | 'subscriber'
  avatar: string
  status: 'active' | 'pending' | 'inactive'
  currentPlan: string
  billing: string
}
```

### 2️⃣ Invoice Table Pattern (Status-Tracked)
**Domain:** Financial Management  
**Columns:** Select, ID, Status (with tooltip), Client, Total, Issued Date, Balance, Action  
**Filters:** 1 (Status) + Global Search

```typescript
interface InvoiceType {
  id: string
  name: string                    // Client
  total: number
  status: 'Sent' | 'Paid' | 'Draft' | 'Partial Payment' | 'Past Due' | 'Downloaded'
  balance: number | string
  issuedDate: string
  avatar: string
}
```

### 3️⃣ Product Table Pattern (Inventory-Based)
**Domain:** E-Commerce  
**Columns:** Select, Product (image+name), Category, SKU, Price, Stock, Status, Action  
**Filters:** 2 (Category, Status) + Global Search

```typescript
interface ProductType {
  id: number
  productName: string
  image: string
  category: string
  sku: number
  price: string
  stock: boolean
  qty: number
  status: string
}
```

### 4️⃣ Order Table Pattern (Transaction-Based)
**Domain:** E-Commerce  
**Columns:** Select, Order ID, Customer (avatar+name), Email, Payment Method, Status, Amount, Date, Action  
**Filters:** 1 (Status) + Global Search

```typescript
interface OrderType {
  id: number
  order: string
  customer: string
  email: string
  avatar: string
  method: string                  // Visa, PayPal, etc.
  status: string                  // Processing, Completed, etc.
  spent: number
  date: string
}
```

### 5️⃣ Customer Table Pattern (Relationship-Based)
**Domain:** E-Commerce / CRM  
**Columns:** Select, Customer (avatar+name), Email, Country, Orders, Total Spent, Status, Action  
**Filters:** 2 (Country, Status) + Global Search

```typescript
interface CustomerType {
  id: number
  customer: string
  email: string
  avatar: string
  country: string
  countryCode: string
  order: number                   // Total orders
  totalSpent: number
  status: string                  // Paid, Pending, etc.
}
```

---

## TanStack React Table - Setup & Usage

### 1. Hook Setup Pattern
```typescript
import {
  createColumnHelper,
  flexRender,
  getCoreRowModel,
  useReactTable,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  getFacetedRowModel,
  getFacetedUniqueValues,
  getFacetedMinMaxValues
} from '@tanstack/react-table'

const columnHelper = createColumnHelper<DataType>()

const [globalFilter, setGlobalFilter] = useState('')
const [rowSelection, setRowSelection] = useState({})

const table = useReactTable({
  data,
  columns: useMemo(() => [...], []),
  state: {
    globalFilter,
    rowSelection
  },
  enableRowSelection: true,
  globalFilterFn: fuzzyFilter,
  filterFns: { fuzzy: fuzzyFilter },
  onGlobalFilterChange: setGlobalFilter,
  onRowSelectionChange: setRowSelection,
  
  // Row models - process data
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  
  // Faceted models - for filter options
  getFacetedRowModel: getFacetedRowModel(),
  getFacetedUniqueValues: getFacetedUniqueValues(),
  getFacetedMinMaxValues: getFacetedMinMaxValues()
})
```

### 2. Column Definition Pattern
```typescript
const columns = useMemo<ColumnDef<DataType, any>[]>(() => [
  // Select column
  columnHelper.display({
    id: 'select',
    header: ({ table }) => (
      <Checkbox
        checked={table.getIsAllRowsSelected()}
        indeterminate={table.getIsSomeRowsSelected()}
        onChange={table.getToggleAllRowsSelectedHandler()}
      />
    ),
    cell: ({ row }) => (
      <Checkbox
        checked={row.getIsSelected()}
        onChange={row.getToggleSelectedHandler()}
      />
    )
  }),
  
  // Data columns
  columnHelper.accessor('fieldName', {
    header: 'Header Label',
    cell: ({ row }) => <span>{row.original.fieldName}</span>,
    enableSorting: true,
    enableGlobalFilter: true
  }),
  
  // Custom cell
  columnHelper.accessor('avatar', {
    header: 'User',
    cell: ({ row }) => (
      <div className='flex items-center gap-2'>
        <CustomAvatar src={row.original.avatar} size={34} />
        <span>{row.original.name}</span>
      </div>
    )
  })
], [])
```

### 3. Filter Pattern (Separate Component)
```typescript
// TableFilters.tsx
interface TableFiltersProps {
  roleFilter: string
  setRoleFilter: (value: string) => void
  planFilter: string
  setPlanFilter: (value: string) => void
  statusFilter: string
  setStatusFilter: (value: string) => void
}

export function TableFilters({
  roleFilter, setRoleFilter,
  planFilter, setPlanFilter,
  statusFilter, setStatusFilter
}: TableFiltersProps) {
  return (
    <div className='flex gap-4 flex-wrap mb-6'>
      <TextField
        select
        value={roleFilter}
        onChange={e => setRoleFilter(e.target.value)}
        label='Role'
      >
        <MenuItem value=''>All Roles</MenuItem>
        <MenuItem value='admin'>Admin</MenuItem>
        <MenuItem value='user'>User</MenuItem>
      </TextField>
      
      {/* Similar for other filters */}
    </div>
  )
}

// In main table component
const [roleFilter, setRoleFilter] = useState('')
const [planFilter, setPlanFilter] = useState('')
const [statusFilter, setStatusFilter] = useState('')

// Filter data
useEffect(() => {
  const filtered = data?.filter(item => {
    if (roleFilter && item.role !== roleFilter) return false
    if (planFilter && item.plan !== planFilter) return false
    if (statusFilter && item.status !== statusFilter) return false
    return true
  })
  setFilteredData(filtered)
}, [roleFilter, planFilter, statusFilter, data])
```

---

## Implementation Checklist

### Essential Features
- [x] TanStack React Table initialization
- [x] Column definitions with types
- [x] Global fuzzy search (debounced 500ms)
- [x] Row selection with checkboxes
- [x] Sorting via column headers
- [x] Pagination with page size selector
- [x] Filter controls (separate component)
- [x] Responsive grid layout

### Nice-to-Have Features
- [ ] Column visibility toggle
- [ ] Column reordering
- [ ] Bulk actions (delete, export)
- [ ] Row expansion (detail view)
- [ ] Virtual scrolling (large lists)
- [ ] Server-side filtering/sorting
- [ ] Export to CSV/Excel
- [ ] Print view

---

## Performance Tips

1. **Memoize columns:** Use `useMemo` to prevent recalculation
2. **Debounce search:** 500ms default is good for UX
3. **Use state sparingly:** Only table-specific state in component
4. **Virtual scrolling:** For 1000+ rows, use `@tanstack/react-virtual`
5. **Lazy load details:** Route to detail page instead of inline expansion

---

## Common Status Color Mapping

```typescript
// Invoice Status Colors
const invoiceStatusObj = {
  'Sent': { color: 'secondary', icon: 'tabler-send-2' },
  'Paid': { color: 'success', icon: 'tabler-check' },
  'Draft': { color: 'primary', icon: 'tabler-mail' },
  'Partial Payment': { color: 'warning', icon: 'tabler-chart-pie-2' },
  'Past Due': { color: 'error', icon: 'tabler-alert-circle' },
  'Downloaded': { color: 'info', icon: 'tabler-arrow-down' }
}

// User Status Colors
const userStatusObj = {
  active: 'success',
  pending: 'warning',
  inactive: 'secondary'
}

// Role Icon & Color
const userRoleObj = {
  admin: { icon: 'tabler-crown', color: 'error' },
  author: { icon: 'tabler-device-desktop', color: 'warning' },
  editor: { icon: 'tabler-edit', color: 'info' },
  maintainer: { icon: 'tabler-chart-pie', color: 'success' },
  subscriber: { icon: 'tabler-user', color: 'primary' }
}
```

---

**Last Updated:** 2026-05-06  
**Source:** Vuexy Admin - Tables Catalog & Domain Analysis  
**Framework:** React 19 + Next.js + TanStack React Table v8 + MUI
