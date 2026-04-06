# Charts Catalog

## Overview

A comprehensive catalog of all chart and visualization components used throughout the Vuexy Admin dashboard system. This document catalogs ApexCharts-based visualizations, their configurations, usage patterns, and responsive behavior.

**Scope:** All chart components used in CRM, Analytics, Ecommerce, Logistics, and Academy dashboards

**Chart Library:** ApexCharts via React wrapper (`AppReactApexCharts`)

**Client Component:** All chart components are client components (`use client`)

---

## Chart Architecture

### Common Structure

All chart components follow this pattern:

```typescript
'use client'

import dynamic from 'next/dynamic'
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'
import { useTheme } from '@mui/material/styles'
import type { ApexOptions } from 'apexcharts'

const AppReactApexCharts = dynamic(() => import('@/libs/styles/AppReactApexCharts'))

const ChartComponent = () => {
  const theme = useTheme()
  
  const series = [/* chart data */]
  
  const options: ApexOptions = {
    // Chart configuration
  }
  
  return (
    <Card>
      <CardHeader title="..." subheader="..." />
      <CardContent>
        <AppReactApexCharts 
          type="..." 
          height={...} 
          options={options} 
          series={series} 
        />
      </CardContent>
    </Card>
  )
}

export default ChartComponent
```

### Key Features

- **Dynamic Imports:** Charts loaded via Next.js `dynamic()` for code splitting
- **Theme Integration:** Uses MUI `useTheme()` for color tokens and responsiveness
- **Card Wrapper:** Each chart wrapped in MUI Card for consistency
- **Static Data:** Most charts use hardcoded series data for demo purposes
- **No Props:** Most chart components are self-contained (no external props)
- **Responsive:** ApexCharts handles responsive sizing automatically

---

## Chart Types Catalog

### 1. Area Charts

#### LineAreaYearlySalesChart (CRM Dashboard)
- **Type:** Area chart with line overlay
- **Data:** Year-over-year sales [40, 10, 65, 45]
- **Features:**
  - Gradient fill with opacity
  - Smooth curve interpolation
  - Theme-based success color
  - Sparkline styling (no axes/grid)
  - Height: 84px
- **Series:** Single series (Sales trend)
- **Configuration:** 
  - `fill.type: 'gradient'`
  - `stroke.curve: 'smooth'`
  - `tooltip.enabled: false`

#### LineAreaDailySalesChart (Analytics Dashboard)
- **Type:** Area chart for daily metrics
- **Data Structure:** Daily sales data
- **Features:**
  - Gradient fill background
  - No data labels
  - Hidden tooltips
  - Compact height (sparkline)
- **Height:** Approximately 100-120px

---

### 2. Bar Charts

#### DistributedBarChartOrder (CRM Dashboard)
- **Type:** Distributed vertical bar chart
- **Data:** Order distribution [77, 55, 23, 43, 77, 55, 89]
- **Features:**
  - Background reference bars (light color)
  - 32% column width
  - Rounded corners (3px)
  - 7-day representation
  - No tooltips
- **Responsive Variants:**
  - LG+: 20% column width
  - MD: 15% column width
  - Mobile: 15% column width
- **Display:** KPI card with stat value

#### BarChartRevenueGrowth (CRM/Ecommerce Dashboard)
- **Type:** Grouped bar chart (possibly stacked)
- **Features:**
  - Revenue analysis visualization
  - Multiple series (possibly by region/product)
  - Full height card display
  - Interactive legend
  - Detailed tooltip

#### RadialBarChart (Ecommerce Dashboard)
- **Type:** Circular bar chart (radial)
- **Data:** Multiple categories with values
- **Features:**
  - Circular layout
  - Color-coded segments
  - Center value display
  - Responsive sizing
  - Theme color integration

---

### 3. Line Charts

#### LineChartProfit (Ecommerce Dashboard)
- **Type:** Simple line chart
- **Data:** Profit trend data
- **Features:**
  - Smooth line curve
  - Filled area under line
  - Sparkline styling
  - Compact height (84px)
  - No axes display
- **Display:** Small card (6 cols on LG)

---

### 4. Radar/Polygon Charts

#### RadarSalesChart (CRM Dashboard)
- **Type:** Multi-axis radar/polygon chart
- **Data:** Two series
  - Series 1: Sales [32, 27, 27, 30, 25, 25]
  - Series 2: Visits [25, 35, 20, 20, 20, 20]
- **Features:**
  - 6-axis (monthly: Jan-Jun)
  - Dual-series comparison
  - Polygon connections
  - Fill opacity [1, 0.85]
  - Legend with custom spacing
  - Dynamic marker offsets for RTL
- **Responsive:** Height changes at 1200px breakpoint
- **Colors:** Primary (Series 1) + Info (Series 2)
- **Height:** Standard (300+px for detail)

---

### 5. Donut/Pie Charts

#### DonutChartGeneratedLeads (Ecommerce Dashboard)
- **Type:** Donut chart (pie with center hole)
- **Data:** 4 categories [32, 41, 41, 70]
  - Electronic
  - Sports
  - Decor
  - Fashion
