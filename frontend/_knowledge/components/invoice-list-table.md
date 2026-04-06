# InvoiceListTable Component Snapshot

**Component:** InvoiceListTable  
**Location:** `src/views/apps/invoice/list/InvoiceListTable.tsx`  
**Epic:** kb-joc.3.3  
**Task:** T022 - Write invoice-list-table component snapshot  
**Status:** Complete

## Component Overview

**Type:** Client Component (`'use client'`)  
**Purpose:** Display invoices in advanced table with filtering, searching, pagination, and row selection

**Architecture Pattern:** TanStack React Table with invoice-specific features

## Key Characteristics

**Column Count:** 8 columns  
**Filters:** 1 (status via select dropdown)  
**Search:** Global fuzzy search with debouncing  
**Actions:** Delete, preview, menu (download, edit, duplicate)  
**Row Selection:** Multi-select with select-all  
**Pagination:** 10, 25, 50 rows per page  
**Sorting:** Column-based sorting  
**Unique Features:** Status tooltip, balance special display, invoice links

## Props Interface

```typescript
interface Props {
  invoiceData?: InvoiceType[]  // Array of invoices to display
}

type InvoiceType = {
  id: string                    // Invoice ID (in routes)
  name: string                  // Client name
  total: number                 // Total amount
  avatar: string               // Client avatar
  service: string              // Service description
  dueDate: string              // Due date string
  address: string              // Client address
  company: string              // Client company
  country: string              // Client country
  contact: string              // Contact number
  avatarColor?: string         // Avatar fallback color
  issuedDate: string           // Issue date string
  companyEmail: string         // Company email
  balance: string | number     // Remaining balance (0 = paid)
  invoiceStatus: InvoiceStatus // Invoice status
}
```

## State Management

```typescript
const [status, setStatus] = useState<InvoiceType['invoiceStatus']>('')
const [rowSelection, setRowSelection] = useState({})
const [data, setData] = useState(...[invoiceData])
const [filteredData, setFilteredData] = useState(data)
const [globalFilter, setGlobalFilter] = useState('')

const { lang: locale } = useParams()
```

### State Flow
```
invoiceData (prop)
  ↓
data (initial state)
  ↓
filteredData (filtered state) ← status + globalFilter
  ↓
Display in table
```

## Column Definitions (8 Total)

### 1. Select Column
**Type:** Checkbox  
**Purpose:** Row selection

```typescript
{
  id: 'select',
  header: ({ table }) => (
    <Checkbox {...table.getToggleAllRowsSelectedHandler()} />
  ),
  cell: ({ row }) => (
    <Checkbox {...row.getToggleSelectedHandler()} />
  )
}
```

**Features:**
- Select-all in header
- Individual row selection
- Indeterminate state for partial selection

### 2. ID Column
**Accessor:** `id`  
**Header:** `#`

```typescript
columnHelper.accessor('id', {
  header: '#',
  cell: ({ row }) => (
    <Typography
      component={Link}
      href={getLocalizedUrl(`/apps/invoice/preview/${row.original.id}`, locale as Locale)}
      color='primary.main'
    >{`#${row.original.id}`}</Typography>
  )
})
```

**Features:**
- Displays as `#<id>`
- Links to preview page
- Primary color link styling
- Locale-aware routing

### 3. Status Column
**Accessor:** `invoiceStatus`  
**Header:** `Status`

```typescript
columnHelper.accessor('invoiceStatus', {
  header: 'Status',
  cell: ({ row }) => (
    <Tooltip
      title={
        <div>
          <Typography variant='body2' component='span' className='text-inherit'>
            {row.original.invoiceStatus}
          </Typography>
          <br />
          <Typography variant='body2' component='span' className='text-inherit'>
            Balance:
          </Typography>{' '}
          {row.original.balance}
          <br />
          <Typography variant='body2' component='span' className='text-inherit'>
            Due Date:
          </Typography>{' '}
          {row.original.dueDate}
        </div>
      }
    >
      <CustomAvatar color={invoiceStatusObj[row.original.invoiceStatus].color} size={28}>
        <i className={classnames('bs-4 is-4', invoiceStatusObj[row.original.invoiceStatus].icon)} />
      </CustomAvatar>
    </Tooltip>
  )
})
```

