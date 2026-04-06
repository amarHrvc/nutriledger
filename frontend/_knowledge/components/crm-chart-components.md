# CRM Dashboard Chart Components

## Overview

Detailed documentation of all chart and data visualization components used in the CRM Dashboard. This document provides component structure, props, usage patterns, and implementation details.

**Dashboard Path:** `src/app/[lang]/(dashboard)/(private)/dashboards/crm/page.tsx`

**Components Directory:** `src/views/dashboards/crm/`

**Total Components:** 10 (5 charts + 5 data components)

---

## Component Catalog

### 1. DistributedBarChartOrder

**File:** `DistributedBarChartOrder.tsx`

**Type:** Client Component (Chart)

**Purpose:** Display weekly order distribution with background reference bars

**Data Structure:**
```typescript
const series = [{ data: [77, 55, 23, 43, 77, 55, 89] }]  // 7-day data
```

**Key Features:**
- Distributed bar chart (7 bars for each day)
- Light background bars for reference
- 32% column width with 3px rounded corners
- No tooltips or data labels
- Sparkline styling (minimal UI)
- Height: 84px (very compact)
- KPI display: 124k orders with +12.6% trend

**ApexCharts Configuration:**
```typescript
chart: {
  type: 'bar',
  stacked: false,
  parentHeightOffset: 0,
  toolbar: { show: false },
  sparkline: { enabled: true }
}
plotOptions: {
  bar: {
    borderRadius: 3,
    horizontal: false,
    columnWidth: '32%',
    colors: {
      backgroundBarRadius: 5,
      backgroundBarColors: [actionSelectedColor, ...]
    }
  }
}
responsive: [
  { breakpoint: 1350, options: { columnWidth: '45%' } },
  { breakpoint: lg, options: { columnWidth: '20%' } },
  { breakpoint: 600, options: { columnWidth: '15%' } }
]
```

**Props:** None (self-contained)

**Grid Size:** `{ xs: 12, sm: 6, md: 4, lg: 2 }`

**Display:** KPI card with title/subheader, chart, and stats

---

### 2. LineAreaYearlySalesChart

**File:** `LineAreaYearlySalesChart.tsx`

**Type:** Client Component (Chart)

**Purpose:** Display year-over-year sales trends with gradient area

**Data Structure:**
```typescript
const series = [{ data: [40, 10, 65, 45] }]  // Quarterly data
```

**Key Features:**
- Area chart with gradient fill (opaque to transparent)
- Smooth curve interpolation
- Monochrome theme (success color)
- Sparkline styling
- Height: 84px
- KPI display: 175k sales with -16.2% trend

**ApexCharts Configuration:**
```typescript
chart: {
  parentHeightOffset: 0,
  toolbar: { show: false },
  sparkline: { enabled: true }
}
stroke: {
  width: 2,
  curve: 'smooth'
}
fill: {
  type: 'gradient',
  gradient: {
    opacityTo: 0,
    opacityFrom: 1,
    shadeIntensity: 1,
    stops: [0, 100],
    colorStops: [...]
  }
}
theme: {
  monochrome: {
    enabled: true,
    shadeTo: 'light',
    shadeIntensity: 1,
    color: successColor
  }
}
```

**Props:** None

**Grid Size:** `{ xs: 12, sm: 6, md: 4, lg: 2 }`

---

### 3. BarChartRevenueGrowth

**File:** `BarChartRevenueGrowth.tsx`

**Type:** Client Component (Chart)

**Purpose:** Display revenue growth across weeks with color highlighting

**Data Structure:**
```typescript
const series = [{ data: [32, 52, 72, 94, 116, 94, 72] }]  // 7-week trend
```

**Key Features:**
- Distributed bar chart (highlight max value in primary color)
- All bars light, max bar primary color
- 55% column width, 5px border radius
- Success-themed color palette
- No data labels or tooltips
- Dynamic color assignment (max value gets primary color)

