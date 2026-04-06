# Tables Catalog

Component variants for common table patterns across Vuexy. Use this to discover available table implementations and their dependencies.

---

## Available Tables

| Name                  | Path                                              | Description                                                                         | Bundle-Deps                        | Reusable |
|-----------------------|---------------------------------------------------|-------------------------------------------------------------------------------------|------------------------------------|----|
| UserListTable         | src/views/apps/user/list/                         | TanStack React Table v8, multi-select, fuzzy search, role/plan/status filters, avatar+name, sortable, paginated | TableFilters, AddUserDrawer, userSlice, userTypes | yes      |
| ProductListTable      | src/views/apps/ecommerce/products/list/           | Product CRUD with TanStack, category/status filters, stock indicators, price columns, image thumbnails | TableFilters, AddProductDrawer, productSlice, ecommerceTypes | yes      |
| ProductCategoryTable  | src/views/apps/ecommerce/products/categories/     | Category management with TanStack, fuzzy search, pagination, row selection, add/edit drawer | AddCategoryDrawer, categorySlice, ecommerceTypes | yes      |
| OrderListTable        | src/views/apps/ecommerce/orders/list/             | Order management with payment/order status, customer lookup, total amount, date range, action menu | orderSlice, ecommerceTypes, payment status enum | yes      |
| CustomerListTable     | src/views/apps/ecommerce/customers/list/          | Customer records with email/phone search, registration date, payment status, add customer drawer | AddCustomerDrawer, ecommerceTypes, payment status enum | yes      |
| ManageReviewsTable    | src/views/apps/ecommerce/manage-reviews/          | Product review management with TanStack, star rating display, reviewer info, status filter, approval actions | reviewSlice, ecommerceTypes, review status enum | yes      |
| InvoiceListTable      | src/views/apps/invoice/list/                      | Invoice tracking with 6 status types (Sent, Paid, Draft, Partial, Past Due, Downloaded), DebouncedInput, financial amounts | invoiceSlice, invoiceTypes, status icons | yes      |
| RolesTable            | src/views/apps/roles-permissions/ (conceptual)    | Admin table for CRUD user roles, permission assignment, usage count (not yet implemented) | rolesSlice, permissionsSlice | yes (when built)      |
| PermissionsTable      | src/views/apps/roles-permissions/ (conceptual)    | Admin table for system permissions management, module grouping (not yet implemented) | permissionsSlice | yes (when built)      |
| BasicReactTable       | src/views/react-table/BasicDataTables.tsx         | Minimal TanStack example: 6 columns, 10 static rows, no filtering/sorting (learning aid) | none | demo     |
| FilteringReactTable   | src/views/react-table/ (ColumnVisibility, KitchenSink, etc.) | Collection of demo examples: row selection, column visibility, inline editing, full-featured | none | demo     |

---

## Table Pattern

**Reusable:** Table is reusable when component takes data as prop and can be combined with different filter/add-drawer patterns.

**Bundle-Deps:** Critical sibling components and data slices that make the table functional:
- Filter drawer (e.g., TableFilters) — dropdown filters for domain-specific columns
- Add/Edit drawer (e.g., AddUserDrawer) — form to create/modify records
- Redux slice (e.g., userSlice) — data fetching and state (optional if using local state)
- Type definition (e.g., userTypes) — TypeScript interfaces for table data

**Default Columns:**
- Selection (checkbox)
- Primary identifier (avatar + name)
- Domain-specific columns (role, plan, status, etc.)
- Actions (delete, view, edit/options menu)

---

## Next Steps

Once you've selected a table from this catalog:
1. Read `_knowledge/components/tables/<table-name>.md` for implementation details
2. Review the `Bundle-Deps` to understand what else is needed
3. Check `pages/apps/<domain>.md` for how the table is integrated into a full page
