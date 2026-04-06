# CustomerListTable Component

**File:** `src/views/apps/ecommerce/customers/list/CustomerListTable.tsx`  
**Reusable:** Yes (swap Redux slice for different domain)  
**Type:** Presentational / Container Hybrid  

---

## Purpose

Core data grid component for customer list pages. Implements TanStack React Table v8 with:
- Multi-select row selection
- Global fuzzy search (name, email, phone)
- Column sorting (asc/desc)
- Payment status and registration status filters
- Client-side pagination
- Inline actions (view profile, edit, delete)
- AddCustomerDrawer integration

---

## Props

| Prop              | Type              | Required | Default | Purpose                                          |
|-------------------|-------------------|----------|---------|--------------------------------------------------|
| `tableData`       | `Customer[]`     | Yes      | —       | Array of customer objects to display in table    |

---

## Key Features

### Table (TanStack React Table v8)
- **Selection:** Multi-select with "select all" header checkbox
- **Columns:** 7 columns (select, customer, email, phone, registration date, payment status, actions)
- **Sorting:** Clickable column headers with asc/desc indicators
- **Filtering:** 
  - Global fuzzy search (customer name, email, phone)
  - Payment status dropdown
- **Pagination:** 10/25/50 rows per page
- **Row Actions:** View profile, edit customer, view orders, delete

---

## Drawer Integration

**AddCustomerDrawer** is embedded for creating new customers:
- Form with validation
- Auto-generates customer ID
- Appends to table data on submit

---

## Payment Status

`	ypescript
paymentStatus = {
  1: { text: 'Paid', color: 'success' },
  2: { text: 'Pending', color: 'warning' },
  3: { text: 'Cancelled', color: 'secondary' },
  4: { text: 'Failed', color: 'error' }
}
`

---

## State Management

**Redux:** None. All state is local to the component.

**Local State:**
- `rowSelection` — selected rows
- `data` — current table data
- `globalFilter` — search input value
- `columnFilters` — payment status
- `sorting` — sort state
- `pagination` — page index and size
- `addCustomerOpen` — drawer visibility

---

## Data Type

`	ypescript
type Customer = {
  id: number
  name: string
  email: string
  phone: string
  registrationDate: string
  paymentStatus: 1 | 2 | 3 | 4
  lastOrder?: string
  totalOrders: number
}
`

---

## Dependencies

### External Libraries
- `@tanstack/react-table` ^8.x
- `@tanstack/match-sorter-utils` ^8.x
- `@mui/material` ^5.x

### Custom Components
- `AddCustomerDrawer` — drawer for adding new customers
- `CustomAvatar`, `CustomTextField`, `OptionMenu`

---

## Notes

- Most feature-rich of the three tables (includes drawer)
- Customer phone number is searchable
- Reusable pattern for user management, team members
