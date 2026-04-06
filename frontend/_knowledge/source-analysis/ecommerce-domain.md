# Ecommerce Domain Source Structure

## Overview

The Vuexy Admin ecommerce domain provides a complete product catalog, order management, customer relationship, and settings infrastructure. Organized under `/src/views/apps/ecommerce/`, it comprises six major functional areas: dashboard, products, orders, customers, reviews, and settings. Built with modern React patterns, TanStack React Table for advanced data operations, and MUI components for UI consistency.

**Key characteristics:**
- Server and Client Components (separation of concerns)
- TanStack React Table for sorting, filtering, pagination
- Advanced form patterns for product/order management
- Multi-level detail views (order details, customer profiles)
- Comprehensive TypeScript entity types
- Drawer-based add/edit workflows
- Real-time form validation

---

## Component Structure

### 1. Dashboard Domain

**Location:** `/src/views/apps/ecommerce/dashboard/`

Ecommerce-specific dashboard with 10+ specialized widgets. Reimplements analytics dashboard for product-focused metrics. Server and Client components working together.

**Key components:**

**StatisticsCard** (Server component)
- 4-column grid layout with stat cards (xs: 6, sm: 3)
- Displays: Sales (230k), Customers (8.549k), Products (1.423k), Revenue ($9745)
- CustomAvatar icons with color theming
- Updated timestamp display

```typescript
type DataType = {
  icon: string
  stats: string
  title: string
  color: ThemeColor
}
```

**Orders** (Client component with Tabs)
- Tabbed interface (New, Preparing, Shipping)
- MUI Timeline component for order flow visualization
- Shows sender/receiver address information
- Status-based color coding

**RevenueReport** - Revenue trend visualization
**PopularProducts** - Top products listing
**LineChartProfit** - Profit trend chart
**Transactions** - Recent transaction list
**RadialBarChart** - Sales distribution
**DonutChartGeneratedLeads** - Lead generation metrics
**EarningReports** - Earning overview
**InvoiceListTable** - Recent invoices

### 2. Products Domain

**Location:** `/src/views/apps/ecommerce/products/`

Manages product catalog with list, add, and category management. Complex form composition patterns.

#### ProductListTable (Client component)

Advanced data table with:
- **TanStack React Table** integration for sorting/filtering/pagination
- **Fuzzy search** with @tanstack/match-sorter-utils
- **Column types:**
  - Checkbox (multi-select)
  - Product image + name
  - Category with icon
  - SKU
  - Stock status (boolean)
  - Price
  - Quantity
  - Status chip with color
  - Actions menu (edit, delete, etc.)

```typescript
export type ProductType = {
  id: number
  productName: string
  category: string
  stock: boolean
  sku: number
  price: string
  qty: number
  status: string
  image: string
  productBrand: string
}

type ProductCategoryType = {
  [key: string]: {
    icon: string
    color: ThemeColor
  }
}

type productStatusType = {
  [key: string]: {
    title: string
    color: ThemeColor
  }
}
```

**DebouncedInput component** - Debounced search input (500ms default)
- Manages input state with useEffect
- Prevents excessive re-renders
- Integrated with TanStack React Table filter

**TableFilters component** - Advanced filtering UI
- Filter by category
- Filter by status
- Responsive chip-based UI

#### ProductAddForm (Server + Client components)

Multi-section form for adding new products:

**ProductAddHeader** - Form title and navigation
**ProductInformation** - Product name, description, vendor
**ProductPricing** - Base/discounted price, tax checkbox, stock toggle
**ProductImage** - Image upload and gallery
**ProductInventory** - Stock tracking, SKU, quantities
**ProductVariants** - Size/color variants composition
**ProductOrganize** - Category selection, tags

Form state management pattern:
```typescript
<Form>
  <CustomTextField
    fullWidth
    label='Base Price'
    placeholder='Enter Base Price'
    className='mbe-6'
  />
  <CustomTextField
    fullWidth
    label='Discounted Price'
    placeholder='$499'
    className='mbe-6'
  />
  <FormControlLabel control={<Checkbox defaultChecked />} label='Charge tax on this product' />
  <Divider className='mlb-2' />
  <div className='flex items-center justify-between'>
    <Typography>In stock</Typography>
    <Switch defaultChecked />
  </div>
</Form>
```

