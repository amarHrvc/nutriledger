# Forms Catalog

**Overview**

Complete catalog of all form patterns, components, and implementations available in the Vuexy Admin Dashboard. This document serves as a quick reference guide for selecting the appropriate form pattern for different use cases.

---

## Quick Reference Table

| Pattern | Page | Type | Fields | Steps | Use Case |
|---------|------|------|--------|-------|----------|
| Basic Form | Form Layouts | Standard | Name, Email, Password | 1 | Simple registration/signup |
| Icon Form | Form Layouts | Standard | Text + Icons | 1 | Contact forms, support tickets |
| Separator Form | Form Layouts | Multi-section | Account + Personal | 1 | Long forms with sections |
| Tabbed Form | Form Layouts | Multi-tab | 3 tabs × 6 fields | 1 | Grouped related fields |
| Accordion Form | Form Layouts | Collapsible | 3 sections | 1 | E-commerce checkout, progressive disclosure |
| Linear Stepper | Form Wizard | Multi-step | Account → Personal → Social | 3 | Guided workflows, registration flows |
| Alternative Stepper | Form Wizard | Alternative | - | - | Alternative layout option |
| Vertical Stepper | Form Wizard | Vertical | - | - | Mobile-friendly steps |
| Custom Horizontal | Form Wizard | Styled | - | - | Branded stepper design |
| Custom Vertical | Form Wizard | Styled | - | - | Branded vertical stepper |
| Product Add Form | Ecommerce | Complex | 7 components | - | Creating/editing products |

---

## Form Patterns by Category

### **SIMPLE FORMS (Single Step, Basic Fields)**

#### **1. FormLayoutsBasic**
**Path**: /forms/form-layouts  
**Files**: FormLayoutsBasic.tsx (122 lines)

**When to Use**:
- Simple signup/login forms
- Quick data collection
- Minimal fields (3-4)

**Components**:
- Text input (Name)
- Email input
- Password inputs (2 with toggle)

**Features**:
- Password visibility toggle
- Helper text for guidance
- Basic validation (no schema)

**Layout**: Vertical stacked fields, responsive to 2 columns

---

#### **2. FormLayoutsIcon**
**Path**: /forms/form-layouts  
**Files**: FormLayoutsIcon.tsx (101 lines)

**When to Use**:
- Contact forms with visual clarity
- Forms requiring icon hints
- Better visual hierarchy needed

**Components**:
- Text input + icon
- Email input + icon
- Phone input + icon
- Textarea + icon

**Features**:
- Start position icons (Tabler)
- Multiline field support
- Visual enhancement without clutter

**Layout**: Vertical fields with icon adornments

---

### **MULTI-SECTION FORMS (Single Step, Multiple Sections)**

#### **3. FormLayoutsSeparator**
**Path**: /forms/form-layouts  
**Files**: FormLayoutsSeparator.tsx (271 lines)

**When to Use**:
- Forms with 8+ fields
- Logical field grouping
- Section-based workflows

**Sections**:
1. Account Details (4 fields)
2. Personal Info (6 fields)

**Features**:
- Divider separators
- Section labels
- Password toggles
- Date picker integration

**Layout**: 2-column grid with dividers, responsive to 1 column

---

### **TABBED FORMS (Single Step, Multiple Tabs)**

#### **4. FormLayoutsTabs**
**Path**: /forms/form-layouts  
**Files**: FormLayoutsTabs.tsx (347 lines)

**When to Use**:
- Forms with 12+ fields
- Multiple categories of information
- Save progress by tab
- Flexible field organization

**Tabs** (3 total):
1. Personal Info (6 fields)
2. Account Details (4 fields)
3. Social Links (6 fields)

**Features**:
- Scrollable tab list
- State management per tab
- Date picker in Tab 1
- Multi-select in Tab 1

**Layout**: Horizontal scrollable tabs, responsive stacking

---

### **ACCORDION FORMS (Single Step, Collapsible Sections)**

#### **5. FormLayoutsCollapsible**
**Path**: /forms/form-layouts  
**Files**: FormLayoutsCollapsible.tsx (343 lines)

**When to Use**:
- Progressive disclosure needed
- Mobile-first design
- E-commerce checkouts
- Overwhelming amount of fields

**Sections** (3, one open at a time):
1. Delivery Address (8 fields)
2. Delivery Options (3 radio options)
3. Payment Method (conditional credit card fields)

**Features**:
- Accordion expand/collapse
- Conditional field rendering
- Radio button groups
- Custom horizontal inputs

