# Form Layouts Page Snapshot

**Overview**

The Form Layouts page showcases six different form layout patterns used for collecting user input across the dashboard. These patterns demonstrate modern form design practices including basic layouts, icon-based inputs, section separators, tabbed forms, collapsible sections, and field alignment variations. Each layout is built with Material-UI components and demonstrates responsive design principles for mobile, tablet, and desktop views.

**File Location**: src/app/[lang]/(dashboard)/(private)/forms/form-layouts/

**Route**: /forms/form-layouts

**Component Location**: src/views/forms/form-layouts/

---

## Page Structure

\\\
FormLayouts Page
├── 2-column layout (desktop)
│   ├── Column 1 (MD: 6 of 12)
│   │   ├── FormLayoutsBasic (6/12 cols)
│   │   └── FormLayoutsIcon (6/12 cols) - parallel with Basic
│   │
│   └── Column 2 (MD: 6 of 12)
│       └── FormLayoutsIcon (when on left column)
│
├── Full width (12/12 cols)
│   ├── FormLayoutsSeparator
│   │   └── Multi-section form with dividers
│   │
│   ├── "Form with Tabs" heading
│   └── FormLayoutsTabs
│       └── Tabbed form with 3 tabs
│
├── Full width (12/12 cols)
│   ├── "Collapsible Sections" heading
│   └── FormLayoutsCollapsible
│       └── Accordion-based multi-step form
│
└── Full width (12/12 cols)
    └── FormLayoutsAlignment
        └── Field alignment variations
\\\

---

## Form Layout Components

### **1. FormLayoutsBasic** (121 lines)

**Purpose**: Fundamental signup/registration form pattern

**Form Fields**:
1. **Name** (text input, full width)
   - Placeholder: "John Doe"
   - Label: "Name"

2. **Email** (email input, full width)
   - Placeholder: "johndoe@gmail.com"
   - Label: "Email"
   - Helper text: "You can use letters, numbers & periods"

3. **Password** (password toggle, full width)
   - Placeholder: "············"
   - Label: "Password"
   - Helper text: "Use 8 or more characters with a mix of letters, numbers & symbols"
   - End adornment: Eye toggle icon (tabler-eye / tabler-eye-off)
   - Togglable: Switches between password dots and plain text

4. **Confirm Password** (password toggle, full width)
   - Placeholder: "············"
   - Label: "Confirm Password"
   - Helper text: "Make sure to type the same password as above"
   - End adornment: Eye toggle icon

**Layout**:
- Grid container with 6-unit spacing
- All fields full width on mobile
- Responsive Grid.size for future adaptations

**Form Actions**:
- **Get Started!** button (primary contained, type="submit")
- **Log In** link (secondary text link) with "Already have an account?" text

**State Management**:
\\\	ypescript
const [isPasswordShown, setIsPasswordShown] = useState(false)
const [isConfirmPasswordShown, setIsConfirmPasswordShown] = useState(false)
\\\

**Features**:
- Password visibility toggle
- Helper text for guidance
- Responsive button and link layout

---

### **2. FormLayoutsIcon** (100 lines)

**Purpose**: Form with icon adornments (start position)

**Form Fields**:
1. **Name** (text input, start icon)
   - Icon: tabler-user
   - Placeholder: "John Doe"

2. **Email** (email input, start icon)
   - Icon: tabler-mail
   - Placeholder: "johndoe@gmail.com"

3. **Phone No.** (tel input, start icon)
   - Icon: tabler-phone
   - Placeholder: "123-456-7890"

4. **Message** (multiline textarea, start icon)
   - Icon: tabler-message
   - Placeholder: "Bio..."
   - Rows: 4
   - Alignment: baseline (for icon alignment with multiline)

**Layout**:
- Grid container with 6-unit spacing
- All fields full width
- Multiline field uses custom sx for proper icon alignment

**Form Actions**:
- **Submit** button (primary contained, type="submit")

**Implementation Notes**:
- Uses InputAdornment with position="start" for icon placement
- Icons sourced from Tabler icon library
- Multiline field requires custom sx styling for proper alignment
- All icons positioned at field start

