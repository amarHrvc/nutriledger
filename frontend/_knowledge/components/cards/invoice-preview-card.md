# InvoicePreviewCard Component

**Path**: `src/views/apps/invoice/preview/PreviewCard.tsx`
**Type**: Display-only Client Component
**Purpose**: Read-only invoice preview card displaying invoice details, items, and summary information

## Overview

The `InvoicePreviewCard` component is a display-only card that presents a complete invoice for viewing and printing. It shows all invoice details including header with company information, customer billing details, itemized services/products, and financial summary. This component is typically used after an invoice has been created or when reviewing existing invoices.

## Features

- **Invoice Header Section**: 
  - Company logo and information
  - Office address and contact details
  - Invoice number display with ID
  - Issued and due date display
- **Customer Information Sections**:
  - Invoice To: Customer name, company, address, contact, email
  - Bill To: Payment details (bank name, country, IBAN, SWIFT code)
- **Line Items Table**:
  - Item name/service description
  - Description details
  - Hours worked
  - Quantity
  - Total amount per item
  - Data-driven table rendering from static array
- **Financial Summary**:
  - Subtotal calculation
  - Discount amount
  - Tax percentage
  - Final Total with visual separation
- **Footer Section**:
  - Salesperson name display
  - Business thank you note
  - Notes section
- **Print-Ready**: Includes print.css for optimal printing
- **Static Data**: Uses hardcoded invoice item data and payment details

## Component Props

```typescript
interface PreviewCardProps {
  invoiceData?: InvoiceType
  id: string
}
```

### Prop Details

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `invoiceData` | `InvoiceType` | No | Single invoice object containing customer details, dates, and status |
| `id` | `string` | Yes | Invoice ID number displayed in the header |

## Type Definitions

### InvoiceType

```typescript
export type InvoiceType = {
  id: string                    // Unique invoice identifier
  name: string                  // Customer name
  total: number                 // Total invoice amount
  avatar: string                // Customer avatar URL
  service: string               // Service description
  dueDate: string               // Invoice due date
  address: string               // Customer address
  company: string               // Company name
  country: string               // Country
  contact: string               // Contact number
  avatarColor?: string          // Avatar background color
  issuedDate: string            // Date invoice was issued
  companyEmail: string          // Company email
  balance: string | number      // Outstanding balance
  invoiceStatus: InvoiceStatus  // Status indicator
}

export type InvoiceStatus = 'Paid' | string
```

### Invoice Item Data

```typescript
interface InvoiceLineItem {
  Item: string                  // Service/product name
  Description: string           // Detailed description
  Hours: number                 // Hours worked or quantity
  Qty: number                   // Quantity of items
  Total: string                 // Total cost string
}
```

Static data array:
```typescript
const data = [
  {
    Item: 'Premium Branding Package',
    Description: 'Branding & Promotion',
    Hours: 48,
    Qty: 1,
    Total: '$32'
  },
  {
    Item: 'Social Media',
    Description: 'Social media templates',
    Hours: 42,
    Qty: 1,
    Total: '$28'
  },
  {
    Item: 'Web Design',
    Description: 'Web designing package',
    Hours: 46,
    Qty: 1,
    Total: '$24'
  },
  {
    Item: 'SEO',
    Description: 'Search engine optimization',
    Hours: 40,
    Qty: 1,
    Total: '$22'
  }
]
```

## Key Dependencies

- **MUI Components**: Card, CardContent, Typography, Grid, Divider
- **Custom Components**:
  - `Logo`: Company logo display component
- **Styling**: 
  - Custom CSS modules (`tableStyles`)
  - Print stylesheet (`print.css`)
- **Type Imports**: InvoiceType from types/apps/invoiceTypes

## Styling Approach

- **MUI System**: Card and Grid layout system for structure
- **CSS Modules**: Custom table styling via `tableStyles.table`
- **Print Styles**: Dedicated `print.css` file for print-optimized formatting
- **Background Colors**: Uses `bg-actionHover` for header section

## Layout Structure

