# Knowledge Base Build Plan
# Vuexy Full-Version — Component & Page Indexing

## Goal

Build a lean, layered knowledge base so an AI agent can:
1. Answer "what does the users list page use?" from a lightweight index
2. Select the right component variant without reading source files
3. Assemble a new page by reading only the chosen component files

Token budget per query: ~1000–1500 tokens (vs 5000–10000 naive)

---

## Final Folder Structure

```
_knowledge/
  KNOWLEDGE.md                  ← master index (already exists)
  KB-PLAN.md                    ← this file

  pages/                        ← per-page snapshots (component manifests)
    auth/
      login-v1.md
      login-v2.md
      register-v1.md
      ...
    apps/
      users-list.md
      invoice-list.md
      ...
    dashboards/
      crm.md
      analytics.md
      ...

  catalogs/                     ← lean multi-variant comparison indexes
    tables.md                   ← all table variants: name | path | description | bundle-deps
    drawers.md
    charts.md
    forms.md
    dialogs.md
    cards-widgets.md
    auth-screens.md

  components/                   ← one file per component (read only after selection)
    tables/
      user-list-table.md
      invoice-list-table.md
      product-list-table.md
      order-list-table.md
      ...
    drawers/
      add-user-drawer.md
      filters-drawer.md
      add-product-drawer.md
      ...
    charts/
      revenue-chart.md
      analytics-chart.md
      ...
    forms/
      login-form.md
      register-form.md
      ...
    dialogs/
      role-dialog.md
      permissions-dialog.md
      ...
```

---

## Query Flow (how agent uses this)

```
User: "I need a user list with filters"

Step 1: KNOWLEDGE.md           ~50 tokens   → catalogs/tables.md + pages/apps/users-list.md exist
Step 2: pages/apps/users-list  ~200 tokens  → see what components the vuexy demo uses
Step 3: catalogs/tables.md     ~250 tokens  → compare all table variants, pick UserListTable
Step 4: components/tables/
          user-list-table.md   ~350 tokens  → get path, bundle-deps, usage notes
Step 5: read source file        ~800 tokens → only at assembly, only this one file
─────────────────────────────────────────────
Total: ~1650 tokens vs 5000–10000 naive
```

---

## Page Snapshot Format

File: `pages/apps/users-list.md`
```
Route: /apps/user/list
View:  src/views/apps/user/list/
Layout: vertical (sidebar)
Auth: required

Components:
- UserListTable     — TanStack Table, server-sort, pagination, avatar+role columns
- FiltersDrawer     — slide-in, role/plan/status dropdowns, controlled
- AddUserDrawer     — form drawer, RHF + Valibot, avatar upload
- RoleChip          — colored chip by role string
- StatusChip        — colored chip by status string

Data:
- Redux: src/redux-store/slices/userSlice
- API:   /api/apps/user/list  (fake-db backed)

Notes: Drawer state managed locally in page component, not Redux
```

---

## Catalog Entry Format

File: `catalogs/tables.md`
```
| Name              | Path                                    | Description                                              | Bundle-Deps                        | Reusable |
|-------------------|-----------------------------------------|----------------------------------------------------------|------------------------------------|----------|
| UserListTable     | src/views/apps/user/list/               | TanStack, server-side, avatar+role cols, row actions     | FiltersDrawer, userSlice           | yes      |
| InvoiceListTable  | src/views/apps/invoice/list/            | TanStack, status chip, amount col, date col, actions     | invoiceSlice                       | yes      |
| ProductListTable  | src/views/apps/ecommerce/products/list/ | TanStack, image col, category, stock, price, actions     | productSlice                       | yes      |
| OrderListTable    | src/views/apps/ecommerce/orders/list/   | TanStack, customer col, order status, payment status     | orderSlice                         | yes      |
| BasicReactTable   | src/views/react-table/basic/            | Minimal TanStack example, static data, no server state   | none                               | demo     |
| FilteringTable    | src/views/react-table/filter/           | TanStack with column filters demo                        | none                               | demo     |
```

---

## Component File Format

File: `components/tables/user-list-table.md`
```
Component: UserListTable
File: src/views/apps/user/list/UserListTable.tsx
Reusable: yes (with Redux slice swap)

Props:
- tableData: UserType[]

Required bundle:
- src/views/apps/user/list/UserFilters.tsx       (filters drawer)
- src/views/apps/user/list/AddNewUserDrawer.tsx  (add user form)
- src/redux-store/slices/userSlice.ts            (data + actions)
- src/types/apps/userTypes.ts                    (types)

Pattern: TanStack useReactTable, getCoreRowModel, getSortedRowModel, getPaginationRowModel
Columns: checkbox, full-name (avatar+name+email), role chip, plan, status chip, actions menu

Usage sketch:
  <UserListTable tableData={users} />
  // FiltersDrawer and AddNewUserDrawer are rendered inside this component
  // Data fetched via dispatch(fetchUsers()) on mount
```

---

## Build Steps — Ordered by Value