---

### **3. FormLayoutsSeparator** (270 lines)

**Purpose**: Multi-section form with labeled sections and dividers

**Section 1: Account Details**
- Label: "1. Account Details"
- Divider after section

**Fields**:
1. **UserName** (text, 6 cols on desktop)
   - Placeholder: "johnDoe"

2. **Email** (email, 6 cols on desktop)
   - Placeholder: "johndoe@gmail.com"

3. **Password** (password toggle, 6 cols on desktop)
   - ID: form-layout-separator-password
   - Eye toggle with helper text

4. **Confirm Password** (password toggle, 6 cols on desktop)
   - ID: form-layout-separator-confirm-password
   - Eye toggle

**Section 2: Personal Info**
- Label: "2. Personal Info"
- Divider before section

**Fields**:
1. **First Name** (text, 6 cols on desktop)
   - Placeholder: "John"

2. **Last Name** (text, 6 cols on desktop)
   - Placeholder: "Doe"

3. **Country** (dropdown, 6 cols on desktop)
   - Options: UK, USA, Australia, Germany

4. **Language** (multi-select dropdown, 6 cols on desktop)
   - Options: English, French, Spanish, Portuguese, Italian, German, Arabic
   - Multiple selection enabled

5. **Birth Date** (date picker, 6 cols on desktop)
   - Format: MM/DD/YYYY
   - Shows year and month dropdowns

6. **Phone Number** (number input, 6 cols on desktop)
   - Placeholder: "123-456-7890"

**Layout**:
- 2-column grid on desktop (6/6 split)
- Full width on mobile
- Dividers separate sections visually

**State Management**:
\\\	ypescript
type FormDataType = {
  username: string
  email: string
  password: string
  isPasswordShown: boolean
  confirmPassword: string
  isConfirmPasswordShown: boolean
  firstName: string
  lastName: string
  country: string
  language: string[]
  date: Date | null
  phoneNumber: string
}
\\\

**Form Actions**:
- **Submit** button (primary contained)
- **Reset** button (secondary tonal, clears all fields)

---

### **4. FormLayoutsTabs** (346 lines)

**Purpose**: Multi-tab form for organizing related fields

**Tabs Structure**:
- **Tab Context**: Uses MUI TabContext for state management
- **Active Tab**: Managed via useState('personal_info')
- **Scrollable**: Tabs scroll on small screens

**Tab 1: Personal Info**

**Fields**:
1. **First Name** (text, 6 cols on desktop)
2. **Last Name** (text, 6 cols on desktop)
3. **Country** (dropdown, 6 cols on desktop)
4. **Language** (multi-select, 6 cols on desktop)
5. **Birth Date** (date picker, 6 cols on desktop)
6. **Phone Number** (tel, 6 cols on desktop)

**Tab 2: Account Details**

**Fields**:
1. **Username** (text, 6 cols on desktop)
2. **Email** (email, 6 cols on desktop)
3. **Password** (password toggle, 6 cols on desktop)
4. **Confirm Password** (password toggle, 6 cols on desktop)

**Tab 3: Social Links**

**Fields**:
1. **Twitter** (url, 6 cols on desktop)
2. **Facebook** (url, 6 cols on desktop)
3. **Google+** (url, 6 cols on desktop)
4. **LinkedIn** (url, 6 cols on desktop)
5. **Instagram** (url, 6 cols on desktop)
6. **Quora** (url, 6 cols on desktop)

**Layout**:
- Horizontal scrollable tab list
- TabPanel wraps form content
- 2-column grid for fields within each tab

**State Management**:
\\\	ypescript
const [value, setValue] = useState('personal_info')  // Active tab

const [formData, setFormData] = useState<FormDataType>({
  firstName: '',
  lastName: '',
  country: '',
  language: [],
  date: null,
  phoneNumber: '',
  username: '',
  email: '',
  password: '',
  isPasswordShown: false,
  confirmPassword: '',
  setIsConfirmPasswordShown: false,
  twitter: '',
  facebook: '',
  google: '',
  linkedin: '',
  instagram: '',
  quora: ''
})
\\\