**Status-to-Icon Mapping (invoiceStatusObj):**
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

**Features:**
- Status icon in colored avatar
- Tooltip shows: Status, Balance, Due Date
- Color-coded by status type
- Tabler icons for visual clarity

### 4. Client Column
**Accessor:** `name`  
**Header:** `Client`

```typescript
columnHelper.accessor('name', {
  header: 'Client',
  cell: ({ row }) => (
    <div className='flex items-center gap-3'>
      {getAvatar({ avatar: row.original.avatar, name: row.original.name })}
      <div className='flex flex-col'>
        <Typography className='font-medium' color='text.primary'>
          {row.original.name}
        </Typography>
        <Typography variant='body2'>{row.original.companyEmail}</Typography>
      </div>
    </div>
  )
})
```

**Features:**
- Avatar with light skin variant
- Client name (bold)
- Company email (secondary text)
- Responsive alignment

### 5. Total Column
**Accessor:** `total`  
**Header:** `Total`

```typescript
columnHelper.accessor('total', {
  header: 'Total',
  cell: ({ row }) => <Typography>{`$${row.original.total}`}</Typography>
})
```

**Features:**
- Formatted with $ prefix
- Numeric value from data
- Simple display

### 6. Issued Date Column
**Accessor:** `issuedDate`  
**Header:** `Issued Date`

```typescript
columnHelper.accessor('issuedDate', {
  header: 'Issued Date',
  cell: ({ row }) => <Typography>{row.original.issuedDate}</Typography>
})
```

**Features:**
- String display (pre-formatted)
- Sortable column
- Direct field display

### 7. Balance Column
**Accessor:** `balance`  
**Header:** `Balance`

```typescript
columnHelper.accessor('balance', {
  header: 'Balance',
  cell: ({ row }) => {
    return row.original.balance === 0 ? (
      <Chip label='Paid' color='success' size='small' variant='tonal' />
    ) : (
      <Typography color='text.primary'>{row.original.balance}</Typography>
    )
  }
})
```

**Features:**
- Special display for paid (balance === 0)
- Shows "Paid" chip (green, tonal)
- Shows balance amount if unpaid
- Supports string or number balance

### 8. Action Column
**Header:** `Action`

```typescript
columnHelper.accessor('action', {
  header: 'Action',
  cell: ({ row }) => (
    <div className='flex items-center'>
      <IconButton onClick={() => setData(data?.filter(invoice => invoice.id !== row.original.id))}>
        <i className='tabler-trash text-textSecondary' />
      </IconButton>
      <IconButton>
        <Link
          href={getLocalizedUrl(`/apps/invoice/preview/${row.original.id}`, locale as Locale)}
          className='flex'
        >
          <i className='tabler-eye text-textSecondary' />
        </Link>
      </IconButton>
      <OptionMenu
        iconButtonProps={{ size: 'medium' }}
        iconClassName='text-textSecondary'
        options={[
          {
            text: 'Download',
            icon: 'tabler-download',
            menuItemProps: { className: 'flex items-center gap-2 text-textSecondary' }
          },
          {
            text: 'Edit',
            icon: 'tabler-pencil',
            href: getLocalizedUrl(`/apps/invoice/edit/${row.original.id}`, locale as Locale),
            linkProps: {
              className: 'flex items-center is-full plb-2 pli-4 gap-2 text-textSecondary'
            }
          },
          {
            text: 'Duplicate',
            icon: 'tabler-copy',
            menuItemProps: { className: 'flex items-center gap-2 text-textSecondary' }
          }
        ]}
      />
    </div>
  ),
  enableSorting: false
})
```

**Action Sub-buttons:**

1. **Delete** (Inline)
   - Icon: tabler-trash
   - Behavior: Inline delete (removes from data state)
   - Direct callback

2. **Preview** (Inline)
   - Icon: tabler-eye
   - Behavior: Links to preview page
   - Route: `/apps/invoice/preview/<id>`
   - Locale-aware

