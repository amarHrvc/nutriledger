# OrderListTable Component

**File:** `src/views/apps/ecommerce/orders/list/OrderListTable.tsx`  
**Reusable:** Yes (swap Redux slice for different domain)  
**Type:** Presentational / Container Hybrid  

---

## Purpose

Core data grid component for order list pages. Implements TanStack React Table v8 with:
- Multi-select row selection
- Global fuzzy search
- Column sorting (asc/desc)
- Payment/order status filters
- Client-side pagination
- Order detail actions (view, invoice, tracking)

---

## Props

| Prop              | Type              | Required | Default | Purpose                                          |
|-------------------|-------------------|----------|---------|--------------------------------------------------|
| `tableData`       | `OrderType[]`    | Yes      | —       | Array of order objects to display in table       |

---

## Key Features

### Table (TanStack React Table v8)
- **Selection:** Multi-select with "select all" header checkbox
- **Columns:** 8 columns (select, order ID, customer, total, payment status, order status, date, actions)
- **Sorting:** Clickable column headers with asc/desc indicators
- **Filtering:** 
  - Global fuzzy search
  - Payment status (Paid, Pending, Cancelled, Failed)
  - Order status filters
- **Pagination:** 10/25/50 rows per page
- **Row Actions:** View invoice, track shipment, cancel, download

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
- `columnFilters` — payment/order status
- `sorting` — sort state
- `pagination` — page index and size

---

## Data Type

`	ypescript
type OrderType = {
  id: number
  orderNumber: string
  customer: string
  email: string
  total: number
  paymentStatus: 1 | 2 | 3 | 4
  orderStatus: string
  createdDate: string
  items: number
}
`

---

## Dependencies

### External Libraries
- `@tanstack/react-table` ^8.x
- `@tanstack/match-sorter-utils` ^8.x
- `@mui/material` ^5.x

---

## Notes

- Payment status uses numeric enum (1-4)
- Order status is flexible string (for future expansion)
- Reusable pattern for invoice management, shipments
