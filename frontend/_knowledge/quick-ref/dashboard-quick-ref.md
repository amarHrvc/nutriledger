# Dashboard Quick Reference

> Fast lookup guide for dashboard components, patterns, and implementations.

---

## Q&A

### How to Add a Chart?

1. **Create chart component** in `views/dashboards/{dashboard-type}/` as client component
2. **Import AppReactApexCharts** via dynamic import for code splitting
3. **Define series data** as static constant or prop
4. **Configure ApexOptions** with MUI theme colors (`var(--mui-palette-primary-main)`)
5. **Wrap in Card** with CardHeader and CardContent
6. **Add to dashboard page** and place in Grid with size props

**Example:**
```typescript
'use client'
import dynamic from 'next/dynamic'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import { useTheme } from '@mui/material/styles'
import type { ApexOptions } from 'apexcharts'

const AppReactApexCharts = dynamic(
  () => import('@/libs/styles/AppReactApexCharts'),
  { ssr: false }
)

const MyChart = () => {
  const theme = useTheme()
  const series = [{ name: 'Sales', data: [30, 40, 35, 50, 49, 60, 70] }]
  const options: ApexOptions = {
    chart: { toolbar: { show: false }, parentHeightOffset: 0 },
    colors: ['var(--mui-palette-primary-main)'],
    xaxis: { categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] }
  }
  return (
    <Card>
      <CardContent>
        <AppReactApexCharts type='bar' height={300} series={series} options={options} />
      </CardContent>
    </Card>
  )
}
export default MyChart
```

### How to Add a Stat Card?

1. **Import CardStatsVertical** or appropriate variant from `components/card-statistics/`
2. **Pass required props** (stats, title, subtitle, avatarIcon, colors)
3. **Place in Grid** with responsive size props
4. **Add to dashboard page** in Grid container

**Example:**
```typescript
import CardStatsVertical from '@components/card-statistics/Vertical'

<Grid size={{ xs: 12, sm: 6, md: 4, lg: 2 }}>
  <CardStatsVertical
    title='Total Sales'
    subtitle='Last Week'
    stats='24.67k'
    avatarIcon='tabler-currency-dollar'
    avatarColor='success'
    chipText='+24.67%'
    chipColor='success'
    chipVariant='tonal'
  />
</Grid>
```

### 5 Dashboard Types

| Dashboard | Location | Focus | Widgets | Key Feature |
|-----------|----------|-------|---------|-------------|
| **CRM** | `crm/page.tsx` | Sales & Customer Mgmt | 12+ | 4 KPI Cards, Revenue Chart |
| **Analytics** | `analytics/page.tsx` | Website & Traffic | 10 | Server-data ProjectsTable |
| **Ecommerce** | `ecommerce/page.tsx` | Products & Orders | 12+ | Product Rankings, Orders |
| **Logistics** | `logistics/page.tsx` | Fleet & Shipments | 10+ | Vehicle Overview, Delivery |
| **Academy** | `academy/page.tsx` | Learning & Progress | 8+ | Student Metrics, Topics |

---

## Types Reference

### ChartType (ApexCharts)

```typescript
type ChartType = 
  | 'area'      // Area/line charts with gradient fill
  | 'bar'       // Vertical bar charts (default)
  | 'column'    // Column charts
  | 'line'      // Line charts
  | 'pie'       // Pie charts
  | 'donut'     // Donut charts (pie with hole)
  | 'radar'     // Radar/polygon charts
  | 'radialBar' // Circular bar charts

// Series format
type ApexChartSeries = Array<{
  name?: string
  data: number[] | Array<{ x: string; y: number }>
}>

// Options type (from apexcharts)
type ApexOptions = {
  chart?: { toolbar?: { show: boolean }; ... }
  colors?: string[]
  xaxis?: { categories?: string[]; ... }
  responsive?: Array<{ breakpoint: number; options: ApexOptions }>
}
```

### WidgetType (Card Variants)

```typescript
type WidgetType =
  | 'CardStatsVertical'              // Icon top, vertical layout
  | 'CardStatsHorizontal'            // Icon right, compact
  | 'CardStatsHorizontalWithBorder'  // Border on hover
  | 'CardStatsHorizontalWithSubtitle' // Extended info
  | 'CardStatsSquare'                // Square aspect ratio
  | 'StatsWithAreaChart'             // Stat + mini chart
  | 'CustomerStats'                  // Customer-focused

type WidgetColor =
  | 'primary' | 'secondary' | 'success' | 'error' | 'warning' | 'info'

interface CardStatsVerticalProps {
  stats: string                  // e.g., '1.28k'
  title: string                  // 'Total Profit'
  subtitle: string               // 'Last Week'
  avatarIcon: string             // 'tabler-credit-card'
  avatarColor: WidgetColor
  avatarSize?: number            // 44 | 32 | 28
  avatarSkin?: 'light' | 'dark'  // Background style
  chipText: string               // '+24.67%'
  chipColor: WidgetColor
  chipVariant?: 'tonal' | 'outlined' | 'filled'
}
```

---

## Pattern Map

### Chart Selection Pattern

**When to use each chart type:**

