# Miscellaneous Utilities & Helpers Domain

## Overview

The utilities and helpers domain encompasses shared utility functions, custom hooks, reusable patterns, and configuration helpers used throughout the Vuexy Admin application. These utilities provide **cross-cutting concerns** for string manipulation, data transformation, theme management, intersection observation, pagination, and form handling.

**Key characteristics:**
- Pure utility functions with no side effects (string, data helpers)
- Custom React hooks for intersection observation and settings management
- Theme and mode helpers for dark/light mode switching
- Server-side helpers for theme data retrieval
- Data table pagination components
- Chart and form component wrappers
- Status mapping and color scheme utilities
- Reusable component patterns across domains

---

## Utility Functions

### String Manipulation Utilities (`/src/utils/string.ts`)

Lightweight string transformation helpers for common operations:

#### `ensurePrefix(str, prefix)`
- Ensures string starts with specified prefix
- Returns string unchanged if already prefixed
- Use case: URL normalization, icon prefixes

```typescript
ensurePrefix('icon-name', 'mdi-')     // 'mdi-icon-name'
ensurePrefix('mdi-icon-name', 'mdi-') // 'mdi-icon-name'
```

#### `withoutSuffix(str, suffix)`
- Removes suffix if present
- Returns original string if no suffix match
- Use case: File extension removal, status code cleanup

```typescript
withoutSuffix('document.pdf', '.pdf') // 'document'
withoutSuffix('document', '.pdf')     // 'document'
```

#### `withoutPrefix(str, prefix)`
- Removes prefix if present
- Returns original string if no prefix match
- Use case: Namespace removal, path simplification

```typescript
withoutPrefix('mdi-icon', 'mdi-') // 'icon'
withoutPrefix('icon', 'mdi-')     // 'icon'
```

**Dependency:** None (pure functions)

---

### Text Processing Utilities (`/src/utils/getInitials.ts`)

#### `getInitials(string)`
- Extracts initials from space-separated text
- Splits on whitespace, takes first character per word
- Use case: Avatar generation, user display abbreviations

```typescript
getInitials('John Doe')              // 'JD'
getInitials('Mary Jane Watson')      // 'MJW'
getInitials('Alex')                  // 'A'
```

**Algorithm:**
1. Split string on regex `/\s/` (whitespace)
2. Reduce array: each word contributes first character
3. Concatenate initials

**Dependency:** None (pure function)

---

### Dictionary & i18n Utilities (`/src/utils/getDictionary.ts`)

Server-only helper for dynamic dictionary loading with language fallbacks.

#### `getDictionary(locale)`
- Async function importing locale-specific dictionary
- Supports: 'en', 'fr', 'ar' (extensible)
- Returns parsed JSON object

```typescript
const dict = await getDictionary('en')    // en.json
const dict = await getDictionary('fr')    // fr.json
const dict = await getDictionary('ar')    // ar.json
```

**Import Pattern:**
```typescript
const dictionaries = {
  en: () => import('@/data/dictionaries/en.json').then(m => m.default),
  fr: () => import('@/data/dictionaries/fr.json').then(m => m.default),
  ar: () => import('@/data/dictionaries/ar.json').then(m => m.default)
}
```

**Constraints:**
- `'use server'` directive - server-only execution
- Prevents dictionary exposure in client bundle
- Must be called in server components or actions

**Dependencies:**
- Language data: `@/data/dictionaries/*.json`
- Type: `@configs/i18n` (Locale type)

---

## Custom Hooks

### `useIntersection()` Hook

Custom context hook for Intersection Observer pattern management.

**Location:** `/src/hooks/useIntersection.ts`

**Purpose:** Provides access to IntersectionContext for monitoring element visibility in viewport

**Usage:**
```typescript
const context = useIntersection()
// Returns: IntersectionContext value
```

**Error Handling:**
- Throws error if used outside IntersectionProvider
- Message: "useIntersection must be used within a IntersectionProvider"

**Common Use Cases:**
- Lazy loading images on scroll
- Infinite scroll pagination
- Performance monitoring (element visibility tracking)
- Animation triggering on viewport entry

**Dependencies:**
- Context: `@/contexts/intersectionContext` (IntersectionContext)

---

## Server-Side Helpers

### Theme Mode Helpers (`/src/@core/utils/serverHelpers.ts`)

