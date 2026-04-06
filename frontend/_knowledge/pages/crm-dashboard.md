# CRM Dashboard Page

## Overview

The CRM Dashboard is a server-side rendered page component that displays comprehensive customer relationship management metrics and analytics. It aggregates multiple specialized chart components and KPI cards in a responsive grid layout, providing a unified view of sales, revenue, projects, and customer interactions.

**Key Path:** `src/app/[lang]/(dashboard)/(private)/dashboards/crm/page.tsx`

**Type:** Async Server Component (Page Component)

---

## Key Features

### Dashboard Composition

1. **Order Statistics Card** - Bar chart showing weekly order distribution
2. **Yearly Sales Chart** - Line/area chart displaying annual sales trends
3. **Profit KPI Card** - Vertical statistic card with trend indicator (-12.2%)
4. **Sales KPI Card** - Vertical statistic card with trend indicator (+24.67%)
5. **Revenue Growth Chart** - Detailed bar chart analysis with growth metrics
6. **Earning Reports** - Tabbed earning breakdown and comparison
7. **Radar Sales Chart** - Radar/polygon chart for multi-dimensional sales analysis
8. **Sales by Countries** - Geographic distribution of sales
9. **Project Status** - Project completion and status overview
10. **Active Projects** - List of active projects with metrics
11. **Last Transaction** - Recent transaction log with server-mode support
12. **Activity Timeline** - Chronological activity feed

### Responsive Grid Layout

- **6-column grid system** with MUI Grid v2
- **Breakpoint-aware sizing** across XS, SM, MD, LG breakpoints
- **Flexible component spans** from 2 to 12 grid columns
- **6-unit spacing** between components

---

## Page Layout

The dashboard uses a responsive 12-column grid with multiple layout tiers:

**Desktop Layout (LG+):**
- Row 1: 4x KPI cards (2 cols each) + 1 chart (4 cols)
- Row 2: 1x Large chart (8 cols) + 1x Medium chart (4 cols)
- Row 3: 4x Cards (4 cols each)
- Row 4: 2x Sections (6 cols each)

**Tablet Layout (MD):**
- Row 1: 2x KPI cards (6 cols each)
- Row 2: 2x KPI cards (6 cols each)
- Row 3: Revenue growth (8 cols)
- Row 4-5: Charts in 2-column pairs (6 cols each)
- Row 6-7: Sections (6 cols each)

**Mobile Layout (XS/SM):**
- All components full width (12 cols, stacked vertically)
- KPI cards: 6 cols on SM (2 per row)

---

## Components Used

### Chart Components
- `DistributedBarChartOrder` - Order volume with background reference bars
- `LineAreaYearlySalesChart` - Year-over-year sales area chart
- `BarChartRevenueGrowth` - Revenue growth bar analysis
- `RadarSalesChart` - Multi-axis radar visualization
- `EarningReportsWithTabs` - Tabbed earning breakdown with category switching

### Data Components
- `SalesByCountries` - Geographic sales distribution
- `ProjectStatus` - Project completion status and metrics
- `ActiveProjects` - Active project list with details
- `LastTransaction` - Transaction history table (server-aware)
- `ActivityTimeline` - Activity feed with timeline view

### UI Components
- `CardStatVertical` - Vertical KPI card with icon, value, subtitle, and trend chip
  - Instance 1: Total Profit (-12.2%, error color)
  - Instance 2: Total Sales (+24.67%, success color)

---

## Type Definitions

```typescript
// CRM Dashboard - Main Page Component
const DashboardCRM = async (): Promise<React.ReactNode>

interface CardStatVerticalProps {
  title: string                    // Card title
  subtitle: string                 // Card subtitle  
  stats: string                    // Main metric value
  avatarColor: ColorVariant        // Icon background color
  avatarIcon: string               // Tabler icon name
  avatarSkin: 'light' | 'dark'     // Icon container skin
  avatarSize: number               // Icon size in pixels
  chipText: string                 // Trend text
  chipColor: ColorVariant          // Trend chip color
  chipVariant: 'tonal' | 'outlined' | 'filled'
}

interface LastTransactionProps {
  serverMode: ServerModeConfig
}

interface ServerModeConfig {
  isDarkMode?: boolean
  theme?: 'light' | 'dark'
  locale?: string
}

type GridBreakpoint = {
  xs?: number | 'auto'
  sm?: number | 'auto'
  md?: number | 'auto'
  lg?: number | 'auto'
  xl?: number | 'auto'
}

type ColorVariant = 'error' | 'success' | 'warning' | 'info' | 'primary'
```