3. **Download** (Menu)
   - Icon: tabler-download
   - Behavior: Download invoice (callback only)

4. **Edit** (Menu)
   - Icon: tabler-pencil
   - Behavior: Navigate to edit page
   - Route: `/apps/invoice/edit/<id>`
   - Locale-aware

5. **Duplicate** (Menu)
   - Icon: tabler-copy
   - Behavior: Duplicate invoice (callback only)

## Filter Implementation

### Status Filter
```typescript
const [status, setStatus] = useState<InvoiceType['invoiceStatus']>('')

// Filter dropdown (inline in CardContent)
<CustomTextField
  select
  value={status}
  onChange={e => setStatus(e.target.value)}
  id='status-select'
  className='max-sm:is-full sm:is-[160px]'
>
  <MenuItem value=''>Select Status</MenuItem>
  <MenuItem value='Sent'>Sent</MenuItem>
  <MenuItem value='Paid'>Paid</MenuItem>
  <MenuItem value='Draft'>Draft</MenuItem>
  <MenuItem value='Partial Payment'>Partial Payment</MenuItem>
  <MenuItem value='Past Due'>Past Due</MenuItem>
  <MenuItem value='Downloaded'>Downloaded</MenuItem>
</CustomTextField>
```

### Filter Logic
```typescript
const table = useReactTable({
  data: filteredData as InvoiceType[],
  // ...
})

// In separate useEffect or directly:
// Filters only by status, combines with global search via TanStack
```

## Search Implementation

### Debounced Search
```typescript
const DebouncedInput = ({
  value: initialValue,
  onChange,
  debounce = 500,
  ...props
}) => {
  const [value, setValue] = useState(initialValue)

  useEffect(() => {
    setValue(initialValue)
  }, [initialValue])

  useEffect(() => {
    const timeout = setTimeout(() => {
      onChange(value)
    }, debounce)

    return () => clearTimeout(timeout)
  }, [value])

  return <CustomTextField {...props} value={value} onChange={e => setValue(e.target.value)} />
}
```

### Search Placement
```typescript
<DebouncedInput
  value={globalFilter ?? ''}
  className='max-sm:is-full min-is-[250px]'
  onChange={value => setGlobalFilter(String(value))}
  placeholder='Search Invoice'
/>
```

**Features:**
- 500ms debounce
- Searches all fields (fuzzy)
- Responsive width
- Placeholder text

## Pagination Implementation

```typescript
// Page size selector
<CustomTextField
  select
  value={table.getState().pagination.pageSize}
  onChange={e => table.setPageSize(Number(e.target.value))}
  className='is-[70px]'
>
  <MenuItem value='10'>10</MenuItem>
  <MenuItem value='25'>25</MenuItem>
  <MenuItem value='50'>50</MenuItem>
</CustomTextField>

// Pagination component
<TablePagination
  component={() => <TablePaginationComponent table={table} />}
  count={table.getFilteredRowModel().rows.length}
  rowsPerPage={table.getState().pagination.pageSize}
  page={table.getState().pagination.pageIndex}
  onPageChange={(_, page) => table.setPageIndex(page)}
/>
```

**Default Page Size:** 10  
**Options:** 10, 25, 50

## TanStack Configuration

