# Analytics Dashboard Page

## Overview

The Analytics Dashboard is a comprehensive server-side rendered page component that provides detailed insights into website performance, sales metrics, and business analytics. It combines multiple visualization types including sliders, charts, tables, and KPI cards to deliver a complete analytical overview.

**Key Path:** `src/app/[lang]/(dashboard)/(private)/dashboards/analytics/page.tsx`

**Type:** Async Server Component with Server Action Integration

**Data Source:** Server-side fetched via `getProfileData()` server action

---

## Key Features

### Dashboard Composition

1. **Website Analytics Slider** - Interactive carousel/slider for top metrics
2. **Daily Sales Chart** - Line/area chart for daily sales trends
3. **Sales Overview** - Summary statistics and overview card
4. **Earning Reports** - Detailed earning breakdown and analysis
5. **Support Tracker** - Support ticket and issue tracking metrics
6. **Sales by Countries** - Geographic sales distribution
7. **Total Earning** - Total earnings KPI and trends
8. **Monthly Campaign State** - Campaign performance tracking
9. **Source Visits** - Traffic source analytics
10. **Projects Table** - Detailed projects listing with filtering

### Responsive Grid Layout

- **6-column responsive grid** with MUI Grid v2
- **Dual-width layout** for various component sizes
- **Dynamic table data** from server-side fetch
- **Consistent 6-unit spacing** throughout

---

## Page Layout

The analytics dashboard uses a multi-tier responsive layout:

**Desktop Layout (LG+):**
- Row 1: 1x Analytics slider (6 cols) + 2x Charts (3 cols each)
- Row 2: 1x Large chart (6 cols) + 1x Tracker (6 cols)
- Row 3: 4x Small cards (3 cols each)
- Row 4: 1x Full-width table (8 cols)

**Tablet Layout (MD):**
- Row 1: 1x Analytics slider (12 cols)
- Row 2: 2x Sales charts (6 cols each)
- Row 3: 2x Earning/Support (6 cols each)
- Row 4: 3x Cards (4 cols each)
- Row 5: 1x Full-width table (12 cols)

**Mobile Layout (XS/SM):**
- All components stacked full-width (12 cols)

---

## Components Used

### Slider/Hero Components
- `WebsiteAnalyticsSlider` - Interactive metrics carousel with navigation

### Chart Components
- `LineAreaDailySalesChart` - Daily sales trend visualization
- `SalesOverview` - Sales summary overview
- `EarningReports` - Earning breakdown and analysis
- `SupportTracker` - Support metrics tracker
- `SalesByCountries` - Geographic sales heatmap/chart
- `TotalEarning` - Total earning KPI card
- `MonthlyCampaignState` - Campaign performance chart
- `SourceVisits` - Traffic source distribution

### Table Components
- `ProjectsTable` - Projects listing table with server-fetched data

---

## Type Definitions

```typescript
// Analytics Dashboard - Main Page Component
const DashboardAnalytics = async (): Promise<React.ReactNode>

// Server-fetched profile data structure
interface ProfileData {
  users: {
    profile: {
      projectTable: ProjectRow[]
      // Other profile properties
    }
  }
}

// Project table row structure
interface ProjectRow {
  id: string
  name: string
  status: 'active' | 'completed' | 'pending'
  progress: number
  members?: User[]
  dueDate?: string
  budget?: number
  [key: string]: unknown
}

interface User {
  id: string
  name: string
  avatar?: string
}

// Component props
interface ProjectsTableProps {
  projectTable: ProjectRow[]
}

// Grid sizing
type GridBreakpoint = {
  xs?: number | 'auto'
  sm?: number | 'auto'
  md?: number | 'auto'
  lg?: number | 'auto'
  xl?: number | 'auto'
}
```

---

## Component Breakdown

### Row 1: Top-Level Analytics
- **WebsiteAnalyticsSlider** (lg: 6) - Interactive carousel for key metrics
- **LineAreaDailySalesChart** (sm: 6, lg: 3) - Daily sales trends
- **SalesOverview** (sm: 6, lg: 3) - Sales summary KPI

### Row 2: Detailed Analytics
- **EarningReports** (md: 6) - Earning breakdown
- **SupportTracker** (md: 6) - Support metrics and tickets

