# KB-TASKS — Vuexy Knowledge Base Build Task Specifications
# Generated via spec-panel expert review of KB-PLAN.md
# Date: 2026-04-05

---

## Spec Panel Review Summary

**Karl Wiegers:** KB-PLAN has clear scope per step but no measurable done-definitions.
Each task needs: exact output file list, format compliance criteria, and a query-validation
test (can an agent answer X using only the KB file?).

**Gojko Adzic:** Acceptance criteria must be executable.
Format: "Given this KB file exists → When agent reads it → Then it can answer [question]
without opening any source file."

**Alistair Cockburn:** Every task serves the same primary actor (AI agent assembling pages).
Scope each task by the actor's goal: "agent can select and assemble [domain] components
from KB alone."

**Martin Fowler:** Dependencies between tasks are load-bearing. A downstream task that
catalogs a component not yet documented fails silently. Make deps explicit and enforceable.

---

## Format Contract (applies to ALL tasks)

Before writing any KB file, the agent MUST conform to these formats from KB-PLAN.md:

### Page Snapshot Format
```
Route: /path
View:  src/views/path/
Layout: vertical | horizontal | blank
Auth: required | guest-only | public

Components:
- ComponentName  — one-line role description

Data:
- Redux: src/redux-store/slices/sliceName
- API:   /api/route

Notes: any non-obvious behavior
```

### Catalog Row Format
```
| Name | Path | Description | Bundle-Deps | Reusable |
```

### Component File Format
```
Component: ComponentName
File: src/views/path/ComponentName.tsx
Reusable: yes | no | demo

Props:
- propName: Type  — description

Required bundle:
- src/path/File.tsx  — role

Pattern: library + hooks used
Columns/Fields: list key UI elements

Usage sketch:
  <ComponentName prop={value} />
  // notes on embedding
```

---

## TASK-01 — Auth Pages Knowledge Base

**Goal:** Agent can identify, compare, and select any auth screen variant (login, register,
forgot-pw, reset-pw, 2FA, verify-email) from KB alone, then read only the chosen source file.

**Priority:** 1 — First (unblocks format validation for all subsequent tasks)
**Effort:** Medium
**Depends on:** nothing

**Source paths to read:**
- `src/views/pages/auth/` — all auth view components
- `src/app/[lang]/(blank-layout-pages)/(guest-only)/` — real login/register/forgot-pw
- `src/app/[lang]/(blank-layout-pages)/pages/auth/` — demo variants

**Deliverables — exact files to create:**

```
_knowledge/pages/auth/login-v1.md
_knowledge/pages/auth/login-v2.md
_knowledge/pages/auth/register-v1.md
_knowledge/pages/auth/register-v2.md
_knowledge/pages/auth/register-multi-steps.md
_knowledge/pages/auth/forgot-password-v1.md
_knowledge/pages/auth/forgot-password-v2.md
_knowledge/pages/auth/reset-password-v1.md
_knowledge/pages/auth/reset-password-v2.md
_knowledge/pages/auth/two-steps-v1.md
_knowledge/pages/auth/two-steps-v2.md
_knowledge/pages/auth/verify-email-v1.md
_knowledge/pages/auth/verify-email-v2.md
_knowledge/pages/auth/login-real.md        ← the actual /login used by the app
_knowledge/catalogs/auth-screens.md
_knowledge/components/auth/login-form.md
_knowledge/components/auth/register-form.md
_knowledge/components/auth/forgot-password-form.md
_knowledge/components/auth/two-steps-form.md
```

**Acceptance Criteria:**

1. All 14 page files exist and follow Page Snapshot Format exactly (Route, View, Layout,
   Auth, Components, Notes sections present).
2. `catalogs/auth-screens.md` contains one row per variant with columns:
   Name | Route | Layout | Form-Component | Has-Illustration | Real-or-Demo.
3. Each component file in `components/auth/` lists all props, required bundle files,
   and a usage sketch.
4. Validation test: given only `catalogs/auth-screens.md`, an agent can answer
   "which auth screen has a multi-step registration flow?" without reading any source file.
5. Validation test: given only `components/auth/login-form.md`, an agent can list
   every file it needs to import to use LoginForm in a new page.

**Notes:**
- Distinguish real auth (`/login`, `/register`, `/forgot-password`) from UI demos
  (`/pages/auth/login-v1` etc.) — mark clearly in each page file.