#### ProductCategoryTable (Client component)

TanStack React Table for category management:

```typescript
export type categoryType = {
  id: number
  categoryTitle: string
  description: string
  totalProduct: number
  totalEarning: number
  image: string
}
```

Features:
- Fuzzy search filtering
- Pagination
- Checkbox selection
- AddCategoryDrawer modal for new categories
- CRUD operations

### 3. Orders Domain

**Location:** `/src/views/apps/ecommerce/orders/`

Order management with list view and detailed order information.

#### OrderListTable (Client component)

Advanced data table with columns:
- Order ID
- Customer name/avatar
- Email
- Payment method (Visa, PayPal, Mastercard)
- Status (processing, completed, cancelled)
- Amount spent
- Order date

Integrates TanStack React Table with fuzzy search and filtering.

#### OrderDetailsView (Server + Client components)

Multi-component detail page structure:

**OrderDetailHeader** - Order ID, status badge, action menu
**OrderDetailsCard** - Product table with:
- Product image + name
- Brand
- Price
- Quantity
- Total amount
- Column totals (sum calculation)

```typescript
type dataType = {
  productName: string
  productImage: string
  brand: string
  price: number
  quantity: number
  total: number
}
```

**BillingAddressCard** - Billing address display
**ShippingAddressCard** - Delivery address with map
**CustomerDetailsCard** - Customer contact info
**ShippingActivityCard** - Shipping timeline with status updates

### 4. Customers Domain

**Location:** `/src/views/apps/ecommerce/customers/`

Customer relationship management with list and detail views.

#### CustomerListTable (Client component)

Advanced data table with columns:
- Customer avatar + name
- Email
- Country (with flag)
- Total orders
- Total spent
- Payment status (Paid, Pending, Cancelled, Failed)
- Actions

```typescript
export type Customer = {
  id: number
  customer: string
  customerId: string
  email: string
  country: string
  countryCode: string
  countryFlag?: string
  order: number
  totalSpent: number
  avatar: string
  status?: string
  contact?: string
}

type PayementStatusType = {
  text: string
  color: ThemeColor
}

export const paymentStatus: { [key: number]: PayementStatusType } = {
  1: { text: 'Paid', color: 'success' },
  2: { text: 'Pending', color: 'warning' },
  3: { text: 'Cancelled', color: 'secondary' },
  4: { text: 'Failed', color: 'error' }
}
```

**AddCustomerDrawer** - Modal for adding new customers
- Form inputs for customer details
- Address information
- Contact preferences

#### CustomerDetailsView (Server + Client components)

**CustomerDetailsHeader** - Customer name, status, action menu
**CustomerDetails** - Comprehensive customer information
**CustomerPlan** - Subscription/plan details
**AddressBookCard** - Saved addresses
**PaymentMethodCard** - Payment methods on file
**OrderListTable** - Customer's order history
**CustomerStatisticsCard** - Spending metrics
**ChangePassword** - Security settings
**RecentDevice** - Login history
**TwoStepVerification** - Security options

### 5. Reviews & Referrals Domain

**Location:** `/src/views/apps/ecommerce/manage-reviews/` and `/src/views/apps/ecommerce/referrals/`

#### ManageReviewsTable

Product review management:
```typescript
export type ReviewType = {
  id: number
  product: string
  companyName: string
  productImage: string
  reviewer: string
  email: string
  avatar: string
  date: string
  status: string
  review: number
  head: string
  para: string
}
```

Displays:
- Product image + name
- Reviewer avatar + name
- Star rating (1-5)
- Review text (heading + paragraph)
- Status (approved/pending/rejected)
- Date

**ReviewsStatistics** - Overview stats (total reviews, avg rating, pending reviews)
**TotalReviews** - Aggregate review count and metrics

#### ReferralsView

```typescript
export type ReferralsType = {
  id: number
  user: string
  email: string
  avatar: string
  referredId: number
  status: string
  value: string
  earning: string
}
```

Components:
- **ReferredUsersTable** - List of referred customers
- **InviteAndShare** - Referral link generation
- **IconStepsCard** - How referral works guide
- **HorizontalStatisticsCard** - Referral metrics

### 6. Settings Domain

