# Ecommerce List Pages Snapshot

**Overview**

The ecommerce module includes three primary list pages for managing products, orders, and customers. Each list page implements a data table with filtering, searching, sorting, pagination, and bulk selection capabilities using TanStack React Table. The architecture follows a server-side data fetching pattern with server components for initial data loading and client-side interactive features for filtering and table management.

**File Location**: src/app/[lang]/(dashboard)/(private)/apps/ecommerce/

**Directory Structure**:
\\\
ecommerce/
├── products/
│   ├── add/          (Add product page)
│   ├── category/     (Product categories)
│   └── list/         (Products list page)
│       ├── page.tsx              (Server component - main page)
│       ├── ProductListTable.tsx   (Client - table with TanStack)
│       ├── ProductCard.tsx        (Client - stats cards)
│       └── TableFilters.tsx       (Client - filter controls)
├── orders/
│   ├── details/      (Order details page)
│   └── list/
│       ├── page.tsx              (Server component - main page)
│       └── (OrderList component)
├── customers/
│   ├── details/      (Customer details page)
│   └── list/
│       ├── page.tsx              (Server component - main page)
│       └── (CustomerListTable component)
└── (other sections)
\\\

---

## Key Features & Architecture

### **1. Data Fetching Pattern**

All list pages use server components with the getEcommerceData() server action:

\\\	ypescript
// Server component pattern
const eCommerceProductsList = async () => {
  // Vars
  const data = await getEcommerceData()
  
  return (
    <Grid container spacing={6}>
      <Grid size={{ xs: 12 }}>
        <ProductCard />
      </Grid>
      <Grid size={{ xs: 12 }}>
        <ProductListTable productData={data?.products} />
      </Grid>
    </Grid>
  )
}
\\\

**Benefits**:
- Server-side data fetching eliminates client-side data loading spinners
- Reduced JavaScript bundle size (data fetching logic stays on server)
- Better SEO due to server-rendered content
- Improved security (sensitive operations on server)

**Fallback Support**: Comments include API fetch examples for optional API-based data loading

---

### **2. Products List Page Structure**

#### **Page Components Hierarchy**

\\\
ProductsList Page (Server Component)
├── ProductCard (Client Component)
│   └── Stats display (4 metric cards)
│       ├── In-Store Sales
│       ├── Website Sales
│       ├── Discount
│       └── Affiliate
└── ProductListTable (Client Component)
    ├── TableFilters (Client Component)
    │   ├── Status filter (dropdown)
    │   ├── Category filter (dropdown)
    │   └── Stock filter (dropdown)
    ├── Global search input
    ├── Page size selector
    ├── Export button
    ├── Add Product button
    └── TanStack Table (sorting, selection, pagination)
        ├── Checkbox column (select)
        ├── Product column (image + name + brand)
        ├── Category column (icon + label)
        ├── Stock column (toggle switch)
        ├── SKU column
        ├── Price column
        ├── Quantity column
        ├── Status column (chip)
        └── Actions column (edit, delete, duplicate)
\\\

#### **ProductCard Component** (ProductCard.tsx)

**Purpose**: Displays KPI statistics for product sales and metrics

**Key Features**:
- 4 statistics cards in responsive grid (1 on mobile, 2 on tablet, 4 on desktop)
- Icons with custom avatars
- Percentage change indicators with colored chips
- Dynamic dividers based on screen size

**Responsive Behavior**:
- **XS (mobile)**: Single column with dividers between items
- **SM (tablet)**: 2 columns with right-side dividers on odd items
- **MD+ (desktop)**: 4 columns with right-side dividers (except last)

**Data Structure**:
\\\	ypescript
type DataType = {
  title: string        // "In-Store Sales"
  value: string        // ",345"
  icon: string         // "tabler-smart-home"
  desc: string         // "5k"
  change?: number      // 5.7 or -3.5
}
\\\

**Static Data**:
- In-Store Sales: ,345 with 5.7% change
- Website Sales: ,347 with 12.4% change
- Discount: ,235 (no change indicator)
- Affiliate: ,345 with -3.5% change

