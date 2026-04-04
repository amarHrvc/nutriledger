# Architecture

## Stack

| Layer        | Library/Tool            | Version |
|--------------|-------------------------|---------|
| Framework    | Next.js (App Router)    | 16      |
| UI Library   | React                   | 19      |
| Component    | MUI (Material UI)       | v7      |
| Styling      | Emotion + Tailwind CSS  | v4      |
| Language     | TypeScript              | 5.9     |
| Package mgr  | pnpm                    |         |
| Auth         | next-auth               | v4      |
| DB ORM       | Prisma                  | v6      |
| State        | Redux Toolkit           | v2      |
| Icons        | Iconify (tabler set)    |         |

## Folder Structure (src/)

```
src/
├── @core/           # Template core — DO NOT modify casually
│   ├── components/  # customizer, custom-inputs, mui wrappers, option-menu, scroll-to-top
│   ├── contexts/    # Settings context
│   ├── hooks/       # useSettings, useImageVariant, useLayoutInit, useObjectCookie
│   ├── styles/      # Layout styles (horizontal/vertical)
│   ├── theme/       # MUI theme + overrides (one file per MUI component)
│   └── utils/       # serverHelpers (getMode, getSystemMode)
├── @layouts/        # Layout components (VerticalLayout, HorizontalLayout, LayoutWrapper)
├── @menu/           # Menu system (vertical + horizontal menu components)
├── app/             # Next.js App Router
│   ├── [lang]/      # All i18n routes
│   │   ├── (dashboard)/(private)/   # Authenticated pages with full layout
│   │   ├── (blank-layout-pages)/    # Auth + error pages (no sidebar)
│   │   │   └── (guest-only)/        # Redirect to dashboard if already logged in
│   │   └── [...not-found]/          # 404 catch-all
│   ├── front-pages/ # Public marketing pages (no auth, different layout)
│   ├── api/         # Next.js API routes
│   └── globals.css
├── components/      # App-level components (Providers, layout parts, AuthRedirect)
├── configs/         # themeConfig.ts, i18n.ts, primaryColorConfig.ts
├── contexts/        # App-level contexts
├── data/
│   ├── dictionaries/  # Translation JSON files (en.json, fr.json, ar.json)
│   └── navigation/    # verticalMenuData.tsx, horizontalMenuData.tsx
├── fake-db/         # Mock data (replaces real DB in dev)
├── hocs/            # AuthGuard.tsx, TranslationWrapper.tsx
├── hooks/           # App hooks (useIntersection, useLayoutInit)
├── libs/            # auth.ts (NextAuth options), styles/
├── prisma/          # schema.prisma, migrations/
├── redux-store/     # store setup + slices/
├── types/           # TypeScript types (apps/, pages/, menuTypes.ts)
├── utils/           # getDictionary.ts
└── views/           # Page view components (heavy UI, imported by app/ pages)
    ├── apps/        # App views (calendar, chat, email, ecommerce, etc.)
    ├── charts/      # Chart views (apex, recharts)
    ├── dashboards/  # Dashboard views (analytics, crm)
    ├── forms/       # Form views
    ├── front-pages/ # Public page views
    ├── pages/       # Misc page views (auth, account-settings, etc.)
    └── react-table/ # Data table views
```

## Routing Pattern

Next.js App Router with `[lang]` dynamic segment at the root.

```
URL: /en/dashboards/crm
File: src/app/[lang]/(dashboard)/(private)/dashboards/crm/page.tsx
```

**Route Groups (parentheses = no URL segment):**
- `(dashboard)` — applies dashboard layout (LayoutWrapper with sidebar)
- `(private)` — applies AuthGuard (server-side session check)
- `(blank-layout-pages)` — no sidebar, blank layout
- `(guest-only)` — redirects to dashboard if session exists

**How `page.tsx` works in this template:**
Each page.tsx is thin — it just imports a view component from `src/views/`:
```tsx
// src/app/[lang]/(dashboard)/(private)/dashboards/crm/page.tsx
import CRMDashboard from '@/views/dashboards/crm'
export default function Page() { return <CRMDashboard /> }
```

**To add a new page:**
1. Create `src/app/[lang]/(dashboard)/(private)/your-section/your-page/page.tsx`
2. Create `src/views/your-section/your-page/index.tsx` with the actual UI
3. Add menu entry in `src/data/navigation/verticalMenuData.tsx`

## Path Aliases (tsconfig.json)

| Alias        | Resolves to         |
|--------------|---------------------|
| `@/`         | `src/`              |
| `@core`      | `src/@core`         |
| `@layouts`   | `src/@layouts`      |
| `@menu`      | `src/@menu`         |
| `@configs`   | `src/configs`       |
| `@components`| `src/components`    |
| `@assets`    | `src/assets`        |

## next.config.ts

- Default redirect: `/` → `/en/dashboards/crm`
- Lang redirect: `/:lang` → `/:lang/dashboards/crm`
- Non-lang paths auto-prefixed with `/en/`
- `BASEPATH` env var supported for sub-path hosting