**Layout**: Full width accordions, mobile-optimized

---

### **MULTI-STEP FORMS (Wizard Pattern)**

#### **6. StepperLinearWithValidation** (Main Wizard)
**Path**: /forms/form-wizard  
**Files**: StepperLinearWithValidation.tsx (612 lines)

**When to Use**:
- Complex multi-step workflows
- Progressive validation needed
- Guided user journeys
- Clear step progression

**Steps** (3 total):
1. Account Details → 4 fields
2. Personal Info → 4 fields
3. Social Links → 4 fields

**Features**:
- React Hook Form integration
- Valibot schema validation
- Cross-field validation (password match)
- Separate form state per step
- Toast notifications
- Error state on step indicator
- Back/Next/Submit buttons

**Validation**:
- Real-time field validation
- Custom error messages
- Schema validation per step

**Layout**: Horizontal stepper, responsive to vertical

---

#### **7. StepperAlternativeLabel**
**Path**: /forms/form-wizard  
**Files**: StepperAlternativeLabel.tsx

**When to Use**:
- Alternative label positioning
- Visual variety
- Specific design requirements

**Features**:
- Alternative label layout
- Same functionality as linear stepper

---

#### **8. StepperVerticalWithNumbers**
**Path**: /forms/form-wizard  
**Files**: StepperVerticalWithNumbers.tsx

**When to Use**:
- Mobile-first designs
- Large form fields need more space
- Vertical layout preference

**Features**:
- Vertical step layout
- Step numbers displayed
- Full-width fields

---

#### **9. StepperVerticalWithoutNumbers**
**Path**: /forms/form-wizard  
**Files**: StepperVerticalWithoutNumbers.tsx

**When to Use**:
- Cleaner aesthetic
- Focus on step titles
- Without number indicators

**Features**:
- Vertical layout
- No step numbering
- Title-based indicators

---

#### **10. StepperCustomHorizontal**
**Path**: /forms/form-wizard  
**Files**: StepperCustomHorizontal.tsx

**When to Use**:
- Brand-specific designs
- Custom styling requirements
- Unique step indicators

**Features**:
- Custom styled stepper
- Horizontal layout
- Branded appearance

---

#### **11. StepperCustomVertical**
**Path**: /forms/form-wizard  
**Files**: StepperCustomVertical.tsx

**When to Use**:
- Brand-specific designs
- Vertical layout preference
- Custom styling requirements

**Features**:
- Custom styled stepper
- Vertical layout
- Branded appearance

---

### **COMPLEX ECOMMERCE FORMS**

#### **12. Product Add/Edit Form**
**Path**: /apps/ecommerce/products/add  
**Files**: page.tsx + 7 components (877 lines)

**When to Use**:
- Creating/editing products
- Complex nested forms
- Multiple form sections with different input types
- Rich text editing needed

**Sections** (6 major):
1. **ProductAddHeader** (Action buttons)
2. **ProductInformation** (Tiptap editor, SKU, barcode)
3. **ProductImage** (Dropzone file upload)
4. **ProductVariants** (Dynamic rows)
5. **ProductInventory** (5-tab interface)
6. **ProductPricing** (Base, discounted, tax)
7. **ProductOrganize** (Vendor, category, tags)

**Features**:
- Rich text editor (Tiptap)
- File upload with drag-drop
- Dynamic form rows
- Tabbed inventory section
- 2-column layout
- Complex nested state

**Components Used**:
- ProductCard (KPI stats)
- TableFilters (Dropdown filters)
- ProductListTable (TanStack React Table)

**Layout**: 8-col main + 4-col sidebar, 12-col mobile

---

## Form Component Library

### **Input Components**

| Component | Type | Features |
|-----------|------|----------|
| CustomTextField | Text, Email, Password, Number | Input adornment, error state, helper text |
| TextArea | Multiline | Rows adjustable, icon support |
| Dropdown (Select) | Single/Multiple | MenuItem options, searchable |
| Date Picker | Date | Year/month dropdowns, format support |
| File Upload | File | Drag-drop, preview, remove |
| Rich Text Editor | Text | Tiptap integration, toolbar |

### **Control Components**

| Component | Type | Features |
|-----------|------|----------|
| Password Toggle | Button | Eye icon, show/hide |
| Icon Button | Button | Icon + action |
| Submit Button | Button | Form submission |
| Cancel Button | Button | Discard/back action |
| Custom Checkbox | Checkbox | Label customization |
| Radio Group | Radio | Horizontal/vertical |
| Switch | Toggle | On/off state |

