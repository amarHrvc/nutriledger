# Logistics Dashboard Page

## Overview

The Logistics Dashboard is a specialized server-side rendered page component designed for supply chain and logistics operations. It provides real-time visibility into vehicle fleet management, shipment statistics, delivery performance, and logistics network operations.

**Key Path:** `src/app/[lang]/(dashboard)/(private)/apps/logistics/dashboard/page.tsx`

**Type:** Async Server Component with Dual Data Sources

**Data Sources:** 
- `getStatisticsData()` - Statistics and metrics data
- `getLogisticsData()` - Vehicle and fleet data

---

## Key Features

### Dashboard Composition

1. **Logistics Statistics Card** - Overview of key logistics metrics with horizontal cards
2. **Vehicle Overview** - Fleet vehicle statistics and metrics
3. **Shipment Statistics** - Shipment volume and status breakdown
4. **Delivery Performance** - On-time delivery and performance metrics
5. **Delivery Exceptions** - Issues and exceptions tracking
6. **Orders by Countries** - Geographic distribution of orders
7. **Overview Table** - Detailed logistics overview with vehicle data

### Real-Time Logistics Monitoring

- **Fleet Management** - Vehicle status and utilization
- **Shipment Tracking** - Shipment metrics and analytics
- **Performance KPIs** - Delivery and operational metrics
- **Geographic Distribution** - Orders and shipments by region
- **Data-Driven Table** - Vehicle and logistics details

---

## Page Layout

The logistics dashboard uses a horizontal-first layout optimized for monitoring:

**Desktop Layout (MD+):**
- Row 1: Full-width statistics cards (horizontal display)
- Row 2: 2x Charts (6 cols each) - Vehicle & Shipment
- Row 3: 3x Charts (4 cols each) - Performance, Exceptions, Geographic
- Row 4: Full-width data table

**Tablet Layout (MD):**
- Row 1: Full-width statistics cards
- Row 2: 2x Charts (6 cols each, stacked as needed)
- Row 3: 3x Charts (varying widths, mobile-optimized)
- Row 4: Full-width table

**Mobile Layout (XS/SM):**
- All components 12 cols (stacked vertically)

---

## Components Used

### Statistics Components
- `LogisticsStatisticsCard` - Horizontal statistics cards (data-driven)

### Chart/Metric Components
- `LogisticsVehicleOverview` - Fleet vehicle analytics
- `LogisticsShipmentStatistics` - Shipment metrics and distribution
- `LogisticsDeliveryPerformance` - On-time delivery metrics
- `LogisticsDeliveryExceptions` - Exception and issue tracking
- `LogisticsOrdersByCountries` - Geographic order distribution

### Table Components
- `LogisticsOverviewTable` - Vehicle and shipment details table

---

## Type Definitions

```typescript
// Logistics Dashboard - Main Page Component
const LogisticsDashboard = async (): Promise<React.ReactNode>

// Statistics data structure
interface StatisticsData {
  statsHorizontalWithBorder: StatCard[]
}

interface StatCard {
  title: string
  subtitle?: string
  value: string | number
  icon: string
  color: string
  // Additional stat properties
}

// Logistics/vehicle data structure
interface LogisticsData {
  vehicles: VehicleRecord[]
}

interface VehicleRecord {
  id: string
  vehicleNumber: string
  status: 'active' | 'inactive' | 'maintenance'
  location?: string
  shipments?: number
  utilization?: number
  driver?: string
  [key: string]: unknown
}

// Component props
interface LogisticsStatisticsCardProps {
  data: StatCard[]
}

interface LogisticsOverviewTableProps {
  vehicleData: VehicleRecord[]
}

// Grid sizing
type GridBreakpoint = {
  xs?: number | 'auto'
  sm?: number | 'auto'
  md?: number | 'auto'
  lg?: number | 'auto'
}
```

---

## Component Breakdown

### Row 1: Key Metrics
- **LogisticsStatisticsCard** (12 cols) - Horizontal stat cards from server data
  - Displays: statsHorizontalWithBorder array
  - Full-width layout showing all key metrics

### Row 2: Fleet & Shipment Overview
- **LogisticsVehicleOverview** (md: 6) - Vehicle fleet metrics
- **LogisticsShipmentStatistics** (md: 6) - Shipment analytics

### Row 3: Performance Analysis
- **LogisticsDeliveryPerformance** (md: 4) - On-time delivery KPI
- **LogisticsDeliveryExceptions** (md: 4) - Issue tracking
- **LogisticsOrdersByCountries** (md: 4) - Geographic distribution

### Row 4: Detail Table
- **LogisticsOverviewTable** (12 cols) - Vehicle data table

---

## Usage Example

