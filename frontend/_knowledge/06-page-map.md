# Page Map

All pages are thin wrappers. Route file imports view from `src/views/`. Pattern:
- Route: `src/app/[lang]/(dashboard)/(private)/X/page.tsx`
- View:  `src/views/X/index.tsx`

## Dashboards

| Route (after /en/) | View location |
|--------------------|---------------|
| `/dashboards/crm` | `views/dashboards/crm/` |
| `/dashboards/analytics` | `views/dashboards/analytics/` |
| `/dashboards/ecommerce` | `views/dashboards/` (ecommerce sub) |
| `/dashboards/academy` | `views/apps/academy/dashboard/` |
| `/dashboards/logistics` | `views/apps/logistics/dashboard/` |

## Apps

| Route | View location | Notes |
|-------|---------------|-------|
| `/apps/calendar` | `views/apps/calendar/` | FullCalendar |
| `/apps/chat` | `views/apps/chat/` | Real-time-ish chat UI |
| `/apps/email` | `views/apps/email/` | Email client UI |
| `/apps/email/[folder]` | `views/apps/email/` | Inbox, sent, draft, etc. |
| `/apps/kanban` | `views/apps/kanban/` | Drag-drop board |
| `/apps/invoice/list` | `views/apps/invoice/list/` | TanStack Table |
| `/apps/invoice/add` | `views/apps/invoice/add/` | Form |
| `/apps/invoice/edit/[id]` | `views/apps/invoice/edit/` | Form |
| `/apps/invoice/preview/[id]` | `views/apps/invoice/preview/` | Print view |
| `/apps/user/list` | `views/apps/user/list/` | TanStack Table |
| `/apps/user/view` | `views/apps/user/view/` | Tabbed detail page |
| `/apps/roles` | `views/apps/roles/` | Role cards + permissions |
| `/apps/permissions` | `views/apps/permissions/` | Table |
| `/apps/ecommerce/dashboard` | `views/apps/ecommerce/dashboard/` | |
| `/apps/ecommerce/products/list` | `views/apps/ecommerce/products/list/` | |
| `/apps/ecommerce/products/add` | `views/apps/ecommerce/products/add/` | |
| `/apps/ecommerce/products/category` | `views/apps/ecommerce/products/category/` | |
| `/apps/ecommerce/orders/list` | `views/apps/ecommerce/orders/list/` | |
| `/apps/ecommerce/orders/details/[id]` | `views/apps/ecommerce/orders/details/` | |
| `/apps/ecommerce/customers/list` | `views/apps/ecommerce/customers/list/` | |
| `/apps/ecommerce/customers/details/[id]` | `views/apps/ecommerce/customers/details/` | |
| `/apps/ecommerce/manage-reviews` | `views/apps/ecommerce/manage-reviews/` | |
| `/apps/ecommerce/referrals` | `views/apps/ecommerce/referrals/` | |
| `/apps/ecommerce/settings` | `views/apps/ecommerce/settings/` | Tabbed settings |
| `/apps/logistics/dashboard` | `views/apps/logistics/dashboard/` | Map + stats |
| `/apps/logistics/fleet` | `views/apps/logistics/fleet/` | Mapbox map |
| `/apps/academy/dashboard` | `views/apps/academy/dashboard/` | |
| `/apps/academy/my-courses` | `views/apps/academy/my-courses/` | |
| `/apps/academy/course-details` | `views/apps/academy/course-details/` | |

## Pages

| Route | View location | Notes |
|-------|---------------|-------|
| `/pages/account-settings` | `views/pages/account-settings/` | Tabbed (account, security, billing, notifications, connections) |
| `/pages/user-profile` | `views/pages/user-profile/` | Tabbed (profile, teams, projects, connections) |
| `/pages/faq` | `views/pages/faq/` | |
| `/pages/pricing` | `views/pages/pricing/` | |
| `/pages/dialog-examples` | `views/pages/dialog-examples/` | |
| `/pages/widget-examples/basic` | `views/pages/widget-examples/basic/` | |
| `/pages/widget-examples/advanced` | `views/pages/widget-examples/advanced/` | |
| `/pages/widget-examples/statistics` | `views/pages/widget-examples/statistics/` | |
| `/pages/widget-examples/charts` | `views/pages/widget-examples/charts/` | |
| `/pages/widget-examples/actions` | `views/pages/widget-examples/actions/` | |
| `/pages/wizard-examples/checkout` | `views/pages/wizard-examples/checkout/` | Multi-step form |
| `/pages/wizard-examples/create-deal` | `views/pages/wizard-examples/create-deal/` | |
| `/pages/wizard-examples/property-listing` | `views/pages/wizard-examples/property-listing/` | |

## Auth Pages (blank layout — no sidebar)

| Route | Notes |
|-------|-------|
| `/login` (guest-only) | Real login, used by app |
| `/register` (guest-only) | |
| `/forgot-password` (guest-only) | |
| `/pages/auth/login-v1` | UI demo only |
| `/pages/auth/login-v2` | UI demo only |
| `/pages/auth/register-v1` | UI demo only |
| `/pages/auth/register-v2` | UI demo only |
| `/pages/auth/register-multi-steps` | UI demo only |
| `/pages/auth/forgot-password-v1` | UI demo only |
| `/pages/auth/forgot-password-v2` | UI demo only |
| `/pages/auth/reset-password-v1` | UI demo only |
| `/pages/auth/reset-password-v2` | UI demo only |
| `/pages/auth/two-steps-v1` | UI demo only |
| `/pages/auth/two-steps-v2` | UI demo only |
| `/pages/auth/verify-email-v1` | UI demo only |
| `/pages/auth/verify-email-v2` | UI demo only |

## Error / Misc Pages (blank layout)

| Route | Notes |
|-------|-------|
| `/pages/misc/401-not-authorized` | |
| `/pages/misc/404-not-found` | |
| `/pages/misc/coming-soon` | |
| `/pages/misc/under-maintenance` | |

## Charts

| Route | Library |
|-------|---------|
| `/charts/apex-charts` | ApexCharts (react-apexcharts) |
| `/charts/recharts` | Recharts |

## Forms

| Route | Notes |
|-------|-------|
| `/forms/form-layouts` | MUI form layouts |
| `/forms/form-validation` | React Hook Form + Valibot |
| `/forms/form-wizard` | Multi-step form wizard |

## React Table

| Route | Notes |
|-------|-------|
| `/react-table` | TanStack Table v8 examples |

## Front Pages (public — no auth, separate layout)

| URL | Notes |
|-----|-------|
| `/front-pages/landing-page` | Marketing landing |
| `/front-pages/pricing` | Public pricing page |
| `/front-pages/help-center` | Help center index |
| `/front-pages/help-center/article/...` | Article page |
| `/front-pages/payment` | Payment success page |
| `/front-pages/checkout` | Checkout page |