**Form Actions**:
- **Submit** button (primary contained, type="submit")
- **Reset** button (secondary tonal, clears all tab data)
- Divider separates content from actions
- CardActions component for footer

---

### **5. FormLayoutsCollapsible** (342 lines)

**Purpose**: Multi-step accordion form (e-commerce checkout pattern)

**Accordion 1: Delivery Address**

**Expand Icon**: tabler-chevron-right

**Fields**:
1. **Full Name** (text, 6 cols)
2. **Phone No.** (tel, 6 cols)
3. **Address** (multiline textarea, full width)
   - Rows: 4
   - Placeholder: "1456, Liberty Street"
4. **ZIP Code** (number, 6 cols)
5. **Landmark** (text, 6 cols)
6. **City** (text, 6 cols)
7. **Country** (dropdown, 6 cols)
8. **Address Type** (radio group, full width)
   - Options: 
     - Home (All day delivery)
     - Office (Delivery between 10 AM - 5 PM)

**Accordion 2: Delivery Options**

**Fields**:
- Custom horizontal radio options (custom component)
- **Standard 3-5 Days**
  - Meta: "Free"
  - Content: "Friday, 15 Nov - Monday, 18 Nov"
  - Default selected: true
  - Value: "standard"

- **Express**
  - Meta: ".00"
  - Content: "Friday, 15 Nov - Sunday, 17 Nov"
  - Value: "express"

- **Overnight**
  - Meta: ".00"
  - Content: "Friday, 15 Nov - Saturday, 16 Nov"
  - Value: "overnight"

**Accordion 3: Payment Method**

**Fields** (6 cols on desktop):
1. **Payment Method** (radio group, full width)
   - Options:
     - Credit/Debit/ATM Card (default)
     - Cash on Delivery

2. **Conditional: Credit Card Fields** (shown only when "credit" selected)
   - Card Number (text, 12 cols)
     - Placeholder: "0000 0000 0000 0000"
   - Name (text, 12 cols)
     - Placeholder: "John Doe"
   - Expiry Date (text, 6 cols)
     - Placeholder: "MM/YY"
   - CVV Code (text, 6 cols)
     - Placeholder: "123"

**Layout**:
- Accordion component manages expand/collapse state
- Only one accordion can be open at a time
- Dividers separate accordion sections
- 2-column grid for most fields
- Full-width grid items for section headers

**State Management**:
\\\	ypescript
const [expanded, setExpanded] = useState<string | false>('panel1')
const [paymentMethod, setPaymentMethod] = useState('credit')
const [selectedOption, setSelectedOption] = useState<string>(initialSelectedOption)

const [cardData, setCardData] = useState<FormData>({
  fullName: '',
  phone: '',
  address: '',
  zipCode: '',
  landmark: '',
  city: '',
  country: '',
  addressType: 'home',
  number: '',
  name: '',
  expiry: '',
  cvv: ''
})
\\\

**Form Actions**:
- Located in Accordion 3 AccordionDetails footer
- **Place Order** button (primary contained)
- **Reset** button (secondary tonal)

**Styling Notes**:
- Custom border radius styling for radio options:
  \[&:first-of-type>*]:rounded-be-none [&:last-of-type>*]:rounded-bs-none [&:nth-of-type(2)>*]:rounded-none\
- This creates a unified button group appearance

---

### **6. FormLayoutsAlignment** (Unknown - not provided)

**Note**: This component is referenced in the page but not shown in source files. Likely demonstrates different field alignment patterns (inline, stacked, etc.).

---

## Type Definitions

### **FormDataType (Tabs)**

\\\	ypescript
type FormDataType = {
  firstName: string
  lastName: string
  country: string
  language: string[]
  date: Date | null
  phoneNumber: string
  username: string
  email: string
  password: string
  isPasswordShown: boolean
  confirmPassword: string
  setIsConfirmPasswordShown: boolean
  twitter: string
  facebook: string
  google: string
  linkedin: string
  instagram: string
  quora: string
}
\\\

