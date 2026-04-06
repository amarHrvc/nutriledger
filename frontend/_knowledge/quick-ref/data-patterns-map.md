# Data Patterns Navigation Map

## Overview

This map shows how different data sources and patterns connect across the application. It guides you to the right pattern for your use case.

---

## 1. Redux Slices → Domain Usage

Redux Toolkit slices manage client-side state for app features. See [07-state-data.md](../07-state-data.md) for full reference.

| Slice | Manages | Domain | Usage Pattern | Files |
|-------|---------|--------|---------------|-------|
| **calendar.ts** | Calendar events | Calendar app | Load from fake-db, dispatch actions | `src/redux-store/slices/calendar.ts` → `src/fake-db/apps/calendar.ts` |
| **chat.ts** | Messages, rooms, contacts | Chat app | Real-time messaging state | `src/redux-store/slices/chat.ts` → `src/fake-db/apps/chat.ts` |
| **email.ts** | Emails, folders, labels | Email app | Thread management, filtering | `src/redux-store/slices/email.ts` → `src/fake-db/apps/email.ts` |
| **kanban.ts** | Boards, columns, tasks | Kanban app | Drag-drop task management | `src/redux-store/slices/kanban.ts` → `src/fake-db/apps/kanban.ts` |

### Redux Usage Example

```typescript
// Component using Redux
import { useDispatch, useSelector } from 'react-redux'
import type { RootState, AppDispatch } from '@/redux-store'

const dispatch = useDispatch<AppDispatch>()
const events = useSelector((state: RootState) => state.calendar.events)
```

---

## 2. API Routes & Data Sources

### Pattern Overview: Fake-DB vs Prisma

| Source | Type | Use Case | Location |
|--------|------|----------|----------|
| **fake-db** | Static mock data | Development, demos, UI preview | `src/fake-db/apps/` & `src/fake-db/pages/` |
| **Prisma** | Real database (SQLite/PostgreSQL) | Auth sessions only (Users, Sessions, Accounts) | `src/prisma/schema.prisma` |
| **Server Actions** | Direct data fetching | Modern Next.js pattern (recommended) | `src/app/server/actions.ts` |
| **API Routes** | HTTP endpoints | Third-party integrations, external access | `src/app/api/` |

### Fake-DB Structure

```
src/fake-db/
├── apps/
│   ├── academy.ts          # Academy course data
│   ├── calendar.ts         # Calendar events
│   ├── chat.ts             # Chat messages
│   ├── ecommerce.ts        # Products, orders
│   ├── email.ts            # Email messages
│   ├── invoice.ts          # Invoice records
│   ├── kanban.ts           # Kanban tasks
│   ├── logistics.ts        # Logistics data
│   ├── permissions.ts      # Permission records
│   └── userList.ts         # User list
└── pages/
    ├── faq.ts              # FAQ content
    ├── pricing.ts          # Pricing page data
    ├── userProfile.ts      # User profile data
    └── widgetExamples.ts   # Widget data
```

### API Route Pattern

```typescript
// src/app/api/apps/ecommerce/route.ts
import { NextResponse } from 'next/server'
import { db } from '@/fake-db/apps/ecommerce'

export async function GET() {
  return NextResponse.json(db)
}
// To use real data: replace db with your Prisma query
// const data = await prisma.product.findMany()
```

### Available API Endpoints

| Endpoint | Data Source | Status | Replace Pattern |
|----------|-------------|--------|-----------------|
| `/api/apps/academy` | fake-db | Mock | `import { db } from '@/fake-db/apps/academy'` → Prisma query |
| `/api/apps/ecommerce` | fake-db | Mock | `import { db } from '@/fake-db/apps/ecommerce'` → Prisma query |
| `/api/apps/invoice` | fake-db | Mock | `import { db } from '@/fake-db/apps/invoice'` → Prisma query |
| `/api/apps/logistics` | fake-db | Mock | `import { db } from '@/fake-db/apps/logistics'` → Prisma query |
| `/api/apps/permissions` | fake-db | Mock | `import { db } from '@/fake-db/apps/permissions'` → Prisma query |
| `/api/apps/user-list` | fake-db | Mock | `import { db } from '@/fake-db/apps/userList'` → Prisma query |
| `/api/pages/faq` | fake-db | Mock | - |
| `/api/pages/pricing` | fake-db | Mock | - |
| `/api/pages/profile` | fake-db | Mock | - |
| `/api/pages/widget-examples` | fake-db | Mock | - |
| `/api/auth/[...nextauth]` | Prisma | Real | NextAuth session storage |
| `/api/login` | Credentials | Real | Demo credentials validation |

---

## 3. Server Components → Data Fetching

Recommended pattern for most pages. Server Components fetch data directly, reducing client bundle.

| Pattern | Data Source | Use When | Example File |
|---------|-------------|----------|--------------|
| **Server Action (Recommended)** | Any (fake-db, Prisma, API) | Fetching data for pages | `src/app/server/actions.ts` |
| **Direct Import** | fake-db (static) | Simple static data | Views import directly from fake-db |
| **Prisma Query** | Database | Real data needed | Server Component awaits Prisma |
| **External API** | Third-party | Integration needed | Any HTTP client |

### Example: Using Server Actions

```typescript
// src/app/server/actions.ts (Server Action)
'use server'

import { db } from '@/fake-db/apps/ecommerce'

export const getEcommerceData = async () => {
  return db  // Replace with: await prisma.product.findMany()
}

// Usage in Page/Component
import { getEcommerceData } from '@/app/server/actions'

export default async function EcommercePage() {
  const data = await getEcommerceData()
  return <EcommerceView data={data} />
}
```

