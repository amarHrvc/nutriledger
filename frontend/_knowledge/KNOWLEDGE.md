# Vuexy Next.js TypeScript - Knowledge Base Index

> Portable knowledge base. Copy this `_knowledge/` folder alongside `full-version/` or `starter-kit/` into any project.
> In a Claude Code session, tell Claude: "read _knowledge/KNOWLEDGE.md as your reference for this codebase."

## Knowledge Base Build Plan

- [KB-PLAN.md](KB-PLAN.md) — Full indexing plan: folder structure, formats, 10-step build order, token flow
- [KB-TASKS.md](KB-TASKS.md) — Quality task specifications: deliverables, acceptance criteria, dependencies per step

## Index

- [01-architecture.md](01-architecture.md) — Stack, folder structure, routing system, path aliases
- [02-auth.md](02-auth.md) — Auth flow, NextAuth, AuthGuard, session, providers
- [03-theming.md](03-theming.md) — MUI v7 theme, overrides, customizer, dark/light mode
- [04-layout-system.md](04-layout-system.md) — Vertical/Horizontal/Collapsed layouts, wiring
- [05-navigation.md](05-navigation.md) — How to add menu items (vertical + horizontal)
- [06-page-map.md](06-page-map.md) — All pages by feature category with file paths
- [07-state-data.md](07-state-data.md) — Redux slices, API routes, fake-db, Prisma
- [08-components.md](08-components.md) — Core components, UI library, key hooks
- [09-i18n.md](09-i18n.md) — Internationalization, locales, RTL, dictionaries
- [**10-apps-crud.md**](#apps-crud-domains-user-roles-invoice) — User, Roles, Permissions, and Invoice CRUD domains

## Source Analysis & Domain Indexes

- [**source-analysis/dashboard-domain.md**](source-analysis/dashboard-domain.md) — Dashboard widgets, chart patterns, ApexCharts integration
- [**source-analysis/ecommerce-domain.md**](source-analysis/ecommerce-domain.md) — E-commerce CRUD, products, orders, customers
- [**source-analysis/forms-wizards-domain.md**](source-analysis/forms-wizards-domain.md) — Form layouts, validation, multi-step wizards
- [**source-analysis/user-domain.md**](source-analysis/user-domain.md) — User CRUD, list tables, detail pages
- [**source-analysis/roles-permissions.md**](source-analysis/roles-permissions.md) — RBAC, role definitions, permission management
- [**source-analysis/invoice-domain.md**](source-analysis/invoice-domain.md) — Invoice CRUD, printing, PDF generation
- [**source-analysis/misc-utilities-domain.md**](source-analysis/misc-utilities-domain.md) — Utility functions, custom hooks, theme helpers, form components
- [**source-analysis/front-pages-domain.md**](source-analysis/front-pages-domain.md) — Public pages, landing page, pricing, help center
- [**source-analysis/chat-app-domain.md**](source-analysis/chat-app-domain.md) — Complex real-time chat app with Redux, messaging, user presence

## Quick Reference

| Want to...                        | Go to               |
|-----------------------------------|---------------------|
| Add a new page/route              | 01-architecture.md  |
| Add a menu item                   | 05-navigation.md    |
| Change theme colors/mode          | 03-theming.md       |
| Add auth to a route               | 02-auth.md          |
| Add a new Redux slice             | 07-state-data.md    |
| Find an existing page to copy     | 06-page-map.md      |
| Use a core component/hook         | 08-components.md    |
| Add a new language                | 09-i18n.md          |

## Fast Answers — Start Here

Quick lookup table for common tasks. Each row points to the best quick-ref file to start with, avoiding unnecessary context switching.

| I want to...                      | Go to                              | Est. Tokens | Use When                              |
|-----------------------------------|------------------------------------|-------------|---------------------------------------|
| Add user form (create/edit)       | quick-ref/user-crud-quick-ref.md  | ~100        | Need user CRUD patterns               |
| Show dashboard chart              | quick-ref/dashboard-quick-ref.md  | ~100        | Building dashboard analytics          |
| Validate form                     | quick-ref/forms-quick-ref.md      | ~100        | Setting up form validation patterns   |
| Create API endpoint               | quick-ref/api-quick-ref.md        | ~80         | Need API route examples               |
| Setup authentication              | quick-ref/auth-quick-ref.md       | ~120        | Implementing auth flows               |
| Add invoice CRUD                  | quick-ref/invoice-quick-ref.md    | ~110        | Invoice management features           |
| Build data table                  | quick-ref/table-quick-ref.md      | ~90         | List tables with sorting/pagination   |
| Configure theme/styling           | quick-ref/theme-quick-ref.md      | ~80         | Theming and CSS customization         |
| Add menu item/navigation           | quick-ref/nav-quick-ref.md        | ~70         | Update navigation menus               |
| Setup Redux state                 | quick-ref/redux-quick-ref.md      | ~100        | State management patterns             |
| Add modal/dialog                  | quick-ref/modal-quick-ref.md      | ~85         | Modal UI components                   |
| Implement search/filters          | quick-ref/filter-quick-ref.md     | ~95         | Advanced filtering patterns           |

**Reading Strategy:**
- **Start here first** (~50-100 tokens) — Gives you working examples and copy-paste snippets
- **Then read** domain-specific docs for context (~300-500 tokens)
- **Go deep** to source-analysis for full patterns (~400+ tokens)

## Domain Navigation Map

Visual guide to how domains connect and which navigation files help you traverse them.

```
┌─────────────────────────────────────────────────────────────────┐
│                    VUEXY ADMIN DASHBOARD                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────┐                                           │
│  │   CORE AUTH      │──── Start here for any user flow         │
│  │                  │                                           │
│  │ • Login          │                                           │
│  │ • Register       │                                           │
│  │ • Reset Password │                                           │
│  └────────┬─────────┘                                           │
│           │                                                     │
│      ┌────┴─────┬─────────────────────────────────────────┐    │
│      │           │                                         │    │
│  ┌───▼──┐  ┌────▼──────┐                          ┌──────▼──┐  │
│  │ USER │  │   ROLES   │                          │   APPS  │  │
│  │ CRUD │  │   CRUD    │                          │   CRUD  │  │
│  │      │  │  & PERMS  │                          │         │  │
│  │ • List│  │           │                          │ • E-com │  │
│  │ • View│  │ • List    │                          │ • Chat  │  │
│  │ • Edit│  │ • Assign  │                          │ • Board │  │
│  │ • Del │  │ • Manage  │                          │         │  │
│  └──────┘  └───────────┘                          └─────────┘  │
│      │           │                                      │        │
│      │    ┌──────┴──────────────────────────────────────┘        │
│      │    │                                                      │
│  ┌───▼────▼──────┐                                               │
│  │  INVOICE CRUD │ (depends on User + Roles)                    │
│  │               │                                               │
│  │ • Create      │                                               │
│  │ • List/View   │                                               │
│  │ • Edit/Delete │                                               │
│  │ • Print/PDF   │                                               │
│  └──────┬────────┘                                               │
│         │                                                        │
│    ┌────┴─────────────────────────────────────────────┐         │
│    │                                                  │         │
│ ┌──▼────────────────────┐  ┌──────────────────────┐ │         │
│ │ CROSS-CUTTING DOMAINS │  │  UTILITY LAYERS      │ │         │
│ │                       │  │                      │ │         │
│ │ • Forms & Validation  │  │ • Custom Hooks       │ │         │
│ │ • Data Tables         │  │ • Theme Helpers      │ │         │
│ │ • Layout System       │  │ • Form Components    │ │         │
│ │ • Modals/Dialogs      │  │ • API Utilities      │ │         │
│ └───────────────────────┘  └──────────────────────┘ │         │
│                                                      │         │
└──────────────────────────────────────────────────────┴─────────┘
```

**Navigation Files to Guide Your Journey:**

- **[auth-to-crud-flow.md](auth-to-crud-flow.md)** — How to transition from Auth → User CRUD → Invoice CRUD workflows
- **[data-patterns-map.md](data-patterns-map.md)** — Data fetching, API integration, and state management patterns across domains
- **[component-selection-map.md](component-selection-map.md)** — Choose the right component/pattern for your feature

**Domain Reading Order:**
1. **Start**: quick-ref files for immediate answers
2. **Build Context**: Read relevant domain files (user-domain.md, invoice-domain.md, etc.)
3. **Master Patterns**: Read source-analysis files for deep understanding
4. **Navigate**: Use navigation files when moving between domains

## Read Protocol

A three-tiered reading strategy to maximize efficiency and minimize token usage while building complete understanding.

### The Three-Tier System

**Tier 1: Quick Reference (~50-100 tokens)**
- Purpose: Get working code examples immediately
- Files: `quick-ref/*.md` — Fast facts, copy-paste snippets, common patterns
- Time: 2-3 minutes
- Good for: Quick answers, immediate implementation needs, "show me an example"

**Tier 2: Detailed Documentation (~300-500 tokens)**
- Purpose: Understand the "why" behind patterns, see full context
- Files: `01-architecture.md`, `02-auth.md`, `source-analysis/*-domain.md`
- Time: 10-15 minutes
- Good for: Learning architecture, understanding design decisions, integration points

**Tier 3: Deep Source Analysis (~400+ tokens)**
- Purpose: Master all patterns in a domain, see production-grade implementation
- Files: `source-analysis/*-domain.md` — Complete walkthroughs with real code
- Time: 20-30 minutes
- Good for: Building complex features, debugging issues, optimizing patterns

### Token Savings Strategy

| Task | Old Path (No KB) | New Path (With KB) | Savings |
|------|------------------|-------------------|---------|
| Add user form | Full codebase exploration | user-crud-quick-ref.md | ~85% |
| Setup invoice CRUD | Multiple domain reads | invoice-quick-ref.md + invoice-domain.md | ~70% |
| Implement auth flow | Trace through entire project | auth-quick-ref.md + auth-to-crud-flow.md | ~80% |
| Create API endpoint | Search + read API files | api-quick-ref.md + 07-state-data.md | ~75% |
| Setup validation | Look at multiple forms | forms-quick-ref.md | ~90% |

### How to Use Each Tier

**For "I want to add X":**
1. Check Fast Answers table above → opens Quick Reference file
2. Copy the snippet, adapt to your needs
3. If stuck, read Tier 2 for why it works
4. For advanced customization, go to Tier 3

**For "How does Y work?":**
1. Start with Tier 2 docs (architecture, flow diagrams)
2. Read Tier 3 source-analysis for implementation details
3. Check Tier 1 for quick reference if you need examples

**For "Debug issue with Z":**
1. Read source-analysis (Tier 3) to understand full pattern
2. Check quick-ref (Tier 1) for common mistakes
3. Refer to actual source code in `src/` folder

## Auth Pages & Flows

### Overview

The auth system provides both **production-ready authentication pages** and **demo UI variants** for showcasing different design patterns and layouts. The system integrates **NextAuth.js v4** with credentials-based login and optional OAuth providers (Google).

**Key Features:**
- ✅ Real production authentication (Login, Register, Forgot Password)
- ✅ 13 demo UI variants with V1 (card-based) and V2 (split layout) designs
- ✅ Multi-step registration stepper
- ✅ 2FA/OTP verification screens
- ✅ Email verification flows
- ✅ Valibot form validation
- ✅ Theme-aware responsive design
- ✅ NextAuth.js + Credentials provider + OAuth ready

### Real Auth Pages (Production)

These are fully functional production pages with real authentication logic:

#### 1. **Login Page**
- **Route**: `/login`
- **File**: `src/views/Login.tsx` + `src/app/[lang]/(blank-layout-pages)/(guest-only)/login/page.tsx`
- **Type**: Server Component with NextAuth.js integration
- **Features**:
  - Email/password form with Valibot validation
  - NextAuth.js signIn with 'credentials' provider
  - Google OAuth signup button
  - Password visibility toggle
  - Remember me checkbox
  - Links to forgot password and register
  - Responsive split layout (illustration left, form right)
  - Theme-aware images
  - Dark/light mode support

**Quick Setup:**
```typescript
// src/app/[lang]/(blank-layout-pages)/(guest-only)/login/page.tsx
import Login from '@views/Login'

export default async function LoginPage({ params: { lang } }: PageProps) {
  const mode = getSystemMode() // Get theme mode
  return <Login mode={mode} />
}
```

#### 2. **Register Page**
- **Route**: `/register`
- **File**: `src/views/Register.tsx` + `src/app/[lang]/(blank-layout-pages)/(guest-only)/register/page.tsx`
- **Type**: Server Component with form submission
- **Features**:
  - Username, email, password form fields
  - Password visibility toggle
  - Terms & privacy policy acceptance checkbox
  - Social signup buttons (Google, Facebook, Twitter, GitHub)
  - Link to login page
  - Form validation with Valibot
  - Responsive split layout with illustrations

**Quick Setup:**
```typescript
// src/app/[lang]/(blank-layout-pages)/(guest-only)/register/page.tsx
import Register from '@views/Register'

export default async function RegisterPage({ params: { lang } }: PageProps) {
  const mode = getSystemMode()
  return <Register mode={mode} />
}
```

#### 3. **Forgot Password Page**
- **Route**: `/forgot-password`
- **File**: `src/views/ForgotPassword.tsx` + `src/app/[lang]/(blank-layout-pages)/(guest-only)/forgot-password/page.tsx`
- **Type**: Server Component
- **Features**:
  - Email-only form for reset link request
  - Valibot email validation
  - Back to login link
  - Responsive split layout with illustrations
  - Theme-aware responsive design

**Quick Setup:**
```typescript
// src/app/[lang]/(blank-layout-pages)/(guest-only)/forgot-password/page.tsx
import ForgotPassword from '@views/ForgotPassword'

export default async function ForgotPasswordPage({ params: { lang } }: PageProps) {
  const mode = getSystemMode()
  return <ForgotPassword mode={mode} />
}
```

### Demo Variants (UI Showcase)

16 non-functional demo variants for design reference and testing different layouts:

#### Demo Variants Table

| # | Screen | Type | File | Layout | Props | Features |
|---|--------|------|------|--------|-------|----------|
| 1 | LoginV1 | Demo | `src/views/pages/auth/LoginV1.tsx` | Card (V1) | none | Email/password, remember me, social |
| 2 | LoginV2 | Demo | `src/views/pages/auth/LoginV2.tsx` | Split (V2) | `mode` | Split layout, responsive, theme-aware |
| 3 | RegisterV1 | Demo | `src/views/pages/auth/RegisterV1.tsx` | Card (V1) | none | Multi-field form, social buttons |
| 4 | RegisterV2 | Demo | `src/views/pages/auth/RegisterV2.tsx` | Split (V2) | `mode` | Split layout, theme-aware |
| 5 | RegisterMultiSteps | Demo | `src/views/pages/auth/register-multi-steps/` | Stepper | `mode` | 3-step form (Account/Personal/Billing) |
| 6 | ForgotPasswordV1 | Demo | `src/views/pages/auth/ForgotPasswordV1.tsx` | Card (V1) | none | Email field, back to login link |
| 7 | ForgotPasswordV2 | Demo | `src/views/pages/auth/ForgotPasswordV2.tsx` | Split (V2) | `mode` | Split layout, theme-aware |
| 8 | ResetPasswordV1 | Demo | `src/views/pages/auth/ResetPasswordV1.tsx` | Card (V1) | none | Password + confirm, visibility toggles |
| 9 | ResetPasswordV2 | Demo | `src/views/pages/auth/ResetPasswordV2.tsx` | Split (V2) | `mode` | Split layout, theme-aware |
| 10 | TwoStepsV1 | Demo | `src/views/pages/auth/TwoStepsV1.tsx` | Card (V1) | none | 6-digit OTP, resend link |
| 11 | TwoStepsV2 | Demo | `src/views/pages/auth/TwoStepsV2.tsx` | Split (V2) | `mode` | OTP input, responsive |
| 12 | VerifyEmailV1 | Demo | `src/views/pages/auth/VerifyEmailV1.tsx` | Card (V1) | none | Email display, skip/resend |
| 13 | VerifyEmailV2 | Demo | `src/views/pages/auth/VerifyEmailV2.tsx` | Split (V2) | `mode` | Email display, responsive |

**Access Demo Pages:**
```
/pages/auth/login-v1/
/pages/auth/login-v2/
/pages/auth/register-v1/
/pages/auth/register-v2/
/pages/auth/register-multi-steps/
/pages/auth/forgot-password-v1/
/pages/auth/forgot-password-v2/
/pages/auth/reset-password-v1/
/pages/auth/reset-password-v2/
/pages/auth/two-steps-v1/
/pages/auth/two-steps-v2/
/pages/auth/verify-email-v1/
/pages/auth/verify-email-v2/
```

### Auth Screens Catalog

Complete reference for all auth screens with metadata and implementation details:

- [**catalogs/auth-screens.md**](catalogs/auth-screens.md) — Full catalog with field mappings, layout analysis, and implementation patterns

### Auth Source Analysis

Comprehensive documentation of auth source files and implementation patterns:

- [**auth-source-analysis.md**](auth-source-analysis.md) — Real pages, demo variants, and integration patterns

### Auth Pages Documentation

Individual page specifications and implementation guides:

**Real Production Pages:**
- [**pages/auth/login.md**](pages/auth/login.md) — Login page implementation, fields, validation, NextAuth integration
- [**pages/auth/register.md**](pages/auth/register.md) — Register page implementation, validation, social signup
- [**pages/auth/forgot-password.md**](pages/auth/forgot-password.md) — Forgot password page, email validation, recovery flow

**Demo Variants:**
- [**pages/auth/demo-login.md**](pages/auth/demo-login.md) — LoginV1 and LoginV2 variants, layout differences
- [**pages/auth/demo-register.md**](pages/auth/demo-register.md) — RegisterV1, RegisterV2, and RegisterMultiSteps variants
- [**pages/auth/demo-password.md**](pages/auth/demo-password.md) — ForgotPassword and ResetPassword variants (V1, V2)
- [**pages/auth/demo-2fa-verify.md**](pages/auth/demo-2fa-verify.md) — TwoSteps and VerifyEmail variants (V1, V2)

### Auth Form Components

Reusable form components for authentication flows:

- [**components/forms/login-form.md**](components/forms/login-form.md) — LoginForm component, fields, validation patterns, usage
- [**components/forms/register-form.md**](components/forms/register-form.md) — RegisterForm component, validation, error handling
- [**components/forms/forgot-password-form.md**](components/forms/forgot-password-form.md) — ForgotPasswordForm component, email validation
- [**components/forms/two-steps-form.md**](components/forms/two-steps-form.md) — TwoStepsForm component, OTP input, resend logic

### NextAuth.js Integration

**Configuration File**: `src/libs/auth.ts`

**Key Features:**
- CredentialsProvider for email/password authentication
- GoogleProvider for OAuth (requires Google Client ID/Secret)
- PrismaAdapter for session persistence
- Custom /api/login endpoint for credential verification
- Hardcoded demo users (replace with real DB lookup)

**Providers:**
```typescript
// Credentials Provider
signIn('credentials', { email, password })

// Google OAuth
signIn('google')
```

**Session Management:**
```typescript
// Server-side: getServerSession()
const session = await getServerSession(authOptions)

// Client-side: useSession()
const { data: session } = useSession()
```

**Protected Routes:**
- Guest-only routes redirect to dashboard if authenticated: `/login`, `/register`, `/forgot-password`
- Private routes enforce AuthGuard: `(dashboard)/(private)/*`

**Environment Variables:**
```env
NEXTAUTH_SECRET=your-secret-key
NEXTAUTH_URL=http://localhost:3000
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
```

### Form Validation with Valibot

All auth forms use **Valibot** for schema-based validation:

**Common Validation Patterns:**

```typescript
import { email, minLength, object, pipe, string } from 'valibot'

// Login validation
const loginSchema = object({
  email: pipe(string(), email('Invalid email')),
  password: pipe(string(), minLength(6, 'Min 6 characters'))
})

// Register validation
const registerSchema = object({
  username: pipe(string(), minLength(3)),
  email: pipe(string(), email()),
  password: pipe(string(), minLength(8))
})

// Use in forms:
const { email, setFieldError } = useForm(loginSchema)
```

### Layout Types

#### V1: Card-Based Layout
- Max-width: 450px on desktop
- Centered card with decorative pseudo-elements
- Suitable for simple forms
- Mobile-responsive

**Pages:** LoginV1, RegisterV1, ForgotPasswordV1, ResetPasswordV1, TwoStepsV1, VerifyEmailV1

#### V2: Split Layout
- Full-screen split view (left: illustration, right: form)
- Responsive illustration (hidden on tablet/mobile)
- Theme-aware images (light/dark/bordered variants)
- Production-grade responsive design
- Supports skin variants

**Pages:** LoginV2, RegisterV2, ForgotPasswordV2, ResetPasswordV2, TwoStepsV2, VerifyEmailV2, Real Auth Pages

### Security Features & Best Practices

**Session Management:**
- Server-side session validation with `getServerSession()`
- PrismaAdapter stores sessions securely
- NEXTAUTH_SECRET for JWT signing
- Automatic session refresh

**Password Security:**
- Valibot validation for password strength
- Visibility toggle for UX
- Never display passwords in logs/errors
- Hash passwords before storage (use bcrypt in production)

**Form Validation:**
- Client-side Valibot validation for UX
- Server-side validation before processing
- CSRF protection via NextAuth.js
- Rate limiting on login endpoint (implement in production)

**Navigation Between Screens:**

```
Login ←→ Register
  ↓
Forgot Password → Reset Password (after email click)
  ↓
2FA/OTP Verification
  ↓
Email Verification
  ↓
Dashboard
```

**Redirect Logic:**
- Guest-only pages redirect authenticated users to `/dashboard`
- Private pages redirect non-authenticated users to `/login`
- Guest check: `if (session) redirect('/dashboard')`
- Auth check: Done via AuthGuard HOC in layout

### Extending the Auth System

**Add a New Auth Page:**
1. Create new view component: `src/views/YourAuthPage.tsx`
2. Create route page: `src/app/[lang]/(blank-layout-pages)/(guest-only)/your-page/page.tsx`
3. Import and render view with `mode` prop
4. Add validation schema with Valibot
5. Wire up form submission to API endpoint or Server Action

**Create a Custom Auth Provider:**
1. Edit `src/libs/auth.ts`
2. Add new provider to `providers` array:
```typescript
import GitHubProvider from "next-auth/providers/github"

GitHubProvider({
  clientId: process.env.GITHUB_ID,
  clientSecret: process.env.GITHUB_SECRET,
})
```
3. Test with `signIn('github')`

**Replace Demo Users with Real Database:**
1. Edit `src/app/api/login/users.ts`
2. Replace hardcoded users with real DB query:
```typescript
// From demo
export const users = [{ id: '1', email: 'test@example.com', ... }]

// To real DB
const user = await prisma.user.findUnique({ where: { email } })
```

### Auth System Routing

```
/                                    (public landing page)
/login                               (guest-only, real auth)
/register                            (guest-only, real auth)
/forgot-password                     (guest-only, real auth)
/pages/auth/login-v1/                (demo showcase, V1 card layout)
/pages/auth/login-v2/                (demo showcase, V2 split layout)
/pages/auth/register-v1/             (demo showcase, V1 card layout)
/pages/auth/register-v2/             (demo showcase, V2 split layout)
/pages/auth/register-multi-steps/    (demo showcase, 3-step stepper)
/pages/auth/forgot-password-v1/      (demo showcase, V1 card layout)
/pages/auth/forgot-password-v2/      (demo showcase, V2 split layout)
/pages/auth/reset-password-v1/       (demo showcase, V1 card layout)
/pages/auth/reset-password-v2/       (demo showcase, V2 split layout)
/pages/auth/two-steps-v1/            (demo showcase, V1 OTP)
/pages/auth/two-steps-v2/            (demo showcase, V2 OTP)
/pages/auth/verify-email-v1/         (demo showcase, V1 email verify)
/pages/auth/verify-email-v2/         (demo showcase, V2 email verify)
/dashboard                           (private, auth-protected)
/api/auth/[...nextauth]              (NextAuth.js handler)
/api/login                           (credentials provider endpoint)
```

### Quick Start: Add Protected Route

```typescript
// 1. Create page under (private) layout group
// src/app/[lang]/(dashboard)/(private)/my-page/page.tsx
export default function MyPage() {
  return <div>Protected content</div>
}

// 2. AuthGuard is automatically applied by parent layout
// src/app/[lang]/(dashboard)/(private)/layout.tsx (already has AuthGuard)

// 3. Access /my-page - automatically redirects to login if not authenticated
```

### Related Documentation

- [**02-auth.md**](02-auth.md) — Core auth system, NextAuth setup, session management, environment variables
- [**01-architecture.md**](01-architecture.md) — Route groups, folder structure, path aliases used in auth system
- [**08-components.md**](08-components.md) — AuthIllustrationWrapper, form components, UI elements

## Apps CRUD Domains: User, Roles, Invoice

### Overview

The application provides four complete CRUD domains for managing users, roles, permissions, and invoices. Each domain follows a consistent TanStack React Table pattern with advanced features like fuzzy search, filtering, sorting, pagination, and row selection.

**Domains:**
1. **User Management** - Create, list, view, and manage user accounts
2. **Roles & Permissions** - Role-based access control (RBAC) with permission management
3. **Invoice Management** - Create, list, preview, and manage invoices

### User Domain

**Purpose:** Comprehensive user administration with listing, creation, filtering, and profile management.

**Key Components:**
- **UserListTable** - TanStack React Table with 8 columns, 3-filter system, search, pagination
- **AddUserDrawer** - React Hook Form drawer for creating new users
- **TableFilters** - Reusable filter component (role, plan, status)
- **UserDetails & UserPlan** - Profile and subscription information

**File Locations:**
\\\
src/views/apps/user/
├── list/
│   ├── UserListTable.tsx      # Main table with react-table
│   ├── AddUserDrawer.tsx      # User creation form
│   ├── TableFilters.tsx       # Filter controls
│   ├── UserListCards.tsx      # Summary cards
│   └── index.tsx              # List page container
└── view/
    ├── user-left-overview/    # Profile section
    │   ├── UserDetails.tsx
    │   └── UserPlan.tsx
    └── user-right/            # Tabbed settings
        ├── overview/
        ├── security/
        ├── billing-plans/
        ├── notifications/
        └── connections/
\\\

**Pages:**
- **Users List:** \/apps/user\ — Table with search, filter, add
- **User Detail:** \/apps/user/view\ — Two-column layout with tabs

**Documentation:**
- [**source-analysis/user-domain.md**](source-analysis/user-domain.md) — Component structure and validation patterns
- [**pages/users-list.md**](pages/users-list.md) — Users list page snapshot
- [**pages/user-detail.md**](pages/user-detail.md) — User detail view snapshot
- [**components/add-user-drawer.md**](components/add-user-drawer.md) — Form component details
- [**components/table-filters.md**](components/table-filters.md) — Filter component implementation

### Roles & Permissions Domain

**Purpose:** Role-based access control (RBAC) management with role cards and permissions table.

**Key Components:**
- **RoleCards** - Visual role display with user counts and avatars
- **RolesTable** - User-to-role association table
- **Permissions Table** - Individual permissions with role assignment

**Predefined Roles:**
1. Administrator - Full system access
2. Editor - Content management
3. Users - Standard user access
4. Support - Support staff access
5. Restricted User - Limited access

**File Locations:**
\\\
src/views/apps/
├── roles/
│   ├── RoleCards.tsx       # Role cards display
│   ├── RolesTable.tsx      # User-role table
│   └── index.tsx           # Roles page
└── permissions/
    └── index.tsx           # Permissions table
\\\

**Pages:**
- **Roles & Permissions:** \/apps/roles\ — Role cards + user-role table + permissions

**Documentation:**
- [**source-analysis/roles-permissions.md**](source-analysis/roles-permissions.md) — RBAC architecture and implementation patterns

### Invoice Domain

**Purpose:** Complete invoice management with listing, creation, editing, and preview.

**Key Components:**
- **InvoiceListTable** - TanStack React Table with 8 columns, status filter, advanced actions
- **InvoiceCard** - Summary statistics
- **InvoiceListTable** - Invoice display with client, total, status, balance

**Invoice Statuses:**
- Sent (secondary)
- Paid (success)
- Draft (primary)
- Partial Payment (warning)
- Past Due (error)
- Downloaded (info)

**File Locations:**
\\\
src/views/apps/invoice/
├── list/
│   ├── InvoiceListTable.tsx   # Main table
│   ├── InvoiceCard.tsx        # Summary cards
│   └── index.tsx              # List page
├── add/                       # Create invoice
├── edit/                      # Edit invoice
├── preview/                   # View details
└── shared/                    # Shared components
\\\

**Pages:**
- **Invoice List:** \/apps/invoice\ — Table with status filter, search, actions
- **Invoice Preview:** \/apps/invoice/preview/<id>\ — Full invoice details
- **Invoice Edit:** \/apps/invoice/edit/<id>\ — Edit form
- **Invoice Add:** \/apps/invoice/add\ — Create new

**Documentation:**
- [**source-analysis/invoice-domain.md**](source-analysis/invoice-domain.md) — Invoice types, components, and patterns
- [**components/invoice-list-table.md**](components/invoice-list-table.md) — InvoiceListTable component snapshot

### Table Patterns & Standards

All tables follow consistent TanStack React Table patterns:

**Common Features:**
- ✅ Fuzzy global search (debounced 500ms)
- ✅ Advanced filtering (1-3 dimensions per table)
- ✅ Column sorting
- ✅ Pagination (10, 25, 50 per page)
- ✅ Row selection (multi-select with select-all)
- ✅ Responsive grid layout

**Comparison Matrix:**

| Feature | UserListTable | RolesTable | Permissions | InvoiceListTable |
|---------|---------------|-----------|-------------|-----------------|
| **Columns** | 8 | 7 | 4 | 8 |
| **Global Search** | ✓ | ✓ | ✓ | ✓ |
| **Filter Dimensions** | 3 | 1 | 0 | 1 |
| **Row Selection** | ✓ | ✓ | ✓ | ✓ |
| **Sorting** | ✓ | ✓ | ✓ | ✓ |
| **Avatar Column** | ✓ | ✓ | ✗ | ✓ |
| **Status Display** | Chip | Chip | ✗ | Tooltip + Chip |

**Documentation:**
- [**catalogs/tables-catalog.md**](catalogs/tables-catalog.md) — Complete table comparison, patterns, and guidelines

### Summary

The Apps CRUD domains provide:
- **4 Complete CRUD Interfaces** with standardized TanStack React Table patterns
- **Advanced Filtering & Search** with fuzzy matching and debouncing
- **Type-Safe Components** with TypeScript across all domains
- **Responsive Design** optimized for mobile, tablet, and desktop
- **Reusable Patterns** for filters, forms, tables, and avatars
- **Production-Ready Code** with accessibility and performance optimizations

**Start Here:**
1. Review [catalogs/tables-catalog.md](catalogs/tables-catalog.md) for table patterns
2. Study user domain for complete CRUD example
3. Use table patterns as template for new domains
4. Extend with additional filters/columns as needed

---

## Dashboard Domain: Analytics, CRM, Ecommerce, Logistics & More

### Overview

The dashboard domain provides **six specialized dashboard implementations** designed for different business functions. Each dashboard demonstrates advanced React patterns including Server Components, concurrent rendering, responsive widget composition, and real-time data visualization using Chart.js and MUI components.

**Dashboard Types:**
1. **Analytics Dashboard** - Business metrics, sales trends, charts
2. **CRM Dashboard** - Customer data, pipeline, activities
3. **Ecommerce Dashboard** - Product sales, orders, revenue
4. **Logistics Dashboard** - Shipment tracking, delivery status
5. **Academy Dashboard** - Course performance, student engagement
6. **Overview Charts Catalog** - 30+ reusable chart components

### Key Features Across All Dashboards

- ✅ **Server + Client Components** - Data fetching at server edge, interactive widgets client-side
- ✅ **Responsive Widget Grid** - xs: 12, sm: 6, md: 3, lg: 2 breakpoints with MUI Grid
- ✅ **Chart Integration** - Chart.js with MUI Paper cards, gradient fills, tooltips
- ✅ **Statistics Cards** - CustomAvatar icons, color-coded by metric
- ✅ **Timeline Components** - MUI Timeline for process visualization
- ✅ **Concurrent Rendering** - Suspense boundaries, skeleton loaders for optimized UX
- ✅ **Type-Safe Widgets** - Full TypeScript interfaces for chart data

### Dashboard Implementations

#### 1. Analytics Dashboard
**Location:** `src/views/dashboards/analytics/`

Primary KPI widgets:
- **TotalEarnings** - Revenue card with trend indicator
- **OrderStatistics** - Multi-series bar chart (completed, pending, cancelled)
- **SalesOverview** - Revenue trend with gradient fill
- **AverageDaily** - Line chart for daily metrics
- **StatisticsCards** - 5 key metrics in responsive grid

**Documentation:**
- [**pages/analytics-dashboard.md**](pages/analytics-dashboard.md) — Full page layout and widget composition

#### 2. CRM Dashboard
**Location:** `src/views/dashboards/crm/`

Customer relationship widgets:
- **DealsChart** - Horizontal bar chart of sales pipeline
- **MeetingSchedule** - Timeline of upcoming meetings
- **CompanyTable** - List of prospects/accounts with MUI Table
- **ActivityTimeline** - Customer interaction history

**Documentation:**
- [**pages/crm-dashboard.md**](pages/crm-dashboard.md) — CRM page layout and components

#### 3. Ecommerce Dashboard
**Location:** `src/views/apps/ecommerce/dashboard/`

Product and sales metrics:
- **StatisticsCard** - Sales, customers, products, revenue (4-column grid)
- **RevenueReport** - Revenue trend visualization
- **PopularProducts** - Best-selling products list
- **Orders** - Tabbed order status with MUI Timeline
- **LineChartProfit** - Profit trend
- **Transactions** - Recent transaction list
- **RadialBarChart** - Sales distribution
- **DonutChartGeneratedLeads** - Lead generation

**Documentation:**
- [**pages/ecommerce-dashboard.md**](pages/ecommerce-dashboard.md) — Ecommerce dashboard layout

#### 4. Logistics Dashboard
**Location:** `src/views/dashboards/logistics/`

Shipping and delivery tracking:
- **ShipmentStatus** - Donut chart of shipment states
- **DeliveryPerformance** - Line chart of on-time delivery %
- **ActiveOrders** - Map visualization of delivery locations
- **TrackingTimeline** - MUI Timeline for shipment stages

**Documentation:**
- [**pages/logistics-dashboard.md**](pages/logistics-dashboard.md) — Logistics dashboard layout

#### 5. Academy Dashboard
**Location:** `src/views/dashboards/academy/`

Educational metrics:
- **StudentEnrollment** - Bar chart of course signups
- **CoursePerformance** - Scatter plot of completion rates
- **InstructorRanking** - Leaderboard table
- **LearningActivity** - Line chart of platform usage

**Documentation:**
- [**pages/academy-dashboard.md**](pages/academy-dashboard.md) — Academy dashboard layout

### Charts & Widgets Catalog

**Location:** `_knowledge/catalogs/` (charts and cards reference files coming in Phase 2 expansion)

**30+ Chart Types** (documented in source analysis and page snapshots):
- Line, Bar, Pie, Donut, Radar, Polar, Bubble, Scatter
- With gradient fills, tooltips, legends, responsive sizing
- All built with Chart.js + MUI components
- Reusable component patterns

**Card & Widget Types:**
- StatisticsCard - Icon + metric + trend
- ChartCard - Chart.js chart in MUI Paper
- TimelineCard - MUI Timeline with custom steps
- DataCard - Key-value displays

**Documentation:**
- [**source-analysis/dashboard-domain.md**](source-analysis/dashboard-domain.md) — Complete dashboard and chart patterns
- [**pages/analytics-dashboard.md**](pages/analytics-dashboard.md), [pages/ecommerce-dashboard.md](pages/ecommerce-dashboard.md) — Chart usage examples

### Dashboard Architecture Patterns

**1. Page Structure (Server Component)**
```typescript
// /dashboards/analytics/page.tsx
export default async function AnalyticsDashboard() {
  const data = await fetchDashboardData() // Server-side data fetch
  return (
    <Grid container spacing={6}>
      <Suspense fallback={<SkeletonLoader />}>
        <TotalEarnings data={data.earnings} /> {/* Client component */}
      </Suspense>
      {/* More widgets */}
    </Grid>
  )
}
```

**2. Widget Composition (Client Component)**
```typescript
function TotalEarnings({ data }: { data: EarningsType }) {
  return (
    <Card>
      <CardContent>
        <CustomAvatar icon='tabler-trending-up' color='success' />
        <Typography>{data.total}</Typography>
        <Chip label={`+${data.trend}%`} color='success' />
      </CardContent>
    </Card>
  )
}
```

**3. Responsive Grid Breakpoints**
```typescript
<Grid size={{ xs: 12, sm: 6, md: 3, lg: 2 }}>
  {/* Widget adapts from full-width mobile to 2-per-row desktop */}
</Grid>
```

### Dashboard Data Flow

```
Dashboard Page (Server)
  ├─ fetchDashboardData() → API/DB
  ├─ Pass data to Client Widgets
  │   ├─ StatisticsCard
  │   ├─ Chart Component
  │   ├─ Timeline Component
  │   └─ Table Component
  └─ Suspense boundaries for loading states
```

### Related Documentation

- [**source-analysis/dashboard-domain.md**](source-analysis/dashboard-domain.md) — Complete dashboard architecture
- [**pages/dashboards/**](pages/dashboards/) — Individual dashboard snapshots
- [**catalogs/charts-catalog.md**](catalogs/charts-catalog.md) — Chart patterns and types
- [**catalogs/cards-widgets-catalog.md**](catalogs/cards-widgets-catalog.md) — Widget component library

---

## Ecommerce Domain: Products, Orders, Customers & Reviews

### Overview

The ecommerce domain provides a **complete product, order, and customer management system** using TanStack React Table, advanced form patterns, and drawer-based workflows. This is an enterprise-grade implementation perfect for building product catalogs, marketplace platforms, and digital storefronts.

**Key Domains:**
1. **Products** - Catalog management, categories, inventory
2. **Orders** - Order listing, details, shipping tracking
3. **Customers** - Customer profiles, purchase history, communications
4. **Reviews & Referrals** - Product reviews, referral programs

### Ecommerce List Pages

**Products List Page** - `src/views/apps/ecommerce/products/list/`
- **ProductListTable** - TanStack React Table (8 columns, search, category/status filters)
- **AddProductDrawer** - Multi-section form for adding products
- **TableFilters** - Category and status filter UI
- Columns: Image, Name, Category, SKU, Stock, Price, Quantity, Status, Actions

**Orders List Page** - `src/views/apps/ecommerce/orders/list/`
- **OrderListTable** - Order management (Order ID, Customer, Email, Payment, Status, Amount, Date)
- **OrderDetailsLink** - Navigation to detailed order view
- Columns: Order ID, Customer (Avatar), Email, Payment Method, Status, Amount, Date, Actions

**Customers List Page** - `src/views/apps/ecommerce/customers/list/`
- **CustomerListTable** - Customer records (Avatar, Name, Email, Country, Orders, Spent, Status)
- **AddCustomerDrawer** - Modal form for new customers
- Columns: Customer, Email, Country, Total Orders, Total Spent, Payment Status, Actions

**Reviews Management Page** - `src/views/apps/ecommerce/manage-reviews/`
- **ManageReviewsTable** - Product reviews (Product, Reviewer, Rating, Review Text, Status)
- **ReviewsStatistics** - Overview metrics
- Columns: Product (Image), Reviewer (Avatar), Star Rating, Review, Status, Date, Actions

**Documentation:**
- [**pages/apps/ecommerce-list-pages.md**](pages/apps/ecommerce-list-pages.md) — List pages layout and tables
- [**catalogs/tables.md**](catalogs/tables.md) — ProductListTable, OrderListTable, CustomerListTable rows

### Ecommerce Product Add/Edit Pages

**Location:** `src/views/apps/ecommerce/products/add/`

**ProductAddForm** - Multi-section form composition:

1. **ProductAddHeader** - Page title and navigation
2. **ProductInformation** - Name, description, vendor
3. **ProductPricing** - Base price, discounted price, tax, stock toggle
4. **ProductImage** - Image upload and gallery
5. **ProductInventory** - SKU, quantities, tracking
6. **ProductVariants** - Size/color variants composition
7. **ProductOrganize** - Category selection, tags

**Form Pattern:**
```typescript
<Form layout='horizontal'>
  <ProductAddHeader />
  <Grid container spacing={6}>
    <Grid size={{ xs: 12, md: 8 }}>
      <ProductInformation />
      <ProductImage />
      <ProductVariants />
    </Grid>
    <Grid size={{ xs: 12, md: 4 }}>
      <ProductPricing />
      <ProductInventory />
      <ProductOrganize />
    </Grid>
  </Grid>
</Form>
```

**Documentation:**
- [**pages/apps/ecommerce-product-add-page.md**](pages/apps/ecommerce-product-add-page.md) — Product add page layout
- [**components/product-add-form-component.md**](components/product-add-form-component.md) — Form component patterns

### Ecommerce Type Definitions

**Core Types:**
```typescript
// Product
export type ProductType = {
  id: number
  productName: string
  category: string
  stock: boolean
  sku: number
  price: string
  qty: number
  status: string
  image: string
  productBrand: string
}

// Order
export type OrderType = {
  id: number
  order: string
  customer: string
  email: string
  avatar: string
  payment: number
  status: string
  spent: number
  method: string
  date: string
}

// Customer
export type Customer = {
  id: number
  customer: string
  customerId: string
  email: string
  country: string
  order: number
  totalSpent: number
  avatar: string
  status?: string
}

// Review
export type ReviewType = {
  id: number
  product: string
  productImage: string
  reviewer: string
  avatar: string
  rating: number
  review: string
  status: string
  date: string
}
```

### Ecommerce Data Flow

```
Ecommerce Pages
  ├─ ProductListPage
  │   ├─ ProductListTable (search, filters)
  │   ├─ TableFilters (category, status)
  │   └─ AddProductDrawer (form)
  │
  ├─ OrderListPage
  │   └─ OrderListTable (status filter)
  │       └─ Link to OrderDetailsPage
  │
  ├─ CustomerListPage
  │   └─ CustomerListTable (payment status)
  │       └─ Link to CustomerDetailsPage
  │
  └─ ReviewsManagementPage
      └─ ManageReviewsTable (status filter)
```

### Related Documentation

- [**source-analysis/ecommerce-domain.md**](source-analysis/ecommerce-domain.md) — Complete ecommerce architecture
- [**pages/apps/ecommerce-list-pages.md**](pages/apps/ecommerce-list-pages.md) — List pages snapshot
- [**pages/apps/ecommerce-product-add-page.md**](pages/apps/ecommerce-product-add-page.md) — Product add page snapshot
- [**components/product-add-form-component.md**](components/product-add-form-component.md) — Form component snapshot
- [**catalogs/tables.md**](catalogs/tables.md) — Table patterns

---

## Forms & Wizards Domain: Form Layouts, Steppers & Validation

### Overview

The forms domain provides **production-ready form patterns** including multi-section form layouts, step-by-step wizards, and comprehensive validation strategies. All forms use **React Hook Form** for state management and **Valibot** for schema-based validation.

**Form Types:**
1. **Multi-Section Forms** - Product add, customer add (ProductAddForm, AddProductDrawer)
2. **Step Wizards** - Multi-step registration, form steppers
3. **Form Layouts** - Different layout styles (vertical, horizontal, compact)
4. **Validation Patterns** - Field-level and form-level validation

### Form Layout Patterns

**Vertical Layout** (Standard)
```typescript
<Form layout='vertical'>
  <Row gutter={[16, 16]}>
    <Col xs={24} md={12}>
      <CustomTextField label='First Name' fullWidth />
    </Col>
    <Col xs={24} md={12}>
      <CustomTextField label='Last Name' fullWidth />
    </Col>
  </Row>
</Form>
```

**Horizontal Layout** (Label-Input pairs)
```typescript
<Form layout='horizontal'>
  <Row gutter={[16, 16]}>
    <Col xs={24} md={8}>
      <label>Email</label>
    </Col>
    <Col xs={24} md={16}>
      <CustomTextField />
    </Col>
  </Row>
</Form>
```

**2-Column Grid** (Responsive)
```typescript
<Grid container spacing={6}>
  <Grid size={{ xs: 12, md: 6 }}>
    <CustomTextField label='First Name' fullWidth />
  </Grid>
  <Grid size={{ xs: 12, md: 6 }}>
    <CustomTextField label='Last Name' fullWidth />
  </Grid>
</Grid>
```

### Form Validation Patterns

**Valibot Schema Validation**
```typescript
import { object, string, email as emailValidation, minLength } from 'valibot'

const formSchema = object({
  email: string('Email must be string', [emailValidation('Email must be valid')]),
  password: string('Password required', [minLength(8, 'Min 8 characters')])
})

// React Hook Form integration
const { register, formState: { errors } } = useForm({
  resolver: valibotResolver(formSchema)
})
```

### Step Wizard Patterns

**Stepper Component Structure**
```typescript
function FormWizard() {
  const [activeStep, setActiveStep] = useState(0)
  const steps = ['Basic Info', 'Address', 'Confirm']

  return (
    <div>
      <Stepper activeStep={activeStep}>
        {steps.map((label) => (
          <Step key={label}>
            <StepLabel>{label}</StepLabel>
          </Step>
        ))}
      </Stepper>

      {activeStep === 0 && <BasicInfoStep />}
      {activeStep === 1 && <AddressStep />}
      {activeStep === 2 && <ConfirmStep />}

      <Box sx={{ display: 'flex', justifyContent: 'space-between' }}>
        <Button disabled={activeStep === 0} onClick={() => setActiveStep(activeStep - 1)}>
          Back
        </Button>
        <Button onClick={() => setActiveStep(activeStep + 1)}>
          Next
        </Button>
      </Box>
    </div>
  )
}
```

### Form Component Catalog

**Available Form Components:**
- CustomTextField - Text input with MUI styling
- CustomSelect - Dropdown select
- CustomCheckbox - Checkbox with label
- CustomRadio - Radio button groups
- CustomAutocomplete - Autocomplete with filtering
- CustomDatePicker - Date selection
- CustomFileUpload - File upload with preview
- CustomSwitch - Toggle switch

**Documentation:**
- [**catalogs/forms-catalog.md**](catalogs/forms-catalog.md) — Form component reference

### Forms & Wizards Implementation Pages

**Form Layouts Page** - `src/views/forms/form-layouts/`
- Demonstrates all layout patterns (vertical, horizontal, compact)
- Responsive grid usage
- Section dividers and typography

**Form Wizard Page** - `src/views/forms/form-wizard/`
- Multi-step wizard with Stepper
- Step validation
- Progress tracking
- Back/Next navigation

**Documentation:**
- [**pages/forms/form-layouts-page.md**](pages/forms/form-layouts-page.md) — Form layout patterns
- [**pages/forms/form-wizard-page.md**](pages/forms/form-wizard-page.md) — Wizard implementation

### Forms Validation Strategy

**Approach:**
1. **Schema Definition** - Valibot schema with validation rules
2. **Form Integration** - React Hook Form with valibotResolver
3. **Field-Level Validation** - Real-time feedback on blur/change
4. **Form-Level Validation** - Before submission

**Error Display:**
```typescript
<CustomTextField
  label='Email'
  {...register('email')}
  error={!!errors.email}
  helperText={errors.email?.message}
/>
```

### Related Documentation

- [**source-analysis/forms-domain.md**](source-analysis/forms-domain.md) — Form architecture and patterns
- [**pages/forms/form-layouts-page.md**](pages/forms/form-layouts-page.md) — Form layouts snapshot
- [**pages/forms/form-wizard-page.md**](pages/forms/form-wizard-page.md) — Wizard implementation snapshot
- [**catalogs/forms-catalog.md**](catalogs/forms-catalog.md) — Form component library
- [**components/product-add-form-component.md**](components/product-add-form-component.md) — Multi-section form example

---

## Integration & Dependencies Overview

### Quick Reference: Where Everything Lives

| Feature | Source Analysis | Pages | Catalogs | Components |
|---------|--|--|--|--|
| **Dashboards** | [dashboard-domain.md](source-analysis/dashboard-domain.md) | [analytics-dashboard.md](pages/analytics-dashboard.md), [crm-dashboard.md](pages/crm-dashboard.md), [ecommerce-dashboard.md](pages/ecommerce-dashboard.md), [logistics-dashboard.md](pages/logistics-dashboard.md), [academy-dashboard.md](pages/academy-dashboard.md) | (dashboard charts referenced in page snapshots) | Dashboard widgets |
| **Ecommerce** | [ecommerce-domain.md](source-analysis/ecommerce-domain.md) | [ecommerce-list-pages.md](pages/apps/ecommerce-list-pages.md), [ecommerce-product-add-page.md](pages/apps/ecommerce-product-add-page.md) | [tables.md](catalogs/tables.md) | [product-add-form-component.md](components/product-add-form-component.md) |
| **Forms** | [forms-wizards-domain.md](source-analysis/forms-wizards-domain.md) | [form-layouts-page.md](pages/forms/form-layouts-page.md), [form-wizard-page.md](pages/forms/form-wizard-page.md) | [forms-catalog.md](catalogs/forms-catalog.md) | Form inputs |
| **Users & CRUD** | [user-domain.md](source-analysis/user-domain.md) | [users-list.md](pages/users-list.md), [user-detail.md](pages/user-detail.md) | [tables-catalog.md](catalogs/tables-catalog.md) | [add-user-drawer.md](components/add-user-drawer.md) |
| **Chat App** | [chat-app-domain.md](source-analysis/chat-app-domain.md) | (part of dashboard apps) | — | Chat components |
| **Front Pages** | [front-pages-domain.md](source-analysis/front-pages-domain.md) | Landing, Pricing, Help Center | — | Front-page sections |
| **Utilities** | [misc-utilities-domain.md](source-analysis/misc-utilities-domain.md) | (used across all pages) | — | Shared utilities |

### Dependency Relationships

**Dashboard → Other Domains**
- Imports chart components from Chart.js library (see source-analysis/dashboard-domain.md for patterns)
- Uses theme colors from 03-theming.md
- Uses layout system from 04-layout-system.md

**Ecommerce → Other Domains**
- ProductListTable, OrderListTable, CustomerListTable from catalogs/tables.md (row 12-14 in tables.md)
- Form components from forms-catalog.md
- Type definitions from source-analysis/ecommerce-domain.md

**Forms → Other Domains**
- Components from catalogs/forms-catalog.md
- Validation with Valibot (see spec in source-analysis/forms-domain.md)
- Layout patterns from pages/forms/form-layouts-page.md

**User/CRUD → Other Domains**
- UserListTable, RolesTable from catalogs/tables-catalog.md
- InvoiceListTable from catalogs/tables-catalog.md
- Add/Edit drawers from components/ documentation

### Shared Patterns Across All Domains

1. **TanStack React Table** - All list pages use consistent patterns
   - Fuzzy search (500ms debounce)
   - Column sorting and filtering
   - Row selection with select-all
   - Pagination (10, 25, 50 rows)

2. **Form Composition** - Multi-section forms with consistent layout
   - Grid-based responsive design
   - Drawer modals for add/edit
   - Validation with Valibot + React Hook Form

3. **Type Safety** - Comprehensive TypeScript types
   - Entity types (Product, Order, Customer, etc.)
   - Status enums
   - UI component prop types

4. **Responsive Design** - Consistent breakpoints
   - xs: 12 (full width)
   - sm: 6 (half width)
   - md: 4 (one-third)
   - lg: 3 (quarter width)

5. **Theme Integration** - All components use MUI theme
   - Color schemes (primary, success, warning, error)
   - Dark/light mode support
   - Customizable theme from 03-theming.md

### Quick Navigation Map

```
User wants to...                     Go to...
├─ Build a dashboard               → Dashboard Domain section above
├─ Build ecommerce pages           → Ecommerce Domain section above
├─ Build form pages                → Forms & Wizards Domain section above
├─ Build data tables               → catalogs/tables-catalog.md
├─ Understand architecture          → 01-architecture.md
├─ Add theme colors                → 03-theming.md
├─ Add menu items                  → 05-navigation.md
├─ Understand all pages            → 06-page-map.md
└─ Find a specific component       → 08-components.md or source-analysis/
```

### Summary

The Vuexy Knowledge Base provides:
- **6 Dashboard implementations** with 30+ chart types
- **4 Ecommerce domains** (products, orders, customers, reviews)
- **2 Forms implementations** (layouts, wizards)
- **3 CRUD domains** (users, roles, invoices)
- **Consistent TanStack React Table patterns** across all domains
- **Type-safe React + TypeScript** with comprehensive interfaces
- **Responsive design** with MUI Grid and theme system
- **Production-ready code** with validation, error handling, and accessibility

**Get Started:**
1. Copy `_knowledge/` folder to your project
2. Read [01-architecture.md](01-architecture.md) for project structure
3. Pick a domain that matches your use case (dashboard, ecommerce, forms, CRUD)
4. Reference the domain's KB files and source analysis
5. Copy patterns and adapt for your needs

