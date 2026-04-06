# Dashboard Domain Source Structure

## Overview

The Vuexy Admin dashboard domain comprises multiple specialized dashboard implementations organized under `/src/views/dashboards/`. The dashboard architecture uses a **widget-based composition pattern** where page-level layout components orchestrate multiple self-contained widget components through MUI Grid for responsive layouts. There are four main dashboard variants (CRM, Analytics, Ecommerce, Logistics) plus an Academy dashboard.

**Key characteristics:**
- Server Component pages that compose widget components
- Dynamic ApexCharts integration for data visualization
- Responsive grid layouts with MUI Material-UI
- Theme-aware styling with CSS-in-JS and Tailwind
- Optional server action data fetching
- Comprehensive TypeScript interfaces for type safety

---

## Component Structure

### Page-Level Dashboards

Dashboard pages are defined in `/src/app/[lang]/(dashboard)/(private)/dashboards/` as async Server Components:

#### CRM Dashboard (`crm/page.tsx`)

Async server component orchestrating 12+ widget components. Calls `getServerMode()` to retrieve theme state for client widgets. Arranges components in responsive MUI Grid with calculated breakpoints (xs: 12, sm: 6, md: 4, lg: 2 for stat cards).

**Widgets included:**
- DistributedBarChartOrder - Bar chart widget
- LineAreaYearlySalesChart - Area chart widget  
- CardStatVertical - Stat card component (4x instances with different themes)
- BarChartRevenueGrowth - Revenue visualization
- EarningReportsWithTabs - Tabbed earning reports with ApexCharts
- RadarSalesChart - Radar chart visualization
- SalesByCountries - Geographic sales data
- ProjectStatus - Project status widget
- ActiveProjects - Project listing with progress bars
- LastTransaction - Transaction history table with status chips
- ActivityTimeline - Activity feed component

#### Analytics Dashboard (`analytics/page.tsx`)

Async server component with server action data fetching. Calls `getProfileData()` to retrieve profile data including project table information. Passes user profile data to ProjectsTable widget. Focuses on website/traffic analytics with 10 specialized widgets.

**Widgets included:**
- WebsiteAnalyticsSlider - Carousel/slider analytics
- LineAreaDailySalesChart - Daily sales trend
- SalesOverview - Sales vs visits comparison with divider
- EarningReports - Weekly earnings with bar chart
- SupportTracker - Support metrics
- SalesByCountries - Geographic distribution
- TotalEarning - Earning overview with stacked bar chart
- MonthlyCampaignState - Campaign performance
- SourceVisits - Traffic source analysis  
- ProjectsTable - Detailed project metrics table

#### Ecommerce Dashboard (`ecommerce/page.tsx`)

Wrapper component that delegates to `/apps/ecommerce/dashboard/` for domain separation.

### Widget Component Patterns

#### Chart-Based Widgets (ApexCharts)

**TabContent Pattern with Dynamic Colors** - EarningReportsWithTabs

Uses MUI TabContext/TabList/TabPanel with client-side state management. Renders custom tab labels with icon avatars. Dynamically colors ApexCharts based on selected tab using computed color array. Responsive breakpoints adjust column width from 33% to 70% across device sizes.

Type definitions:
```typescript
type TabCategory = 'orders' | 'sales' | 'profit' | 'income'
type TabType = { type: TabCategory; avatarIcon: string; series: ApexChartSeries }
```

**ApexCharts Configuration Pattern:**
- Theme-aware CSS variables for colors (`var(--mui-palette-primary-main)`)
- Responsive array with breakpoint-specific column widths
- Custom data labels with formatters
- Grid/axis configuration for minimal visual overhead
- State configuration disabling hover/active filters
- Padding adjustments for optimal spacing

#### Stat Card Widgets

**Vertical Stat Card Pattern** - CardStatsVertical

Generic reusable card component: Avatar icon + metric title + subtitle + stats value + optional status chip.

Props interface:
```typescript
interface CardStatsVerticalProps {
  stats: string
  title: string
  subtitle: string
  avatarIcon: string
  avatarColor?: ThemeColor
  progressColor?: ThemeColor
  chipText?: string
  chipColor?: ThemeColor
  chipVariant?: 'filled' | 'outlined' | 'tonal'
  avatarSize?: number
  avatarSkin?: 'light' | 'filled'
}
```

