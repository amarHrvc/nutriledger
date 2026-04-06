# Cards & Widgets Catalog

## Overview

A comprehensive catalog of reusable card and widget components used throughout the Vuexy Admin dashboard system. These components provide consistent UI patterns for displaying statistics, metrics, and information in various layouts.

**Scope:** All card/widget components in `src/components/card-statistics/`

**Base UI:** MUI Card, CardContent, Typography, Chip, Avatar

**Type Safety:** All components use TypeScript props from `@/types/pages/widgetTypes`

---

## Card Component Architecture

### Common Structure

All card components follow this pattern:

```typescript
// Component file: Card[Type].tsx
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Typography from '@mui/material/Typography'
import CustomAvatar from '@core/components/mui/Avatar'

interface ComponentProps {
  // Props from widgetTypes
}

const CardComponent = (props: ComponentProps) => {
  const { stats, title, subtitle, avatarIcon, avatarColor } = props

  return (
    <Card>
      <CardContent className='flex flex-col gap-y-3'>
        {/* Layout-specific content */}
      </CardContent>
    </Card>
  )
}

export default CardComponent
```

### Key Patterns

- **CustomAvatar:** Icon container with color and skin variants
- **Chip Component:** Trend/status indicators (color, variant, size)
- **Typography Variants:** Semantic text sizing (h4, h5, body2, subtitle1)
- **Flex Layout:** Tailwind classes for spacing and alignment
- **CSS Variables:** MUI theme color tokens for dynamic theming
- **Hover Effects:** Optional transitions and interactive states

---

## Card Types & Variants

### 1. CardStatsVertical

**File:** `Vertical.tsx`

**Purpose:** Vertical KPI card with icon, title, value, and trend indicator

**Usage Location:** CRM Dashboard (Profit & Sales cards)

**Props:**
```typescript
interface CardStatsVerticalProps {
  stats: string                              // Main metric value (e.g., '1.28k')
  title: string                              // Card title (e.g., 'Total Profit')
  subtitle: string                           // Card subtitle (e.g., 'Last Week')
  avatarIcon: string                         // Tabler icon class (e.g., 'tabler-credit-card')
  avatarColor: ColorVariant                  // Icon background color
  avatarSize?: number                        // Icon size (default: 44px)
  avatarSkin?: 'light' | 'dark'             // Icon container style
  chipText: string                           // Trend text (e.g., '+24.67%')
  chipColor: ColorVariant                    // Trend chip color
  chipVariant?: 'tonal' | 'outlined' | 'filled'
}

type ColorVariant = 'error' | 'success' | 'warning' | 'info' | 'primary' | 'secondary'
```

**Layout:**
```
┌─────────────────────────┐
│  🟦 Avatar Icon         │
│  Title                  │
│  Subtitle               │
│  Stats Value            │
│  [+24.67% Trend Chip]   │
└─────────────────────────┘
```

**Example Usage:**
```typescript
<CardStatsVertical
  title='Total Sales'
  subtitle='Last Week'
  stats='24.67k'
  avatarColor='success'
  avatarIcon='tabler-currency-dollar'
  avatarSkin='light'
  avatarSize={44}
  chipText='+24.67%'
  chipColor='success'
  chipVariant='tonal'
/>
```

**Features:**
- Icon on top
- Vertical text alignment
- Trend indicator chip
- Compact height (~160px)
- Good for mobile view

---

### 2. CardStatsHorizontal

**File:** `Horizontal.tsx`

**Purpose:** Horizontal statistic card with icon on right side

**Props:**
```typescript
interface CardStatsHorizontalProps {
  stats: string | number                     // Metric value
  title: string                              // Card title
  avatarIcon: string                         // Icon class
  avatarColor?: ColorVariant                 // Icon color
  avatarSkin?: 'light' | 'dark'             // Icon style
  avatarSize?: number                        // Icon size
}
```

**Layout:**
```
┌─────────────────────────────────┐
│  Stats Value        🟦 Icon     │
│  Title                          │
└─────────────────────────────────┘
```

**Features:**
- Compact horizontal layout
- Icon on right
- Space-efficient
- Good for dashboards with limited width
- Full height fill (`className='bs-full'`)

---

### 3. CardStatsHorizontalWithBorder

**File:** `HorizontalWithBorder.tsx`

**Purpose:** Horizontal card with animated bottom border on hover

**Props:**
```typescript
interface CardStatsHorizontalWithBorderProps {
  stats: string | number                     // Metric value
  title: string                              // Card title
  trendNumber: number                        // Trend percentage (+/-)
  avatarIcon: string                         // Icon class
  color?: ColorVariant                       // Theme color (border & icon)
}
```

**Layout:**
```
┌─────────────────────────────────┐
│  🟦 Stats Value                 │
│  Title                          │
│  +12.5% than last week         │
│────────────────────────────────│ (animated on hover)
```