**Location:** `/src/views/apps/ecommerce/settings/`

Store configuration and preferences.

#### Settings Sections

**Notifications** - Email/push notification preferences
**ShippingDelivery** - Shipping methods, rates, zones
**CustomerInformation** - Customer form fields, validations
**Locations** - Warehouse/store locations
**Payments** - Payment gateway integration
  - PaymentProviders - Stripe, PayPal, Razorpay
  - SupportedMethods - Card, wallet, bank transfer
  - ManualMethods - Wire transfer, check payment

**StoreDetails** - Core store information
  - Profile - Store name, description
  - BillingInformation - Tax ID, legal entity
  - StoreCurrency - Currency selection, formatting
  - TimeZone - Timezone configuration
  - OrderIdFormat - Order ID numbering scheme

---

## Type Definitions

### Core Entity Types

```typescript
// Product
export type ProductType = {
  id: number
  productName: string
  category: string
  stock: boolean
  sku: number
  price: string
  qty: number
  status: string
  image: string
  productBrand: string
}

// Order
export type OrderType = {
  id: number
  order: string
  customer: string
  email: string
  avatar: string
  payment: number
  status: string
  spent: number
  method: string
  date: string
  time: string
  methodNumber: number
}

// Customer
export type Customer = {
  id: number
  customer: string
  customerId: string
  email: string
  country: string
  countryCode: string
  countryFlag?: string
  order: number
  totalSpent: number
  avatar: string
  status?: string
  contact?: string
}

// Review
export type ReviewType = {
  id: number
  product: string
  companyName: string
  productImage: string
  reviewer: string
  email: string
  avatar: string
  date: string
  status: string
  review: number
  head: string
  para: string
}

// Referral
export type ReferralsType = {
  id: number
  user: string
  email: string
  avatar: string
  referredId: number
  status: string
  value: string
  earning: string
}

// Aggregate
export type ECommerceType = {
  products: ProductType[]
  orderData: OrderType[]
  customerData: Customer[]
  reviews: ReviewType[]
  referrals: ReferralsType[]
}
```

### UI-Specific Types

**Category Types:**
```typescript
export type categoryType = {
  id: number
  categoryTitle: string
  description: string
  totalProduct: number
  totalEarning: number
  image: string
}

type ProductCategoryType = {
  [key: string]: {
    icon: string
    color: ThemeColor
  }
}
```

**Status Types:**
```typescript
type paymentStatusType = {
  text: string
  color: ThemeColor
}

type productStatusType = {
  [key: string]: {
    title: string
    color: ThemeColor
  }
}
```

---

## Patterns & Architecture

### 1. TanStack React Table Pattern

All data tables (products, orders, customers, reviews) use:

```typescript
const fuzzyFilter: FilterFn<any> = (row, columnId, value, addMeta) => {
  const itemRank = rankItem(row.getValue(columnId), value)
  addMeta({ itemRank })
  return itemRank.passed
}

const table = useReactTable({
  data,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  globalFilterFn: fuzzyFilter,
  state: {
    globalFilter: searchValue,
    pagination,
    rowSelection
  }
})
```

Features:
- Fuzzy search filtering
- Multi-column sorting
- Server-safe pagination (client-side in demo)
- Row selection with checkboxes
- Column definition helpers

### 2. Drawer Modal Pattern

Adding/editing entities uses drawer modals:

```typescript
const [addCustomerOpen, setAddCustomerOpen] = useState(false)

<Button onClick={() => setAddCustomerOpen(true)}>
  + Add Customer
</Button>

<AddCustomerDrawer
  open={addCustomerOpen}
  handleClose={() => setAddCustomerOpen(false)}
/>
```

### 3. Form Composition Pattern

Product add form breaks down into sections:

```typescript
const ProductForm = () => (
  <div>
    <ProductAddHeader />
    <Grid container spacing={6}>
      <Grid size={{ xs: 12, md: 8 }}>
        <ProductInformation />
        <ProductImage />
        <ProductVariants />
      </Grid>
      <Grid size={{ xs: 12, md: 4 }}>
        <ProductPricing />
        <ProductInventory />
        <ProductOrganize />
      </Grid>
    </Grid>
  </div>
)
```

### 4. Detail View Layout Pattern