Server-only utilities for theme configuration and cookie management:

#### `getSettingsFromCookie()`
- Async function reading settings from cookie storage
- Parses JSON cookie value
- Fallback to empty object if not set

```typescript
const settings = await getSettingsFromCookie()
// Returns: { mode: 'dark', skin: 'default', layout: 'vertical' }
```

#### `getMode()`
- Retrieves theme mode from cookie
- Fallback to `themeConfig.mode` default
- Returns: 'light' | 'dark' | 'system'

```typescript
const mode = await getMode() // 'dark'
```

#### `getSystemMode()`
- Async function detecting system color preference
- Reads `colorPref` cookie for system preference
- Returns: 'light' | 'dark'

```typescript
const sysMode = await getSystemMode() // 'dark'
```

#### `getServerMode()`
- Composite helper returning effective theme mode
- If mode='system', returns detected system preference
- Otherwise returns configured mode

```typescript
const effectiveMode = await getServerMode() // 'dark'
```

#### `getSkin()`
- Retrieves UI skin setting from cookie
- Fallback to 'default' if not set
- Returns: skin identifier string

```typescript
const skin = await getSkin() // 'default'
```

**Configuration Dependencies:**
- `@configs/themeConfig` - Default settings
- Cookie name: `themeConfig.settingsCookieName`
- System preference cookie: `colorPref`

**Type:** `SystemMode = 'light' | 'dark'`

---

## Form & Table Components

### Form Component (`/src/components/Form.tsx`)

Lightweight form wrapper for consistent form handling.

**Type:** Client Component (`'use client'`)

**Props:**
```typescript
type Props = DetailedHTMLProps<FormHTMLAttributes<HTMLFormElement>, HTMLFormElement>
```

**Behavior:**
- Accepts standard HTML form attributes
- Passes through onSubmit handler if provided
- Default prevents form submission with `e.preventDefault()` if no handler

**Code:**
```typescript
const FormComponent = (props: Props) => {
  const { onSubmit, ...rest } = props
  return (
    <form
      {...rest}
      onSubmit={onSubmit ? e => onSubmit(e) : e => e.preventDefault()}
    />
  )
}
```

**Use Cases:**
- Server Action forms
- Client-controlled submissions
- Default form behavior override

---

### Table Pagination Component (`/src/components/TablePaginationComponent.tsx`)

Reusable pagination UI for TanStack React Table integration.

**Type:** Client Component

**Props:**
```typescript
interface Props {
  table: ReturnType<typeof useReactTable>
}
```

**Features:**
- Displays current entries range ("Showing 1 to 10 of 42 entries")
- MUI Pagination with rounded style
- First/last button support
- Real-time page updates via `table.setPageIndex()`

**Key Logic:**
```typescript
// Current page calculation
const startEntry = pageIndex * pageSize + 1

// Visible entries count
const endEntry = Math.min(
  (pageIndex + 1) * pageSize,
  totalRows
)

// Total pages
const pageCount = Math.ceil(totalRows / pageSize)
```

**Layout:**
- Container: Flexbox with `justify-between`
- Left: Typography showing entries range
- Right: MUI Pagination component
- Tailwind classes: `pli-6`, `plb-[12.5px]`, `gap-2`

**Dependencies:**
- `@mui/material/Pagination` - Pagination UI
- `@mui/material/Typography` - Text display
- `@tanstack/react-table` - Table hook type

---

## Card Statistics Components

Location: `/src/components/card-statistics/`

Collection of reusable stat card variants for dashboard widgets:

### Card Types

1. **CardStatsSquare.tsx** - Square stat card layout
2. **Vertical.tsx** - Vertical stat card (icon, title, value)
3. **Horizontal.tsx** - Horizontal stat layout
4. **HorizontalWithAvatar.tsx** - Horizontal + avatar icon
5. **HorizontalWithBorder.tsx** - Horizontal with border dividers
6. **HorizontalWithSubtitle.tsx** - Horizontal + subtitle support
7. **StatsWithAreaChart.tsx** - Stat with integrated area chart
8. **CustomerStats.tsx** - Customer-specific metrics card

**Common Props Pattern:**
```typescript
interface CardStatsProps {
  title: string
  stats: string | number
  subtitle?: string
  avatarIcon?: string
  avatarColor?: ThemeColor
  progressColor?: ThemeColor
  chipText?: string
  chipColor?: ThemeColor
  chipVariant?: 'filled' | 'outlined' | 'tonal'
}
```