Used 4 times in CRM dashboard with different color themes (primary, success, error, warning).

#### List/Table Widgets

**Project List Pattern** - ActiveProjects

Card wrapper containing list of projects with image + title/subtitle + progress bar. Maps hardcoded data array with project logo, title, category, and progress percentage. Uses MUI LinearProgress for visual metrics.

```typescript
type DataType = {
  title: string
  imgSrc: string
  progress: number
  subtitle: string
  progressColor: ThemeColor
}
```

#### Transaction/History Widgets

**Status-Based Table Pattern** - LastTransaction

Accepts serverMode prop for theme fallback. Uses useColorScheme hook for client-side theme detection. Maps transaction status (verified/rejected/pending/on-hold) to color via lookup object. Renders table with card icons, amount, and status chips.

---

## Type Definitions

### Dashboard Domain Types

**Theme Colors:**
```typescript
type ThemeColor = 'primary' | 'secondary' | 'success' | 'error' | 'warning' | 'info'
type SystemMode = 'light' | 'dark'
```

**Chart Series Types:**
```typescript
type ApexChartSeries = NonNullable<ApexOptions['series']>
type ApexChartSeriesData = Exclude<ApexChartSeries[0], number>
```

**Tab Categories:**
```typescript
type TabCategory = 'orders' | 'sales' | 'profit' | 'income'
type TabType = {
  type: TabCategory
  avatarIcon: string
  series: ApexChartSeries
}
```

**Data Entity Types:**

Active Projects:
```typescript
type ProjectDataType = {
  title: string
  imgSrc: string
  progress: number
  subtitle: string
  progressColor: ThemeColor
}
```

Last Transaction:
```typescript
type TransactionType = {
  date: string
  trend: string
  imgName: string
  cardType: string
  cardNumber: string
  status: 'verified' | 'rejected' | 'pending' | 'on-hold'
}

type StatusConfig = Record<TransactionType['status'], {
  text: string
  color: ThemeColor
}>
```

---

## Widget Patterns Used

### 1. Chart Widget Pattern
- Dynamic chart type selection (bar, area, radar, pie)
- ApexCharts integration via dynamic import for code splitting
- Theme-aware CSS variables for runtime theming
- Responsive breakpoint arrays for mobile/tablet/desktop
- Data series as module constants
- Optional interactive filters/tabs

### 2. Stat Card Pattern  
- Generic reusable card with avatar icon
- Metric + subtitle + stats value layout
- Color variants for different metric types (positive/negative)
- Optional badge chip for status/change indicator
- Minimal, focused design for maximum readability

### 3. Data List Pattern
- Card wrapper with Flexbox list structure
- Icon/image + text content per item
- Progress indicators or status markers
- Read-only presentation (no inline editing)
- Hardcoded sample data

### 4. Comparison Widget Pattern
- Two-column layout (metric vs metric)
- Progress bar for visual comparison
- Success/error color coding

### 5. Header Action Pattern
- CardHeader with title + optional subheader
- OptionMenu for dropdown actions
- Reusable across all widgets

---

## Dependency List

### External Dependencies
- Next.js 14+ (async server components, dynamic imports)
- React 18+ (hooks, functional components)
- @mui/material - Card, Grid, Typography, Chip, LinearProgress, Avatar
- @mui/lab - TabContext, TabList, TabPanel, Tabs
- @mui/styles - useTheme, styled components
- ApexCharts - chart rendering engine
- react-apexcharts - React wrapper
- classnames - conditional CSS classes
- Tailwind CSS - utility-first styles