**Styling Notes**:
- Uses MUI Grid with responsive size prop
- Responsive dividers using classnames for breakpoint logic
- Custom Avatar with icons from Tabler icon set
- Chips for percentage display (success/error based on sign)

---

#### **TableFilters Component** (TableFilters.tsx)

**Purpose**: Provides filtering interface for table data

**Filter Fields**:
1. **Status** dropdown
   - Options: Scheduled, Published, Inactive
   - Default: "Select Status"

2. **Category** dropdown
   - Options: Accessories, Home Decor, Electronics, Shoes, Office, Games
   - Default: "Select Category"

3. **Stock** dropdown
   - Options: In Stock, Out of Stock
   - Default: "Select Stock"

**Filtering Logic**:
- Runs on mount and dependency change
- Filters productData by selected criteria
- Multiple filters work together (AND logic)
- Updates parent state via setData callback

**Props**:
\\\	ypescript
{
  setData: (data: ProductType[]) => void  // Callback to update filtered results
  productData?: ProductType[]              // Source data to filter
}
\\\

**Hook Usage**:
- useState: category, stock, status (three independent filter states)
- useEffect: Watches all filter changes and productData, applies filtering

**Responsive**:
- 12/12 cols on mobile (full width)
- 12/4 cols on larger screens (3 columns)

---

#### **ProductListTable Component** (ProductListTable.tsx)

**Purpose**: Core data table with advanced features (TanStack React Table v8)

**Key Features**:

1. **Fuzzy Filtering**
   - Debounced search input (500ms delay)
   - Uses @tanstack/match-sorter-utils for ranking
   - Real-time filtering as user types

2. **TanStack Table Features**
   - Row selection (checkbox column with header select-all)
   - Column sorting (click headers)
   - Pagination (configurable page size: 10, 25, 50)
   - Global filter with debouncing
   - Faceted filtering support (prepared for future use)

3. **Columns** (8 total):
   - **Select**: Checkbox with multi-select capability
   - **Product**: Image thumbnail + product name + brand
   - **Category**: Icon avatar + category label
   - **Stock**: Toggle switch (true/false)
   - **SKU**: Numeric stock keeping unit
   - **Price**: Product price
   - **QTY**: Quantity
   - **Status**: Colored chip (Scheduled/Published/Inactive)
   - **Actions**: Edit button, dropdown menu (Download/Delete/Duplicate)

4. **Action Menu Options**:
   - Download: Export product data
   - Delete: Remove from table (filtered from state)
   - Duplicate: Copy product configuration

5. **Table Controls**:
   - Global search box (debounced input)
   - Page size dropdown (10, 25, 50)
   - Export button (secondary tonal)
   - Add Product button (primary, links to /apps/ecommerce/products/add)
   - Custom pagination component

**Category Mapping**:
\\\	ypescript
{
  Accessories: { icon: 'tabler-headphones', color: 'error' },
  'Home Decor': { icon: 'tabler-smart-home', color: 'info' },
  Electronics: { icon: 'tabler-device-laptop', color: 'primary' },
  Shoes: { icon: 'tabler-shoe', color: 'success' },
  Office: { icon: 'tabler-briefcase', color: 'warning' },
  Games: { icon: 'tabler-device-gamepad-2', color: 'secondary' }
}
\\\

**Status Mapping**:
\\\	ypescript
{
  Scheduled: { title: 'Scheduled', color: 'warning' },
  Published: { title: 'Publish', color: 'success' },
  Inactive: { title: 'Inactive', color: 'error' }
}
\\\

**State Management**:
\\\	ypescript
const [rowSelection, setRowSelection] = useState({})        // Selected rows
const [data, setData] = useState(productData)               // All products
const [filteredData, setFilteredData] = useState(data)      // Filtered results
const [globalFilter, setGlobalFilter] = useState('')        // Search query
\\\

**DebouncedInput Component** (Internal):
- Debounced text input with custom delay (default 500ms)
- Prevents excessive table re-renders during typing
- Uses useEffect with cleanup timeout for debouncing