**Features:**
- **Animated Border:** Grows and color changes on hover
- **Dynamic Color:** Entire card themed by color prop
- **Trend Display:** Shows percentage change
- **Light Avatar Skin:** Subtle icon background
- **Hover Effect:**
  - Border grows to 3px
  - Box shadow appears
  - Border color brightens
  - Margin adjusts for visual lift

**CSS Variables Used:**
```css
--mui-palette-{color}-darkerOpacity   /* Default border color */
--mui-palette-{color}-main             /* Hover border color */
--mui-customShadows-lg                 /* Hover shadow */
```

---

### 4. CardStatsHorizontalWithSubtitle

**File:** `HorizontalWithSubtitle.tsx`

**Purpose:** Horizontal card with subtitle text below main title

**Props:**
```typescript
interface CardStatsHorizontalWithSubtitleProps {
  stats: string | number
  title: string
  subtitle: string                           // Additional description
  avatarIcon: string
  avatarColor?: ColorVariant
  avatarSkin?: 'light' | 'dark'
  avatarSize?: number
}
```

**Layout:**
```
┌─────────────────────────────────┐
│  Stats        🟦 Icon           │
│  Title                          │
│  Subtitle (secondary color)     │
└─────────────────────────────────┘
```

**Features:**
- Extended information
- Two-line text content
- Icon on right
- Subtle subtitle styling

---

### 5. CardStatsHorizontalWithAvatar

**File:** `HorizontalWithAvatar.tsx`

**Purpose:** Horizontal card with user avatar support

**Props:**
```typescript
interface CardStatsHorizontalWithAvatarProps {
  stats: string | number
  title: string
  subtitle?: string
  avatarImage?: string                       // User avatar image URL
  avatarIcon?: string                        // Fallback icon
  avatarColor?: ColorVariant
}
```

**Features:**
- User avatar or icon
- Optional subtitle
- Flexible avatar source (image or icon)
- Responsive avatar sizing

---

### 6. CardStatsSquare

**File:** `CardStatsSquare.tsx`

**Purpose:** Square/equal-aspect ratio card for grid layouts

**Props:**
```typescript
interface CardStatsSquareProps {
  stats: string | number
  title: string
  avatarIcon: string
  avatarColor?: ColorVariant
  avatarSkin?: 'light' | 'dark'
}
```

**Layout:**
```
┌─────────────┐
│  🟦 Icon    │
│             │
│  Stats      │
│  Title      │
└─────────────┘
```

**Features:**
- Square aspect ratio
- Icon centered top
- Good for uniform grid layouts
- Equal width and height

---

### 7. StatsWithAreaChart

**File:** `StatsWithAreaChart.tsx`

**Purpose:** Card combining statistic with small area chart visualization

**Props:**
```typescript
interface StatsWithAreaChartProps {
  stats: string | number
  title: string
  chartSeries: Array<{ data: number[] }>    // Area chart data
  chartCategories: string[]                  // X-axis labels
  avatarColor?: ColorVariant
  avatarIcon?: string
}
```

**Layout:**
```
┌──────────────────────────────┐
│  Stats    Title              │
│  🟦 Icon  Trend %            │
├──────────────────────────────┤
│                              │
│    ╱╲                        │
│   ╱  ╲___                    │
│  ╱        ╲                  │
│ ╱          ╲                 │
└──────────────────────────────┘
```

**Features:**
- Embedded chart visualization
- Area chart with gradient
- Compact chart display
- Statistical context

---

### 8. CustomerStats

**File:** `CustomerStats.tsx`

**Purpose:** Specialized card for customer metrics

**Props:**
```typescript
interface CustomerStatsProps {
  stats: string | number
  title: string
  avatarIcon?: string
  avatarColor?: ColorVariant
  trend?: number                             // Customer change %
  icon?: React.ReactNode                     // Custom icon element
}
```

**Features:**
- Customer-focused layout
- Trend indicators
- Custom icon support
- Visual trend arrows (↑/↓)

---

## Color Variants

All cards support MUI theme colors:

```typescript
type ColorVariant = 
  | 'primary'      // Primary brand color
  | 'secondary'    // Secondary brand color
  | 'success'      // Green - positive/success
  | 'error'        // Red - negative/error
  | 'warning'      // Orange - warning
  | 'info'         // Blue - informational
```

---

## Avatar Skins

CustomAvatar component supports two skin variants:

### Light Skin
```css
background: rgba(var(--mui-palette-{color}-mainChannel) / 0.1)
color: var(--mui-palette-{color}-main)
```

### Dark Skin
```css
background: var(--mui-palette-{color}-main)
color: white or var(--mui-palette-common-white)
```

---

## Icon System

All components use **Tabler Icons** via CSS classes:

```typescript
avatarIcon='tabler-credit-card'      // Credit card icon
avatarIcon='tabler-currency-dollar'  // Dollar sign
avatarIcon='tabler-shopping-cart'    // Shopping cart
avatarIcon='tabler-users'            // Multiple users
avatarIcon='tabler-trending-up'      // Trending up
avatarIcon='tabler-trending-down'    // Trending down
```

Icon sizing: `text-[28px]` or `text-[26px]` (Tailwind)

