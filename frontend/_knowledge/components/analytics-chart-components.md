# Analytics Dashboard Chart Components

## Overview

Detailed documentation of all chart, slider, and data visualization components used in the Analytics Dashboard. This document provides comprehensive component structure, props, usage patterns, and implementation details.

**Dashboard Path:** `src/app/[lang]/(dashboard)/(private)/dashboards/analytics/page.tsx`

**Components Directory:** `src/views/dashboards/analytics/`

**Total Components:** 10 (1 slider + 8 charts + 1 table)

---

## Component Catalog

### 1. WebsiteAnalyticsSlider

**File:** `WebsiteAnalyticsSlider.tsx`

**Type:** Client Component (Interactive Slider)

**Purpose:** Interactive carousel displaying website analytics metrics

**Third-party Library:** Keen Slider (carousel)

**Key Features:**
- Carousel/slider with navigation
- Multiple slides (Traffic, Spending, Revenue Sources)
- Dark background gradient
- Metric grid display within each slide
- Auto-rotate functionality (likely)
- Responsive slide layout

**Data Structure:**
```typescript
type DataType = {
  img: string                     // Graphic illustration image
  title: string                   // Slide title
  details: { [key: string]: string }  // Metric key-value pairs
}

const data: DataType[] = [
  {
    title: 'Traffic',
    img: '/images/cards/graphic-illustration-1.png',
    details: {
      Sessions: '28%',
      'Page Views': '3.1k',
      Leads: '1.2k',
      Conversions: '12%'
    }
  },
  // ... Spending, Revenue Sources
]
```

**Slide Layout:**
```
┌─────────────────────────────────────────┐
│ Dark Background                         │
│                                         │
│ Website Analytics                       │
│ Total 28.5% Conversion Rate             │
│                                         │
│ [Graphic]    [Metrics Grid]             │
│              Sessions: 28%              │
│              Page Views: 3.1k           │
│              Leads: 1.2k                │
│              Conversions: 12%           │
│                                         │
│ ◄ prev  Slide 1 of 3  next ►           │
└─────────────────────────────────────────┘
```

**Props:** None (self-contained)

**Grid Size:** `{ xs: 12, lg: 6 }`

**Features:**
- Badge component (possibly for slide counter)
- Keen Slider plugin for carousel functionality
- Responsive grid inside slider
- Image positioning varies by breakpoint
- Typography in white text

---

### 2. LineAreaDailySalesChart

**File:** `LineAreaDailySalesChart.tsx`

**Type:** Client Component (Chart)

**Purpose:** Daily sales trends with area chart

**Key Features:**
- Area chart with gradient fill
- Daily data points
- Sparkline styling
- Compact height
- Success-themed color

**Similar to CRM LineAreaYearlySalesChart:**
- Smooth curve interpolation
- Gradient fill (opaque to transparent)
- No tooltips/data labels
- Theme-aware monochrome styling

**Grid Size:** `{ xs: 12, sm: 6, lg: 3 }`

---

### 3. SalesOverview

**File:** `SalesOverview.tsx`

**Type:** Client Component (Chart/Card)

**Purpose:** Sales summary overview

**Key Features:**
- Compact visualization
- Sales metrics display
- KPI-style card
- Icon and stat layout

**Grid Size:** `{ xs: 12, sm: 6, lg: 3 }`

---

### 4. EarningReports

**File:** `EarningReports.tsx`

**Type:** Client Component (Hybrid)

**Purpose:** Earnings breakdown with chart and progress metrics

**Key Features:**
- **Chart Component:** Area chart showing weekly earnings
- **Metrics Listing:** 3 earning categories (Earnings, Profit, Expense)
- **Progress Bars:** Visual progress per category
- **Color-coded:** Each category has unique color (primary, info, error)
- **Icons:** Avatar icons for each category

**Data Structure:**
```typescript
type DataType = {
  stats: string                   // Amount value
  title: string                   // Category name
  progress: number                // Progress percentage
  avatarIcon: string             // Icon class
  avatarColor?: ThemeColor       // Icon color
  progressColor?: ThemeColor     // Progress bar color
}

const series = [{ data: [37, 76, 65, 41, 99, 53, 70] }]  // Weekly data

const data: DataType[] = [
  {
    title: 'Earnings',
    progress: 64,
    stats: '$545.69',
    progressColor: 'primary',
    avatarColor: 'primary',
    avatarIcon: 'tabler-currency-dollar'
  },
  {
    title: 'Profit',
    progress: 59,
    stats: '$256.34',
    progressColor: 'info',
    avatarColor: 'info',
    avatarIcon: 'tabler-chart-pie-2'
  },
  {
    title: 'Expense',
    progress: 22,
    stats: '$74.19',
    progressColor: 'error',
    avatarColor: 'error',
    avatarIcon: 'tabler-brand-paypal'
  }
]
```