- `register-multi-steps.md` is a unique variant (wizard-style), document step count
  and state management approach.

---

## TASK-02 — User Domain CRUD

**Goal:** Agent can assemble a user list page with filters and add-user drawer by reading
only the KB files, not the source. Sets the CRUD pattern template for all subsequent domains.

**Priority:** 1 — First (parallel with TASK-01 possible, but TASK-01 validates format first)
**Effort:** Medium
**Depends on:** TASK-01 (format validated)

**Source paths to read:**
- `src/views/apps/user/list/` — UserListTable, UserFilters, AddNewUserDrawer
- `src/views/apps/user/view/` — user detail page components
- `src/redux-store/slices/userSlice.ts`
- `src/types/apps/userTypes.ts`
- `src/app/[lang]/(dashboard)/(private)/apps/user/list/page.tsx`
- `src/app/[lang]/(dashboard)/(private)/apps/user/view/page.tsx`

**Deliverables — exact files to create:**

```
_knowledge/pages/apps/users-list.md
_knowledge/pages/apps/users-view.md
_knowledge/catalogs/tables.md            ← create with UserListTable row
_knowledge/catalogs/drawers.md           ← create with FiltersDrawer + AddUserDrawer rows
_knowledge/components/tables/user-list-table.md
_knowledge/components/drawers/add-user-drawer.md
_knowledge/components/drawers/user-filters-drawer.md
```

**Acceptance Criteria:**

1. `pages/apps/users-list.md` lists every component rendered on that page with a
   one-line role description for each.
2. `catalogs/tables.md` exists with correct columns (Name | Path | Description |
   Bundle-Deps | Reusable) and UserListTable row populated.
3. `catalogs/drawers.md` exists with FiltersDrawer and AddUserDrawer rows.
4. `components/tables/user-list-table.md` documents: props, all required bundle files
   (with paths), TanStack hooks used, column list, and usage sketch.
5. `components/drawers/add-user-drawer.md` documents: RHF schema reference, validation
   library (Valibot), avatar upload behavior, and whether it manages its own open/close
   state or receives it as prop.
6. Validation test: given `components/tables/user-list-table.md` alone, an agent can
   produce a correct import list for a new page that embeds UserListTable.
7. Validation test: given `pages/apps/users-list.md`, an agent can answer "is drawer
   state in Redux or local component state?" without reading source.

**Notes:**
- UserView page is tabbed — document which tabs exist and what each tab's component is.
- Confirm whether FiltersDrawer is a standalone component or inline in UserListTable.

---

## TASK-03 — Tables Catalog Completion

**Goal:** `catalogs/tables.md` becomes the single lookup table for all list-page table
variants. Agent picks the right table by reading one file.

**Priority:** 1 — First (enables all downstream list-page assembly)
**Effort:** Low-Medium
**Depends on:** TASK-02 (tables.md exists, UserListTable row already present)

**Source paths to read:**
- `src/views/apps/invoice/list/`
- `src/views/apps/ecommerce/products/list/`
- `src/views/apps/ecommerce/orders/list/`
- `src/views/apps/ecommerce/customers/list/`
- `src/views/apps/roles/`
- `src/views/apps/permissions/`
- `src/views/react-table/` — basic and filter demo variants

**Deliverables — exact files to create/update:**

```
_knowledge/catalogs/tables.md            ← append remaining rows
_knowledge/components/tables/invoice-list-table.md
_knowledge/components/tables/product-list-table.md
_knowledge/components/tables/order-list-table.md
_knowledge/components/tables/customer-list-table.md
_knowledge/components/tables/roles-table.md
_knowledge/components/tables/permissions-table.md
_knowledge/components/tables/basic-react-table.md
_knowledge/components/tables/filtering-react-table.md
```

**Acceptance Criteria:**

1. `catalogs/tables.md` contains a row for every table variant listed above plus
   UserListTable from TASK-02. No source file needed to compare variants.
2. The Reusable column accurately distinguishes production tables (yes) from demo
   tables (demo) — demo tables have no Redux slice dep.
3. Each component file documents Bundle-Deps as exact file paths, not just names.
4. Validation test: given `catalogs/tables.md`, an agent can answer "which tables
   include a status chip column?" without reading any source file.
5. Validation test: given `catalogs/tables.md`, an agent can answer "which table
   has no external dependencies?" (answer: BasicReactTable).