**Styling**:
- Uses tableStyles.table from @core/styles/table.module.css
- Custom row selection styles with .selected class
- Responsive overflow handling with overflow-x-auto
- Flex utilities for control alignment

---

### **3. Orders List Page Structure**

**Location**: /src/app/[lang]/(dashboard)/(private)/apps/ecommerce/orders/list/

**Similar pattern to products**:
- Server component that fetches orderData via getEcommerceData()
- Renders OrderList client component
- Passes order data as props

**Expected Components**:
- OrderList (main client component)
- Order filtering/searching capabilities
- Order status chips (Pending, Processing, Shipped, Delivered, Cancelled)
- Payment method indicators

---

### **4. Customers List Page Structure**

**Location**: /src/app/[lang]/(dashboard)/(private)/apps/ecommerce/customers/list/

**Similar pattern to products**:
- Server component that fetches customerData via getEcommerceData()
- Renders CustomerListTable client component
- Passes customer data as props

**Expected Components**:
- CustomerListTable (main client component)
- Customer filtering by country, status
- Contact information display
- Order history and spending totals

---

## Type Definitions

### **ProductType** (src/types/apps/ecommerceTypes.ts)

\\\	ypescript
export type ProductType = {
  id: number              // Unique identifier
  productName: string     // Display name
  category: string        // Category (Accessories, Electronics, etc.)
  stock: boolean          // In stock status
  sku: number             // Stock keeping unit
  price: string           // Price (currency formatted)
  qty: number             // Quantity available
  status: string          // Scheduled | Published | Inactive
  image: string           // Image URL
  productBrand: string    // Brand name
}
\\\

**Usage Example**:
\\\	ypescript
{
  id: 1,
  productName: "Wireless Earbuds",
  category: "Electronics",
  stock: true,
  sku: 12345,
  price: ".99",
  qty: 150,
  status: "Published",
  image: "https://...",
  productBrand: "TechCorp"
}
\\\

### **OrderType** (src/types/apps/ecommerceTypes.ts)

\\\	ypescript
export type OrderType = {
  id: number              // Order ID
  order: string           // Order number
  customer: string        // Customer name
  email: string           // Customer email
  avatar: string          // Customer avatar URL
  payment: number         // Payment amount
  status: string          // Order status
  spent: number           // Total spent
  method: string          // Payment method name
  date: string            // Order date
  time: string            // Order time
  methodNumber: number    // Payment method code
}
\\\

**Usage Example**:
\\\	ypescript
{
  id: 101,
  order: "ORD-2024-001",
  customer: "John Doe",
  email: "john@example.com",
  avatar: "https://...",
  payment: 250,
  status: "Delivered",
  spent: 250,
  method: "Credit Card",
  date: "2024-01-15",
  time: "10:30 AM",
  methodNumber: 1
}
\\\

### **Customer** (src/types/apps/ecommerceTypes.ts)

\\\	ypescript
export type Customer = {
  id: number              // Customer ID
  customer: string        // Customer name
  customerId: string      // Customer identifier
  email: string           // Email address
  country: string         // Country name
  countryCode: string     // ISO country code
  countryFlag?: string    // Flag emoji or image URL
  order: number           // Number of orders
  totalSpent: number      // Total spending amount
  avatar: string          // Avatar URL
  status?: string         // Customer status (Active, Inactive, etc.)
  contact?: string        // Contact information
}
\\\

**Usage Example**:
\\\	ypescript
{
  id: 1,
  customer: "Alice Johnson",
  customerId: "CUST-001",
  email: "alice@example.com",
  country: "United States",
  countryCode: "US",
  countryFlag: "🇺🇸",
  order: 15,
  totalSpent: 5400,
  avatar: "https://...",
  status: "Active",
  contact: "+1-555-0123"
}
\\\

### **ECommerceType** (Master type)

\\\	ypescript
export type ECommerceType = {
  products: ProductType[]
  orderData: OrderType[]
  customerData: Customer[]
  reviews: ReviewType[]
  referrals: ReferralsType[]
}
\\\

---

## Component Dependencies

