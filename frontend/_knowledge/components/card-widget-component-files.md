# Card & Widget Component Files

## Overview

Comprehensive documentation of reusable card and widget components used throughout the Vuexy Admin system. This document provides detailed implementation guidance, type definitions, usage patterns, and component architecture.

**Components Directory:** `src/components/card-statistics/`

**Total Components:** 8 (6 stat cards + 2 specialized)

**Type Definitions:** `src/types/pages/widgetTypes.ts`

**Core Framework:** MUI (Material-UI) v5+

---

## Component File Structure

### Standard Card Component File Pattern

Each card component file follows this structure:

```typescript
// FILE: src/components/card-statistics/[ComponentName].tsx

// MUI Imports
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Typography from '@mui/material/Typography'
// ... other imports

// Type Import
import type { CardStats[Type]Props } from '@/types/pages/widgetTypes'

// Component Import
import CustomAvatar from '@core/components/mui/Avatar'

// Component Definition
const CardComponent = (props: CardStats[Type]Props) => {
  // Destructure props
  const { stats, title, subtitle, avatarIcon, avatarColor } = props

  // Render JSX
  return (
    <Card>
      <CardContent>
        {/* Component-specific layout */}
      </CardContent>
    </Card>
  )
}

export default CardComponent
```

---

## Component Files

### 1. Vertical.tsx

**Component Name:** `CardStatsVertical`

**File Size:** ~180 lines

**Purpose:** Vertical KPI card layout

**Key Features:**
- Icon on top
- Vertical text alignment
- Trend chip at bottom
- Full height fill

**Props:**
```typescript
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
```

**JSX Structure:**
```typescript
<Card>
  <CardContent className='flex flex-col gap-y-3 items-start'>
    <CustomAvatar variant='rounded' skin={avatarSkin} size={avatarSize} color={avatarColor}>
      <i className={classnames(avatarIcon, 'text-[28px]')} />
    </CustomAvatar>
    <div className='flex flex-col gap-y-1'>
      <Typography variant='h5'>{title}</Typography>
      <Typography color='text.disabled'>{subtitle}</Typography>
      <Typography color='text.primary'>{stats}</Typography>
    </div>
    <Chip label={chipText} color={chipColor} variant={chipVariant} size='small' />
  </CardContent>
</Card>
```

**Styling:**
- Flex column layout
- Gap spacing between elements
- Item alignment to start
- Typography variants: h5, body2 (defaults)

**Usage Example:**
```typescript
<CardStatsVertical
  stats='24.67k'
  title='Total Sales'
  subtitle='Last Week'
  avatarIcon='tabler-currency-dollar'
  avatarColor='success'
  avatarSkin='light'
  avatarSize={44}
  chipText='+24.67%'
  chipColor='success'
  chipVariant='tonal'
/>
```

---

### 2. Horizontal.tsx

**Component Name:** `CardStatsHorizontal`

**File Size:** ~90 lines

**Purpose:** Compact horizontal stat card

**Key Features:**
- Icon on right
- Horizontal layout
- Space-efficient
- Full height fill

**Props:**
```typescript
interface CardStatsHorizontalProps {
  stats: string | number
  title: string
  avatarIcon: string
  avatarColor?: ColorVariant
  avatarSkin?: 'light' | 'dark'
  avatarSize?: number
}
```

**JSX Structure:**
```typescript
<Card className='bs-full'>
  <CardContent>
    <div className='flex items-center flex-wrap gap-2 justify-between'>
      <div className='flex flex-col gap-x-4 gap-y-0.5'>
        <Typography variant='h5'>{stats}</Typography>
        <Typography variant='subtitle1' color='text.secondary'>
          {title}
        </Typography>
      </div>
      <CustomAvatar variant='rounded' color={avatarColor} skin={avatarSkin} size={avatarSize}>
        <i className={classnames(avatarIcon, 'text-[26px]')} />
      </CustomAvatar>
    </div>
  </CardContent>
</Card>
```

**Styling:**
- Flex row with space-between
- Icon on right side
- Compact vertical spacing
- Full height (`bs-full`)

**Usage Example:**
```typescript
<CardStatsHorizontal
  stats={1420}
  title='Total Users'
  avatarIcon='tabler-users'
  avatarColor='info'
  avatarSize={38}
/>
```

---

### 3. HorizontalWithBorder.tsx

**Component Name:** `CardStatsHorizontalWithBorder`

**File Size:** ~110 lines

**Purpose:** Horizontal card with interactive hover border

