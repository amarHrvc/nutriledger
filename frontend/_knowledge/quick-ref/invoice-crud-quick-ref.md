# Invoice CRUD Quick Reference

**Epic:** kb-joc.1.3  
**Status:** Complete  
**Last Updated:** 2026-05-04

## Quick Answers

### Q: How to display a list of invoices with search, filters, and pagination?
**A:** Use `InvoiceListTable` from `src/views/apps/invoice/list/InvoiceListTable.tsx`
- Accepts `invoiceData: InvoiceType[]`
- Provides: fuzzy search, status filter, row selection, pagination (10/25/50)
- 8 columns: select, ID, status, client, total, issued date, balance, actions
- Special features: status tooltip (balance + due date), paid chip display

### Q: How to create/add a new invoice?
**A:** Navigate to `/apps/invoice/add` page
- Separate dedicated form page (not drawer or dialog)
- Component: `InvoiceAddPage` in `src/views/apps/invoice/add/`
- Requires: client details, service, amounts, dates
- Payment details: IBAN, bank name, SWIFT code

### Q: How to view/preview invoice details?
**A:** Click preview link or navigate to `/apps/invoice/preview/{id}`
- Uses `SingleInvoiceType` (invoice + paymentDetails)
- Component: `InvoicePreview` page
- Shows complete invoice with payment information

### Q: How to filter invoices by status?
**A:** Use status dropdown with 6 options
- Sent, Paid, Draft, Partial Payment, Past Due, Downloaded
- Single select (not multi-select)
- Updates filteredData via useEffect
- Display: status icon + colored avatar

### Q: How to mark invoice as paid?
**A:** Check if `balance === 0`
- Displays as "Paid" chip (success color, tonal variant)
- Update: set balance to 0 and invoiceStatus to "Paid"
- API: PUT `/api/invoices/{id}` with updated balance

### Q: What actions are available per invoice?
**A:** Three actions in InvoiceListTable:
- **Delete** (inline icon): removes from data state
- **Preview** (inline icon): links to preview page
- **More menu**: Download, Edit (links to edit page), Duplicate

---

## Type Definitions

### InvoiceStatusType (Invoice Status Options)

```typescript
type InvoiceStatusType = 
  | 'Sent'
  | 'Paid'
  | 'Draft'
  | 'Partial Payment'
  | 'Past Due'
  | 'Downloaded'
```

### InvoiceType (Core Invoice Object)

```typescript
type InvoiceType = {
  id: string                    // Invoice ID (route parameter)
  name: string                  // Client name
  total: number                 // Total invoice amount
  avatar: string               // Client avatar image path
  service: string              // Service description
  dueDate: string              // Due date (formatted string)
  address: string              // Client address
  company: string              // Client company
  country: string              // Client country
  contact: string              // Contact number
  avatarColor?: string         // Avatar fallback color
  issuedDate: string           // Issue date (formatted string)
  companyEmail: string         // Company email address
  balance: string | number     // Remaining balance (0 = Paid)
  invoiceStatus: InvoiceStatusType
}
```

### InvoicePaymentType (Payment Details)

```typescript
type InvoicePaymentType = {
  iban: string                 // International Bank Account Number
  totalDue: string             // Total due amount
  bankName: string             // Bank name
  country: string              // Bank country
  swiftCode: string            // SWIFT/BIC code
}
```

### SingleInvoiceType (Complete Invoice + Payment)

```typescript
type SingleInvoiceType = {
  invoice: InvoiceType
  paymentDetails: InvoicePaymentType
}
```

---

## Pattern Map: Status Icon & Color Mapping

```typescript
const invoiceStatusObj = {
  'Sent': { color: 'secondary', icon: 'tabler-send-2' },
  'Paid': { color: 'success', icon: 'tabler-check' },
  'Draft': { color: 'primary', icon: 'tabler-mail' },
  'Partial Payment': { color: 'warning', icon: 'tabler-chart-pie-2' },
  'Past Due': { color: 'error', icon: 'tabler-alert-circle' },
  'Downloaded': { color: 'info', icon: 'tabler-arrow-down' }
}
```

---

## Code Snippets

### Status Column with Tooltip

```typescript
columnHelper.accessor('invoiceStatus', {
  header: 'Status',
  cell: ({ row }) => (
    <Tooltip
      title={
        <div>
          <Typography>{row.original.invoiceStatus}</Typography>
          <br />
          <Typography>Balance: {row.original.balance}</Typography>
          <br />
          <Typography>Due: {row.original.dueDate}</Typography>
        </div>
      }
    >
      <CustomAvatar 
        color={invoiceStatusObj[row.original.invoiceStatus].color}
        size={28}
      >
        <i className={invoiceStatusObj[row.original.invoiceStatus].icon} />
      </CustomAvatar>
    </Tooltip>
  )
})
```

### Balance Column - Paid Display

```typescript
columnHelper.accessor('balance', {
  header: 'Balance',
  cell: ({ row }) => 
    row.original.balance === 0 ? (
      <Chip label='Paid' color='success' size='small' variant='tonal' />
    ) : (
      <Typography>{row.original.balance}</Typography>
    )
})
```

### Action Column Implementation

```typescript
columnHelper.accessor('action', {
  header: 'Action',
  cell: ({ row }) => (
    <div className='flex items-center'>
      <IconButton onClick={() => setData(data?.filter(i => i.id !== row.original.id))}>
        <i className='tabler-trash' />
      </IconButton>
      <IconButton>
        <Link href={`/apps/invoice/preview/${row.original.id}`}>
          <i className='tabler-eye' />
        </Link>
      </IconButton>
      <OptionMenu options={[
        { text: 'Download', icon: 'tabler-download' },
        { text: 'Edit', icon: 'tabler-pencil', href: `/apps/invoice/edit/${row.original.id}` },
        { text: 'Duplicate', icon: 'tabler-copy' }
      ]} />
    </div>
  )
})
```

---

## Routes & Navigation

| Operation | Route | Component |
|-----------|-------|-----------|
| **List** | /apps/invoice/list | InvoiceListTable |
| **Add** | /apps/invoice/add | InvoiceAddPage |
| **Preview** | /apps/invoice/preview/:id | InvoicePreview |
| **Edit** | /apps/invoice/edit/:id | InvoiceEditPage |

---

## TanStack React Table Config

```typescript
const table = useReactTable({
  data: filteredData,
  columns,
  filterFns: { fuzzy: fuzzyFilter },
  state: { rowSelection, globalFilter },
  initialState: { pagination: { pageSize: 10 } },
  enableRowSelection: true,
  globalFilterFn: fuzzyFilter,
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getFacetedRowModel: getFacetedRowModel(),
})
```

---

## File Structure

```
src/views/apps/invoice/
├── add/                      # Add invoice page
├── edit/                     # Edit invoice page
├── list/                     # List invoices
│   ├── index.tsx            # Page container
│   ├── InvoiceListTable.tsx # TanStack table
│   ├── InvoiceCard.tsx      # Stats cards
│   └── (filter components)
├── preview/                  # Preview invoice
└── shared/                   # Shared components

src/types/apps/
└── invoiceTypes.ts          # Type definitions
```

---

## Key Characteristics

- **8 columns**: Select, ID, Status, Client, Total, Issued Date, Balance, Actions
- **1 status filter**: Dropdown with 6 status options
- **Global search**: Fuzzy search with 500ms debounce
- **Pagination**: 10, 25, 50 rows per page
- **Row selection**: Multi-select with select-all checkbox
- **Server data**: Uses server component for initial load
- **Client interactivity**: TanStack table for filtering/sorting/pagination

