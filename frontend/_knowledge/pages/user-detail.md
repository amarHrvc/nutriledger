# User Detail Page Snapshot

**Page:** `/apps/user/view`  
**Route:** `src/app/[lang]/(dashboard)/(private)/apps/user/view/page.tsx`  
**Epic:** kb-joc.2.2  
**Task:** T017 - Write user-detail view snapshot  
**Status:** Complete

## Page Implementation

### Root Component: UserViewTab

**Type:** Async Server Component  
**Purpose:** Display detailed user profile with tabbed interface for profile, security, billing, notifications, and connections

### Component Structure

```typescript
// React Imports
import type { ReactElement } from 'react'

// Next Imports
import dynamic from 'next/dynamic'

// MUI Imports
import Grid from '@mui/material/Grid'

// Type Imports
import type { PricingPlanType } from '@/types/pages/pricingTypes'

// Component Imports
import UserLeftOverview from '@views/apps/user/view/user-left-overview'
import UserRight from '@views/apps/user/view/user-right'

// Data Imports
import { getPricingData } from '@/app/server/actions'

// Dynamic imports for lazy loading
const OverViewTab = dynamic(() => import('@views/apps/user/view/user-right/overview'))
const SecurityTab = dynamic(() => import('@views/apps/user/view/user-right/security'))
const BillingPlans = dynamic(() => import('@views/apps/user/view/user-right/billing-plans'))
const NotificationsTab = dynamic(() => import('@views/apps/user/view/user-right/notifications'))
const ConnectionsTab = dynamic(() => import('@views/apps/user/view/user-right/connections'))

// Tab content builder
const tabContentList = (data?: PricingPlanType[]): { [key: string]: ReactElement } => ({
  overview: <OverViewTab />,
  security: <SecurityTab />,
  'billing-plans': <BillingPlans data={data} />,
  notifications: <NotificationsTab />,
  connections: <ConnectionsTab />
})

// Main async component
const UserViewTab = async () => {
  const data = await getPricingData()

  return (
    <Grid container spacing={6}>
      <Grid size={{ xs: 12, lg: 4, md: 5 }}>
        <UserLeftOverview />
      </Grid>
      <Grid size={{ xs: 12, lg: 8, md: 7 }}>
        <UserRight tabContentList={tabContentList(data)} />
      </Grid>
    </Grid>
  )
}

export default UserViewTab
```

## Page Architecture

### Layout Pattern
```
Two-column responsive layout:
┌─────────────────────────────────────┐
│ Left Side (4 lg, 5 md, 12 xs)       │ Right Side (8 lg, 7 md, 12 xs)
├─────────────────────────────────────┼──────────────────────────────┐
│ UserLeftOverview                    │ UserRight (Tabbed)           │
│ ├── UserDetails                     │ ├── Tab Navigation           │
│ └── UserPlan                        │ │   - Overview               │
│                                     │ │   - Security               │
│                                     │ │   - Billing & Plans        │
│                                     │ │   - Notifications          │
│                                     │ │   - Connections            │
│                                     │ └── Tab Panel Content         │
└─────────────────────────────────────┴──────────────────────────────┘
```

### Responsive Behavior
- **Mobile (xs):** Both columns stack full-width (12)
- **Tablet (md):** Left 5, Right 7 (narrow left sidebar)
- **Desktop (lg):** Left 4, Right 8 (wider right content)

## Component Breakdown

### Left Side: UserLeftOverview

**Location:** `src/views/apps/user/view/user-left-overview/index.tsx`

**Purpose:** Display user profile information and current plan

**Sub-components:**
1. **UserDetails** - User profile information
   - Avatar/profile picture
   - User name and title
   - Contact information
   - Location/address
   - Edit profile actions

2. **UserPlan** - Current subscription plan
   - Plan name
   - Plan features
   - Upgrade/downgrade options
   - Plan status

### Right Side: UserRight

**Location:** `src/views/apps/user/view/user-right/index.tsx`

**Type:** Client Component (`'use client'`)

**Purpose:** Tabbed interface for user settings and management

**State:**
```typescript
const [activeTab, setActiveTab] = useState('overview')
```

**Tabs (5 total):**

1. **Overview** (default)
   - Location: `user-right/overview`
   - Components: InvoiceListTable, ProjectListTable, UserActivityTimeline
   - Content: User's recent activities, invoices, projects, timeline

2. **Security**
   - Location: `user-right/security`
   - Components: ChangePassword, RecentDevice, TwoStepVerification
   - Content: Password management, active sessions, 2FA settings

3. **Billing & Plans**
   - Location: `user-right/billing-plans`
   - Components: CurrentPlan, BillingAddress, PaymentMethod
   - Content: Current subscription, billing address, payment methods
   - Props: `data: PricingPlanType[]` (pricing plans from server)

4. **Notifications**
   - Location: `user-right/notifications`
   - Content: Email/push notification preferences
   - Toggle switches for notification types

5. **Connections**
   - Location: `user-right/connections`
   - Content: Connected apps/services, API keys, integrations

**Tab Navigation:**
- Icon-based pills with labels
- Scrollable variant for mobile
- Each tab has an icon from Tabler icon set:
  - Overview: tabler-users
  - Security: tabler-lock
  - Billing & Plans: tabler-bookmark
  - Notifications: tabler-bell
  - Connections: tabler-link

**Tab Rendering:**
```typescript
<TabPanel value={activeTab} className='p-0'>
  {tabContentList[activeTab]}
</TabPanel>
```

## Data Flow

### Server-Side Data Fetching
```typescript
const data = await getPricingData()
```