---

## Usage Patterns

### Pattern 1: KPI Display
```typescript
<CardStatsVertical
  title='Revenue'
  subtitle='This Month'
  stats='$42,358'
  avatarIcon='tabler-chart-bar'
  avatarColor='primary'
  chipText='+15.2%'
  chipColor='success'
/>
```

### Pattern 2: Compact Metric
```typescript
<CardStatsHorizontal
  title='Total Users'
  stats={1420}
  avatarIcon='tabler-users'
  avatarColor='info'
  avatarSize={38}
/>
```

### Pattern 3: Interactive Metric
```typescript
<CardStatsHorizontalWithBorder
  title='Sales'
  stats='$12,450'
  trendNumber={8.5}
  avatarIcon='tabler-shopping-cart'
  color='success'
/>
```

### Pattern 4: Chart-Enhanced
```typescript
<StatsWithAreaChart
  title='Growth'
  stats='24.5%'
  avatarIcon='tabler-trending-up'
  avatarColor='success'
  chartSeries={[{ data: [10, 20, 15, 25, 20, 30, 25] }]}
  chartCategories={['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']}
/>
```

---

## Responsive Behavior

All cards are flex-based and responsive:

- **Mobile (XS):** Full width (12 cols)
- **Tablet (SM/MD):** Half width (6 cols) or full
- **Desktop (LG):** Variable width (2-4 cols)
- **Large Desktop (XL):** Final layout sizing

Cards grow/shrink based on parent grid sizing.

---

## Styling & Customization

### Tailwind Classes Used
```css
flex flex-col gap-y-3        /* Vertical stacking */
flex items-center gap-2      /* Horizontal alignment */
flex flex-wrap               /* Wrap behavior */
justify-between              /* Space distribution */
text-[28px]                  /* Icon sizing */
bs-full                      /* Block size full height */
```

### CSS Variables
All colors use MUI CSS variables for theme compliance:
```css
var(--mui-palette-primary-main)
var(--mui-palette-success-darkerOpacity)
var(--mui-customShadows-lg)
```

---

## Component Props Interface

```typescript
// From @/types/pages/widgetTypes
interface CardStatsVerticalProps {
  stats: string
  title: string
  subtitle: string
  avatarIcon: string
  avatarColor: ColorVariant
  avatarSize?: number
  avatarSkin?: 'light' | 'dark'
  chipText: string
  chipColor: ColorVariant
  chipVariant?: 'tonal' | 'outlined' | 'filled'
}

interface CardStatsHorizontalProps {
  stats: string | number
  avatarIcon: string
  title: string
  avatarColor?: ColorVariant
  avatarSkin?: 'light' | 'dark'
  avatarSize?: number
}

interface CardStatsHorizontalWithBorderProps {
  title: string
  stats: string | number
  trendNumber: number
  avatarIcon: string
  color?: ColorVariant
}

// Additional types...
```

---

## Performance Considerations

1. **No External APIs:** All components are presentational
2. **Memoization:** Can wrap with React.memo if props don't change
3. **Static Content:** Perfect for displaying static metrics
4. **Fast Render:** Simple component tree, minimal re-renders
5. **Bundle Size:** Small components (< 2KB each)

---

## Accessibility

- **Semantic HTML:** Card, Typography, Chip components
- **Color Contrast:** MUI ensures WCAG AA compliance
- **Text Sizing:** Typography variants for hierarchy
- **Icon Support:** Icons are decorative; text conveys meaning
- **Screen Readers:** Semantic structure understood by AT

---

## Common Use Cases

1. **Dashboard KPIs:** CardStatsVertical for main metrics
2. **Metric Overview:** CardStatsHorizontal for compact display
3. **Status Indicators:** HorizontalWithBorder for interactive metrics
4. **Customer Metrics:** CustomerStats for user-focused data
5. **Trends:** StatsWithAreaChart for historical context
6. **Uniform Grids:** CardStatsSquare for equal layouts

---

## File Locations

All card components located in:
```
src/components/card-statistics/
├── CardStatsSquare.tsx
├── CustomerStats.tsx
├── Horizontal.tsx
├── HorizontalWithAvatar.tsx
├── HorizontalWithBorder.tsx
├── HorizontalWithSubtitle.tsx
├── StatsWithAreaChart.tsx
└── Vertical.tsx
```

---

## Related Types

Located in: `src/types/pages/widgetTypes.ts`

```typescript
export type CardStatsVerticalProps = { ... }
export type CardStatsHorizontalProps = { ... }
export type CardStatsHorizontalWithBorderProps = { ... }
// ... other card types
```

---

## Best Practices

1. **Choose Right Variant:** Pick layout based on space and context
2. **Consistent Colors:** Use color variants consistently
3. **Icon Selection:** Match icon to metric context
4. **Typography:** Use MUI variant prop for text sizing
5. **Responsive:** Cards auto-respond to grid sizing
6. **Accessibility:** Ensure color not sole info method
7. **Props Type Safety:** Always import correct TypeScript types