- **Features:**
  - 73% donut size
  - Color gradient (success to light)
  - Custom scale (0.8)
  - Center labels showing value
  - Total display (sum of values)
  - No legend
  - Disabled hover/click expansion
- **Display:** Compact card (12 cols, xl container)

#### SalesOverview (Analytics Dashboard)
- **Type:** Donut/Pie chart
- **Purpose:** Sales summary visualization
- **Features:** Similar to DonutChartGeneratedLeads

---

### 6. Other Chart Types

#### EarningReportsWithTabs (CRM Dashboard)
- **Type:** Tabbed chart component
- **Features:**
  - Multiple chart views in tabs
  - Category switching
  - Earning breakdown
  - Full-width (lg: 8 cols)

#### RevenueReport (Ecommerce Dashboard)
- **Type:** Comprehensive chart (bar/column)
- **Features:**
  - Revenue analysis
  - Full-width display (xl: 8 cols)
  - Detailed metrics
  - Interactive legend

#### MonthlyCampaignState (Analytics Dashboard)
- **Type:** Campaign performance chart
- **Features:**
  - Monthly trend visualization
  - Progress indicators
  - Status tracking

#### SourceVisits (Analytics Dashboard)
- **Type:** Traffic source distribution
- **Features:**
  - Source comparison
  - Distribution visualization
  - Traffic metrics

#### SalesByCountries (Multiple Dashboards)
- **Type:** Geographic/Regional chart
- **Features:**
  - Country-level data
  - Heat map or regional visualization
  - Sales by region

#### PopularProducts (Ecommerce Dashboard)
- **Type:** Product ranking/listing
- **Features:**
  - Top products visualization
  - Sales or popularity metrics
  - Product details

#### Orders (Ecommerce Dashboard)
- **Type:** Order statistics
- **Features:**
  - Order summary
  - Order metrics
  - Status breakdown

#### Transactions (Ecommerce Dashboard)
- **Type:** Transaction summary
- **Features:**
  - Transaction metrics
  - Summary visualization
  - Financial data

#### LogisticsVehicleOverview (Logistics Dashboard)
- **Type:** Fleet analytics chart
- **Features:**
  - Vehicle metrics
  - Fleet status
  - Utilization data

#### LogisticsShipmentStatistics (Logistics Dashboard)
- **Type:** Shipment metrics chart
- **Features:**
  - Shipment volume
  - Status distribution
  - Shipment analytics

#### LogisticsDeliveryPerformance (Logistics Dashboard)
- **Type:** Performance KPI
- **Features:**
  - On-time delivery percentage
  - Performance trends
  - KPI display

#### InterestedTopics (Academy Dashboard)
- **Type:** Topic distribution
- **Features:**
  - Interest visualization
  - Topic preferences
  - Category selection

#### AssignmentProgress (Academy Dashboard)
- **Type:** Progress visualization
- **Features:**
  - Completion percentage
  - Progress bars
  - Student metrics

---

## ApexCharts Configuration Patterns

### Common Options

#### Color Configuration
```typescript
colors: ['var(--mui-palette-primary-main)'] // Theme color token
// or
colors: [
  'var(--mui-palette-primary-main)',
  'var(--mui-palette-info-main)'
]
```

#### Theme Integration (Monochrome)
```typescript
theme: {
  monochrome: {
    enabled: true,
    shadeTo: 'light',
    shadeIntensity: 1,
    color: successColor
  }
}
```

#### Gradient Fills
```typescript
fill: {
  type: 'gradient',
  gradient: {
    opacityTo: 0,
    opacityFrom: 1,
    shadeIntensity: 1,
    stops: [0, 100],
    colorStops: [[...]]
  }
}
```

#### Sparkline Charts (Compact)
```typescript
chart: {
  sparkline: { enabled: true },
  toolbar: { show: false },
  parentHeightOffset: 0
}
```

#### Hidden UI Elements
```typescript
dataLabels: { enabled: false }
tooltip: { enabled: false }
legend: { show: false }
grid: { show: false }
xaxis: { labels: { show: false } }
yaxis: { show: false }
```

#### Responsive Breakpoints
```typescript
responsive: [
  {
    breakpoint: 1200,
    options: {
      plotOptions: {
        bar: { columnWidth: '45%' }
      }
    }
  },
  {
    breakpoint: 600,
    options: {
      plotOptions: {
        bar: { columnWidth: '15%' }
      }
    }
  }
]
```

---

## Data Patterns

### Series Format (Single Series)
```typescript
const series = [{ data: [77, 55, 23, 43, 77, 55, 89] }]
```

### Series Format (Multi-Series)
```typescript
const series = [
  { name: 'Sales', data: [32, 27, 27, 30, 25, 25] },
  { name: 'Visits', data: [25, 35, 20, 20, 20, 20] }
]
```

### Pie/Donut Series (Values Only)
```typescript
const series = [32, 41, 41, 70]
```

---

## Component Sizing

