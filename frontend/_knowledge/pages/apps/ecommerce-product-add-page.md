# Product Add/Edit Page Snapshot

**Overview**

The Product Add page is a comprehensive form interface for creating and editing e-commerce products. It uses a 2-column layout with a left main content area (8 columns) containing core product information, images, variants, and inventory details, and a right sidebar (4 columns) for pricing and organization/categorization. The page incorporates advanced features like a rich text editor (Tiptap) for product descriptions, file upload with drag-and-drop, dynamic form fields, and tabbed inventory management.

**File Location**: src/app/[lang]/(dashboard)/(private)/apps/ecommerce/products/add/

**Route**: /apps/ecommerce/products/add

---

## Page Layout Structure

\\\
ProductAdd Page (main component)
├── ProductAddHeader (top section with title + action buttons)
│   ├── "Add a new product" heading
│   ├── Discard button
│   ├── Save Draft button
│   └── Publish Product button
│
├── Left Column (MD: 8 of 12)
│   ├── ProductInformation
│   │   ├── Product Name field
│   │   ├── SKU field
│   │   ├── Barcode field
│   │   └── Description editor (Tiptap with toolbar)
│   │       ├── Bold, Italic, Underline, Strikethrough
│   │       └── Text alignment (left, center, right, justify)
│   │
│   ├── ProductImage
│   │   ├── Dropzone for drag-and-drop
│   │   ├── File preview list
│   │   ├── Remove individual file
│   │   ├── Remove all files
│   │   ├── Upload files button
│   │   └── Add media from URL link
│   │
│   ├── ProductVariants
│   │   ├── Repeating variant rows
│   │   ├── Option type dropdown (Size, Color, Weight, Smell)
│   │   ├── Variant value input
│   │   ├── Delete variant button
│   │   └── Add Another Option button
│   │
│   └── ProductInventory
│       └── Tabbed interface (5 tabs)
│           ├── Restock
│           │   ├── Add to stock input
│           │   ├── Confirm button
│           │   └── Stock status display
│           ├── Shipping
│           │   ├── Fulfilled by Seller (radio)
│           │   └── Fulfilled by Company (radio)
│           ├── Global Delivery
│           │   ├── Worldwide delivery (radio)
│           │   ├── Selected Countries (radio + input)
│           │   └── Local delivery (radio)
│           ├── Attributes
│           │   ├── Fragile Product (checkbox)
│           │   ├── Biodegradable (checkbox)
│           │   ├── Frozen Product (checkbox + temperature)
│           │   └── Expiry Date (checkbox + date picker)
│           └── Advanced
│               ├── Product ID Type (ISBN, UPC, EAN, JAN)
│               └── Product ID value
│
└── Right Column (MD: 4 of 12)
    ├── ProductPricing
    │   ├── Base Price input
    │   ├── Discounted Price input
    │   ├── Charge tax checkbox
    │   └── In stock toggle switch
    │
    └── ProductOrganize
        ├── Vendor dropdown
        ├── Category dropdown (with + button)
        ├── Collection dropdown
        ├── Status dropdown
        └── Tags input
\\\

---

## Component Details

### **ProductAddHeader** (6 lines)

**Purpose**: Page header with title and action buttons

**Content**:
- Heading: "Add a new product"
- Subtitle: "Orders placed across your store"
- Three buttons: Discard, Save Draft, Publish Product

**Layout**:
- Responsive: Flex wrap with max-sm flex-col
- Buttons grouped on the right (desktop) or below (mobile)

**Button Actions** (not yet implemented):
- Discard: Clear form and navigate back
- Save Draft: Save product without publishing
- Publish Product: Finalize and publish product

---

### **ProductInformation** (202 lines)

**Purpose**: Core product metadata and detailed description

**Key Features**:

1. **Basic Fields**:
   - Product Name (required, text input)
   - SKU (required, text input)
   - Barcode (optional, text input)

2. **Rich Text Editor** (Tiptap):
   - Placeholder: "Write something here..."
   - Default content: "Keep your account secure with authentication step."
   - Responsive height: 135px with vertical scroll

**EditorToolbar Component** (Internal):

Provides formatting controls for the editor:

\\\	ypescript
type EditorState = {
  isBold: boolean
  isItalic: boolean
  isUnderline: boolean
  isStrike: boolean
  isLeftAligned: boolean
  isCenterAligned: boolean
  isRightAligned: boolean
  isJustified: boolean
}
\\\

**Toolbar Buttons**:
- **Bold**: Toggle bold text (tabler-bold icon)
- **Underline**: Toggle underline (tabler-underline icon)
- **Italic**: Toggle italic (tabler-italic icon)
- **Strikethrough**: Toggle strikethrough (tabler-strikethrough icon)
- **Align Left**: Left alignment (tabler-align-left icon)
- **Align Center**: Center alignment (tabler-align-center icon)
- **Align Right**: Right alignment (tabler-align-right icon)
- **Align Justify**: Full justification (tabler-align-justified icon)

**Editor Configuration**:
- Extensions: StarterKit, Bold, Italic, Underline, Strike, TextAlign, Placeholder
- Immediate rendering: disabled (for better performance)
- Content editable: true

**Styling**:
- Uses @/libs/styles/tiptapEditor.css custom stylesheet
- Custom icon button component with conditional coloring
- Inactive buttons show gray text (text-textSecondary)
- Active buttons show primary color

---

### **ProductImage** (142 lines)

**Purpose**: File upload interface with drag-and-drop support

**Key Features**:

1. **Dropzone Component**:
   - Uses react-dropzone library
   - Drag-and-drop area with visual feedback
   - Browse button for manual file selection
   - 12px padding on desktop, 5px padding on mobile

2. **File Upload Process**:
   - Accept all file types (images and documents)
   - Display image preview or file icon
   - Show file name and size (formatted as KB or MB)
   - Individual remove button per file
   - Remove All Files button

3. **File Size Formatting**:
   - Converts bytes to KB/MB: Math.round(file.size / 100) / 10
   - Threshold: 1000 KB = 1 MB
   - Format: "2.5 kb" or "1.2 mb"

4. **Preview Rendering**:
   - Images: Display thumbnail (38x38px)
   - Non-images: Show file description icon

5. **Action Buttons**:
   - Upload Files: Upload selected files (tonal contained)
   - Add media from URL: Link to add from external URL

**State Management**:
\\\	ypescript
const [files, setFiles] = useState<File[]>([])  // Uploaded files
\\\

**Hooks**:
- useDropzone: Provides getRootProps, getInputProps for dropzone functionality

**Styling**:
- Uses AppReactDropzone styled component
- Custom styling via MUI's styled() API
- Responsive padding adjustments

---

### **ProductVariants** (68 lines)

**Purpose**: Dynamic product variant management (Size, Color, Weight, etc.)

**Key Features**:

1. **Dynamic Row Management**:
   - Add/remove variant rows dynamically
   - Each row contains option type and value
   - Initial row count: 1

2. **Variant Structure**:
   \\\	ypescript
   {
     optionType: string    // Size, Color, Weight, Smell
     optionValue: string   // e.g., "Large", "Red", "50kg"
   }
   \\\

3. **Option Types** (Dropdown):
   - Size
   - Color
   - Weight
   - Smell

4. **Row Layout**:
   - **XS/SM**: Full width (12/12)
   - **MD+**: Option dropdown (4 cols) + Value input (8 cols)
   - X button on right for delete

5. **Actions**:
   - Add Another Option: Increments row count, renders new row
   - Delete row: Removes closest .repeater-item from DOM

**State Management**:
\\\	ypescript
const [count, setCount] = useState(1)  // Number of variant rows
\\\

**Implementation Notes**:
- Uses Array.from(Array(count).keys()).map() for dynamic rendering
- Delete uses direct DOM manipulation (e.target.closest())
- SyntheticEvent used for form event typing

---

### **ProductInventory** (280 lines)

**Purpose**: Complex inventory management with tabbed interface

**Tabs** (5 total):

#### **1. Restock Tab**

**Purpose**: Manage stock levels

**Fields**:
- Add to stock: Input for quantity to add
- Confirm button: Save new stock level

**Display**:
- Product in stock now: 54
- Product in transit: 390
- Last time restocked: 24th June, 2022
- Total stock over lifetime: 2,430