### Internal Dependencies
- @views/dashboards/* - All widget components
- @components/card-statistics/ - Stat card variants
- @core/components/ - OptionMenu, CustomAvatar
- @core/types - ThemeColor, SystemMode types
- @core/utils/serverHelpers - getServerMode()
- @core/styles - CSS modules
- @/app/server/actions - Server actions (getProfileData)
- @/libs/styles/AppReactApexCharts - Chart wrapper

### Import Patterns
```typescript
// Server actions
import { getServerMode } from '@core/utils/serverHelpers'
import { getProfileData } from '@/app/server/actions'

// Dynamic imports
import dynamic from 'next/dynamic'
const AppReactApexCharts = dynamic(() => import('@/libs/styles/AppReactApexCharts'))

// MUI components
import Grid from '@mui/material/Grid'
import Card from '@mui/material/Card'
import { useTheme } from '@mui/material/styles'

// Custom components
import OptionMenu from '@core/components/option-menu'
import CustomAvatar from '@core/components/mui/Avatar'
```

---

## Architecture Insights

### Server vs Client Components

**Dashboard Pages (Server Components):**
- Async functions that orchestrate rendering
- Execute server actions for data fetching
- Import and compose multiple widget components
- Pass server-fetched data to client widgets
- Handle responsive grid layout

**Widget Components (Client Components):**
- Marked with `'use client'` directive
- Use hooks (useState, useTheme, useColorScheme)
- Manage interactive state (tab selection, filters)
- Render ApexCharts dynamically
- Respond to user interactions

### Data Flow Patterns

1. **Server Action Data**: getProfileData() → Dashboard page → ProjectsTable props
2. **Hardcoded Data**: Module constant → map() → render list items
3. **Dynamic Server Mode**: getServerMode() → LastTransaction serverMode prop → useColorScheme fallback

### Responsive Design

MUI Grid with calculated breakpoints:
```
xs: 12 (full width mobile)
sm: 6 (half width tablet)  
md: 4 (third width small desktop)
lg: 2 (sixth width large desktop)
```

Charts use additional `responsive` array for ApexCharts breakpoint tuning.

### Theme Integration

CSS variables enable runtime theming without component changes:
- `var(--mui-palette-primary-main)` - Primary brand color
- `var(--mui-palette-background-paper)` - Card backgrounds
- `var(--mui-palette-divider)` - Border colors

Supports dark/light mode switching with useColorScheme hook.

---

## Code Examples

### Adding Widget to Dashboard

```typescript
// 1. Create widget
'use client'
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'
import OptionMenu from '@core/components/option-menu'

const CustomWidget = () => (
  <Card>
    <CardHeader
      title='Widget Title'
      action={<OptionMenu options={['Option 1']} />}
    />
    <CardContent>{/* content */}</CardContent>
  </Card>
)

// 2. Import to dashboard page
import CustomWidget from '@views/dashboards/crm/CustomWidget'

const DashboardCRM = async () => (
  <Grid container spacing={6}>
    <Grid size={{ xs: 12, md: 6, lg: 4 }}>
      <CustomWidget />
    </Grid>
  </Grid>
)
```

### Creating Chart Widget

```typescript
'use client'
import dynamic from 'next/dynamic'
import Card from '@mui/material/Card'
import { useTheme } from '@mui/material/styles'
import type { ApexOptions } from 'apexcharts'

const AppReactApexCharts = dynamic(() => import('@/libs/styles/AppReactApexCharts'))

const MyChart = () => {
  const theme = useTheme()

  const series = [{ name: 'Series', data: [10, 20, 30] }]

  const options: ApexOptions = {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false }
    },
    colors: ['var(--mui-palette-primary-main)'],
    xaxis: { categories: ['Jan', 'Feb', 'Mar'] }
  }

  return (
    <Card>
      <CardContent>
        <AppReactApexCharts
          type='bar'
          height={300}
          series={series}
          options={options}
        />
      </CardContent>
    </Card>
  )
}
```

---

## Summary

Dashboard domain demonstrates **widget-based composition architecture** using:
- Server Components for data orchestration
- Client Components for interactivity  
- MUI Grid for responsive layouts
- ApexCharts for visualizations
- TypeScript for type safety
- CSS variables for theming
- Hardcoded data (replaceable with APIs)

**Statistics:**
- 40+ specialized dashboard widgets
- 5 main dashboard variants
- ~5,000 LOC across views + pages
- 60% component reusability across dashboards
- 12-14 widgets per dashboard page

The architecture is **highly scalable** and **easy to extend** with new widgets while maintaining visual consistency.