# Ecommerce Quick Reference

**Domain:** `/src/views/apps/ecommerce/` | **Type File:** `ecommerceTypes.ts` | **Fake DB:** `ecommerce.ts`

---

## Q&A Quick Reference

### **Q: How do I add a new product?**
**A:** Use the **Product Add Form** (pages: `/products/add`)
1. Fill `ProductInformation` - name, SKU, description (Tiptap editor)
2. Upload images with `ProductImage` (drag-drop support)
3. Add variants in `ProductVariants` (Size, Color, Weight, Smell)
4. Set pricing in `ProductPricing` (base/discounted, tax toggle)
5. Manage stock in `ProductInventory` (tabbed: Restock, Shipping, Delivery, Attributes, Advanced)
6. Organize in `ProductOrganize` (category, vendor, tags, status)
7. Submit via `useActionState` or server action

### **Q: How do I list and search products?**
**A:** Use **ProductListTable** Client Component (`/products/list`)
1. Data table powered by **TanStack React Table**
2. **DebouncedInput** (500ms) for fuzzy search across all columns
3. Columns: image, name, category, SKU, stock, price, qty, status, actions
4. **TableFilters** for category & status filtering
5. Multi-select with checkboxes for bulk operations
6. Sorting, pagination built-in

### **Q: How do I manage orders?**
**A:** Use **OrderListTable** + **OrderDetailsView**
1. **List View** (`/orders/list`) - shows order ID, customer, payment method, status, amount, date
2. **Detail View** (`/orders/[id]`) - comprehensive breakdown:
   - `OrderDetailsCard` - product table with totals
   - `BillingAddressCard` - billing info
   - `ShippingActivityCard` - timeline with status updates (MUI Timeline)
   - `CustomerDetailsCard` - customer contact info
   - Status updates: Use order status type mapping for color/label

### **Q: How do I manage customers?**
**A:** Use **CustomerListTable** + **CustomerDetailsView**
1. **List View** (`/customers/list`) - avatar, name, email, country, orders, total spent, payment status
2. **Detail View** (`/customers/[id]`) - full profile:
   - Customer basic info + status
   - `AddressBookCard` - multiple addresses
   - `PaymentMethodCard` - saved payment methods
   - Order history table
   - Security: password change, 2FA, login history
3. **AddCustomerDrawer** - modal for creating new customers

---

## Type Definitions

```typescript
// Core Product Type
export type ProductType = {
  id: number
  productName: string
  category: string              // Maps to ProductCategoryType
  stock: boolean                // In stock status
  sku: number
  price: string
  qty: number
  status: string                // Maps to productStatusType
  image: string
  productBrand: string
}

// Core Order Type
export type OrderType = {
  id: number
  order: string                 // Order ID
  customer: string              // Customer name
  email: string
  avatar: string
  payment: number               // Payment method index
  status: string                // order-processing, order-completed, etc.
  spent: number
  method: string                // "Visa", "PayPal", "Mastercard"
  date: string
  time: string
  methodNumber: number
}

// Core Customer Type
export type Customer = {
  id: number
  customer: string              // Customer name
  customerId: string
  email: string
  country: string
  countryCode: string
  countryFlag?: string
  order: number                 // Total orders count
  totalSpent: number
  avatar: string
  status?: string               // Payment status
  contact?: string
}

// Payment Status Mapping
export const paymentStatus: { [key: number]: { text: string; color: ThemeColor } } = {
  1: { text: 'Paid', color: 'success' },
  2: { text: 'Pending', color: 'warning' },
  3: { text: 'Cancelled', color: 'secondary' },
  4: { text: 'Failed', color: 'error' }
}
```

---

## Pattern Map

### **1. Product CRUD Pattern**

```
List Page (/products/list)
├── ProductCard (stats: sales, customers, products, revenue)
├── TableFilters (category, status dropdowns)
├── DebouncedInput (fuzzy search, 500ms debounce)
└── ProductListTable (TanStack table)
    ├── Columns: image, name, category, SKU, stock, price, qty, status, actions
    ├── Multi-select with checkboxes
    ├── Sorting + Pagination
    └── Actions: Edit (drawer), Delete, View details

Add/Edit Page (/products/add or drawer)
├── ProductAddHeader (title + action buttons)
├── Left Column (md: 8)
│   ├── ProductInformation (name, SKU, barcode, description with Tiptap)
│   ├── ProductImage (dropzone, preview, drag-drop)
│   └── ProductVariants (option type + value repeater)
├── Right Column (md: 4)
│   ├── ProductPricing (base, discounted, tax checkbox, stock toggle)
│   ├── ProductInventory (tabbed: Restock, Shipping, Delivery, Attributes, Advanced)
│   └── ProductOrganize (vendor, category, collection, status, tags)
└── Submit via useActionState or server action
```

### **2. Order Status Pattern**

```typescript
// Order Status Types
type OrderStatus = 
  | 'order-processing'  // Initial state
  | 'order-completed'   // Fulfilled
  | 'order-cancelled'   // Cancelled

// Order Status Mapping
const orderStatusMap: { [key: string]: { color: ThemeColor; label: string } } = {
  'order-processing': { color: 'warning', label: 'Processing' },
  'order-completed': { color: 'success', label: 'Completed' },
  'order-cancelled': { color: 'error', label: 'Cancelled' }
}

// Timeline Component for Shipping Status
<Timeline>
  <TimelineItem key="shipped">
    <TimelineSeparator>
      <TimelineDot color="success" />
      <TimelineConnector />
    </TimelineSeparator>
    <TimelineContent>Shipped on {date}</TimelineContent>
  </TimelineItem>
  {/* More timeline items */}
</Timeline>
```