### **FormData (Collapsible)**

\\\	ypescript
type FormData = {
  fullName: string
  phone: string
  address: string
  zipCode: string
  landmark: string
  city: string
  country: string
  addressType: string     // 'home' | 'office'
  number: string          // Card number
  name: string            // Cardholder name
  expiry: string          // MM/YY
  cvv: string            // Card CVV
}
\\\

### **CustomInputHorizontalData (Collapsible)**

\\\	ypescript
type CustomInputHorizontalData = {
  title: string          // "Standard 3-5 Days"
  meta: string           // "Free" or ".00"
  content: string        // Date range description
  isSelected?: boolean   // Default selection
  value: string          // Radio button value
}
\\\

---

## Component Dependencies

### **External Libraries**:
- **MUI Components**: 
  - Card, CardHeader, CardContent, CardActions
  - Grid (responsive layout)
  - Button, IconButton
  - TextField (CustomTextField wrapper)
  - InputAdornment
  - Divider
  - Typography
  - Tab, TabContext, TabList, TabPanel (MUI Lab)
  - Accordion, AccordionSummary, AccordionDetails
  - Radio, RadioGroup
  - FormControlLabel, FormLabel
  - MenuItem
  
- **React**: useState, SyntheticEvent, ChangeEvent

- **Date Handling**: AppReactDatepicker (custom wrapper around react-datepicker)

### **Custom Components**:
- **CustomTextField**: Enhanced Material-UI TextField with custom styling
- **CustomInputHorizontal**: Custom radio/checkbox component for delivery options
- **Form**: Semantic form wrapper component
- **Link**: Next.js Link component

---

## Responsive Design Patterns

### **Basic Field Grid**:
- Mobile (XS): 12/12 (full width, stacked)
- Desktop (SM+): 6/6 (two columns)

### **Page Layout**:
- Mobile (XS): Single column
- Tablet (SM): Single column
- Desktop (MD): 
  - Basic form: 6 cols
  - Icon form: 6 cols (parallel)
  - Separator: 12 cols (full width)
  - Tabs: 12 cols (full width)
  - Collapsible: 12 cols (full width)

### **Form Section Spacing**:
- Grid spacing: 6 units between fields
- Consistent padding in CardContent
- Dividers create visual breaks

---

## State Management Patterns

All forms use **local React.useState** for field management:

1. **Simple Toggle**: Single boolean for password visibility
2. **Form Object**: Single state object containing all form fields
3. **Tab Management**: String state for active tab value
4. **Accordion Management**: String or false for expanded panel

**Pattern**: Spread operator for immutable updates
\\\	ypescript
onChange={e => setFormData({ ...formData, fieldName: e.target.value })}
\\\

---

## Usage Patterns

### **1. Basic Form Pattern**
\\\	ypescript
<form onSubmit={e => e.preventDefault()}>
  <Grid container spacing={6}>
    <Grid size={{ xs: 12 }}>
      <CustomTextField fullWidth label='Field' />
    </Grid>
  </Grid>
</form>
\\\

### **2. Field with Icon**
\\\	ypescript
<CustomTextField
  fullWidth
  label='Field'
  slotProps={{
    input: {
      startAdornment: (
        <InputAdornment position='start'>
          <i className='tabler-icon' />
        </InputAdornment>
      )
    }
  }}
/>
\\\

### **3. Password Toggle Field**
\\\	ypescript
<CustomTextField
  fullWidth
  label='Password'
  type={isShown ? 'text' : 'password'}
  slotProps={{
    input: {
      endAdornment: (
        <InputAdornment position='end'>
          <IconButton onClick={() => setIsShown(!isShown)}>
            <i className={isShown ? 'tabler-eye-off' : 'tabler-eye'} />
          </IconButton>
        </InputAdornment>
      )
    }
  }}
/>
\\\

### **4. Tabbed Form Pattern**
\\\	ypescript
<TabContext value={activeTab}>
  <TabList onChange={(e, v) => setActiveTab(v)}>
    <Tab label='Tab 1' value='tab1' />
    <Tab label='Tab 2' value='tab2' />
  </TabList>
  <TabPanel value='tab1'>{/* Content */}</TabPanel>
  <TabPanel value='tab2'>{/* Content */}</TabPanel>