Order/Customer details use 2-column layout:

```typescript
<Grid container spacing={6}>
  <Grid size={{ xs: 12, md: 8 }}>
    <OrderDetailsCard /> {/* Main table */}
    <BillingAddressCard />
    <ShippingActivityCard />
  </Grid>
  <Grid size={{ xs: 12, md: 4 }}>
    <CustomerDetailsCard />
    <OrderDetailHeader /> {/* Status, actions */}
  </Grid>
</Grid>
```

### 5. Timeline Pattern

Order shipping status uses MUI Timeline:

```typescript
<Timeline>
  {timelineData.map(item => (
    <TimelineItem key={item.id}>
      <TimelineSeparator>
        <TimelineDot color={item.color} />
        <TimelineConnector />
      </TimelineSeparator>
      <TimelineContent>
        {item.content}
      </TimelineContent>
    </TimelineItem>
  ))}
</Timeline>
```

---

## Dependency List

### Core Dependencies
- Next.js 14+ (server/client components, navigation)
- React 18+ (hooks, context)
- @mui/material - Core UI components
- @mui/lab - Timeline, Tab components
- @tanstack/react-table - Advanced data tables
- @tanstack/match-sorter-utils - Fuzzy search
- classnames - Conditional CSS
- Tailwind CSS - Utility styles

### Internal Dependencies
- @core/types - ThemeColor, SystemMode
- @core/components - CustomAvatar, CustomTextField, OptionMenu
- @core/styles - Table CSS module
- @components/Form - Form wrapper
- @components/Link - Localized links
- @components/TablePaginationComponent - Pagination UI
- @/types/apps/ecommerceTypes - Entity type definitions
- @/utils/getInitials - Avatar initials utility
- @/utils/i18n - Localization utilities

### Import Patterns

```typescript
// TanStack React Table
import {
  createColumnHelper,
  flexRender,
  getCoreRowModel,
  useReactTable,
  getFilteredRowModel,
  getFacetedRowModel,
  getFacetedUniqueValues,
  getFacetedMinMaxValues,
  getPaginationRowModel,
  getSortedRowModel
} from '@tanstack/react-table'
import { rankItem } from '@tanstack/match-sorter-utils'

// MUI Timeline
import TimelineDot from '@mui/lab/TimelineDot'
import TimelineItem from '@mui/lab/TimelineItem'
import TimelineContent from '@mui/lab/TimelineContent'
import TimelineSeparator from '@mui/lab/TimelineSeparator'
import TimelineConnector from '@mui/lab/TimelineConnector'
import MuiTimeline from '@mui/lab/Timeline'
```

---

## Data Flow

### List → Detail Navigation
```
ProductListTable 
  → Link to /products/list/[id]
  → ProductDetailsView (Server + Client)
  → imports ProductDetailsCard, etc.
```

### Add Form Workflow
```
ProductListTable
  → + Add button
  → opens Drawer (AddProductDrawer)
  → FormSubmission
  → updates table data
  → closes drawer
```

### Table Filtering Flow
```
User enters search → DebouncedInput (500ms)
  → onChange calls table.setGlobalFilter()
  → fuzzyFilter runs (TanStack React Table)
  → table re-renders with filtered rows
```

---

## Component Statistics

**Total Ecommerce Components:** 60+
- Dashboard: 10 widgets
- Products: 20+ (listing, add, category)
- Orders: 15+ (list, details, shipping)
- Customers: 18+ (list, details, profile)
- Reviews: 3 (management, statistics)
- Referrals: 4 (users, sharing, metrics)
- Settings: 15+ (various configuration sections)

**Lines of Code:** ~8,000 LOC across ecommerce domain
**State Management:** useState for local state, no external state library
**Data Fetching:** Hardcoded data (demo mode), easily replaceable with API calls

---

## Summary

Ecommerce domain demonstrates **enterprise-grade data management** using:
- TanStack React Table for production-ready tables
- Advanced form composition patterns
- Drawer modals for CRUD operations
- Timeline components for process visualization
- Comprehensive TypeScript typing
- Server/Client component separation
- Responsive grid layouts
- Fuzzy search and filtering

**Perfect for:** Building product catalogs, order management systems, customer portals, marketplace dashboards.