---

## Component Breakdown

### Row 1: KPI Statistics (4 cards)
- **DistributedBarChartOrder** (lg: 2) - Order metrics with background bars
- **LineAreaYearlySalesChart** (lg: 2) - Yearly sales trend area chart
- **CardStatVertical - Profit** (lg: 2) - Total profit KPI
- **CardStatVertical - Sales** (lg: 2) - Total sales KPI

### Row 2: Primary Charts (2 components)
- **BarChartRevenueGrowth** (md: 8, lg: 4) - Revenue growth analysis
- **EarningReportsWithTabs** (lg: 8) - Earning breakdown with tabs

### Row 3: Analytics Cards (4 components)
- **RadarSalesChart** (md: 6, lg: 4) - Multi-dimensional sales
- **SalesByCountries** (md: 6, lg: 4) - Geographic distribution
- **ProjectStatus** (md: 6, lg: 4) - Project metrics
- **ActiveProjects** (md: 6, lg: 4) - Active projects list

### Row 4: Bottom Sections (2 components)
- **LastTransaction** (md: 6) - Transaction history with server-mode support
- **ActivityTimeline** (md: 6) - Activity feed timeline

---

## Usage Example

```typescript
import Grid from '@mui/material/Grid'
import { getServerMode } from '@core/utils/serverHelpers'
import DistributedBarChartOrder from '@views/dashboards/crm/DistributedBarChartOrder'
// ... import other components

const DashboardCRM = async () => {
  const serverMode = await getServerMode()

  return (
    <Grid container spacing={6}>
      <Grid size={{ xs: 12, sm: 6, md: 4, lg: 2 }}>
        <DistributedBarChartOrder />
      </Grid>
      {/* ... more Grid components ... */}
      <Grid size={{ xs: 12, md: 6 }}>
        <LastTransaction serverMode={serverMode} />
      </Grid>
    </Grid>
  )
}

export default DashboardCRM
```

---

## Key Implementation Notes

1. **Async Server Component** - Uses async for server-side data fetching
2. **MUI Grid v2** - Uses size prop for responsive design
3. **6-unit Spacing** - Consistent spacing between components
4. **Mobile-first** - Base is 12 cols, narrower on larger screens
5. **Server Actions** - getServerMode() for configuration
6. **No Client State** - Purely server-rendered at page level
7. **Dynamic Imports** - Charts use Next.js dynamic imports
8. **Theme Integration** - Components use MUI theme tokens

---

## File Structure

```
src/
├── app/[lang]/(dashboard)/(private)/dashboards/
│   └── crm/
│       └── page.tsx (THIS FILE)
├── views/dashboards/crm/
│   ├── DistributedBarChartOrder.tsx
│   ├── LineAreaYearlySalesChart.tsx
│   ├── BarChartRevenueGrowth.tsx
│   ├── EarningReportsWithTabs.tsx
│   ├── RadarSalesChart.tsx
│   ├── SalesByCountries.tsx
│   ├── ProjectStatus.tsx
│   ├── ActiveProjects.tsx
│   ├── LastTransaction.tsx
│   └── ActivityTimeline.tsx
├── components/card-statistics/
│   └── Vertical.tsx
└── core/utils/
    └── serverHelpers.ts
```

---

## Performance Optimizations

1. **Code Splitting** - Charts use dynamic imports
2. **Lazy Loading** - Components load on page render
3. **Memoization** - Client components use React.memo
4. **Server Rendering** - Data fetched server-side
5. **Grid v2 Efficiency** - Optimized layout calculations

---

## Responsive Behavior

### Breakpoint Strategy

- **XS (0px+)** - All components 12 cols (full width)
- **SM (600px+)** - KPI cards become 6 cols (2 per row)
- **MD (900px+)** - Mixed 6-8 col spans for variety
- **LG (1200px+)** - Final desktop layout with 2-4 col cards

---

## Accessibility

- Semantic HTML structure
- Card components with accessible labels
- Chart ARIA attributes
- Color contrast compliance
- Keyboard navigation support
- Responsive design for all devices