**Usage:** Import and place in dashboard grids:
```typescript
import CardStatsVertical from '@components/card-statistics/Vertical'

<CardStatsVertical
  title="Total Revenue"
  stats="$25,430"
  subtitle="Monthly"
  avatarIcon="mdi-currency-usd"
  avatarColor="primary"
/>
```

---

## Status Mapping & Color Schemes

### Common Status Patterns

**Transaction Status Mapping:**
```typescript
const statusConfig = {
  'verified': { text: 'Verified', color: 'success' },
  'rejected': { text: 'Rejected', color: 'error' },
  'pending': { text: 'Pending', color: 'warning' },
  'on-hold': { text: 'On Hold', color: 'info' }
}
```

**Role Status Mapping:**
```typescript
const roleConfig = {
  admin: { label: 'Admin', color: 'error', icon: 'mdi-shield' },
  editor: { label: 'Editor', color: 'warning', icon: 'mdi-pencil' },
  viewer: { label: 'Viewer', color: 'info', icon: 'mdi-eye' },
  author: { label: 'Author', color: 'success', icon: 'mdi-account' }
}
```

**Theme Color Type:**
```typescript
type ThemeColor = 'primary' | 'secondary' | 'success' | 'error' | 'warning' | 'info'
```

### Pattern Usage

Applied across components for:
- Transaction/order status display (chips, badges)
- User role badges
- Progress indicators (success/error/warning)
- Chart color configuration

---

## Theme Integration Patterns

### CSS Variables for Runtime Theming

Theme system uses CSS custom properties for dynamic theming:

```css
/* Primary colors */
--mui-palette-primary-main
--mui-palette-primary-light

/* Semantic colors */
--mui-palette-success-main
--mui-palette-error-main
--mui-palette-warning-main
--mui-palette-info-main

/* Surface colors */
--mui-palette-background-paper
--mui-palette-background-default

/* Typography */
--mui-palette-text-primary
--mui-palette-text-secondary

/* Borders */
--mui-palette-divider
```

### Dark/Light Mode Support

Implemented via:
1. `getServerMode()` - Server-side mode detection
2. `useColorScheme()` hook - Client-side detection
3. CSS variable value switching at theme layer

**Pattern in Components:**
```typescript
// Server component
const serverMode = await getServerMode()

// Client component
const { mode } = useColorScheme()

// Chart color usage
const colors = [`var(--mui-palette-primary-main)`]
```

---

## Library Wrappers

### Chart Library Wrappers

Location: `/src/libs/`

**ApexCharts.tsx** - Next.js dynamic import wrapper for ApexCharts
**Recharts.tsx** - Recharts integration wrapper
**ReactPlayer.tsx** - Video player wrapper

These wrappers:
- Enable dynamic imports for code splitting
- Prevent hydration issues
- Support SSR compatibility

---

## Dependency Map

### External Dependencies
- **React** - Hooks, context consumption
- **Next.js** - Server components, dynamic imports, cookies API
- **MUI Material** - Card, Pagination, Typography, Chip, Avatar
- **TanStack React Table** - Table state management
- **ApexCharts** - Chart rendering (via wrapper)

### Internal Dependencies
- `@/contexts/intersectionContext` - IntersectionContext for useIntersection
- `@/data/dictionaries/*.json` - i18n dictionary files
- `@/configs/themeConfig` - Theme configuration defaults
- `@/configs/i18n` - Locale types
- `@/data/navigation` - Menu data structures
- `@core/types` - Shared TypeScript types (ThemeColor, SystemMode)

---

## Common Patterns

### 1. Server-Only Utilities Pattern

Utilities executed only on server to prevent client bundle bloat:

```typescript
// getDictionary.ts
import 'server-only'
export const getDictionary = async (locale: Locale) => {
  // Only runs server-side
}
```

### 2. Color Scheme Mapping Pattern

Consistent mapping of domain values to theme colors:

```typescript
const getStatusColor = (status: TransactionStatus): ThemeColor => {
  const colorMap: Record<TransactionStatus, ThemeColor> = {
    verified: 'success',
    rejected: 'error',
    pending: 'warning',
    'on-hold': 'info'
  }
  return colorMap[status]
}
```