```typescript
const table = useReactTable({
  data: filteredData as InvoiceType[],
  columns,
  filterFns: {
    fuzzy: fuzzyFilter
  },
  state: {
    rowSelection,
    globalFilter
  },
  initialState: {
    pagination: {
      pageSize: 10
    }
  },
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

## UI Layout

### Header Section (CardContent)
```
┌─────────────────────────────────────────┐
│ Show [Dropdown] | Search + Status Filter│
└─────────────────────────────────────────┘
```

### Table Section
```
┌──┬────┬────┬────┬────┬────┬────┬──┐
│✓ │ #  │ ◆  │ ◆  │    │    │    │ ⋮ │
├──┼────┼────┼────┼────┼────┼────┼──┤
│✓ │ #1 │ ✓  │    │    │    │    │ ⋮ │
│✓ │ #2 │ ✓  │    │    │    │    │ ⋮ │
└──┴────┴────┴────┴────┴────┴────┴──┘
```

### Footer Section (Pagination)
```
Rows: 1-10 of 25 | [<] [1] [2] [3] [>]
```

## Styling & Responsive

### Responsive Classes
- `max-sm:is-full` - Full width on mobile
- `sm:is-[160px]` - Status filter width
- `min-is-[250px]` - Search input minimum width
- `is-[70px]` - Page size selector width

### Tailwind Utilities
- `flex items-center` - Alignment
- `gap-3` - Spacing
- `text-textSecondary` - Icon colors
- `font-medium` - Client name weight

## Integration with Parent

### Parent: InvoiceList (container)
```typescript
import InvoiceListTable from './InvoiceListTable'

const InvoiceList = ({ invoiceData }) => (
  <Grid container spacing={6}>
    <Grid size={{ xs: 12 }}>
      <InvoiceCard />
    </Grid>
    <Grid size={{ xs: 12 }}>
      <InvoiceListTable invoiceData={invoiceData} />
    </Grid>
  </Grid>
)
```

### Routing Integration
- Uses `useParams()` for locale
- Links to preview: `/apps/invoice/preview/<id>`
- Links to edit: `/apps/invoice/edit/<id>`
- Locale-aware routes via `getLocalizedUrl()`

## Data Mutations

### Delete Operation
```typescript
<IconButton onClick={() => setData(data?.filter(invoice => invoice.id !== row.original.id))}>
  <i className='tabler-trash text-textSecondary' />
</IconButton>
```

**Behavior:**
- Direct inline delete
- Removes from client-side state
- No confirmation dialog
- Immediate UI update

## Accessibility Features

- **Semantic Table:** Proper thead/tbody structure
- **Checkbox Selection:** Native checkboxes with states
- **Sorting Indicators:** Visual sort direction
- **Links:** Keyboard navigable
- **Icon Buttons:** Proper focus states
- **Tooltips:** Additional context for status
- **Empty State:** Shows "No data available" message

## Performance Characteristics

### Optimizations
- **useMemo for columns:** Memoizes column definitions
- **DebouncedInput:** 500ms debounce on search
- **TanStack:** Row models computed efficiently
- **getAvatar helper:** Prevents avatar recalculation

### Potential Bottlenecks
- Large invoice lists (1000+) may slow down
- Filter/search runs on every keystroke
- No virtual scrolling implemented

## Known Limitations

1. **No Confirmation Dialog:** Delete happens immediately
2. **No Bulk Delete:** Can only delete one row at a time
3. **No Edit Inline:** Must navigate to edit page
4. **No Email Sending:** Download/duplicate are callbacks only
5. **No Date Picker:** Search works on formatted strings
6. **No Amount Formatting:** Total shown as plain number

## Testing Checklist

- [ ] All 8 columns render
- [ ] Status filter works (6 options)
- [ ] Search filters by client name
- [ ] Sorting works per column
- [ ] Pagination shows correct rows
- [ ] Row selection works (individual + select all)
- [ ] Delete removes row
- [ ] Preview link navigates
- [ ] Edit link navigates
- [ ] Balance shows "Paid" when === 0
- [ ] Empty state shows with no data
- [ ] Responsive on mobile

## Summary

InvoiceListTable is a **feature-rich invoice management table** that:
- **Displays 8 columns** with invoice and client information
- **Filters by status** with 6 invoice status types
- **Searches globally** with fuzzy matching
- **Supports pagination** with 10, 25, 50 options
- **Enables row selection** for bulk operations
- **Provides quick actions** (delete, preview, edit, duplicate)
- **Shows special displays** (status tooltip, paid chip)
- **Maintains locale awareness** for international support

**Best Practices:**
- ✅ Status-specific icons and colors
- ✅ Client avatar with fallback
- ✅ Tooltip for additional context
- ✅ Balanced inline + menu actions
- ✅ Locale-aware routing
- ✅ Responsive layout
- ✅ Type-safe columns