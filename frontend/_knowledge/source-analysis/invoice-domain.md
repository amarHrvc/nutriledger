# Invoice Domain - Source Code Analysis

**Epic:** kb-joc.1.3  
**Task:** T015 - Read invoice domain source  
**Date:** 2026-05-04  
**Status:** Complete

## Overview

The Invoice domain manages invoice creation, listing, previewing, and editing operations. It provides a comprehensive interface for invoice management with filtering, searching, status tracking, and payment management.

## Directory Structure

```
src/views/apps/invoice/
├── add/                       # Add new invoice page
├── edit/                      # Edit invoice page
├── list/                      # List invoices view
│   ├── index.tsx              # Invoice list page container
│   ├── InvoiceListTable.tsx   # Main table with react-table
│   ├── InvoiceCard.tsx        # Summary cards component
│   └── (likely filter components)
├── preview/                   # Preview/view invoice details
└── shared/                    # Shared invoice components

src/types/apps/
└── invoiceTypes.ts           # Invoice type definitions
```

## Type Definitions

### Core Types (`src/types/apps/invoiceTypes.ts`)

#### InvoiceStatus
```typescript
type InvoiceStatus = 'Paid' | string

// Supported statuses (inferred from InvoiceListTable):
- 'Sent'
- 'Paid'
- 'Draft'
- 'Partial Payment'
- 'Past Due'
- 'Downloaded'
```

#### InvoiceClientType
```typescript
type InvoiceClientType = {
  name: string           // Client name
  address: string        // Client address
  company: string        // Client company
  country: string        // Client country
  contact: string        // Contact number
  companyEmail: string   // Company email address
}
```

#### InvoiceType (Main Entity)
```typescript
type InvoiceType = {
  id: string             // Invoice identifier (used in routes)
  name: string           // Client name
  total: number          // Total invoice amount (numeric)
  avatar: string         // Client avatar image path
  service: string        // Service description
  dueDate: string        // Due date (formatted string)
  address: string        // Client address
  company: string        // Client company
  country: string        // Client country
  contact: string        // Contact number
  avatarColor?: string   // Avatar fallback color
  issuedDate: string     // Issue date (formatted string)
  companyEmail: string   // Company email
  balance: string | number  // Remaining balance (0 = Paid)
  invoiceStatus: InvoiceStatus  // Current status
}
```

#### InvoicePaymentType (Payment Details)
```typescript
type InvoicePaymentType = {
  iban: string          // International Bank Account Number
  totalDue: string      // Total due amount
  bankName: string      // Bank name
  country: string       // Bank country
  swiftCode: string     // SWIFT/BIC code
}
```

#### SingleInvoiceType (Complete Invoice)
```typescript
type SingleInvoiceType = {
  invoice: InvoiceType
  paymentDetails: InvoicePaymentType
}
```

#### InvoiceLayoutProps (Route Props)
```typescript
type InvoiceLayoutProps = {
  id: string | undefined
}
```

## Core Components Analysis

### InvoiceListTable.tsx (Main Component)

**Type:** Client Component (`'use client'`)  
**Purpose:** Display invoices in tabular format with advanced filtering and operations

**Key Features:**
- **TanStack Powered:** Uses @tanstack/react-table v8
- **Fuzzy Search:** Full-text search with debouncing (500ms)
- **Status Filter:** Filter by invoice status (7 options)
- **Row Selection:** Multi-select with select-all checkbox
- **Sorting:** Column-based sorting
- **Pagination:** Page size selector (10, 25, 50)
- **Bulk Actions:** Delete single or multiple

**State Management:**
```typescript
- status: InvoiceType['invoiceStatus']  // Status filter
- rowSelection: Record<>                // Selected rows
- data: InvoiceType[]                  // Current data
- filteredData: InvoiceType[]          // Display data
- globalFilter: string                 // Search query
```