**Component Structure:**
```
Card
├── CardHeader with OptionMenu
├── Chart (EarningReports - Area chart)
└── CardContent
    └── For each metric:
        ├── Avatar icon
        ├── Title & amount
        └── LinearProgress bar
```

**ApexCharts Options:**
```typescript
chart: { parentHeightOffset: 0, toolbar: { show: false } }
tooltip: { enabled: false }
grid: { show: false, padding: {...} }
dataLabels: { enabled: false }
colors: [primaryColorWithOpacity]
```

**Props:** None

**Grid Size:** `{ xs: 12, md: 6 }`

---

### 5. SupportTracker

**File:** `SupportTracker.tsx`

**Type:** Client Component (Tracker/Dashboard)

**Purpose:** Support ticket and issue tracking metrics

**Key Features:**
- Support ticket counts
- Status breakdown
- Trend indicators
- Icon-based display

**Grid Size:** `{ xs: 12, md: 6 }`

---

### 6. SalesByCountries

**File:** `SalesByCountries.tsx`

**Type:** Client Component (Map/Chart)

**Purpose:** Geographic distribution of sales

**Key Features:**
- Country-level granularity
- Sales metrics per region
- Map or regional heat map
- Interactive legend

**Grid Size:** `{ xs: 12, md: 6, lg: 4 }`

---

### 7. TotalEarning

**File:** `TotalEarning.tsx`

**Type:** Client Component (KPI Card)

**Purpose:** Total earning summary

**Key Features:**
- Earning total display
- Trend indicator
- Icon-based layout
- Compact card format

**Grid Size:** `{ xs: 12, md: 6, lg: 4 }`

---

### 8. MonthlyCampaignState

**File:** `MonthlyCampaignState.tsx`

**Type:** Client Component (Chart)

**Purpose:** Monthly campaign performance tracking

**Key Features:**
- Monthly trend visualization
- Campaign metrics
- Performance indicators
- Chart-based display

**Grid Size:** `{ xs: 12, md: 6, lg: 4 }`

---

### 9. SourceVisits

**File:** `SourceVisits.tsx`

**Type:** Client Component (Chart)

**Purpose:** Traffic source analysis

**Key Features:**
- Source distribution (Direct, Organic, Referral, Campaign)
- Visit metrics per source
- Pie or bar chart visualization
- Legend display

**Grid Size:** `{ xs: 12, md: 6, lg: 4 }`

---

### 10. ProjectsTable

**File:** `ProjectsTable.tsx`

**Type:** Client Component (Table)

**Purpose:** Detailed projects listing with server data

**Key Features:**
- Data table with project information
- Server-fetched data (projectTable prop)
- Sortable/filterable columns (likely)
- Project status display
- Progress indicators

**Props:**
```typescript
interface ProjectsTableProps {
  projectTable: ProjectRow[]
}

interface ProjectRow {
  id: string
  name: string
  status: 'active' | 'completed' | 'pending'
  progress: number
  members?: User[]
  dueDate?: string
  [key: string]: unknown
}
```

**Grid Size:** `{ xs: 12, lg: 8 }`

---

## Component Dependencies

### Shared Imports (Analytics)
```typescript
'use client'                                    // Client directive
import dynamic from 'next/dynamic'              // Dynamic imports
import { useTheme } from '@mui/material/styles' // Theme access
import type { ApexOptions } from 'apexcharts'  // Chart types
```

### Chart Components
```typescript
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'
import Typography from '@mui/material/Typography'
import LinearProgress from '@mui/material/LinearProgress'
```

### Slider Components
```typescript
import { useKeenSlider } from 'keen-slider/react'
import type { KeenSliderPlugin } from 'keen-slider/react'
import AppKeenSlider from '@/libs/styles/AppKeenSlider'
```

### Utility Components
```typescript
import OptionMenu from '@core/components/option-menu'
import CustomAvatar from '@core/components/mui/Avatar'
```

---

## Theme Integration

All components use MUI theme integration:

```typescript
const theme = useTheme()

// Color tokens
theme.palette.primary.main
theme.palette.info.main
theme.palette.error.main
theme.palette.text.secondary

// Typography
theme.typography.fontFamily
theme.typography.body2.fontSize
theme.typography.h5

// Breakpoints
theme.breakpoints.values.lg
```

---

## Key Differences: Analytics vs CRM

| Aspect | Analytics | CRM |
|--------|-----------|-----|
| **Slider** | WebsiteAnalyticsSlider (carousel) | None |
| **Earning Display** | EarningReports (chart + metrics) | EarningReportsWithTabs (tabbed) |
| **Size Focus** | Varied (3-4 cols on LG) | Uniform (2-4 cols) |
| **Interactivity** | Slider carousel | Tabbed chart |
| **Data Source** | Server (getProfileData) | Static data |
| **Table** | Full-width (8 cols) | Last Transaction (6 cols) |