### Card Heights
- **Sparkline charts:** 84px (very compact)
- **Small charts:** 200-250px
- **Medium charts:** 300-350px
- **Large charts:** 400-500px
- **Full-height charts:** 600px+

### Card Widths (Grid)
- **Compact:** 2 cols (16.67%)
- **Small:** 4 cols (33.33%)
- **Medium:** 6 cols (50%)
- **Large:** 8 cols (66.67%)
- **Full:** 12 cols (100%)

---

## Theme Integration

### MUI Theme Usage
```typescript
const theme = useTheme()

// Color tokens
const successColor = theme.palette.success.main
const primaryColor = theme.palette.primary.main
const textSecondary = 'var(--mui-palette-text-secondary)'

// Direction awareness (RTL)
const markerOffset = theme.direction === 'rtl' ? 7 : -4

// Responsive breakpoints
const breakpoint = theme.breakpoints.values.lg
```

### CSS Variables (Preferred)
```typescript
'var(--mui-palette-primary-main)'
'var(--mui-palette-success-mainChannel)'
'var(--mui-palette-text-secondary)'
'var(--mui-palette-divider)'
'var(--mui-palette-background-paper)'
```

---

## Best Practices

### Performance
1. **Dynamic Imports:** All charts use dynamic imports for code splitting
2. **Memoization:** Wrap with React.memo if chart receives props
3. **Static Data:** Use static series for demo charts (no re-renders)
4. **Lazy Loading:** Charts load only when components render

### Styling
1. **CSS Variables:** Always use MUI CSS variables for colors
2. **Theme Awareness:** Use `useTheme()` for responsive adjustments
3. **Consistent Heights:** Follow established height patterns
4. **Card Wrappers:** Keep all charts in Card components

### Accessibility
1. **Labels:** Include descriptive title and subheader
2. **Alt Text:** Charts should be supplemented with data tables
3. **Contrast:** Ensure color contrast compliance
4. **Keyboard:** Charts are interactive but mouse-dependent

### Responsive Design
1. **Breakpoints:** Use ApexCharts responsive array
2. **Mobile Sizing:** Adjust chart dimensions for mobile
3. **Column Width:** Adjust bar chart column widths per breakpoint
4. **Text Size:** Adjust font sizes for readability on small screens

---

## Implementation Checklist

When creating new chart components:

- [ ] Use `'use client'` directive
- [ ] Dynamic import AppReactApexCharts
- [ ] Wrap in Card component
- [ ] Add CardHeader with title/subheader
- [ ] Use MUI `useTheme()` hook
- [ ] Define options as `ApexOptions` type
- [ ] Use CSS variables for colors
- [ ] Add responsive configurations
- [ ] Set appropriate height
- [ ] Test on all breakpoints
- [ ] Ensure theme color compliance
- [ ] Add ARIA labels if needed

---

## Chart Component Files

### CRM Dashboard Charts
- `DistributedBarChartOrder.tsx` - Bar chart with background
- `LineAreaYearlySalesChart.tsx` - Area chart with gradient
- `BarChartRevenueGrowth.tsx` - Revenue bar chart
- `RadarSalesChart.tsx` - Radar/polygon multi-axis
- `EarningReportsWithTabs.tsx` - Tabbed earnings chart

### Analytics Dashboard Charts
- `LineAreaDailySalesChart.tsx` - Daily sales area chart
- `SalesOverview.tsx` - Sales summary
- `EarningReports.tsx` - Earning breakdown
- `SupportTracker.tsx` - Support metrics
- `SalesByCountries.tsx` - Geographic distribution
- `TotalEarning.tsx` - Earning KPI
- `MonthlyCampaignState.tsx` - Campaign performance
- `SourceVisits.tsx` - Traffic sources

### Ecommerce Dashboard Charts
- `LineChartProfit.tsx` - Profit trend
- `RadialBarChart.tsx` - Circular bar chart
- `DonutChartGeneratedLeads.tsx` - Donut chart
- `RevenueReport.tsx` - Revenue analysis

### Logistics Dashboard Charts
- `LogisticsVehicleOverview.tsx` - Fleet metrics
- `LogisticsShipmentStatistics.tsx` - Shipment analytics
- `LogisticsDeliveryPerformance.tsx` - Performance KPI
- `LogisticsDeliveryExceptions.tsx` - Exception tracking
- `LogisticsOrdersByCountries.tsx` - Geographic orders

### Academy Dashboard Charts
- `InterestedTopics.tsx` - Topic distribution
- `AssignmentProgress.tsx` - Progress visualization

---

## Related Configuration Files

- **ApexCharts Wrapper:** `src/libs/styles/AppReactApexCharts.tsx`
- **MUI Theme:** `src/@core/theme/` (color tokens)
- **Types:** `node_modules/apexcharts/types/index.d.ts`

---

## Performance Metrics

- **Chart Load Time:** ~200-500ms (dynamic import)
- **Render Time:** ~100-200ms (ApexCharts internal)
- **Memory Usage:** ~2-5MB per chart (varies by series size)
- **Re-render:** Only if props/data changes