</TabContext>
\\\

### **5. Accordion Pattern**
\\\	ypescript
<Accordion expanded={expanded === 'panel1'} onChange={handleExpandChange('panel1')}>
  <AccordionSummary>
    <Typography>Section Title</Typography>
  </AccordionSummary>
  <AccordionDetails>{/* Content */}</AccordionDetails>
</Accordion>
\\\

---

## Key Features Across Forms

1. **Password Toggle**: Eye icon toggles between masked/visible passwords
2. **Helper Text**: Guidance text for complex fields (e.g., password requirements)
3. **Multi-select**: Language field supports multiple selections
4. **Date Picker**: Integrated date selection with year/month dropdowns
5. **Conditional Fields**: Payment card fields shown only when credit card selected
6. **Radio Groups**: Address type and payment method use radio buttons
7. **Section Dividers**: Visual separation of form sections
8. **Tab Organization**: Grouping related fields by category
9. **Accordion Steps**: Progressive disclosure of form sections
10. **Icon Adornments**: Visual enhancement with Tabler icons

---

## Accessibility Features

- **Semantic HTML**: Proper form structure with labels
- **ARIA Attributes**: aria-label on icon buttons
- **Tab Navigation**: Full keyboard navigation support
- **Helper Text**: Additional context for required fields
- **Icons + Text**: Icons paired with text labels (not icons alone)
- **Label Association**: All fields have associated labels
- **Color + Text**: Status indicated by both color and text

---

## Performance Considerations

1. **Form State**: Each form maintains its own state (no global management)
2. **Event Handlers**: Arrow functions defined inline (could be memoized for production)
3. **Re-renders**: Form updates trigger component re-render (acceptable for small forms)
4. **Date Picker**: Custom styled wrapper for performance

---

## Form Validation (Not Implemented)

Current forms are demo forms without validation. Production implementations should add:

1. **Real-time Validation**: Validate fields as user types
2. **Submit Validation**: Validate entire form before submission
3. **Error Display**: Show validation errors near fields
4. **Disabled Submit**: Disable submit button until form is valid
5. **Error Messages**: Specific messages for different validation failures

---

## File Statistics

- **page.tsx**: 45 lines (page container)
- **FormLayoutsBasic.tsx**: 122 lines
- **FormLayoutsIcon.tsx**: 101 lines
- **FormLayoutsSeparator.tsx**: 271 lines
- **FormLayoutsTabs.tsx**: 347 lines
- **FormLayoutsCollapsible.tsx**: 343 lines
- **FormLayoutsAlignment.tsx**: Unknown (not provided)
- **Total**: ~1,229+ lines

---

## Related Components

- **Form**: Semantic form wrapper (src/components/Form.tsx)
- **CustomTextField**: Enhanced MUI TextField (src/core/components/mui/TextField)
- **CustomInputHorizontal**: Radio/checkbox component (src/core/components/custom-inputs/Horizontal)
- **AppReactDatepicker**: Styled date picker wrapper

---

## Key Implementation Takeaways

1. **Grid-based Layouts**: MUI Grid provides responsive column management
2. **Input Adornments**: Icons enhance visual feedback without cluttering
3. **State Organization**: Group related fields in single state object
4. **Tab Context**: MUI's TabContext simplifies tab management
5. **Accordion for Steps**: Natural UI for multi-step processes
6. **Helper Text**: Guidance text improves usability
7. **Dividers**: Visual separation aids form comprehension
8. **Responsive Design**: Single codebase adapts to all screen sizes
9. **Material-UI Components**: Composable, accessible, well-documented
10. **TypeScript Types**: Forms benefit from strong typing

---

*Last Updated: Phase 3 Ecommerce & Forms, Task kb-79w.3.4*
*File Format: Markdown Snapshot (Architecture Documentation)*
*Implementation: Next.js 15 + React 19 + Material-UI + React Datepicker*