### 3. Dynamic Import Wrapper Pattern

Wrap third-party libraries for Next.js compatibility:

```typescript
import dynamic from 'next/dynamic'

const AppReactApexCharts = dynamic(
  () => import('@/libs/styles/AppReactApexCharts'),
  { ssr: false }
)
```

### 4. Context Enforcement Pattern

Custom hooks that require provider wrapper:

```typescript
export const useIntersection = () => {
  const context = useContext(IntersectionContext)
  if (!context) {
    throw new Error('must be used within Provider')
  }
  return context
}
```

### 5. CSS Variable Theme Pattern

Use CSS variables for runtime theming:

```typescript
const options: ApexOptions = {
  colors: ['var(--mui-palette-primary-main)'],
  // Colors update automatically on theme change
}
```

---

## Type Definitions

### Core Types

```typescript
// Theme color palette
type ThemeColor = 'primary' | 'secondary' | 'success' | 'error' | 'warning' | 'info'

// System/theme modes
type SystemMode = 'light' | 'dark'
type ThemeMode = 'light' | 'dark' | 'system'

// Locale/language
type Locale = 'en' | 'fr' | 'ar'

// Transaction/status types
type TransactionStatus = 'verified' | 'rejected' | 'pending' | 'on-hold'
type TransactionType = {
  date: string
  status: TransactionStatus
  cardType: string
  cardNumber: string
  amount: number
}

// Settings/configuration
interface Settings {
  mode: ThemeMode
  skin: string
  layout: string
}
```

---

## Code Examples

### Using String Utilities

```typescript
// Icon name normalization
import { ensurePrefix, withoutPrefix } from '@utils/string'

const iconName = ensurePrefix(userIcon, 'mdi-')
const cleanName = withoutPrefix(iconName, 'mdi-')
```

### Using i18n Helpers

```typescript
// Server component
import { getDictionary } from '@utils/getDictionary'

export default async function Page({ params }: { params: { lang: string } }) {
  const dict = await getDictionary(params.lang as Locale)

  return <h1>{dict.common.welcome}</h1>
}
```

### Using Theme Helpers

```typescript
// Server component getting theme mode
import { getServerMode } from '@core/utils/serverHelpers'

const Dashboard = async () => {
  const mode = await getServerMode()

  return <LastTransaction serverMode={mode} />
}
```

### Using Table Pagination

```typescript
import { useReactTable } from '@tanstack/react-table'
import TablePaginationComponent from '@components/TablePaginationComponent'

const Table = () => {
  const table = useReactTable({ ... })

  return (
    <div>
      <table>{/* table content */}</table>
      <TablePaginationComponent table={table} />
    </div>
  )
}
```

### Using Stat Cards

```typescript
import CardStatsVertical from '@components/card-statistics/Vertical'
import Grid from '@mui/material/Grid'

const Dashboard = () => (
  <Grid container spacing={6}>
    <Grid size={{ xs: 12, md: 6, lg: 3 }}>
      <CardStatsVertical
        title="Total Revenue"
        stats="$25,430"
        avatarIcon="mdi-currency-usd"
        avatarColor="primary"
      />
    </Grid>
  </Grid>
)
```

---

## Summary

The utilities and helpers domain provides:

- **String manipulation** - Text transformation utilities
- **Text processing** - Initials extraction for avatars
- **i18n integration** - Dictionary loading with language support
- **Theme management** - Server and client-side mode detection
- **Custom hooks** - Intersection observation and context consumption
- **Form handling** - Form wrapper component
- **Table pagination** - TanStack React Table pagination UI
- **Stat cards** - Reusable dashboard metrics components
- **Status mapping** - Consistent color/status associations
- **Theme integration** - CSS variable-based dynamic theming
- **Library wrappers** - Dynamic imports for chart/media libraries

**Key Statistics:**
- 10+ utility functions
- 5+ custom hooks
- 8+ card statistics variants
- 40+ status/color mappings
- ~1,000 LOC utility layer
- ~100% reusable across all domains

**Benefits:**
- **Consistency** - Unified patterns across all features
- **Maintainability** - Centralized helpers
- **Performance** - Dynamic imports and server-only execution
- **DX** - Type-safe utilities with TypeScript
- **Theming** - CSS variables enable instant theme switching