```typescript
// Sales Trend → Line or Area Chart
<AppReactApexCharts type='area' series={monthlySales} />

// Weekly Distribution → Bar Chart
<AppReactApexCharts type='bar' series={weeklyData} />

// Category Comparison → Radar Chart
<AppReactApexCharts type='radar' series={categoryData} />

// Market Share → Donut Chart
<AppReactApexCharts type='donut' series={[32, 41, 41, 70]} />

// Performance KPI → Radial Bar Chart
<AppReactApexCharts type='radialBar' series={performanceData} />
```

### ApexCharts Setup Pattern

**Standard configuration with theme colors:**

```typescript
'use client'
import dynamic from 'next/dynamic'
import { useTheme } from '@mui/material/styles'
import type { ApexOptions } from 'apexcharts'

const AppReactApexCharts = dynamic(
  () => import('@/libs/styles/AppReactApexCharts'),
  { ssr: false }
)

const ChartComponent = () => {
  const theme = useTheme()
  const series = [{ name: 'Series 1', data: [30, 40, 35, 50, 49, 60, 70] }]
  const options: ApexOptions = {
    chart: {
      toolbar: { show: false },
      parentHeightOffset: 0,
      sparkline: { enabled: false }
    },
    colors: ['var(--mui-palette-primary-main)'],
    dataLabels: { enabled: false },
    grid: { show: true, borderColor: 'var(--mui-palette-divider)' },
    stroke: { curve: 'smooth' },
    xaxis: {
      categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
      labels: { style: { fontSize: '12px' } }
    },
    responsive: [
      { breakpoint: 1200, options: { chart: { height: 280 } } },
      { breakpoint: 600, options: { chart: { height: 200 } } }
    ]
  }

  return (
    <AppReactApexCharts type='bar' height={400} series={series} options={options} />
  )
}
export default ChartComponent
```

### Widget Card Pattern

**Reusable card structure:**

```typescript
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CustomAvatar from '@core/components/mui/Avatar'
import type { WidgetColor } from '@/types/pages/widgetTypes'

interface WidgetCardProps {
  title: string
  stats: string | number
  avatarIcon: string
  avatarColor?: WidgetColor
  chipText?: string
}

const WidgetCard = ({
  title, stats, avatarIcon, avatarColor = 'primary', chipText
}: WidgetCardProps) => {
  return (
    <Card>
      <CardContent className='flex flex-col gap-y-3'>
        <div className='flex items-center justify-between'>
          <div>
            <Typography variant='h4'>{stats}</Typography>
            {chipText && <Chip label={chipText} color={avatarColor} size='small' />}
          </div>
          <CustomAvatar skin='light' color={avatarColor} className={avatarIcon} />
        </div>
      </CardContent>
    </Card>
  )
}
export default WidgetCard
```

---

## Key Imports

```typescript
// MUI Components
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'
import Grid from '@mui/material/Grid'
import Typography from '@mui/material/Typography'
import Chip from '@mui/material/Chip'
import { useTheme } from '@mui/material/styles'

// Custom Components
import CustomAvatar from '@core/components/mui/Avatar'
import CardStatsVertical from '@components/card-statistics/Vertical'

// Utilities
import { getServerMode } from '@core/utils/serverHelpers'
import { getProfileData } from '@/app/server/actions'

// ApexCharts
import dynamic from 'next/dynamic'
import type { ApexOptions } from 'apexcharts'
const AppReactApexCharts = dynamic(
  () => import('@/libs/styles/AppReactApexCharts')
)
```

---

## Common Props & Patterns

### Grid Breakpoints (MUI Grid v2)
- **xs: 12** = Full width (mobile)
- **sm: 6** = Half width (tablet)
- **md: 4** = 1/3 width (mid)
- **lg: 2** = 1/6 width (desktop)

### Avatar Configuration
- **skin:** 'light' (light bg) | 'dark' (dark bg)
- **color:** 'primary' | 'success' | 'error' | 'warning' | 'info'
- **icon:** 'tabler-credit-card' | 'tabler-currency-dollar' | 'tabler-users'

### Chip Variants
- **variant:** 'tonal' (filled, low contrast) | 'outlined' | 'filled'
- **size:** 'small' | 'medium'
- **color:** 'success' (green) | 'error' (red) | 'warning' (orange)

---

## CSS Variables (Theme Colors)

```typescript
'var(--mui-palette-primary-main)'       // Brand color
'var(--mui-palette-success-main)'       // Green
'var(--mui-palette-error-main)'         // Red
'var(--mui-palette-warning-main)'       // Orange
'var(--mui-palette-info-main)'          // Blue
'var(--mui-palette-background-paper)'   // Card background
'var(--mui-palette-divider)'            // Border color
```

---

## Performance Tips

1. **Dynamic Imports** → All charts use `dynamic()` for code splitting
2. **React.memo** → Wrap components to prevent re-renders
3. **Static Data** → Use module constants for chart series
4. **Responsive Config** → ApexCharts handles mobile sizing
5. **Server Components** → Fetch data server-side for better LCP
6. **Lazy Loading** → Widgets load only when rendered

---

**Version:** Vuexy Admin v10.11.1 | **Dashboards:** 5 | **Widgets:** 50+ | **Updated:** 2025