---

## TASK-04 — Invoice Domain CRUD

**Goal:** Agent can assemble any invoice page (list, add, edit, preview) from KB alone.
Establishes the add/edit/preview pattern reused by ecommerce and other domains.

**Priority:** 1 — First or Second
**Effort:** Medium
**Depends on:** TASK-03 (catalogs/tables.md complete, invoice table row present)

**Source paths to read:**
- `src/views/apps/invoice/list/`
- `src/views/apps/invoice/add/`
- `src/views/apps/invoice/edit/`
- `src/views/apps/invoice/preview/`
- `src/redux-store/slices/invoiceSlice.ts`
- `src/types/apps/invoiceTypes.ts`

**Deliverables — exact files to create:**

```
_knowledge/pages/apps/invoice-list.md
_knowledge/pages/apps/invoice-add.md
_knowledge/pages/apps/invoice-edit.md
_knowledge/pages/apps/invoice-preview.md
_knowledge/components/forms/invoice-form.md
_knowledge/components/cards/invoice-preview-card.md
```

**Acceptance Criteria:**

1. All 4 page files follow Page Snapshot Format. Each lists exact component names
   and their roles.
2. `invoice-add.md` and `invoice-edit.md` document whether they share the same form
   component or use separate implementations.
3. `components/forms/invoice-form.md` documents: all fields, validation library,
   dynamic row behavior (adding/removing line items), and submit action (Redux thunk
   or direct API call).
4. `invoice-preview.md` documents print/PDF behavior if present.
5. Validation test: given `invoice-add.md` and `components/forms/invoice-form.md`,
   an agent can answer "does the invoice form support adding multiple line items
   dynamically?" without reading source.

---

## TASK-05 — Dashboards

**Goal:** Agent can identify which dashboard to reference for charts, stat cards, and
KPI widgets, and select the right chart variant from a catalog.

**Priority:** 2 — Second
**Effort:** High
**Depends on:** TASK-04 (core CRUD pattern complete)

**Source paths to read:**
- `src/views/dashboards/crm/`
- `src/views/dashboards/analytics/`
- `src/views/dashboards/` (ecommerce dashboard)
- `src/views/apps/academy/dashboard/`
- `src/views/apps/logistics/dashboard/`

**Deliverables — exact files to create:**

```
_knowledge/pages/dashboards/crm.md
_knowledge/pages/dashboards/analytics.md
_knowledge/pages/dashboards/ecommerce.md
_knowledge/pages/dashboards/academy.md
_knowledge/pages/dashboards/logistics.md
_knowledge/catalogs/charts.md
_knowledge/catalogs/cards-widgets.md
_knowledge/components/charts/apex-area-chart.md
_knowledge/components/charts/apex-bar-chart.md
_knowledge/components/charts/apex-donut-chart.md
_knowledge/components/charts/recharts-line.md
_knowledge/components/cards/stat-card.md
_knowledge/components/cards/activity-card.md
```

**Acceptance Criteria:**

1. All 5 dashboard page files list every widget/card/chart component with chart
   library identified (ApexCharts vs Recharts) per component.
2. `catalogs/charts.md` columns: Name | Dashboard | Library | Chart-Type |
   Data-Source | Reusable.
3. `catalogs/cards-widgets.md` columns: Name | Type | Metric-displayed |
   Has-trend-indicator | Reusable.
4. Each chart component file documents: props for data input, series format
   expected, and ApexCharts/Recharts config shape.
5. Validation test: given `catalogs/charts.md`, an agent can answer "which charts
   use Recharts (not ApexCharts)?" without reading source.
6. Validation test: given `catalogs/cards-widgets.md`, an agent can answer "which
   stat card shows a trend sparkline?" without reading source.

**Notes:**
- Academy and Logistics dashboards are under `views/apps/`, not `views/dashboards/`.
  Document this distinction in each page file.

---

## TASK-06 — Ecommerce Domain

**Goal:** Agent can assemble any ecommerce page (products, orders, customers, settings,
reviews, referrals) from KB alone.

**Priority:** 2 — Second
**Effort:** High
**Depends on:** TASK-03 (tables catalog), TASK-05 (dashboard pattern established)