### **External Libraries**:
- **MUI Components**: Grid, Card, CardHeader, Button, Chip, Checkbox, IconButton, Switch, MenuItem, TextField, Typography, TablePagination
- **TanStack React Table v8**: 
  - createColumnHelper
  - useReactTable
  - getCoreRowModel, getFilteredRowModel, getSortedRowModel, getPaginationRowModel
  - getFacetedRowModel, getFacetedUniqueValues, getFacetedMinMaxValues
  - flexRender
- **@tanstack/match-sorter-utils**: rankItem for fuzzy filtering
- **classnames**: CSS class composition for conditional styling
- **Next.js**: Link for navigation, useParams for i18n
- **React**: useState, useEffect, useMemo hooks

### **Custom Components**:
- **CustomAvatar**: Rounded avatar with icon or image support
- **CustomTextField**: Enhanced Material-UI TextField with MUI styling
- **OptionMenu**: Dropdown menu for row actions
- **TablePaginationComponent**: Custom pagination UI wrapper

### **Utilities**:
- **getLocalizedUrl**: i18n URL routing helper
- **tableStyles**: CSS module for table styling

### **Server Functions**:
- **getEcommerceData**: Server action for fetching ecommerce data

---

## Usage Patterns

### **1. Data Flow (Products List)**

\\\
page.tsx (Server Component)
  ↓
  getEcommerceData() [server action]
    ↓
    Returns: { products, orderData, customerData, ... }
    ↓
    └─→ ProductCard (visualize KPI stats)
    └─→ ProductListTable (interactive table)
         ├─→ TableFilters (category, status, stock dropdowns)
         └─→ TanStack Table (sorting, search, pagination, selection)
\\\

### **2. Filtering Flow**

\\\
User selects filter option (e.g., category)
  ↓
TableFilters onChange handler fires
  ↓
setCategory(value) updates state
  ↓
useEffect dependency: [category, stock, status, productData]
  ↓
Filter logic applies: product.category === category AND ...
  ↓
setData(filteredData) callback → parent table state updates
  ↓
ProductListTable receives new filteredData prop
  ↓
Table re-renders with filtered results
\\\

### **3. Search/Sort/Paginate Flow**

\\\
User types in search box
  ↓
DebouncedInput waits 500ms
  ↓
onChange fires → setGlobalFilter(value)
  ↓
TanStack applies fuzzyFilter to all rows
  ↓
rankItem ranks matches from @tanstack/match-sorter-utils
  ↓
Table updates with ranked/filtered results
  ↓

User clicks column header to sort
  ↓
header.column.getToggleSortingHandler() fires
  ↓
getSortedRowModel() reorders data ASC/DESC
  ↓
Column shows sort indicator (chevron up/down)
  ↓

User changes page size or pagination
  ↓
getPaginationRowModel() slices rows for current page
  ↓
Custom pagination component updates page indicators
\\\

### **4. Row Selection Flow**

\\\
User checks row checkbox
  ↓
row.getToggleSelectedHandler() fires
  ↓
setRowSelection(updated) updates table state
  ↓
Checkbox column updates selection visual
  ↓
Can batch operations on selected rows (delete, export, etc.)
\\\

---

## Implementation Highlights

### **Server vs. Client Component Strategy**

**Server Components** (page.tsx):
- Fetch data from getEcommerceData() server action
- No JavaScript sent to browser for this component
- Perform data transformation if needed
- Pass data to client components as props

**Client Components** (TableFilters, ProductListTable, ProductCard):
- Handle all user interactions (filtering, searching, sorting)
- Manage local UI state (pagination, row selection, search query)
- Provide real-time feedback and updates
- All JavaScript for interactivity runs in browser

**Benefits**:
- Initial page load is fast (server-rendered content)
- Interactive features work smoothly (client-side state)
- Efficient bundle size (data fetching on server)
- Security (API calls on server, not exposed in browser)

### **Performance Optimizations**

1. **Debounced Input**: 500ms delay prevents excessive re-renders during typing
2. **useMemo for Columns**: Column definitions cached to prevent recreation on every render
3. **Pagination**: Only renders visible page (default 10 rows per page)
4. **Efficient Filtering**: Array filtering with early returns stops at first mismatch
5. **Lazy Evaluation**: TanStack only calculates data needed for current view
6. **Row Selection State**: Uses TanStack's efficient state management