```typescript
import Grid from '@mui/material/Grid'
import { getLogisticsData, getStatisticsData } from '@/app/server/actions'
import LogisticsStatisticsCard from '@views/apps/logistics/dashboard/LogisticsStatisticsCard'
import LogisticsVehicleOverview from '@views/apps/logistics/dashboard/LogisticsVehicleOverview'
// ... import other components

const LogisticsDashboard = async () => {
  // Fetch statistics data
  const data = await getStatisticsData()
  // Fetch vehicle/logistics data
  const vehicleData = await getLogisticsData()

  return (
    <Grid container spacing={6}>
      {/* Statistics row */}
      <Grid size={{ xs: 12 }}>
        <LogisticsStatisticsCard data={data?.statsHorizontalWithBorder} />
      </Grid>

      {/* Fleet analytics */}
      <Grid size={{ xs: 12, md: 6 }}>
        <LogisticsVehicleOverview />
      </Grid>
      <Grid size={{ xs: 12, md: 6 }}>
        <LogisticsShipmentStatistics />
      </Grid>

      {/* Performance metrics */}
      <Grid size={{ xs: 12, md: 4 }}>
        <LogisticsDeliveryPerformance />
      </Grid>
      <Grid size={{ xs: 12, md: 4 }}>
        <LogisticsDeliveryExceptions />
      </Grid>
      <Grid size={{ xs: 12, md: 4 }}>
        <LogisticsOrdersByCountries />
      </Grid>

      {/* Data table */}
      <Grid size={{ xs: 12 }}>
        <LogisticsOverviewTable vehicleData={vehicleData?.vehicles} />
      </Grid>
    </Grid>
  )
}

export default LogisticsDashboard
```

---

## Key Implementation Notes

1. **Async Server Component** - Dual server action calls
2. **Horizontal Statistics** - Stats cards with border styling
3. **Server Data Integration** - Statistics and vehicle data passed to components
4. **Full-Width Statistics** - Statistics row spans entire width (xs: 12)
5. **6-Column Cards** - Performance metrics in 3-column layout
6. **Full-Width Table** - Vehicle data displays in full-width table
7. **Logistics-Specific** - Tailored for supply chain operations
8. **Real-Time Ready** - Structure supports live data updates

---

## File Structure

```
src/
├── app/[lang]/(dashboard)/(private)/apps/
│   └── logistics/
│       └── dashboard/
│           └── page.tsx (THIS FILE)
├── views/apps/logistics/dashboard/
│   ├── LogisticsStatisticsCard.tsx
│   ├── LogisticsVehicleOverview.tsx
│   ├── LogisticsShipmentStatistics.tsx
│   ├── LogisticsDeliveryPerformance.tsx
│   ├── LogisticsDeliveryExceptions.tsx
│   ├── LogisticsOrdersByCountries.tsx
│   └── LogisticsOverviewTable.tsx
├── app/server/
│   └── actions.ts (getStatisticsData, getLogisticsData functions)
└── fake-db/
    ├── statistics/...
    └── logistics/...
```

---

## Data Flow

1. Page component is async
2. Simultaneously calls:
   - `getStatisticsData()` for metrics
   - `getLogisticsData()` for vehicle data
3. Both data sources resolved before rendering
4. Statistics data passed to LogisticsStatisticsCard
5. Vehicle data passed to LogisticsOverviewTable
6. Other components are self-contained

---

## Responsive Behavior

### Statistics Row
- XS-LG: 12 cols (always full width)
- Contains horizontal cards that may stack on small screens

### Fleet & Shipment Charts
- XS: 12 cols (stacked)
- MD+: 6 cols (side by side)

### Performance Cards
- XS: 12 cols (full width, stacked)
- MD+: 4 cols (3 per row)

### Overview Table
- XS-LG: 12 cols (always full width)
- Horizontal scrolling on small screens

---

## Logistics Components

### LogisticsStatisticsCard
- Displays horizontal statistics with borders
- Data-driven from server
- Multiple metric display
- KPI visualization

### Vehicle Overview
- Fleet metrics and status
- Vehicle utilization data
- Status indicators

### Shipment Statistics
- Shipment volume metrics
- Status distribution (pending, shipped, delivered)
- Trend visualization

### Delivery Performance
- On-time delivery percentage
- Performance trends
- KPI tracking

### Delivery Exceptions
- Exception count and types
- Issue categorization
- Alert indicators

### Orders by Countries
- Geographic heat map
- Regional order distribution
- International metrics

### Overview Table
- Vehicle details listing
- Fleet-wide view
- Sortable/filterable (likely)
- Real-time data display

---

## Performance Considerations

1. **Parallel Data Fetching** - Both server actions can fetch in parallel
2. **Server-Side Rendering** - No client-side data loading
3. **Dynamic Imports** - Charts load on demand
4. **Table Virtualization** - Large datasets handled efficiently
5. **Memoization** - Components prevent unnecessary re-renders

---

## Logistics-Specific Features

1. **Vehicle Tracking** - Fleet status and location awareness
2. **Shipment Monitoring** - Real-time shipment metrics
3. **Performance Analytics** - Delivery KPIs and trends
4. **Exception Management** - Issue tracking and alerts
5. **Geographic Intelligence** - Order distribution by region
6. **Data-Driven Statistics** - Server-provided metrics

---

## Accessibility

- Semantic HTML structure
- Card-based layout hierarchy
- Statistics with accessible labels
- Table with proper headers
- Color contrast compliance
- Keyboard navigation support
- Mobile-responsive design
- Screen reader compatibility

---

## Use Cases

- Fleet managers monitoring vehicle status
- Logistics coordinators tracking shipments
- Operations teams analyzing performance
- Supply chain executives reviewing KPIs
- Customer service viewing delivery status