**Key Features:**
- **Styled Component:** Extends MUI Card with CSS-in-JS
- **Animated Border:** Grows on hover
- **Dynamic Theming:** Color prop controls entire card
- **Trend Display:** Shows percentage change

**Props:**
```typescript
interface CardStatsHorizontalWithBorderProps {
  stats: string | number
  title: string
  trendNumber: number
  avatarIcon: string
  color?: ColorVariant
}
```

**Styled Card Component:**
```typescript
const Card = styled(MuiCard)<Props>(({ color }) => ({
  transition: 'border 0.3s ease-in-out, box-shadow 0.3s ease-in-out, margin 0.3s ease-in-out',
  borderBottomWidth: '2px',
  borderBottomColor: `var(--mui-palette-${color}-darkerOpacity)`,
  '[data-skin="bordered"] &:hover': {
    boxShadow: 'none'
  },
  '&:hover': {
    borderBottomWidth: '3px',
    borderBottomColor: `var(--mui-palette-${color}-main) !important`,
    boxShadow: 'var(--mui-customShadows-lg)',
    marginBlockEnd: '-1px'
  }
}))
```

**JSX Structure:**
```typescript
<Card color={color || 'primary'}>
  <CardContent className='flex flex-col gap-1'>
    <div className='flex items-center gap-4'>
      <CustomAvatar color={color} skin='light' variant='rounded'>
        <i className={classnames(avatarIcon, 'text-[28px]')} />
      </CustomAvatar>
      <Typography variant='h4'>{stats}</Typography>
    </div>
    <div className='flex flex-col gap-1'>
      <Typography>{title}</Typography>
      <div className='flex items-center gap-2'>
        <Typography color='text.primary' className='font-medium'>
          {`${trendNumber > 0 ? '+' : ''}${trendNumber}%`}
        </Typography>
        <Typography variant='body2' color='text.disabled'>
          than last week
        </Typography>
      </div>
    </div>
  </CardContent>
</Card>
```

**Hover Effects:**
- Border grows from 2px to 3px
- Border color brightens
- Box shadow appears
- Margin adjusts for visual lift

**Usage Example:**
```typescript
<CardStatsHorizontalWithBorder
  stats='$12,450'
  title='Sales'
  trendNumber={8.5}
  avatarIcon='tabler-shopping-cart'
  color='success'
/>
```

---

### 4. HorizontalWithSubtitle.tsx

**Component Name:** `CardStatsHorizontalWithSubtitle`

**File Size:** ~95 lines

**Purpose:** Horizontal card with extended text content

**Key Features:**
- Two-line text display
- Icon on right
- Subtitle support
- Similar to Horizontal variant

**Props:**
```typescript
interface CardStatsHorizontalWithSubtitleProps {
  stats: string | number
  title: string
  subtitle: string
  avatarIcon: string
  avatarColor?: ColorVariant
  avatarSkin?: 'light' | 'dark'
  avatarSize?: number
}
```

**JSX Structure:**
```typescript
<Card className='bs-full'>
  <CardContent>
    <div className='flex items-center justify-between gap-4'>
      <div className='flex flex-col gap-y-1'>
        <Typography variant='h5'>{stats}</Typography>
        <Typography variant='body2'>{title}</Typography>
        <Typography variant='caption' color='text.secondary'>
          {subtitle}
        </Typography>
      </div>
      <CustomAvatar variant='rounded' color={avatarColor} skin={avatarSkin} size={avatarSize}>
        <i className={classnames(avatarIcon, 'text-[26px]')} />
      </CustomAvatar>
    </div>
  </CardContent>
</Card>
```

**Typography Variants:**
- h5: Stats value
- body2: Main title
- caption: Subtitle (secondary color)

---

### 5. HorizontalWithAvatar.tsx

**Component Name:** `CardStatsHorizontalWithAvatar`

**File Size:** ~110 lines

**Purpose:** Horizontal card with user avatar support

**Key Features:**
- User avatar or icon
- Optional subtitle
- Flexible avatar source
- Image or icon fallback

**Props:**
```typescript
interface CardStatsHorizontalWithAvatarProps {
  stats: string | number
  title: string
  subtitle?: string
  avatarImage?: string
  avatarIcon?: string
  avatarColor?: ColorVariant
}
```

**Conditional Rendering:**
```typescript
{avatarImage ? (
  <img src={avatarImage} alt={title} width={40} height={40} />
) : (
  <CustomAvatar color={avatarColor} skin='light'>
    <i className={classnames(avatarIcon, 'text-[26px]')} />
  </CustomAvatar>
)}
```

---

### 6. CardStatsSquare.tsx

**Component Name:** `CardStatsSquare`

