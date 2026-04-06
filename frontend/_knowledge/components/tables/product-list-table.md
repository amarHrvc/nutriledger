# ProductListTable Component

**File:** `src/views/apps/ecommerce/products/list/ProductListTable.tsx`  
**Reusable:** Yes (swap Redux slice for different domain)  
**Type:** Presentational / Container Hybrid  

---

## Purpose

Core data grid component for product list pages. Implements TanStack React Table v8 with:
- Multi-select row selection
- Global fuzzy search
- Column sorting (asc/desc)
- Category/status filters
- Client-side pagination
- Inline actions (delete, view, edit)
- Stock/inventory indicators

---

## Props

| Prop              | Type              | Required | Default | Purpose                                          |
|-------------------|-------------------|----------|---------|--------------------------------------------------|
| `tableData`       | `ProductType[]`  | Yes      | —       | Array of product objects to display in table     |

---

## Key Features

### Table (TanStack React Table v8)
- **Selection:** Multi-select with "select all" header checkbox
- **Columns:** 9 columns (select, product, category, brand, price, stock, status, rating, actions)
- **Sorting:** Clickable column headers with asc/desc indicators
- **Filtering:** 
  - Global fuzzy search
  - Category dropdown filters
  - Status chip filters
- **Pagination:** 10/25/50 rows per page
- **Row Actions:** Delete, view, edit menu

### Filters (TableFilters)
- **Category:** Dynamically populated from product categories
- **Status:** active, inactive, draft
- **Stock:** Low stock indicators

---

## State Management

**Redux:** None. All state is local to the component.

**Local State:**
- `rowSelection` — selected rows
- `data` — current table data
- `globalFilter` — search input value
- `columnFilters` — category/status filters
- `sorting` — sort state
- `pagination` — page index and size

---

## Data Type

`	ypescript
type ProductType = {
  id: number
  name: string
  slug: string
  category: string
  brand: string
  price: number
  stock: number
  rating: number
  status: 'active' | 'inactive' | 'draft'
  image?: string
}
`

---

## Dependencies

### External Libraries
- `@tanstack/react-table` ^8.x
- `@tanstack/match-sorter-utils` ^8.x
- `@mui/material` ^5.x

### Custom Components
- `TableFilters` — category/status filters
- `CustomAvatar`, `CustomTextField`, `OptionMenu`

---

## Notes

- Client-side pagination and filtering
- Similar to UserListTable but with product-specific columns
- Reusable pattern for other ecommerce entities (suppliers, categories)