### Row 3: Regional & Categorical Analysis
- **SalesByCountries** (md: 6, lg: 4) - Geographic sales distribution
- **TotalEarning** (md: 6, lg: 4) - Total earning metrics
- **MonthlyCampaignState** (md: 6, lg: 4) - Campaign performance
- **SourceVisits** (md: 6, lg: 4) - Traffic source analysis

### Row 4: Data Tables
- **ProjectsTable** (lg: 8) - Full projects listing with server data

---

## Usage Example

```typescript
import Grid from '@mui/material/Grid'
import { getProfileData } from '@/app/server/actions'
import WebsiteAnalyticsSlider from '@views/dashboards/analytics/WebsiteAnalyticsSlider'
// ... import other components

const DashboardAnalytics = async () => {
  // Fetch data server-side
  const data = await getProfileData()

  return (
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
      {/* More Grid components */}
      <Grid size={{ xs: 12, lg: 8 }}>
        <ProjectsTable projectTable={data?.users.profile.projectTable} />
      </Grid>
    </Grid>
  )
}

export default DashboardAnalytics
```

---

## Key Implementation Notes

1. **Async Server Component** - Uses getProfileData() server action
2. **Server-Side Data Fetching** - Static data from fake-db or API
3. **Dynamic Table Data** - ProjectsTable receives data prop
4. **MUI Grid v2** - Responsive size prop for all components
5. **Large Layout Variation** - More diverse component sizes than CRM
6. **No Client-Side API Calls** - All data fetched server-side
7. **Mobile-Optimized** - Proper responsive breakpoints
8. **Component Independence** - Each chart self-contained

---

## File Structure

```
src/
├── app/[lang]/(dashboard)/(private)/dashboards/
│   └── analytics/
│       └── page.tsx (THIS FILE)
├── views/dashboards/analytics/
│   ├── WebsiteAnalyticsSlider.tsx
│   ├── LineAreaDailySalesChart.tsx
│   ├── SalesOverview.tsx
│   ├── EarningReports.tsx
│   ├── SupportTracker.tsx
│   ├── SalesByCountries.tsx
│   ├── TotalEarning.tsx
│   ├── MonthlyCampaignState.tsx
│   ├── SourceVisits.tsx
│   └── ProjectsTable.tsx
├── app/server/
│   └── actions.ts (getProfileData function)
└── fake-db/
    └── analytics/...
```

---

## Data Flow

1. Page component is async
2. Calls `getProfileData()` server action
3. Server action fetches from fake-db (or API endpoint)
4. Data includes projectTable array
5. Data passed to ProjectsTable component
6. All other components are self-contained

---

## Responsive Behavior

### Component Sizing Strategy

**WebsiteAnalyticsSlider**
- XS/SM: 12 cols (full width)
- LG: 6 cols (half width)

**LineAreaDailySalesChart & SalesOverview**
- XS/SM: 12 cols each (stacked)
- SM: 6 cols (side by side)
- LG: 3 cols (1/4 width each)

**Middle Charts (Earning, Support)**
- XS: 12 cols (stacked)
- MD: 6 cols (side by side)
- LG: 6 cols (continues)

**Regional/Categorical Cards**
- XS: 12 cols (full width)
- MD: 6 cols (2 per row)
- LG: 4 cols (3 per row, 4 total = wraps to 2 rows)

**ProjectsTable**
- XS-MD: 12 cols (full width)
- LG: 8 cols (2/3 width)

---

## Analytics Components

### WebsiteAnalyticsSlider
- Interactive carousel/slider
- Shows key performance metrics
- Likely has prev/next navigation
- Auto-rotation possible

### Chart Components
- ApexCharts-based visualizations
- Responsive sizing
- Theme-aware colors
- Interactive tooltips

### ProjectsTable
- Data-driven from server
- Likely sortable/filterable
- Displays project metadata
- Server-side data handling

---

## Performance Optimizations

1. **Server-Side Rendering** - All data fetched on server
2. **No Client-Side Data Fetching** - Improves initial load
3. **Dynamic Imports** - Charts loaded via dynamic import
4. **Memoization** - Components wrapped with React.memo
5. **Table Virtualization** - ProjectsTable may use virtualization

---

## Accessibility Features

- Semantic HTML structure
- Chart ARIA labels
- Table with proper headers
- Color contrast compliance
- Keyboard navigation
- Screen reader support
- Responsive mobile design