#### **2. Shipping Tab**

**Purpose**: Define shipping responsibility

**Options** (Radio buttons):
1. **Fulfilled by Seller**
   - Description: "You'll be responsible for product delivery. Any damage or delay during shipping may cost you a Damage fee"

2. **Fulfilled by Company name**
   - Description: "Your product, Our responsibility. For a measly fee, we will handle the delivery process for you."

#### **3. Global Delivery Tab**

**Purpose**: Set delivery scope and regions

**Options** (Radio buttons):
1. **Worldwide delivery**
   - Note: "Only available with Shipping method: Fulfilled by Company name"

2. **Selected Countries**
   - Input field: "USA" (placeholder)
   - Allows custom country entry

3. **Local delivery**
   - Description: "Deliver to your country of residence"
   - Link: "Change profile address"

#### **4. Attributes Tab**

**Purpose**: Product-specific attributes and constraints

**Checkboxes**:
1. **Fragile Product**
   - Simple checkbox, no additional input

2. **Biodegradable**
   - Simple checkbox, no additional input

3. **Frozen Product**
   - Checkbox + temperature input (placeholder: "40 C")

4. **Expiry Date of Product**
   - Checkbox + date picker (format: MM/DD/YYYY)

#### **5. Advanced Tab**

**Purpose**: Product ID management

**Fields**:
- Product ID Type (dropdown):
  - ISBN
  - UPC
  - EAN
  - JAN
- Product ID (text input, placeholder: "100023")

**Layout**:
- Grid layout with responsive columns
- Type: 12/12 cols on mobile, 12/6 on tablet, 12/7 on desktop
- ID value: 12/12 cols on mobile, 12/6 on tablet, 12/5 on desktop

**State Management**:
\\\	ypescript
const [activeTab, setActiveTab] = useState('restock')  // Current tab
const [date, setDate] = useState<Date | null>(null)    // Expiry date
\\\

**Hooks**:
- useTheme: Access theme for breakpoint queries
- useMediaQuery: Check if screen is below MD breakpoint

**Styling**:
- TabContext: MUI Tab container for state management
- CustomTabList: Vertical tab list on desktop, horizontal on mobile
- Tab icons from Tabler icon set (box, car, world, link, lock)
- Divider: Horizontal on mobile, vertical on desktop

**Layout Notes**:
- Flex layout: Side-by-side on desktop, stacked on mobile
- Tab list: 4/12 width on desktop (md:is-4/12)
- Tab content: 8/12 width on desktop (md:is-8/12)
- Divider orientation: Responsive based on screen size

---

### **ProductPricing** (35 lines)

**Purpose**: Pricing and tax configuration

**Fields**:

1. **Base Price**
   - Full width input
   - Placeholder: "Enter Base Price"
   - Currency assumed based on context

2. **Discounted Price**
   - Full width input
   - Placeholder: ""
   - Shows sale/promotional price

3. **Charge tax on this product**
   - Checkbox
   - Default: checked
   - Only affects this specific product

4. **In stock**
   - Toggle switch
   - Default: enabled
   - Quick stock availability toggle

**Layout**:
- Form wrapper for semantic structure
- Vertical spacing (mbe-6 class = margin-block-end)
- Last item has divider (mlb-2 = margin-logical-block)

**State**: Uncontrolled component (uses defaultChecked/defaultValue)

---

### **ProductOrganize** (74 lines)

**Purpose**: Product categorization and organization

**Fields**:

1. **Vendor** (Dropdown)
   - Options:
     - Men's Clothing
     - Women's Clothing
     - Kid's Clothing
   - Required for product classification

2. **Category** (Dropdown with + button)
   - Options:
     - Household
     - Office
     - Electronics
     - Management
     - Automotive
   - Plus button: Create new category (not implemented)

3. **Collection** (Dropdown)
   - Options:
     - Men's Clothing
     - Women's Clothing
     - Kid's Clothing
   - Groups related products

4. **Status** (Dropdown)
   - Options:
     - Published
     - Inactive
     - Scheduled
   - Determines product visibility

5. **Tags** (Text input)
   - Multiple tags separated by commas
   - Placeholder: "Fashion, Trending, Summer"
   - No validation on format