**Source paths to read:**
- `src/views/apps/ecommerce/dashboard/`
- `src/views/apps/ecommerce/products/list/`
- `src/views/apps/ecommerce/products/add/`
- `src/views/apps/ecommerce/products/category/`
- `src/views/apps/ecommerce/orders/list/`
- `src/views/apps/ecommerce/orders/details/`
- `src/views/apps/ecommerce/customers/list/`
- `src/views/apps/ecommerce/customers/details/`
- `src/views/apps/ecommerce/manage-reviews/`
- `src/views/apps/ecommerce/referrals/`
- `src/views/apps/ecommerce/settings/`

**Deliverables — exact files to create:**

```
_knowledge/pages/apps/ecommerce/dashboard.md
_knowledge/pages/apps/ecommerce/products-list.md
_knowledge/pages/apps/ecommerce/products-add.md
_knowledge/pages/apps/ecommerce/products-category.md
_knowledge/pages/apps/ecommerce/orders-list.md
_knowledge/pages/apps/ecommerce/orders-details.md
_knowledge/pages/apps/ecommerce/customers-list.md
_knowledge/pages/apps/ecommerce/customers-details.md
_knowledge/pages/apps/ecommerce/manage-reviews.md
_knowledge/pages/apps/ecommerce/referrals.md
_knowledge/pages/apps/ecommerce/settings.md
_knowledge/components/drawers/add-product-drawer.md     ← if exists
_knowledge/components/forms/product-add-form.md
```

**Acceptance Criteria:**

1. All 11 page files follow Page Snapshot Format.
2. Products-list, orders-list, and customers-list rows are appended to
   `catalogs/tables.md` (from TASK-03).
3. `products-add.md` and `orders-details.md` document the form/detail pattern
   (are they single-page forms or multi-section with tabs?).
4. `settings.md` documents which tabs exist and what each tab controls.
5. Validation test: given `pages/apps/ecommerce/orders-details.md`, an agent can
   answer "does the order details page allow editing the order status inline?"
   without reading source.

---

## TASK-07 — Forms and Wizards

**Goal:** Agent can identify and reuse form layouts, validation patterns, and multi-step
wizard constructs from KB alone.

**Priority:** 2 — Second
**Effort:** Medium
**Depends on:** TASK-02 (RHF + Valibot pattern established via AddUserDrawer)

**Source paths to read:**
- `src/views/forms/form-layouts/`
- `src/views/forms/form-validation/`
- `src/views/forms/form-wizard/`
- `src/views/pages/wizard-examples/checkout/`
- `src/views/pages/wizard-examples/create-deal/`
- `src/views/pages/wizard-examples/property-listing/`

**Deliverables — exact files to create:**

```
_knowledge/pages/forms/form-layouts.md
_knowledge/pages/forms/form-validation.md
_knowledge/pages/forms/form-wizard.md
_knowledge/pages/wizards/checkout.md
_knowledge/pages/wizards/create-deal.md
_knowledge/pages/wizards/property-listing.md
_knowledge/catalogs/forms.md
```

**Acceptance Criteria:**

1. All 6 page files follow Page Snapshot Format.
2. `catalogs/forms.md` columns: Name | Type (layout|validation|wizard) |
   Validation-Library | Step-Count (for wizards) | RHF-used | Reusable.
3. `form-validation.md` documents which validation rules are demonstrated
   (required, min length, pattern, custom, async).
4. Each wizard page documents step count, step names, and state persistence
   approach (local state vs Redux vs URL params).
5. Validation test: given `catalogs/forms.md`, an agent can answer "which wizard
   has the most steps?" without reading source.

---

## TASK-08 — Misc Pages

**Goal:** Agent can assemble account settings, user profile, dialog examples, and
widget pages from KB alone.

**Priority:** On demand (low-medium value)
**Effort:** Low
**Depends on:** TASK-02 (base CRUD pattern)

**Source paths to read:**
- `src/views/pages/account-settings/`
- `src/views/pages/user-profile/`
- `src/views/pages/faq/`
- `src/views/pages/pricing/`
- `src/views/pages/dialog-examples/`
- `src/views/pages/widget-examples/`
- `src/views/apps/roles/`
- `src/views/apps/permissions/`

**Deliverables — exact files to create:**