### Server Action Catalog

| Action | Location | Data Source | Domains Using |
|--------|----------|-------------|----------------|
| `getEcommerceData()` | actions.ts | fake-db/apps/ecommerce | eCommerce app |
| `getAcademyData()` | actions.ts | fake-db/apps/academy | Academy app |
| `getLogisticsData()` | actions.ts | fake-db/apps/logistics | Logistics app |
| `getInvoiceData()` | actions.ts | fake-db/apps/invoice | Invoice app |
| `getUserData()` | actions.ts | fake-db/apps/userList | User list app |
| `getPermissionsData()` | actions.ts | fake-db/apps/permissions | Permissions app |
| `getProfileData()` | actions.ts | fake-db/pages/userProfile | Profile page |
| `getFaqData()` | actions.ts | fake-db/pages/faq | FAQ page |
| `getPricingData()` | actions.ts | fake-db/pages/pricing | Pricing page |

---

## 4. Forms → Data Submission

Forms handle user input and submission to APIs or database.

| Pattern | Handler Type | Use Case | Example | See Also |
|---------|--------------|----------|---------|----------|
| **Basic Form + State** | `useState` | Simple forms without validation | FormLayoutsBasic.tsx | views/forms/ |
| **React Hook Form** | Client component | Forms with validation, errors | Login.tsx, Register.tsx | 02-auth.md |
| **Valibot Validation** | Pipe validators | Schema-based validation | Auth forms | pages/auth/login.md |
| **Form Action** | Server Action | Progressive form submission | Contact form | See actions.ts |
| **API Route Handler** | POST route | Manual form submission | Custom endpoints | /api/ routes |

### Form Submission Flow

```
User Input (form)
    ↓
[Client-side Validation] (optional: Valibot, React Hook Form)
    ↓
[Form Action or API Route]
    ↓
[Server: Validate + Process]
    ↓
[Database/Prisma Update] (or fake-db for demo)
    ↓
[Response + Redirect/Toast]
```

### Forms in This Template

| Form | Location | Validation | Submission | Pattern |
|------|----------|-----------|-----------|---------|
| **Login** | views/Login.tsx | Valibot | signIn('credentials') | OAuth or credentials |
| **Register** | views/Register.tsx | Valibot | API POST | Create user in DB |
| **Forgot Password** | views/ForgotPassword.tsx | Valibot | API POST | Send reset email |
| **Basic Forms** | views/forms/form-layouts/ | None (demo) | onSubmit (client) | State-based handling |
| **Form Validation** | views/forms/form-validation/ | Yes (demo) | onSubmit (client) | Validation examples |

---

## Data Flow Diagrams

### Redux Pattern
```
Fake-DB (static data)
    ↓
Server Action / useEffect fetch
    ↓
Redux Slice (dispatch action)
    ↓
Redux Store
    ↓
useSelector (component reads)
    ↓
UI Renders
```

### Server Component Pattern (Recommended)
```
Fake-DB / Prisma / API
    ↓
Server Action (src/app/server/actions.ts)
    ↓
Server Component (page or layout)
    ↓
Pass data as props to Client Components
    ↓
UI Renders
```

### Form Submission Pattern
```
Form Input (client)
    ↓
Validation (Valibot / React Hook Form)
    ↓
Form Action or API POST
    ↓
Server: Validate + Prisma Insert/Update
    ↓
Response (success/error)
    ↓
Client: Update UI / Redirect
```

---

## Migration Guide: Fake-DB → Real Data

### Step 1: Replace Import
```typescript
// Before:
import { db } from '@/fake-db/apps/ecommerce'

// After:
import prisma from '@/libs/prisma'
```

### Step 2: Replace Query
```typescript
// Before (Server Action):
export const getEcommerceData = async () => {
  return db
}

// After:
export const getEcommerceData = async () => {
  return await prisma.product.findMany()
}
```

### Step 3: Update API Route (if used)
```typescript
// Before:
import { db } from '@/fake-db/apps/ecommerce'
export async function GET() {
  return NextResponse.json(db)
}

// After:
import prisma from '@/libs/prisma'
export async function GET() {
  const data = await prisma.product.findMany()
  return NextResponse.json(data)
}
```

---

## Quick Links

- **Full State & Data Guide**: [07-state-data.md](../07-state-data.md)
- **Auth System**: [02-auth.md](../02-auth.md)
- **Architecture Overview**: [01-architecture.md](../01-architecture.md)
- **Form Implementation**: [pages/auth/login.md](../pages/auth/login.md)
- **Redux Setup**: [Redux in Architecture](../01-architecture.md#redux-toolkit)
- **Prisma Schema**: `src/prisma/schema.prisma`
- **Server Actions**: `src/app/server/actions.ts`

---

## Summary Table: Choose Your Pattern

| Need | Use Pattern | File | Learn More |
|------|-------------|------|-----------|
| **Client state for UI** | Redux slice | `src/redux-store/slices/` | 07-state-data.md |
| **Page data (fetch)** | Server Action | `src/app/server/actions.ts` | This file |
| **Authentication** | Prisma + NextAuth | `src/prisma/schema.prisma` | 02-auth.md |
| **User form input** | React Hook Form + Valibot | `views/forms/` | pages/auth/login.md |
| **Third-party API access** | API Route | `src/app/api/` | 07-state-data.md |
| **Mock data (demo)** | Import from fake-db | `src/fake-db/` | 07-state-data.md |