**State Management**:
\\\	ypescript
const [vendor, setVendor] = useState('')        // Selected vendor
const [category, setCategory] = useState('')    // Selected category
const [collection, setCollection] = useState('')  // Selected collection
const [status, setStatus] = useState('')        // Selected status
\\\

**Layout**:
- Form element with flex col gap-6 spacing
- All fields full width
- Responsive design built-in to MUI components

---

## Type Definitions

### **File Type** (ProductImage.tsx)

\\\	ypescript
type FileProp = {
  name: string      // File name (e.g., "product.png")
  type: string      // MIME type (e.g., "image/png")
  size: number      // File size in bytes
}
\\\

### **Editor State** (ProductInformation.tsx)

\\\	ypescript
type EditorState = {
  isBold: boolean
  isItalic: boolean
  isUnderline: boolean
  isStrike: boolean
  isLeftAligned: boolean
  isCenterAligned: boolean
  isRightAligned: boolean
  isJustified: boolean
}
\\\

### **Tab Values** (ProductInventory.tsx)

\\\	ypescript
type TabValue = 'restock' | 'shipping' | 'global-delivery' | 'attributes' | 'advanced'
\\\

---

## Component Dependencies

### **External Libraries**:
- **MUI Components**: Card, CardHeader, CardContent, Grid, Button, TextField, Checkbox, Switch, Divider, FormControlLabel, FormGroup, Radio, RadioGroup, Tab, TabContext, TabPanel, MenuItem, IconButton, Typography
- **Tiptap Editor**: Editor, EditorContent, useEditor, useEditorState, extensions (Bold, Italic, Underline, Strike, TextAlign, StarterKit, Placeholder)
- **react-dropzone**: useDropzone hook for file upload
- **MUI Lab**: TabContext, TabPanel
- **React Datepicker**: AppReactDatepicker component

### **Custom Components**:
- **CustomTextField**: Enhanced Material-UI TextField
- **CustomIconButton**: Enhanced Material-UI IconButton
- **CustomTabList**: Custom Tab list wrapper
- **Form**: Semantic form wrapper
- **Link**: Next.js Link component

### **Utilities**:
- **AppReactDropzone**: Styled dropzone component wrapper
- **AppReactDatepicker**: Styled date picker component wrapper
- **classnames**: CSS class composition

---

## Usage Patterns

### **1. New Product Creation Flow**

\\\
User navigates to /apps/ecommerce/products/add
  ↓
Page loads with empty form fields
  ↓
User fills ProductInformation:
  ├─ Product Name
  ├─ SKU
  ├─ Barcode
  └─ Description (rich text)
  ↓
User uploads images via ProductImage dropzone
  ↓
User adds variants via ProductVariants
  ↓
User configures inventory via ProductInventory tabs
  ↓
User sets pricing via ProductPricing
  ↓
User organizes product via ProductOrganize
  ↓
User clicks "Publish Product" in ProductAddHeader
  ↓
Form submission (handler not shown)
\\\

### **2. Rich Text Editing**

\\\
User clicks in description editor
  ↓
Editor focuses and shows toolbar
  ↓
User types content or selects existing text
  ↓
User clicks format button (bold, italic, etc.)
  ↓
Tiptap applies formatting via editor.chain() API
  ↓
Editor re-renders with new formatting applied
\\\

### **3. Dynamic Variant Addition**

\\\
User sees initial variant row
  ↓
User fills Option Type (Size) and Value (Large)
  ↓
User clicks "Add Another Option"
  ↓
setCount(count + 1) triggers re-render
  ↓
New row appears in map() output
  ↓
Repeat for each variant needed
\\\

### **4. Inventory Tab Navigation**

\\\
User clicks on "Shipping" tab
  ↓
handleChange fires with tab value
  ↓
setActiveTab('shipping') updates state
  ↓
TabContext re-renders active TabPanel
  ↓
Only active tab's content is visible (others are display: none)
\\\

### **5. File Upload Process**

\\\
User drags file to dropzone area
  ↓
useDropzone detects drop event
  ↓
onDrop callback fires with acceptedFiles
  ↓
Files are mapped and state updates: setFiles(newFiles)
  ↓
File list renders with preview thumbnails
  ↓