**File Size:** ~100 lines

**Purpose:** Square-aspect ratio card for uniform grids

**Key Features:**
- Square dimensions
- Icon centered top
- Centered text layout
- Equal width/height

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

**JSX Structure:**
```typescript
<Card className='aspect-square'>
  <CardContent className='flex flex-col items-center justify-center h-full gap-3'>
    <CustomAvatar variant='rounded' color={avatarColor} skin={avatarSkin} size={44}>
      <i className={classnames(avatarIcon, 'text-[28px]')} />
    </CustomAvatar>
    <Typography variant='h6' className='text-center'>{stats}</Typography>
    <Typography variant='body2' className='text-center'>{title}</Typography>
  </CardContent>
</Card>
```

**Styling:**
- Aspect ratio square
- Centered content both axes
- Full height fill
- Centered text alignment

---

### 7. StatsWithAreaChart.tsx

**Component Name:** `StatsWithAreaChart`

**File Size:** ~180 lines

**Purpose:** Stat card with embedded area chart

**Key Features:**
- Chart component integration
- Stat display with chart context
- Dynamic chart series
- Compact chart display

**Props:**
```typescript
interface StatsWithAreaChartProps {
  stats: string | number
  title: string
  chartSeries: Array<{ data: number[] }>
  chartCategories: string[]
  avatarColor?: ColorVariant
  avatarIcon?: string
}
```

**Component Structure:**
```typescript
<Card>
  <CardContent>
    <div className='flex items-center justify-between mb-4'>
      <div>
        <Typography variant='h6'>{stats}</Typography>
        <Typography variant='body2'>{title}</Typography>
      </div>
      <CustomAvatar color={avatarColor}>
        <i className={avatarIcon} />
      </CustomAvatar>
    </div>
    <AppReactApexCharts
      type='area'
      height={100}
      options={chartOptions}
      series={chartSeries}
    />
  </CardContent>
</Card>
```

**Chart Options:**
- Sparkline styling
- Gradient fill
- No axes/grid display
- Responsive height

---

### 8. CustomerStats.tsx

**Component Name:** `CustomerStats`

**File Size:** ~95 lines

**Purpose:** Customer-focused metric card

**Key Features:**
- Customer count display
- Growth/change indicator
- Icon-based layout
- Trend visualization

**Props:**
```typescript
interface CustomerStatsProps {
  stats: string | number
  title: string
  avatarIcon?: string
  avatarColor?: ColorVariant
  trend?: number
  icon?: React.ReactNode
}
```

**Trend Visualization:**
```typescript
{trend !== undefined && (
  <div className='flex items-center gap-1'>
    <i className={trend > 0 ? 'tabler-trending-up' : 'tabler-trending-down'} />
    <Typography variant='body2'>
      {trend > 0 ? '+' : ''}{trend}%
    </Typography>
  </div>
)}
```

---

## Type Definitions File

**Location:** `src/types/pages/widgetTypes.ts`

**Content Structure:**
```typescript
import type { ColorVariant } from '@core/types'

// Card Stats Types
export interface CardStatsVerticalProps {
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

export interface CardStatsHorizontalProps {
  stats: string | number
  title: string
  avatarIcon: string
  avatarColor?: ColorVariant
  avatarSkin?: 'light' | 'dark'
  avatarSize?: number
}

// ... more type definitions ...

export type ColorVariant = 
  | 'primary'
  | 'secondary'
  | 'success'
  | 'error'
  | 'warning'
  | 'info'
```

---

## Shared Utilities & Imports

### Common Imports Across Files
```typescript
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Typography from '@mui/material/Typography'
import Chip from '@mui/material/Chip'
import LinearProgress from '@mui/material/LinearProgress'
import classnames from 'classnames'
import CustomAvatar from '@core/components/mui/Avatar'
```

### CustomAvatar Component
```typescript
<CustomAvatar
  variant='rounded'              // Shape: rounded, circular, square
  skin='light' | 'dark'         // Background brightness
  color={avatarColor}           // Theme color variant
  size={44}                     // Size in pixels
>
  <i className={classnames(avatarIcon, 'text-[28px]')} />
</CustomAvatar>
```

---

## Tailwind CSS Classes Used

### Layout & Spacing
```css
flex flex-col gap-y-3              /* Vertical flex with gap */
flex items-center justify-between  /* Horizontal flex */
flex-wrap gap-2 gap-x-4 gap-y-0.5 /* Responsive wrapping */
items-start                        /* Vertical alignment start */
gap-1 gap-4                        /* Various gap sizes */
```