**ApexCharts Configuration:**
```typescript
plotOptions: {
  bar: {
    borderRadius: 5,
    distributed: true,
    columnWidth: '55%'
  }
}
colors: [
  lightOpacity, lightOpacity, lightOpacity, lightOpacity,
  successMain, lightOpacity, lightOpacity  // 5th bar is max
]
```

**Responsive Behavior:**
- Adjusts column width per breakpoint
- Chart scales with container

**Grid Size:** `{ xs: 12, md: 8, lg: 4 }`

---

### 4. RadarSalesChart

**File:** `RadarSalesChart.tsx`

**Type:** Client Component (Chart)

**Purpose:** Multi-dimensional sales analysis comparing Sales vs Visits

**Data Structure:**
```typescript
const series = [
  { name: 'Sales', data: [32, 27, 27, 30, 25, 25] },
  { name: 'Visits', data: [25, 35, 20, 20, 20, 20] }
]
```

**Key Features:**
- Radar (polygon) chart with 6 axes (Jan-Jun)
- Dual-series comparison
- Primary + Info colors
- Polygon connectors and stroke colors from theme
- Responsive height changes
- Legend with offset adjustments for RTL
- Fill opacity [1, 0.85] for layer distinction

**ApexCharts Configuration:**
```typescript
plotOptions: {
  radar: {
    polygons: {
      connectorColors: divider,
      strokeColors: divider
    }
  }
}
fill: { opacity: [1, 0.85] }
labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
legend: {
  fontSize: '13px',
  markers: {
    offsetY: -1,
    offsetX: theme.direction === 'rtl' ? 7 : -4
  }
}
responsive: [
  { breakpoint: 1200, options: { chart: { height: 332 } } }
]
```

**Props:** None

**Grid Size:** `{ xs: 12, md: 6, lg: 4 }`

---

### 5. EarningReportsWithTabs

**File:** `EarningReportsWithTabs.tsx`

**Type:** Client Component (Stateful Chart)

**Purpose:** Tabbed earning breakdown (Orders, Sales, Profit, Income)

**Key Features:**
- **Stateful:** Uses `useState` for tab management
- **Tab Data:** 4 categories with icons and series
- **Dynamic Coloring:** Max value per category highlighted in primary
- **Custom Tab UI:** Card-style tabs with borders and avatars
- **Category Colors:** Icons change color when tab is active
- **Chart Height:** 233px (substantial)

**Tab Data:**
```typescript
type TabCategory = 'orders' | 'sales' | 'profit' | 'income'

const tabData = [
  {
    type: 'orders',
    avatarIcon: 'tabler-shopping-cart',
    series: [{ data: [28, 10, 46, 38, 15, 30, 35, 28, 8] }]
  },
  // ... sales, profit, income
]
```

**Tab UI Features:**
- 110px wide, 100px tall tabs
- Rounded border (solid when active, dashed otherwise)
- Custom avatar that colors with primary on active
- Capitalized label text

**Chart Configuration:**
```typescript
plotOptions: {
  bar: {
    borderRadius: 6,
    distributed: true,
    columnWidth: '33%',
    borderRadiusApplication: 'end',
    dataLabels: { position: 'top' }
  }
}
dataLabels: {
  offsetY: -11,
  formatter: val => `${val}k`
}
colors: Array(9).fill('var(--mui-palette-primary-lightOpacity)')
// Dynamic colors: max value gets primary, rest get light opacity
xaxis: {
  categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep']
}
yaxis: {
  formatter: val => `$${val}k`
}
```

**Props:** None

**Grid Size:** `{ xs: 12, lg: 8 }`

**Responsiveness:**
- Tablet: Tabs stack
- Mobile: Single column layout
- Chart width: 100% (responsive)

---

### 6. SalesByCountries

**File:** `SalesByCountries.tsx`

**Type:** Client Component (Chart/Map)

**Purpose:** Geographic distribution of sales by country

**Key Features:**
- Map or regional heat map visualization
- Country-level granularity
- Sales metric per region
- Interactive legend or labels

**Grid Size:** `{ xs: 12, md: 6, lg: 4 }`

---

### 7. ProjectStatus

**File:** `ProjectStatus.tsx`