1. **Grid Container**: 12-column responsive layout using MUI Grid
2. **Header Section**: Full-width company info and invoice metadata
3. **Customer Details**: Two-column layout (Invoice To / Bill To)
4. **Items Table**: Full-width table section with overflow handling
5. **Summary Section**: Flexible layout with totals on one side
6. **Dividers**: Dashed dividers separate major sections

## Usage Patterns

### Basic Implementation with Static ID

```typescript
import PreviewCard from '@/views/apps/invoice/preview/PreviewCard'

export default function InvoicePreviewPage() {
  return (
    <PreviewCard 
      invoiceData={sampleInvoiceData}
      id="12345"
    />
  )
}
```

### Dynamic Invoice Loading

```typescript
'use client'

import { useParams } from 'next/navigation'
import PreviewCard from '@/views/apps/invoice/preview/PreviewCard'
import { getInvoiceById } from '@/actions/invoice'
import { Suspense } from 'react'

export default function InvoiceViewPage() {
  const params = useParams()
  const invoiceId = params.id as string

  return (
    <Suspense fallback={<div>Loading invoice...</div>}>
      <InvoicePreviewContent id={invoiceId} />
    </Suspense>
  )
}

async function InvoicePreviewContent({ id }: { id: string }) {
  const invoiceData = await getInvoiceById(id)
  return <PreviewCard invoiceData={invoiceData} id={id} />
}
```

### Printing Integration

```typescript
function InvoiceActions() {
  const handlePrint = () => {
    window.print()
  }

  return (
    <div>
      <PreviewCard invoiceData={invoiceData} id={id} />
      <button onClick={handlePrint}>Print Invoice</button>
    </div>
  )
}
```

## Data Flow

1. **Props Reception**: Component receives `invoiceData` and `id` as props
2. **Header Rendering**: 
   - Company logo and address from static config
   - Invoice number from `id` prop
   - Dates from `invoiceData.issuedDate` and `invoiceData.dueDate`
3. **Customer Details**:
   - Invoice To section populated from invoiceData fields
   - Bill To section with static payment details
4. **Items Rendering**:
   - Static data array mapped into table rows
   - Each item displays Item, Description, Hours, Qty, Total
5. **Summary**: Static values for subtotal, discount, tax, total

## Component Characteristics

- **Purely Presentational**: No state management or event handlers
- **Data-Driven**: Renders based on passed props and static data
- **Print-Optimized**: Includes CSS media queries for printing via print.css
- **Read-Only**: No interactive elements or form inputs
- **Static Content**: Most values are hardcoded (company details, payment info, item data)

## Accessibility Considerations

- Semantic table structure with thead and tbody
- Typography components with proper color contrast
- Logical content flow and hierarchy
- Dividers provide visual section separation
- Grid layout ensures responsive structure

## Performance Considerations

- Lightweight presentational component with minimal re-renders
- No state or effects to manage
- Static data array is constant
- Efficient table rendering with map function
- CSS modules for optimized styling

## Print Styling

The component includes a dedicated `print.css` file for print-specific formatting:
- Optimizes layout for paper size
- Removes unnecessary UI elements
- Adjusts colors and spacing for printing
- Prevents page breaks within sections
- Sets proper margins and padding

## Common Customization Points

1. **Company Information**: Update address, phone, email in header section
2. **Item Data**: Modify the static data array with actual invoice items
3. **Financial Summary**: Adjust subtotal, discount, tax, total values
4. **Payment Details**: Update bank name, IBAN, SWIFT code
5. **Salesperson/Notes**: Change footer text content
6. **Styling**: Modify CSS module classes and colors

## Related Components

- **PreviewActions**: Sibling component for action buttons
- **Logo**: Company branding component
- **CustomTextField**: Used in AddCard but not in PreviewCard

## Implementation Notes

- All numeric values are static/hardcoded - no calculations performed
- Invoice item data comes from const array, not props
- No interactive features - purely for display and printing
- Company details and payment info are static
- This is a read-only display component paired with AddCard (creation)
