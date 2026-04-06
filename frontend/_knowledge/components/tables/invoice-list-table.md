# InvoiceListTable Component

**File:** `src/views/apps/invoice/list/InvoiceListTable.tsx`  
**Reusable:** Yes (swap Redux slice for different domain)  
**Type:** Presentational / Container Hybrid  

---

## Purpose

Core data grid component for invoice list pages. Implements TanStack React Table v8 with:
- Multi-select row selection
- Global fuzzy search
- Column sorting (asc/desc)
- Invoice status filters
- Client-side pagination
- Inline actions (view, edit, download, send, delete)

---

## Props

| Prop              | Type              | Required | Default | Purpose                                          |
|-------------------|-------------------|----------|---------|--------------------------------------------------|
| `invoiceData`     | `InvoiceType[]`  | No       | []      | Array of invoice objects to display in table    |

---

## Key Features

### Table (TanStack React Table v8)
- **Selection:** Multi-select with "select all" header checkbox
- **Columns:** 8 columns (select, invoice ID, client, amount, status, sent date, balance, actions)
- **Sorting:** Clickable column headers with asc/desc indicators
- **Filtering:** 
  - Global fuzzy search (invoice ID, client name, email)
  - Status dropdown (Sent, Paid, Draft, Partial Payment, Past Due, Downloaded)
- **Pagination:** 10/25/50 rows per page
- **Row Actions:** View invoice, edit, download, send, duplicate, delete

### Invoice Status

`	ypescript
invoiceStatusObj = {
  'Sent': { color: 'secondary', icon: 'tabler-send-2' },
  'Paid': { color: 'success', icon: 'tabler-check' },
  'Draft': { color: 'primary', icon: 'tabler-mail' },
  'Partial Payment': { color: 'warning', icon: 'tabler-chart-pie-2' },
  'Past Due': { color: 'error', icon: 'tabler-alert-circle' },
  'Downloaded': { color: 'info', icon: 'tabler-arrow-down' }
}
`

---

## DebouncedInput Component

Built-in search input with 500ms debounce:
- Reduces re-renders during typing
- Smooth UX for large datasets
- Used for global search field

---

## State Management

**Redux:** None. All state is local to the component.

**Local State:**
- `rowSelection` — selected rows
- `data` — current table data
- `filteredData` — after filter/search
- `globalFilter` — search input value
- `status` — selected invoice status filter
- Pagination state managed by TanStack

---

## Data Type

`	ypescript
type InvoiceType = {
  id: number
  invoiceNumber: string
  client: string
  clientEmail: string
  amount: number
  issuedDate: string
  dueDate: string
  invoiceStatus: 'Sent' | 'Paid' | 'Draft' | 'Partial Payment' | 'Past Due' | 'Downloaded'
  balance?: number
  notes?: string
}
`

---

## Dependencies

### External Libraries
- `@tanstack/react-table` ^8.x
- `@tanstack/match-sorter-utils` ^8.x
- `@mui/material` ^5.x

### Custom Components
- `CustomTextField` — search input wrapper
- `CustomAvatar`, `OptionMenu`, `TablePaginationComponent`
- Tabler icons (via icon library) — status icons

### Utilities
- `getInitials(clientName)` — extract initials for avatar
- `getLocalizedUrl(path, locale)` — i18n-aware routing

---

## Notes

- Most feature-rich table with status icons and actions
- Invoice status is fully enumerated (6 variants)
- Balance/amount handling for financial data
- DebouncedInput pattern is reusable for other tables
- Follows same TanStack pattern as user/product/order tables