### Step 1 — Auth Pages + Screens (HIGH VALUE)
Every project needs auth. 12 variants, clean isolated components.

- `pages/auth/` — 12 page snapshots (login-v1, login-v2, register-v1, register-v2, forgot-pw-v1, forgot-pw-v2, reset-pw-v1, reset-pw-v2, 2fa-v1, 2fa-v2, verify-email-v1, verify-email-v2)
- `catalogs/auth-screens.md` — all variants in one comparison table
- `components/auth/` — LoginForm, RegisterForm, ForgotPasswordForm (one file each)

Effort: Medium | Reuse: Very High

---

### Step 2 — User Domain (HIGH VALUE)
Cleanest CRUD example in the app. Sets the pattern for all other list pages.

- `pages/apps/users-list.md`
- `pages/apps/users-view.md`
- `catalogs/tables.md` — add UserListTable entry
- `catalogs/drawers.md` — add FiltersDrawer, AddUserDrawer entries
- `components/tables/user-list-table.md`
- `components/drawers/add-user-drawer.md`
- `components/drawers/filters-drawer.md`

Effort: Medium | Reuse: Very High (template for other CRUDs)

---

### Step 3 — Tables Catalog Completion (HIGH VALUE)
With Step 2 done, complete the full tables catalog so all list pages are discoverable.

- `catalogs/tables.md` — add InvoiceListTable, ProductListTable, OrderListTable, CustomerListTable
- `components/tables/` — one file per remaining table variant

Effort: Low-Medium | Reuse: High (every list page downstream)

---

### Step 4 — Invoice Domain (HIGH VALUE)
Most complete CRUD pattern: list + add + edit + preview. Good reference.

- `pages/apps/invoice-list.md`
- `pages/apps/invoice-add.md`
- `pages/apps/invoice-edit.md`
- `pages/apps/invoice-preview.md`
- `components/forms/invoice-form.md`
- `components/cards/invoice-preview-card.md`

Effort: Medium | Reuse: High (add/edit pattern reused everywhere)

---

### Step 5 — Dashboards (MEDIUM VALUE)
5 dashboards. High visual variety, good chart + stat-card examples.

- `pages/dashboards/` — crm, analytics, ecommerce, logistics, academy
- `catalogs/charts.md` — all chart variants
- `catalogs/cards-widgets.md` — stat cards, activity cards
- `components/charts/` — per chart type

Effort: High | Reuse: Medium (charts reusable, dashboard layouts less so)

---

### Step 6 — Ecommerce Domain (MEDIUM VALUE)
Largest domain (12 pages). Index after simpler CRUDs are done.

- `pages/apps/ecommerce/` — all 12 page snapshots
- `components/tables/` — product, order, customer tables
- `components/drawers/` — add product, category
- `catalogs/` — update tables, drawers

Effort: High | Reuse: Medium-High

---

### Step 7 — Forms & Wizards (MEDIUM VALUE)
Form patterns, multi-step wizards. Reusable form constructs.

- `pages/forms/` — layouts, validation, wizard
- `pages/wizards/` — checkout, create-deal, property-listing
- `catalogs/forms.md`
- `components/forms/` — per form pattern

Effort: Medium | Reuse: Medium

---

### Step 8 — Misc Pages (LOW-MEDIUM VALUE)
Account settings, user profile, dialogs, widgets.

- `pages/misc/` — account-settings, user-profile, faq, pricing, dialog-examples
- `catalogs/dialogs.md`
- `components/dialogs/` — role, permissions dialogs

Effort: Low | Reuse: Low-Medium

---

### Step 9 — Front Pages (LOW VALUE for admin apps)
Landing, pricing, help-center, checkout, payment. Only relevant if building marketing pages.

- `pages/front-pages/` — 5 page snapshots

Effort: Low | Reuse: Low (only if building public-facing pages)

---

### Step 10 — Complex Apps (DEFER unless needed)
Email, Chat, Kanban, Calendar — each is a mini-application. Index on demand.

- `pages/apps/email.md`
- `pages/apps/chat.md`
- `pages/apps/kanban.md`
- `pages/apps/calendar.md`

Effort: High | Reuse: Low (too domain-specific)

---

## Priority Summary

| Step | Domain              | Value  | Effort | Do When          |
|------|---------------------|--------|--------|------------------|
| 1    | Auth pages          | High   | Med    | First            |
| 2    | User CRUD           | High   | Med    | First            |
| 3    | Tables catalog      | High   | Low    | First            |
| 4    | Invoice CRUD        | High   | Med    | First or Second  |
| 5    | Dashboards          | Medium | High   | Second           |
| 6    | Ecommerce           | Medium | High   | Second           |
| 7    | Forms & Wizards     | Medium | Med    | Second           |
| 8    | Misc pages          | Low    | Low    | On demand        |
| 9    | Front pages         | Low    | Low    | On demand        |
| 10   | Email/Chat/Kanban   | Low    | High   | Defer            |

Start with Steps 1–2 to validate the format, then decide whether to do 3–4 before moving to the next session.