### **Layout Components**

| Component | Type | Features |
|-----------|------|----------|
| Card | Container | CardHeader, CardContent, CardActions |
| Grid | Grid | Responsive sizing, spacing |
| Divider | Separator | Horizontal/vertical |
| Tab Group | Tabs | TabContext, scrollable |
| Accordion | Collapse | Expand/collapse, nested |
| Stepper | Progress | Step indicators, validation |

---

## Form Validation Approaches

### **1. No Validation** (Uncontrolled)
- **Used in**: FormLayoutsBasic, FormLayoutsIcon
- **Benefit**: Simplest implementation
- **Drawback**: No client-side validation

### **2. React Hook Form + Valibot** (Recommended)
- **Used in**: StepperLinearWithValidation
- **Benefit**: Powerful validation, schema-based, type-safe
- **Features**:
  - Real-time validation
  - Cross-field validation
  - Custom error messages
  - Schema composition

### **3. Custom Validation Logic**
- **Used in**: FormLayoutsSeparator, FormLayoutsTabs
- **Benefit**: Full control over validation
- **Drawback**: More boilerplate code

---

## State Management Approaches

### **1. Local State (useState)**
- **Approach**: Single state object with spread updates
- **Used in**: All form layouts
- **Example**:
  \\\	ypescript
  const [formData, setFormData] = useState({ ... })
  onChange={e => setFormData({ ...formData, field: value })}
  \\\

### **2. React Hook Form**
- **Approach**: Form library manages state
- **Used in**: StepperLinearWithValidation
- **Benefits**: Built-in validation, error handling, performance

### **3. Uncontrolled Components**
- **Approach**: DOM manages state via refs
- **Used in**: ProductAddForm (potential pattern)
- **Benefit**: Simpler for simple forms

---

## Pattern Selection Guide

### **Choose Basic Form When**:
- You need a simple form (< 5 fields)
- No complex validation required
- Single step/no progression
- Quick implementation priority

### **Choose Icon Form When**:
- Visual enhancement desired
- Icons help with field clarity
- Contact/feedback forms
- Better UX without clutter

### **Choose Separator Form When**:
- 6-8 fields total
- Natural groupings exist
- Logical sections needed
- Single step workflow

### **Choose Tabbed Form When**:
- 9+ fields total
- Multiple categories
- Fields can be grouped logically
- Users want to see structure
- Mobile-unfriendly (many fields)

### **Choose Accordion Form When**:
- Progressive disclosure needed
- Mobile-first design
- Too many fields to show at once
- E-commerce patterns (checkout)
- Users should see options before committing

### **Choose Stepper Form When**:
- Multi-step workflow needed
- Validation between steps
- Guided user experience
- Complex/long processes
- Clear progression important
- Users need to understand flow

### **Choose Product Add Form When**:
- Creating/editing complex entities
- Multiple input types needed (text, file, rich text)
- Nested form sections
- Advanced features (dynamic rows, tabs)
- Admin/backend interfaces

---

## Quick Implementation Checklist

### **For Simple Forms**:
- [ ] Choose Basic or Icon pattern
- [ ] Wrap in Card component
- [ ] Add CustomTextField components
- [ ] Add submit/cancel buttons
- [ ] Add helper text if needed
- [ ] Test responsiveness

### **For Multi-Section Forms**:
- [ ] Choose Separator or Tabbed pattern
- [ ] Plan field groupings
- [ ] Create section labels/titles
- [ ] Add dividers (separator) or tabs (tabbed)
- [ ] Implement state management
- [ ] Add form actions

### **For Multi-Step Forms**:
- [ ] Choose appropriate stepper variant
- [ ] Define steps and fields
- [ ] Implement validation schema
- [ ] Set up form state per step
- [ ] Add back/next/submit logic
- [ ] Handle completion state

### **For Complex Forms**:
- [ ] Break into logical sections
- [ ] Use appropriate layout pattern
- [ ] Implement validation
- [ ] Handle file uploads if needed
- [ ] Test on multiple devices
- [ ] Add loading/success states

---

## Common Patterns & Recipes

### **Password Confirmation Validation**
\\\	ypescript
// Valibot schema
const schema = object({
  password: pipe(string(), nonEmpty(), minLength(8)),
  confirmPassword: pipe(string(), nonEmpty())
})
.pipe(
  forward(
    check(input => input.password === input.confirmPassword, 'Passwords do not match'),
    ['confirmPassword']
  )
)
\\\

