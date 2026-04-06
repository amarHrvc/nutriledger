# AddInvoiceCard Component

**Path**: `src/views/apps/invoice/add/AddCard.tsx`
**Type**: Form-based Client Component
**Purpose**: Comprehensive invoice creation form with customer selection, line item management, and billing details

## Overview

The `AddInvoiceCard` component is the main form interface for creating new invoices in the application. It provides a complete invoice composition experience with dynamic line item management, customer selection via dropdown or new customer drawer, date selection, and automatic calculation displays.

## Features

- **Invoice Header Section**: Company information and invoice metadata display
- **Customer Management**: 
  - Dropdown selection from existing customers
  - Add New Customer option that opens a drawer
  - Auto-fill customer details when selected
  - Support for both selected and newly-added customers
- **Invoice Metadata**:
  - Invoice ID display (read-only)
  - Date Issued picker (with date formatting)
  - Date Due picker (with date formatting)
- **Line Items Management**:
  - Dynamic repeater pattern for invoice items
  - Item selection dropdown (App Design, Customization, etc.)
  - Description multiline text field
  - Cost input (numeric validation)
  - Hours input (numeric validation)
  - Automatic price calculation display
  - Per-item discount and tax indicators
  - Delete individual line items functionality
  - Add Item button to create additional line items
- **Billing Information Section**:
  - Total Due amount
  - Bank details (name, country)
  - IBAN and SWIFT code display
- **Summary Section**:
  - Salesperson field
  - Notes field (multiline)
  - Subtotal, Discount, Tax, and Total calculations
- **Responsive Design**: Adapts layout for different screen sizes (mobile, tablet, desktop)

## Component Props

```typescript
interface AddCardProps {
  invoiceData?: InvoiceType[]
}
```

### Prop Details

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `invoiceData` | `InvoiceType[]` | No | Array of existing invoice records used for customer dropdown population |

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
```

### FormDataType (Customer Form Data)

```typescript
export type FormDataType = {
  name: string           // Customer name
  company: string        // Company name
  email: string          // Company email
  address: string        // Full address
  country: string        // Country code/name
  contactNumber: string  // Contact phone number
}
```

## Internal State Management

```typescript
const [open, setOpen] = useState(false)
const [count, setCount] = useState(1)
const [selectData, setSelectData] = useState<InvoiceType | null>(null)
const [issuedDate, setIssuedDate] = useState<Date | null | undefined>(null)
const [dueDate, setDueDate] = useState<Date | null | undefined>(null)
const [formData, setFormData] = useState<FormDataType>(initialFormData)
```

## Key Dependencies

- **MUI Components**: Card, CardContent, Grid, Typography, InputAdornment, Divider, Button, IconButton, MenuItem, Tooltip, InputLabel, useMediaQuery
- **Custom Components**:
  - `AddCustomerDrawer`: Drawer for adding new customers
  - `CustomTextField`: Application's custom text input wrapper
  - `Logo`: Company logo display component
  - `AppReactDatepicker`: React Datepicker wrapper component
- **Styling**: Custom CSS modules and Tailwind utility classes

## Styling Approach

- **MUI System**: Grid layout system, spacing utilities, theme integration
- **CSS Classes**: Custom classes for styling (e.g., `bg-actionHover`, `border-dashed`)
- **Tailwind CSS**: Responsive classes (`sm:flex-row`, `md:absolute`, etc.), spacing, and layout utilities
- **Responsive Utilities**: Uses `useMediaQuery` hook for responsive component behavior

## Usage Patterns

### Basic Implementation

```typescript
import AddCard from '@/views/apps/invoice/add/AddCard'
import { invoiceData } from '@/fake-db/apps/invoice'

export default function CreateInvoicePage() {
  return <AddCard invoiceData={invoiceData} />
}
```

### Integration with Next.js Page

```typescript
'use client'

import AddCard from '@/views/apps/invoice/add/AddCard'
import { getInvoices } from '@/actions/invoice'

export default async function InvoiceAddPage() {
  const invoices = await getInvoices()
  
  return (
    <div className='p-4'>
      <AddCard invoiceData={invoices} />
    </div>
  )
}
```

## Code Flow

1. **Initialization**: Component initializes with empty line item count (1) and initial form data for new customers
2. **Customer Selection**: 
   - User can select existing customer from dropdown
   - Or click Add New Customer to open drawer
   - On form submission from drawer, `onFormSubmit` updates `formData` state
3. **Date Selection**: User selects issued and due dates via date picker components
4. **Line Item Management**:
   - Each line item is rendered dynamically based on `count` state
   - Clicking Add Item increments count, adding new line item
   - Clicking delete icon removes the line item DOM element
5. **Calculation Display**: Form shows calculated totals (subtotal, discount, tax, total)

## Event Handlers

### onFormSubmit

```typescript
const onFormSubmit = (data: FormDataType) => {
  setFormData(data)  // Store new customer data for display
}
```

Called when the AddCustomerDrawer form is submitted. Updates `formData` state to display new customer details.

### deleteForm

```typescript
const deleteForm = (e: SyntheticEvent) => {
  e.preventDefault()
  e.target.closest('.repeater-item').remove()
}
```

Removes a line item from the DOM when the delete button is clicked.

## Accessibility Considerations

- All input fields use MUI TextField components with proper labels
- Interactive elements (buttons, selects) are keyboard accessible
- Responsive design ensures mobile usability
- Dividers help separate content sections visually
- Tooltips provide additional context for tax fields

## Performance Considerations

- Component uses client-side state for form management
- Line items are rendered efficiently with array mapping and keys
- Responsive breakpoints use `useMediaQuery` hook for optimal performance
- Event handlers use standard React patterns (e.preventDefault, etc.)

## Common Customization Points

1. **Line Item Types**: Modify the dropdown MenuItem values in line item section
2. **Default Values**: Change `defaultValue` props on input fields
3. **Customer Data**: Replace `invoiceData?.slice(0, 5)` limit for more/fewer customers
4. **Calculation Logic**: Update subtotal, discount, tax, and total calculations
5. **Billing Information**: Modify bank details, IBAN, SWIFT code display values
6. **Layout**: Adjust Grid sizing for different breakpoints

## Related Components

- **AddCustomerDrawer**: Opens when user selects Add New Customer
- **CustomTextField**: Form input wrapper used throughout
- **AppReactDatepicker**: Date selection wrapper component
- **Logo**: Company branding in invoice header

## Implementation Notes

- Component uses DOM manipulation for deleting items - could be refactored to use state-based deletion
- All monetary values are hardcoded or placeholder values in current implementation
- Customer dropdown limits to 5 most recent customers
- Date format is standardized as yyyy-MM-dd via AppReactDatepicker
- Component is marked as Client Component due to interactive features and state management