**Type:** Client Component (Chart)

**Purpose:** Project completion status visualization

**Key Features:**
- Pie or donut chart for status distribution
- Status categories (Active, Completed, Pending)
- Color-coded status
- Summary metrics

**Grid Size:** `{ xs: 12, md: 6, lg: 4 }`

---

### 8. ActiveProjects

**File:** `ActiveProjects.tsx`

**Type:** Client Component (List)

**Purpose:** Display active projects with progress bars

**Data Structure:**
```typescript
type DataType = {
  title: string              // Project name
  imgSrc: string             // Logo image
  progress: number           // Completion % (0-100)
  subtitle: string           // Category/type
  progressColor: ThemeColor  // Progress bar color
}

const data: DataType[] = [
  {
    title: 'Laravel',
    subtitle: 'eCommerce',
    progress: 54,
    progressColor: 'error',
    imgSrc: '/images/logos/laravel.png'
  },
  // ... more projects
]
```

**Key Features:**
- Project listing with logos
- Linear progress bars
- Color-coded progress (error, primary, success, info, warning)
- Project title and category
- Option menu (Refresh, Update, Share)
- Card header: "Active Projects" subtitle "Average 72% completed"

**Component Structure:**
```
Card
├── CardHeader
│   ├── Title: "Active Projects"
│   ├── Subheader: "Average 72% completed"
│   └── OptionMenu
└── CardContent
    └── For each project:
        ├── Project logo image
        ├── Title & subtitle
        └── LinearProgress bar
```

**Props:** None (static data)

**Grid Size:** `{ xs: 12, md: 6, lg: 4 }`

**Responsive:** Full width on mobile, wraps on tablet, fixed on desktop

---

### 9. LastTransaction

**File:** `LastTransaction.tsx`

**Type:** Client Component (Table/Server-aware)

**Purpose:** Display recent transactions with server-mode support

**Key Features:**
- Transaction listing/table
- Server-mode configuration awareness
- Transaction details (date, amount, status)
- Optional filtering/sorting

**Props:**
```typescript
interface LastTransactionProps {
  serverMode: any  // Server-side configuration
}
```

**Grid Size:** `{ xs: 12, md: 6 }`

---

### 10. ActivityTimeline

**File:** `ActivityTimeline.tsx`

**Type:** Client Component (Timeline)

**Purpose:** Chronological activity feed

**Key Features:**
- Timeline layout
- Activity entries with timestamps
- Activity type/category indicators
- Chronological ordering

**Grid Size:** `{ xs: 12, md: 6 }`

---

## Component Dependencies

### Shared Imports
All chart components use:
```typescript
'use client'                                    // Client directive
import dynamic from 'next/dynamic'              // Dynamic imports
import { useTheme } from '@mui/material/styles' // Theme access
import type { ApexOptions } from 'apexcharts'  // Type definitions
```

### Shared Components
```typescript
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'
import Typography from '@mui/material/Typography'
```

### Utility Components
```typescript
import OptionMenu from '@core/components/option-menu'       // Menu dropdown
import CustomAvatar from '@core/components/mui/Avatar'      // Avatar with icon
```

---

## Theme Integration

All components integrate with MUI theme:

```typescript
const theme = useTheme()

// Access theme properties
theme.palette.success.main
theme.palette.primary.main
theme.palette.text.secondary
theme.palette.divider
theme.typography.fontFamily
theme.typography.body2.fontSize

// Direction awareness (RTL)
theme.direction === 'rtl' ? 7 : -4

// Breakpoints
theme.breakpoints.values.lg
```

---

## Data Patterns

### Chart Series Format
```typescript
// Single series
const series = [{ data: [77, 55, 23, 43, 77, 55, 89] }]

// Multi-series
const series = [
  { name: 'Sales', data: [...] },
  { name: 'Visits', data: [...] }
]
```