User can remove individual files or remove all
\\\

---

## State Management Strategy

The page uses **local component state** (React.useState) for all form fields:

### **ProductInformation**:
- Editor state managed by Tiptap (useEditor hook)

### **ProductImage**:
- files: File[] state

### **ProductVariants**:
- count: number of variant rows

### **ProductInventory**:
- activeTab: current tab (string)
- date: expiry date selection (Date | null)

### **ProductOrganize**:
- vendor, category, collection, status: string states

### **ProductPricing**:
- All fields are uncontrolled (defaultValue/defaultChecked)

**Note**: A production implementation would likely use a form library (React Hook Form, Formik) to handle validation, submission, and complex state management.

---

## Responsive Design Patterns

### **Two-Column Layout**:
- Desktop (MD+): 8-col left + 4-col right
- Tablet/Mobile (XS-SM): 12-col stacked layout

### **Inventory Tabs**:
- Desktop: Tab list (vertical) + content (side-by-side)
- Tablet/Mobile: Tab list (horizontal) + content (stacked)
- Divider orientation changes (vertical ↔ horizontal)

### **Variant Rows**:
- Desktop (SM+): Option (4 cols) + Value (8 cols)
- Mobile (XS): Full width stacked

### **Spacing**:
- Consistent 6-unit gaps between sections
- Smaller gaps (2-4 units) within component groups

---

## Accessibility Features

- **Semantic HTML**: Form elements use proper structure
- **Labels**: All inputs have associated labels
- **ARIA Attributes**: Radio groups use aria-labelledby
- **Tab Navigation**: Tab component provides keyboard navigation
- **Icon Buttons**: Button elements properly labeled
- **Color + Text**: Status labels, chips use both color and text
- **Date Picker**: Supports keyboard date entry
- **Rich Text Editor**: Toolbar buttons keyboard accessible

---

## File Statistics

- **ProductAddHeader.tsx**: 26 lines
- **ProductInformation.tsx**: 202 lines (includes Tiptap editor)
- **ProductImage.tsx**: 142 lines (includes dropzone)
- **ProductVariants.tsx**: 68 lines
- **ProductInventory.tsx**: 280 lines (complex tabbed interface)
- **ProductPricing.tsx**: 35 lines
- **ProductOrganize.tsx**: 74 lines
- **page.tsx**: 50 lines
- **Total**: ~877 lines

---

## Key Implementation Details

### **Tiptap Integration**:
- Uses StarterKit with selective extension configuration
- EditorContent provides the editable area
- EditorToolbar manages formatting buttons
- useEditorState hook for reactive state tracking
- Custom CSS from tiptapEditor.css

### **File Upload with react-dropzone**:
- Drag-and-drop interface with fallback browse button
- File preview with type detection
- File size formatting (KB/MB)
- Direct DOM manipulation for row removal

### **Complex Tabbed Interface**:
- MUI TabContext for state management
- Vertical tab list on desktop (responsive orientation)
- Custom pill-style tabs
- Divider orientation changes with breakpoint

### **Dynamic Form Fields**:
- Array mapping for variants (Array.from(Array(count).keys()))
- Direct DOM access for deletion (querySelector closest)
- Counter state to trigger re-renders

### **Rich Text Editing**:
- Tiptap for advanced text editing
- Chain API for command composition
- useEditorState hook for non-reactive updates
- Placeholder plugin for empty state guidance

---

## Performance Considerations

1. **Editor Initialization**: immediatelyRender: false prevents server-side rendering
2. **File Previews**: Use URL.createObjectURL() for local file display
3. **Variant Rows**: Direct DOM manipulation for deletion (alternative: use state array)
4. **Tab Content**: Only active tab rendered (implicit via CSS display: none)

---

## Related Pages

- **Products List**: /apps/ecommerce/products/list (linked from add header)
- **Product Details**: /apps/ecommerce/products/[id]/view (for editing)
- **Product Categories**: /apps/ecommerce/products/category

---

*Last Updated: Phase 3 Ecommerce & Forms, Task kb-79w.3.2*
*File Format: Markdown Snapshot (Architecture Documentation)*
*Implementation: Next.js 15 + React 19 + Tiptap 2.x + Material-UI + react-dropzone*