**Purpose:** Fetch pricing plans for Billing & Plans tab

**Alternative:** API call structure provided as comment
```typescript
// Recommended approach if using API:
// const res = await fetch(`${process.env.API_URL}/pages/pricing`)
```

### Client-Side State
```typescript
UserRight:
  activeTab: string
    └── Triggers tab panel content switch
```

### Props Passing
```
getPricingData()
  ↓
tabContentList(data) - Builds tab objects
  ↓
UserRight (receives tabContentList)
  ↓
Renders based on activeTab state
```

## Code Splitting Strategy

### Dynamic Imports (Code Splitting)
```typescript
// All tab content loaded dynamically
const OverViewTab = dynamic(() => import('@views/apps/user/view/user-right/overview'))
const SecurityTab = dynamic(() => import('@views/apps/user/view/user-right/security'))
const BillingPlans = dynamic(() => import('@views/apps/user/view/user-right/billing-plans'))
const NotificationsTab = dynamic(() => import('@views/apps/user/view/user-right/notifications'))
const ConnectionsTab = dynamic(() => import('@views/apps/user/view/user-right/connections'))
```

**Benefits:**
- Only load tabs that are viewed
- Reduces initial page bundle size
- Faster initial page load
- Tabs load on-demand

## Type Definitions

### PricingPlanType
```typescript
interface PricingPlanType {
  // Pricing plan structure for Billing & Plans tab
  // Defined in: src/types/pages/pricingTypes
  // Used for: BillingPlans tab content
}
```

## Key Features

### 1. Async Server Component
- Fetches data server-side with `getPricingData()`
- Zero waterfall for initial data load
- Reduces client-side JavaScript

### 2. Dynamic Code Splitting
- Each tab content loaded separately
- Only downloads when tab is clicked
- Improves initial page performance

### 3. Responsive Two-Column Layout
- Left sidebar for profile
- Right panel for settings
- Adapts to all screen sizes

### 4. Tabbed Navigation
- 5 distinct user management areas
- Icon + label for clarity
- Smooth tab switching

## Navigation & Routing

### Route Access
- Route: `/apps/user/view`
- Locale-aware: `[lang]` param in route
- Layout: Dashboard private area

### Related Routes
- Users list: `/apps/user` or `/apps/user/list`
- User edit: (likely `/apps/user/edit` or in a modal)

## Performance Optimizations

1. **Async Server Component:** Data fetched on server
2. **Dynamic Imports:** Tab content split into separate chunks
3. **Lazy Loading:** Tabs only load when viewed
4. **Grid Layout:** CSS Grid for efficient layout

## Accessibility

- **Semantic Tabs:** MUI TabContext + TabPanel
- **Icon + Text:** Tabs have both icons and labels
- **Keyboard Navigation:** Tab navigation via keyboard
- **ARIA Labels:** Handled by MUI components

## Known Patterns

### Pattern: Async Server Component with Client Subcomponents
```
Server Component (Async)
  └── Fetches data
      └── Passes to Client Component
          └── Client Component manages interactivity
```

### Pattern: Dynamic Tab Content
```
Tab content array
  └── Dynamic imports
      └── Lazy loaded on demand
```

### Pattern: Responsive Two-Column Layout
```
Grid container
  ├── Grid item (responsive sizes)
  │   └── Left sidebar content
  └── Grid item (responsive sizes)
      └── Right tabbed content
```

## Testing Considerations

### Unit Tests
```typescript
describe('UserViewTab', () => {
  test('renders left and right sections', () => {
    // Render async component
    // Verify UserLeftOverview present
    // Verify UserRight present
  })

  test('passes pricing data to tabs', () => {
    // Verify data passed to BillingPlans tab
  })
})

describe('UserRight Component', () => {
  test('tabs are clickable', () => {
    // Click each tab
    // Verify active state changes
  })

  test('displays correct tab content', () => {
    // Verify each tab shows correct content
  })
})
```

## Integration Points

### Data Source
- `getPricingData()` from server actions
- Could be replaced with API fetch
- Provides PricingPlanType[] data

### Child Components
- **Left:** UserLeftOverview, UserDetails, UserPlan
- **Right:** UserRight with 5 tab components
- **Tabs:** Overview, Security, BillingPlans, Notifications, Connections

## Extension Points

### Adding New Tab
```typescript
// 1. Create new component
const NewTab = dynamic(() => import('@views/apps/user/view/user-right/new-tab'))

// 2. Add to tabContentList
'new-tab': <NewTab />

// 3. Add to tab navigation in UserRight
<Tab icon={<i className='tabler-icon' />} value='new-tab' label='New Tab' iconPosition='start' />
```

### Modifying Layout
```typescript
// Change column sizes
<Grid size={{ xs: 12, lg: 3, md: 4 }}>  // Make left narrower
  <UserLeftOverview />
</Grid>
<Grid size={{ xs: 12, lg: 9, md: 8 }}>  // Make right wider
  <UserRight tabContentList={tabContentList(data)} />
</Grid>
```

## Summary

The User Detail page provides:
- **Two-Column Layout:** Profile on left, settings on right
- **Tabbed Interface:** 5 distinct user management sections
- **Server-Side Data:** Async fetching of pricing data
- **Code Splitting:** Dynamic imports for tab content
- **Responsive Design:** Mobile, tablet, and desktop optimized
- **Type Safety:** TypeScript throughout

**Best Practices:**
- ✅ Async server components for data fetching
- ✅ Dynamic imports for code splitting
- ✅ Responsive grid layout
- ✅ Proper separation of concerns
- ✅ Client/server boundary clarity