**Status-to-Icon-Color Mapping (invoiceStatusObj):**
```typescript
{
  'Sent': { 
    color: 'secondary', 
    icon: 'tabler-send-2' 
  },
  'Paid': { 
    color: 'success', 
    icon: 'tabler-check' 
  },
  'Draft': { 
    color: 'primary', 
    icon: 'tabler-mail' 
  },
  'Partial Payment': { 
    color: 'warning', 
    icon: 'tabler-chart-pie-2' 
  },
  'Past Due': { 
    color: 'error', 
    icon: 'tabler-alert-circle' 
  },
  'Downloaded': { 
    color: 'info', 
    icon: 'tabler-arrow-down' 
  }
}
```

**Columns (7 total):**

1. **Select** - Checkbox for row selection
   - Multi-select enabled
   - Select-all in header

2. **ID (#)** - Invoice number
   - Displays as `#<id>`
   - Styled as primary link color
   - Links to preview page: `/apps/invoice/preview/<id>`

3. **Status** - Invoice status with icon
   - Displays icon + status details in tooltip
   - Tooltip shows: status, balance, due date
   - Color-coded avatar with icon
   - CustomAvatar with light skin variant

4. **Client** - Client name and email
   - Avatar + name + company email
   - Name displayed as bold text
   - Email as secondary text

5. **Total** - Invoice total amount
   - Formatted as `$<amount>`
   - Numeric value in data

6. **Issued Date** - Invoice issue date
   - Formatted string display
   - Sortable column

7. **Balance** - Remaining balance
   - Shows "Paid" chip if balance === 0
   - Otherwise shows balance amount
   - Success colored chip when paid

8. **Action** - Delete, preview, and menu
   - Delete icon button (inline)
   - Eye icon button (view/preview)
   - Option menu with 3 actions:
     - Download (icon: tabler-download)
     - Edit (links to `/apps/invoice/edit/<id>`)
     - Duplicate (icon: tabler-copy)

**Custom Components:**
- `DebouncedInput` - Search with 500ms debounce
- `getAvatar()` - Avatar display logic with fallback

**Avatar Function Logic:**
```typescript
getAvatar(params: Pick<InvoiceType, 'avatar' | 'name'>) {
  if (avatar) {
    return <CustomAvatar src={avatar} skin='light' size={34} />
  } else {
    return (
      <CustomAvatar skin='light' size={34}>
        {getInitials(name)}
      </CustomAvatar>
    )
  }
}
```

**TanStack Configuration:**
```typescript
- enableRowSelection: true
- globalFilterFn: fuzzyFilter
- initialState: { pagination: { pageSize: 10 } }
- Row models: Core, Filtered, Sorted, Paginated
- Faceted models: Unique values, Min/Max
```

**Status Filter Logic:**
- Separate state for status filter
- Updates filtered data via useEffect
- Empty value shows all invoices
- Single-filter (unlike User domain with 3 filters)

**Tooltip Implementation:**
```
Tooltip shows:
- Status name
- Balance amount
- Due date
Positioned on status icon
```

**Dependencies:**
- MUI: Card, CardContent, Button, Typography, Checkbox, Chip, IconButton, MenuItem, Tooltip, TablePagination
- React Table: Full suite (core, filtering, sorting, pagination, faceted)
- Custom: OptionMenu, CustomAvatar, TablePaginationComponent, CustomTextField
- Utilities: getInitials, getLocalizedUrl, useParams

### InvoiceCard.tsx

**Purpose:** Summary cards showing invoice statistics  
**Details:** Not shown in excerpt, but likely displays:
- Total invoices
- Paid invoices
- Pending invoices
- Total revenue

## Data Flow & State Management

### Invoice List State Flow
```
invoiceData (props)
  ↓
data (useState)
  ↓
filteredData (useState) ← status filter + globalFilter
  ↓
table.getFilteredRowModel()
  ↓
Display on page
```

### Filter Combination
```typescript
useEffect(() => {
  const filtered = data?.filter(invoice => {
    if (status && invoice.invoiceStatus !== status) return false
    // Global filter handled by TanStack fuzzyFilter
    return true
  })
  setFilteredData(filtered)
}, [status, data])
```

## Routing & Navigation

### Invoice Routes
- **List:** `/apps/invoice/` or `/apps/invoice/list`
- **Preview:** `/apps/invoice/preview/<id>`
- **Edit:** `/apps/invoice/edit/<id>`
- **Add:** `/apps/invoice/add`

### Route Integration in Components
```typescript
// Preview link
href={getLocalizedUrl(`/apps/invoice/preview/${row.original.id}`, locale)}

// Edit link in option menu
href: getLocalizedUrl(`/apps/invoice/edit/${row.original.id}`, locale)
```

## Validation Patterns

### Invoice Data Validation
- No client-side validation in table
- Assumes data is pre-validated
- Balance can be 0 (paid) or any number (pending)
- Status must be one of predefined values

### Form Validation
- Handled in Add and Edit pages (not shown in this analysis)
- Client and payment details required

## Component Features & Patterns

### Status Badge Pattern
```
CustomAvatar + Icon
├── Light skin variant
├── Color-mapped to status
└── Tabler icon inside
```

### Multi-action Column Pattern
```
Action column
├── Single delete button (inline)
├── Single view button (inline)
└── Option menu (with More actions)
    ├── Download
    ├── Edit (link)
    └── Duplicate
```

### Tooltip Content Pattern
```
Complex tooltip with:
├── Status name
├── Balance info
└── Due date
```

## Performance Considerations

1. **useMemo for Columns:** Memoized with [data, filteredData]
2. **DebouncedInput:** 500ms debounce to reduce re-renders
3. **TanStack Optimizations:** Row models computed by library
4. **Avatar Caching:** getAvatar() helper prevents recalculation

## Accessibility Features

- **Semantic Table:** Proper thead/tbody structure
- **Native Checkboxes:** Used for selection
- **Tooltip Descriptions:** Provide context for status icons
- **Link Colors:** Primary color indicates clickable areas
- **Icon + Text:** All icons have text labels (in menu)

## Integration Points

### With Other Domains
- No direct user/role integration visible in list
- Could show user who created invoice (not in data)
- Could show associated project (not in data)

### With Other Pages
- Links to preview page for viewing details
- Links to edit page for modifying invoices
- Links to add page for creating invoices
- Supports localized routes (useParams for locale)

## Comparison with Other Domains

| Aspect | User | Roles | Invoice |
|--------|------|-------|---------|
| **Filters** | 3 (role, plan, status) | 1 (role) | 1 (status) |
| **Add Function** | Drawer | Dialog | Separate page |
| **Edit Function** | Not in table | Dialog | Separate page |
| **Columns** | 7 | 7 | 8 |
| **Data Model** | UsersType | UsersType | InvoiceType |
| **Display Style** | Simple badges | Icons+colors | Tooltips |

## Known Patterns & Conventions

### Custom Hooks
- `DebouncedInput` - Shared pattern across domains
- `fuzzyFilter` - Shared filter implementation

### Component Organization
- Separate components for list, add, edit, preview
- Shared types in centralized file
- Reusable table patterns

### Date Handling
- Dates stored as formatted strings
- No date validation visible
- No date range filtering

## Common Invoice Scenarios

1. **View Invoice List:** Search by client name, filter by status
2. **Mark as Paid:** Filter to unpaid, update balance to 0
3. **Generate Report:** Export or download invoice
4. **Duplicate Invoice:** Create new invoice from existing
5. **Edit Invoice:** Update amounts, dates, client info

## Summary

The Invoice domain provides:
- **Advanced Table Interface:** TanStack-powered with search and filtering
- **Status Management:** 6 different invoice statuses with visual icons
- **Payment Tracking:** Balance field with special "Paid" display
- **Client Management:** Complete client details per invoice
- **Multi-action Pattern:** Delete, view, and bulk operations
- **Responsive Design:** Mobile-first with MUI Grid layout
- **Routing Support:** Preview and edit pages for detailed operations

The architecture emphasizes invoice discovery and quick actions through the list interface, with detailed operations deferred to dedicated pages.