### **3. Customer Data Pattern**

```typescript
// Customer Data with Payment Status
Customer {
  id, name, email, country, countryFlag, order count, totalSpent, avatar
  └── Payment Status (1=Paid, 2=Pending, 3=Cancelled, 4=Failed)

// Customer Detail View Structure
├── CustomerDetailsHeader (name, status chip, action menu)
├── Main Content (md: 8)
│   ├── CustomerPlan (subscription details)
│   ├── AddressBookCard (multiple saved addresses)
│   ├── PaymentMethodCard (credit cards, wallets)
│   ├── OrderListTable (customer's orders history)
│   └── CustomerStatisticsCard (spending metrics)
└── Sidebar (md: 4)
    ├── ChangePassword
    ├── RecentDevice (login history)
    └── TwoStepVerification
```

---

## Code Snippets

### **Product Add Form (useActionState Pattern)**

```typescript
'use client'

import { useState } from 'react'
import { useActionState } from 'react'
import Grid from '@mui/material/Grid'
import { ProductInformation, ProductPricing, ProductImage, 
         ProductInventory, ProductVariants, ProductOrganize } from './components'

async function addProductAction(prevState: any, formData: FormData) {
  const productData = {
    name: formData.get('productName'),
    sku: formData.get('sku'),
    price: formData.get('basePrice'),
    category: formData.get('category'),
    // ... rest of fields
  }
  
  try {
    const response = await fetch('/api/products', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(productData)
    })
    
    if (!response.ok) throw new Error('Failed to add product')
    return { success: true, message: 'Product added successfully' }
  } catch (error) {
    return { error: error.message }
  }
}

export function ProductAddForm() {
  const [state, formAction, isPending] = useActionState(addProductAction, {})

  return (
    <form action={formAction}>
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
      
      <div className="mt-6 flex gap-2">
        <button type="button">Discard</button>
        <button type="submit" disabled={isPending}>
          {isPending ? 'Publishing...' : 'Publish Product'}
        </button>
      </div>

      {state.error && <p className="text-error">{state.error}</p>}
      {state.success && <p className="text-success">{state.message}</p>}
    </form>
  )
}
```

### **Order Status Handling (Timeline + Badge)**

```typescript
'use client'

import { Chip } from '@mui/material'
import TimelineDot from '@mui/lab/TimelineDot'
import TimelineItem from '@mui/lab/TimelineItem'
import TimelineSeparator from '@mui/lab/TimelineSeparator'
import TimelineContent from '@mui/lab/TimelineContent'
import MuiTimeline from '@mui/lab/Timeline'

// Order status mapping
const orderStatusConfig = {
  'order-processing': { color: 'warning', label: 'Processing', icon: '⏳' },
  'order-completed': { color: 'success', label: 'Completed', icon: '✓' },
  'order-cancelled': { color: 'error', label: 'Cancelled', icon: '✕' }
}

export function OrderStatusBadge({ status }: { status: string }) {
  const config = orderStatusConfig[status] || { color: 'default', label: status }
  return <Chip label={config.label} color={config.color} />
}

export function ShippingTimeline({ events }: { events: ShippingEvent[] }) {
  return (
    <MuiTimeline>
      {events.map((event, idx) => (
        <TimelineItem key={idx}>
          <TimelineSeparator>
            <TimelineDot color={event.status === 'completed' ? 'success' : 'warning'} />
            {idx < events.length - 1 && <TimelineConnector />}
          </TimelineSeparator>
          <TimelineContent>
            <strong>{event.title}</strong>
            <p>{event.date} at {event.time}</p>
            <p>{event.address}</p>
          </TimelineContent>
        </TimelineItem>
      ))}
    </MuiTimeline>
  )
}
```

---

## Key Imports

```typescript
// TanStack React Table
import { useReactTable, getCoreRowModel, getFilteredRowModel, 
         getPaginationRowModel, getSortedRowModel } from '@tanstack/react-table'
import { rankItem } from '@tanstack/match-sorter-utils'

// MUI Timeline for Order Status
import MuiTimeline from '@mui/lab/Timeline'
import TimelineItem from '@mui/lab/TimelineItem'
import TimelineDot from '@mui/lab/TimelineDot'

// Types
import type { ProductType, OrderType, Customer } from '@/types/apps/ecommerceTypes'

// Components
import { DebouncedInput } from '@/components/DebouncedInput'
import { TablePaginationComponent } from '@/components/TablePaginationComponent'
```

---

## Data Flow Summary

1. **Product List** → Search (DebouncedInput) → TanStack fuzzy filter → Table renders
2. **Add Product** → Form (ProductAddForm) → useActionState → /api/products → Success/Error
3. **Order Management** → OrderListTable → Click row → OrderDetailsView → OrderDetailsCard + ShippingActivityCard (Timeline)
4. **Customer Management** → CustomerListTable → Click row → CustomerDetailsView → Multiple detail cards + AddressBook + OrderHistory

---

## Quick Tips

- **TanStack React Table** handles all sorting, filtering, pagination - no manual implementation needed
- **DebouncedInput** prevents excessive re-renders on every keystroke (500ms debounce)
- **ProductAddForm** splits into 6 sections for better organization and reusability
- **Order Timeline** uses MUI Timeline for visual shipping status representation
- **Payment Status** is numeric (1-4) with mapped labels - always use the mapping constant
- **All forms use useActionState** for progressive enhancement and loading states
- **Server/Client separation** - pages are async Server Components, interactive parts are Client