```
_knowledge/pages/misc/account-settings.md
_knowledge/pages/misc/user-profile.md
_knowledge/pages/misc/faq.md
_knowledge/pages/misc/pricing.md
_knowledge/pages/misc/dialog-examples.md
_knowledge/pages/misc/widget-examples.md
_knowledge/pages/misc/roles.md
_knowledge/pages/misc/permissions.md
_knowledge/catalogs/dialogs.md
_knowledge/components/dialogs/role-dialog.md
_knowledge/components/dialogs/permissions-dialog.md
```

**Acceptance Criteria:**

1. `account-settings.md` and `user-profile.md` document each tab name and the
   primary component/form within that tab.
2. `catalogs/dialogs.md` columns: Name | Trigger | Has-Form | Validation |
   Controlled-by.
3. `dialog-examples.md` documents every dialog variant shown on that page.
4. Validation test: given `catalogs/dialogs.md`, an agent can answer "which dialog
   includes a permissions table?" without reading source.

---

## TASK-09 — Front Pages

**Goal:** Agent can assemble public-facing marketing pages (landing, pricing, help
center, checkout, payment) from KB alone.

**Priority:** On demand (low value for admin-focused projects)
**Effort:** Low
**Depends on:** nothing (isolated layout, no shared components with dashboard)

**Source paths to read:**
- `src/views/front-pages/landing-page/`
- `src/views/front-pages/pricing/`
- `src/views/front-pages/help-center/`
- `src/views/front-pages/payment/`
- `src/views/front-pages/checkout/`

**Deliverables — exact files to create:**

```
_knowledge/pages/front-pages/landing-page.md
_knowledge/pages/front-pages/pricing.md
_knowledge/pages/front-pages/help-center.md
_knowledge/pages/front-pages/payment.md
_knowledge/pages/front-pages/checkout.md
```

**Acceptance Criteria:**

1. All 5 page files use Page Snapshot Format with Layout marked as `front-pages`
   (distinct from dashboard and blank).
2. Each file notes which sections/components make up the page layout
   (hero, features, testimonials, etc.).
3. Validation test: given all 5 page files, an agent can answer "which front pages
   have a pricing table component?" without reading source.

---

## TASK-10 — Complex Apps (Deferred)

**Goal:** Agent can understand the structure of Email, Chat, Kanban, and Calendar apps
enough to locate the right component when assembling a similar feature.

**Priority:** Defer — index on demand only
**Effort:** High per app
**Depends on:** All previous tasks complete

**Source paths to read (per app, on demand):**
- `src/views/apps/email/`
- `src/views/apps/chat/`
- `src/views/apps/kanban/`
- `src/views/apps/calendar/`

**Deliverables — exact files to create (per app, on demand):**

```
_knowledge/pages/apps/email.md
_knowledge/pages/apps/chat.md
_knowledge/pages/apps/kanban.md
_knowledge/pages/apps/calendar.md
```

**Acceptance Criteria:**

1. Each page file documents the top-level layout pattern (split-pane, sidebar +
   content, board columns, etc.).
2. Documents key third-party libraries used (FullCalendar, drag-drop library, etc.).
3. Documents state management approach (is it all local state, or does it use Redux?).
4. Notes any non-standard routing (dynamic segments, parallel routes).
5. Does NOT attempt to document every sub-component — only the top-level structure
   and primary components. These are too large to fully catalog.

**Trigger:** Only execute TASK-10 sub-tasks when a user explicitly asks to build
something similar to Email, Chat, Kanban, or Calendar.

---

## Execution Order

```
Phase 1 (immediate):  TASK-01  TASK-02 (can run in parallel after format lock)
Phase 1 (immediate):  TASK-03  (unblocks all list pages)
Phase 1 (immediate):  TASK-04  (completes CRUD pattern set)

Phase 2 (second):     TASK-05  TASK-07 (can run in parallel)
Phase 2 (second):     TASK-06  (after TASK-03 and TASK-05)

Phase 3 (on demand):  TASK-08  TASK-09  (trigger on user request)
Phase 4 (deferred):   TASK-10  (one sub-task at a time, on demand)
```

## Quality Gate (applies before marking any task done)

- [ ] All deliverable files exist at exact paths listed above
- [ ] Every file uses the correct format (Page Snapshot / Catalog Row / Component File)
- [ ] No source file path in KB is a guess — every path was verified by reading the file
- [ ] Catalog rows contain only entries whose component files also exist in `components/`
- [ ] Both validation tests per task can be answered from KB alone (no source read needed)