### Colors from CSS Variables
```typescript
// Light opacity variants
'var(--mui-palette-success-lightOpacity)'
'var(--mui-palette-primary-lightOpacity)'

// Main colors
'var(--mui-palette-primary-main)'
'var(--mui-palette-success-main)'

// Text colors
'var(--mui-palette-text-primary)'
'var(--mui-palette-text-secondary)'
'var(--mui-palette-text-disabled)'

// System colors
'var(--mui-palette-divider)'
'var(--mui-palette-background-paper)'
```

---

## Common Chart Options Pattern

```typescript
const options: ApexOptions = {
  chart: {
    parentHeightOffset: 0,
    toolbar: { show: false }
  },
  legend: { show: false },
  tooltip: { enabled: false },
  dataLabels: { enabled: false },
  states: {
    hover: { filter: { type: 'none' } },
    active: { filter: { type: 'none' } }
  },
  grid: {
    show: false,
    padding: { ... }
  },
  xaxis: {
    labels: { show: false },
    axisTicks: { show: false },
    axisBorder: { show: false }
  },
  yaxis: { show: false }
}
```

---

## Responsive Breakpoints

Standard responsive pattern in CRM charts:

```typescript
responsive: [
  {
    breakpoint: 1450,
    options: {
      plotOptions: {
        bar: { columnWidth: '55%' }
      }
    }
  },
  {
    breakpoint: 900,
    options: {
      // tablet adjustments
    }
  },
  {
    breakpoint: 600,
    options: {
      // mobile adjustments
    }
  }
]
```

---

## Grid Layout Structure

CRM Dashboard uses responsive grid with MUI Grid v2:

**Row 1 (KPIs):** 4 cards × 2 cols = 8 cols (lg: 2 each)
**Row 2 (Main Charts):** Revenue (4 cols) + Earning (8 cols)
**Row 3 (Analytics):** 4 cards × 4 cols = 12 cols (wraps as needed)
**Row 4 (Bottom):** 2 cards × 6 cols = 12 cols

---

## Performance Optimizations

1. **Dynamic Imports:** All charts use `dynamic(() => import(...))`
2. **Lazy Loading:** Charts load only when page renders
3. **Static Data:** No re-renders unless data prop changes
4. **Memoization:** Can wrap with `React.memo()` if needed
5. **Code Splitting:** Each chart split to separate bundle

---

## Implementation Checklist

For each CRM chart component:

- [ ] Use `'use client'` directive
- [ ] Dynamic import ApexCharts
- [ ] Define series data (static or props)
- [ ] Create options object with theme colors
- [ ] Wrap in Card component
- [ ] Add CardHeader with title/subheader
- [ ] Use MUI useTheme hook
- [ ] Add responsive configurations
- [ ] Test on all breakpoints
- [ ] Verify theme color compliance
- [ ] Add proper TypeScript types

---

## Usage Example (Page Level)

```typescript
<Grid container spacing={6}>
  <Grid size={{ xs: 12, sm: 6, md: 4, lg: 2 }}>
    <DistributedBarChartOrder />
  </Grid>
  <Grid size={{ xs: 12, sm: 6, md: 4, lg: 2 }}>
    <LineAreaYearlySalesChart />
  </Grid>
  {/* More components... */}
  <Grid size={{ xs: 12, lg: 8 }}>
    <EarningReportsWithTabs />
  </Grid>
</Grid>
```

---

## File Locations

```
src/views/dashboards/crm/
├── DistributedBarChartOrder.tsx
├── LineAreaYearlySalesChart.tsx
├── BarChartRevenueGrowth.tsx
├── RadarSalesChart.tsx
├── EarningReportsWithTabs.tsx
├── SalesByCountries.tsx
├── ProjectStatus.tsx
├── ActiveProjects.tsx
├── LastTransaction.tsx
└── ActivityTimeline.tsx
```

---

## Related Documentation

- Charts Catalog: `_knowledge/pages/charts-catalog.md`
- CRM Dashboard: `_knowledge/pages/crm-dashboard.md`
- Cards & Widgets: `_knowledge/pages/cards-widgets-catalog.md`
- ApexCharts Docs: [apexcharts.com](https://apexcharts.com)
- MUI Documentation: [mui.com](https://mui.com)