---

## Color Patterns (Analytics)

### Earning Categories
- **Earnings:** Primary color
- **Profit:** Info color
- **Expense:** Error (red) color

### Chart Colors
- **Primary light opacity:** Background/light bars
- **Primary main:** Highlighted/active elements
- **Theme variables:** Dynamic theming

---

## Responsive Behavior

### Slider (WebsiteAnalyticsSlider)
- XS/SM: Full width carousel
- LG: Half width (6 cols)
- Slides auto-order via CSS

### Chart Components
- XS: 12 cols (full width)
- SM: 6 cols (2 per row)
- LG: 3-4 cols (3 per row)

### Table
- XS-MD: 12 cols (full width, horizontal scroll)
- LG: 8 cols (2/3 width)

---

## Data Fetching Pattern

```typescript
// Page component (async)
const DashboardAnalytics = async () => {
  const data = await getProfileData()  // Server-side fetch

  return (
    <Grid container spacing={6}>
      {/* Slider - self-contained */}
      <Grid size={{ xs: 12, lg: 6 }}>
        <WebsiteAnalyticsSlider />
      </Grid>
      
      {/* Charts - self-contained */}
      <Grid size={{ xs: 12, lg: 8 }}>
        <EarningReports />
      </Grid>

      {/* Table - receives server data */}
      <Grid size={{ xs: 12, lg: 8 }}>
        <ProjectsTable projectTable={data?.users.profile.projectTable} />
      </Grid>
    </Grid>
  )
}
```

---

## Performance Features

1. **Dynamic Imports:** Charts load asynchronously
2. **Slider Library:** Keen Slider optimized for performance
3. **Static Data:** Most components use static series (no re-renders)
4. **Server Rendering:** Page fetches data server-side
5. **Code Splitting:** Each component separate bundle chunk

---

## Styling Patterns

### Keen Slider Classes
```css
keen-slider__slide    /* Slide container */
p-6 pbe-3            /* Padding (Tailwind) */
is-full              /* Full width (Block size) */
```

### Typography Styling
```typescript
className='font-medium text-[var(--mui-palette-common-white)]'  // White text
variant='h5'                                                    // Heading 5
className='mbe-0.5'                                            // Margin block end
```

---

## Component Checklist

Each Analytics component should include:

- [ ] Proper client component directive
- [ ] Theme integration with useTheme
- [ ] CSS variable color usage
- [ ] Card wrapper structure
- [ ] Responsive grid sizing
- [ ] TypeScript prop interfaces
- [ ] Dynamic imports (for charts)
- [ ] Memoization (if needed)
- [ ] Proper spacing/padding
- [ ] Theme color compliance

---

## Usage Example (Page Level)

```typescript
<Grid container spacing={6}>
  <Grid size={{ xs: 12, lg: 6 }}>
    <WebsiteAnalyticsSlider />
  </Grid>
  <Grid size={{ xs: 12, sm: 6, lg: 3 }}>
    <LineAreaDailySalesChart />
  </Grid>
  <Grid size={{ xs: 12, sm: 6, lg: 3 }}>
    <SalesOverview />
  </Grid>
  <Grid size={{ xs: 12, md: 6 }}>
    <EarningReports />
  </Grid>
  {/* More components... */}
  <Grid size={{ xs: 12, lg: 8 }}>
    <ProjectsTable projectTable={data?.users.profile.projectTable} />
  </Grid>
</Grid>
```

---

## File Locations

```
src/views/dashboards/analytics/
├── WebsiteAnalyticsSlider.tsx
├── LineAreaDailySalesChart.tsx
├── SalesOverview.tsx
├── EarningReports.tsx
├── SupportTracker.tsx
├── SalesByCountries.tsx
├── TotalEarning.tsx
├── MonthlyCampaignState.tsx
├── SourceVisits.tsx
└── ProjectsTable.tsx
```

---

## Related Documentation

- Analytics Dashboard: `_knowledge/pages/analytics-dashboard.md`
- Charts Catalog: `_knowledge/pages/charts-catalog.md`
- CRM Components: `_knowledge/components/crm-chart-components.md`
- Cards & Widgets: `_knowledge/pages/cards-widgets-catalog.md`
- Keen Slider Docs: [keen-slider.io](https://keen-slider.io)

---

## Summary

The Analytics Dashboard provides comprehensive business intelligence visualization with:

1. **Interactive Slider** - Website analytics carousel
2. **Multiple Chart Types** - Area, bar, pie charts
3. **Metric Displays** - Progress bars, KPI cards
4. **Data Table** - Full project listing
5. **Server Integration** - Real data fetching
6. **Responsive Design** - Mobile-first approach
7. **Theme Integration** - Dynamic color theming
8. **Performance Optimized** - Code splitting, lazy loading