### **Password Visibility Toggle**
\\\	ypescript
const [isShown, setIsShown] = useState(false)

<CustomTextField
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

### **Field with Icon Adornment**
\\\	ypescript
<CustomTextField
  slotProps={{
    input: {
      startAdornment: (
        <InputAdornment position='start'>
          <i className='tabler-icon-name' />
        </InputAdornment>
      )
    }
  }}
/>
\\\

### **Conditional Field Rendering**
\\\	ypescript
{paymentMethod === 'credit' && (
  <Grid size={{ xs: 12 }}>
    {/* Credit card fields */}
  </Grid>
)}
\\\

### **Multi-Select Dropdown**
\\\	ypescript
<CustomTextField
  select
  slotProps={{
    select: { multiple: true }
  }}
  value={selectedArray}
  onChange={e => setSelectedArray(e.target.value as string[])}
>
  {options.map(opt => (
    <MenuItem key={opt} value={opt}>{opt}</MenuItem>
  ))}
</CustomTextField>
\\\

### **Dynamic Form Rows**
\\\	ypescript
const [count, setCount] = useState(1)

{Array.from(Array(count).keys()).map((_, index) => (
  <Grid key={index} size={{ xs: 12 }}>
    {/* Row content */}
  </Grid>
))}

<Button onClick={() => setCount(count + 1)}>Add Row</Button>
\\\

---

## Testing Patterns

### **Basic Form Testing**
\\\	ypescript
import { render, screen, fireEvent } from '@testing-library/react'

test('should submit form with valid data', () => {
  render(<BasicForm />)
  
  fireEvent.change(screen.getByPlaceholderText('John Doe'), {
    target: { value: 'Jane Doe' }
  })
  
  fireEvent.click(screen.getByText('Submit'))
  
  expect(screen.getByText('Success!')).toBeInTheDocument()
})
\\\

### **Validation Testing**
\\\	ypescript
test('should show validation error on submit', async () => {
  render(<FormWithValidation />)
  
  fireEvent.click(screen.getByText('Submit'))
  
  await waitFor(() => {
    expect(screen.getByText('Email is required')).toBeInTheDocument()
  })
})
\\\

---

## Performance Optimization Tips

1. **Memoize Form Components**: Prevent unnecessary re-renders
   \\\	ypescript
   const FormSection = React.memo(({ fields, onChange }) => { ... })
   \\\

2. **Debounce onChange**: For heavy validation
   \\\	ypescript
   const debouncedOnChange = useCallback(
     debounce((value) => validate(value), 500),
     []
   )
   \\\

3. **Lazy Load File Inputs**: Only load on user interaction
   \\\	ypescript
   const FileUpload = lazy(() => import('./FileUpload'))
   <Suspense fallback={<div>Loading...</div>}>
     <FileUpload />
   </Suspense>
   \\\

4. **Split Large Forms**: Use tabs/accordion to reduce DOM elements

5. **Use React.lazy for Heavy Components**: Wizard steps, editors

---

## Accessibility Checklist

- [ ] All inputs have associated labels
- [ ] Error messages linked to fields (aria-describedby)
- [ ] Form has landmark roles (form, main)
- [ ] Tab order is logical
- [ ] Color not only indicator (+ text)
- [ ] Helper text provided for complex fields
- [ ] Icon buttons have aria-label
- [ ] Keyboard navigation fully supported
- [ ] Focus visible on all interactive elements
- [ ] Error messages announced

---

## Browser Support

| Feature | IE11 | Edge | Chrome | Firefox | Safari |
|---------|------|------|--------|---------|--------|
| Stepper | ✓ | ✓ | ✓ | ✓ | ✓ |
| Tabs | ✓ | ✓ | ✓ | ✓ | ✓ |
| Accordion | ✓ | ✓ | ✓ | ✓ | ✓ |
| File Input | ✓ | ✓ | ✓ | ✓ | ✓ |
| Date Picker | ~ | ✓ | ✓ | ✓ | ✓ |
| Rich Editor | ~ | ✓ | ✓ | ✓ | ✓ |

---

## Related Documentation

- [Form Layouts Page](./form-layouts-page.md)
- [Form Wizard Page](./form-wizard-page.md)
- [Product Add Form](./ecommerce-product-add-page.md)
- [Form Validation Patterns](../components/product-add-form-component.md)

---

*Last Updated: Phase 3 Ecommerce & Forms, Task kb-79w.3.6*
*File Format: Markdown Reference*
*Total Patterns Documented: 12 Major + 5+ Component Types*