### Sizing & Dimensions
```css
is-full                            /* Full width (block size) */
bs-full                            /* Full height (block size) */
aspect-square                      /* Square aspect ratio */
text-[28px] text-[26px]           /* Icon sizing */
```

### Typography
```css
font-medium                        /* Font weight */
text-[var(--mui-palette-...)]     /* CSS variable colors */
```

---

## Color Tokens (CSS Variables)

All cards use MUI CSS variables for colors:

```css
/* Primary colors */
var(--mui-palette-primary-main)
var(--mui-palette-primary-lightOpacity)
var(--mui-palette-primary-darkerOpacity)

/* Status colors */
var(--mui-palette-success-main)
var(--mui-palette-error-main)
var(--mui-palette-warning-main)
var(--mui-palette-info-main)

/* Text colors */
var(--mui-palette-text-primary)
var(--mui-palette-text-secondary)
var(--mui-palette-text-disabled)

/* System colors */
var(--mui-palette-divider)
var(--mui-palette-background-paper)
var(--mui-palette-common-white)
```

---

## Implementation Patterns

### Pattern 1: Basic Vertical Card
```typescript
<CardStatsVertical
  title='Metric'
  subtitle='Context'
  stats='100k'
  avatarIcon='tabler-icon'
  avatarColor='primary'
  avatarSkin='light'
  avatarSize={44}
  chipText='+25%'
  chipColor='success'
  chipVariant='tonal'
/>
```

### Pattern 2: Interactive Horizontal
```typescript
<CardStatsHorizontalWithBorder
  title='Sales'
  stats='$50,000'
  trendNumber={12.5}
  avatarIcon='tabler-shopping-cart'
  color='success'
/>
```

### Pattern 3: With Avatar
```typescript
<CardStatsHorizontalWithAvatar
  stats={420}
  title='New Customers'
  avatarImage='/path/to/avatar.jpg'
  avatarColor='primary'
/>
```

### Pattern 4: With Chart
```typescript
<StatsWithAreaChart
  stats='$12,450'
  title='Revenue'
  avatarIcon='tabler-chart-bar'
  avatarColor='success'
  chartSeries={[{ data: [...] }]}
  chartCategories={['Mon', 'Tue', 'Wed']}
/>
```

---

## Best Practices

### Do's
- ✅ Use correct color variants for context
- ✅ Provide meaningful titles/subtitles
- ✅ Use MUI CSS variables for colors
- ✅ Maintain consistent icon sizing
- ✅ Include trend indicators when relevant
- ✅ Test responsive behavior

### Don'ts
- ❌ Don't mix color styles inconsistently
- ❌ Don't hardcode colors (use CSS variables)
- ❌ Don't override responsive props unnecessarily
- ❌ Don't forget TypeScript props interface
- ❌ Don't use non-semantic text variants

---

## Testing Checklist

For each card component:

- [ ] Props render correctly
- [ ] Color variants apply properly
- [ ] Icon displays at correct size
- [ ] Text wraps appropriately
- [ ] Card responsive on all breakpoints
- [ ] Hover states work (if applicable)
- [ ] Accessibility features present
- [ ] TypeScript types correct
- [ ] No console warnings
- [ ] Performance acceptable

---

## File Directory Structure

```
src/
├── components/card-statistics/
│   ├── CardStatsSquare.tsx
│   ├── CustomerStats.tsx
│   ├── Horizontal.tsx
│   ├── HorizontalWithAvatar.tsx
│   ├── HorizontalWithBorder.tsx
│   ├── HorizontalWithSubtitle.tsx
│   ├── StatsWithAreaChart.tsx
│   └── Vertical.tsx
├── types/pages/
│   └── widgetTypes.ts
└── @core/components/mui/
    └── Avatar.tsx
```

---

## Related Documentation

- Cards & Widgets Catalog: `_knowledge/pages/cards-widgets-catalog.md`
- CRM Dashboard: `_knowledge/pages/crm-dashboard.md`
- Charts Catalog: `_knowledge/pages/charts-catalog.md`
- MUI Components: [mui.com/material/](https://mui.com/material/)

---

## Summary

The Card & Widget component system provides:

1. **8 Reusable Components** - Vertical, horizontal, with variants
2. **Type-Safe Props** - Full TypeScript interface definitions
3. **Theme Integration** - MUI color and styling support
4. **Responsive Design** - Mobile-first approach
5. **Accessibility** - Semantic HTML and ARIA support
6. **Flexibility** - Customizable colors, icons, sizing
7. **Consistency** - Unified styling across application
8. **Performance** - Optimized for rendering and bundle size