### **Responsive Design**

**ProductCard Stats**:
- Mobile (XS): Single column with dividers
- Tablet (SM): 2 columns with strategic dividers
- Desktop (MD+): 4 columns in row

**TableFilters**:
- Mobile (XS): 12/12 (full width, stacked)
- Tablet+ (SM+): 12/4 (three columns)

**ProductListTable**:
- Mobile: Horizontal scroll for table
- Desktop: Full width table display
- Controls: Stack on mobile, horizontal on desktop

**Table Pagination**:
- Mobile: Simplified pagination controls
- Desktop: Full pagination UI with page info

### **Accessibility Features**

- **Semantic HTML**: Proper table structure with <thead>, <tbody>, <tr>, <td>
- **Checkboxes**: Proper ARIA attributes for selection state
- **Sorting**: Visual indicators (chevron icons) for sort direction
- **Status Chips**: Color + text labels (not color alone)
- **Action Buttons**: Icon buttons with proper focus management
- **Keyboard Navigation**: All controls keyboard accessible
- **Labels**: Form fields have proper labels and descriptions

---

## Related Pages & Navigation

**From Products List**:
- Add Product button → /apps/ecommerce/products/add
- Product name (future) → /apps/ecommerce/products/[id]/view
- Edit icon (future) → Edit drawer/modal
- Delete action → Remove from table and database

**From Orders List**:
- Order number (future) → /apps/ecommerce/orders/[id]/details
- Customer name (future) → /apps/ecommerce/customers/[id]/details

**From Customers List**:
- Customer name (future) → /apps/ecommerce/customers/[id]/details
- Country flag (future) → Filter by country

---

## Data Source & API Integration

### **Current Implementation** (Server Action)

\\\	ypescript
// src/app/server/actions.ts
export const getEcommerceData = async () => {
  // Returns { products, orderData, customerData, reviews, referrals }
}
\\\

### **Alternative**: API-Based Data Loading

For API-based data loading instead of server action:

\\\	ypescript
const getEcommerceData = async () => {
  const res = await fetch(\\/apps/ecommerce\)
  
  if (!res.ok) {
    throw new Error('Failed to fetch ecommerce data')
  }
  
  return res.json()
}
\\\

**Environment Variables**:
- \process.env.API_URL\: Base API URL (e.g., https://api.example.com)

---

## File Statistics

**Products List Page Implementation**:
- page.tsx: 46 lines
- ProductCard.tsx: 117 lines
- TableFilters.tsx: 113 lines
- ProductListTable.tsx: 406 lines (with TanStack configuration)
- **Total: ~682 lines**

**Orders List Page**:
- page.tsx: ~32 lines
- OrderList component: ~400-500 lines (similar to ProductListTable)

**Customers List Page**:
- page.tsx: ~32 lines
- CustomerListTable component: ~400-500 lines (similar structure)

---

## Key Learnings & Best Practices

1. **TanStack React Table v8** provides powerful, headless table utilities without coupling to UI frameworks
2. **Server Components** enable efficient data fetching with minimal JavaScript overhead
3. **Fuzzy Filtering** with match-sorter-utils is effective for user-friendly search (tolerates typos)
4. **Column Helper Pattern** provides type-safe column definitions with great DX
5. **Responsive Grids** need thoughtful breakpoint planning for small to large screens
6. **Debouncing Search** is essential for search inputs to avoid performance issues
7. **Row Selection** is prepared for bulk operations (delete, export, etc.)
8. **Filter Composition** allows AND/OR logic for multi-filter scenarios
9. **Pagination** improves perceived performance and usability for large datasets
10. **Action Menus** provide context-specific operations without cluttering the UI

---

*Last Updated: Phase 3 Ecommerce & Forms, Task kb-79w.3.1*
*File Format: Markdown Snapshot (Architecture Documentation)*
*Implementation: Next.js 15 + React 19 + TanStack React Table v8 + Material-UI*