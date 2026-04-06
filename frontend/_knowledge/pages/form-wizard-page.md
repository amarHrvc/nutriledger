# Form Wizard/Stepper Page Snapshot

**Overview**

The Form Wizard page showcases multiple stepper/wizard implementations for multi-step forms. The main implementation features a linear stepper with form validation using React Hook Form and Valibot schema validation. Additional variations include alternative label styles, vertical layouts with/without step numbers, and custom horizontal/vertical steppers.

**File Location**: src/app/[lang]/(dashboard)/(private)/forms/form-wizard/

**Route**: /forms/form-wizard

**Component Location**: src/views/forms/form-wizard/

---

## Page Structure

\\\
FormWizard Page
├── Header Info (with links to MUI docs)
│
├── StepperLinearWithValidation (primary implementation)
│   └── 3-step horizontal stepper
│
├── StepperAlternativeLabel
│   └── Horizontal stepper with alternative label layout
│
├── StepperVerticalWithNumbers
│   └── Vertical stepper with step numbers
│
├── StepperVerticalWithoutNumbers
│   └── Vertical stepper without step numbers
│
├── StepperCustomHorizontal
│   └── Custom styled horizontal stepper
│
└── StepperCustomVertical
    └── Custom styled vertical stepper
\\\

---

## StepperLinearWithValidation Component (612 lines)

**Purpose**: Linear horizontal stepper with form validation for multi-step workflows

**Steps** (3 total):

### **Step 1: Account Details**
- Subtitle: "Enter your account details"
- Fields:
  1. **Username** (text, 6 cols on desktop)
     - Placeholder: "johnDoe"
     - Validation: required, minLength 1
  
  2. **Email** (email, 6 cols on desktop)
     - Placeholder: "johndoe@gmail.com"
     - Validation: required, valid email format
  
  3. **Password** (password toggle, 6 cols on desktop)
     - Placeholder: "············"
     - Validation: required, minLength 8
     - Toggle: Eye icon for visibility
  
  4. **Confirm Password** (password toggle, 6 cols on desktop)
     - Placeholder: "············"
     - Validation: required, must match password field

### **Step 2: Personal Info**
- Subtitle: "Setup Information"
- Fields:
  1. **First Name** (text, 6 cols on desktop)
     - Placeholder: "John"
     - Validation: required, minLength 1
  
  2. **Last Name** (text, 6 cols on desktop)
     - Placeholder: "Doe"
     - Validation: required, minLength 1
  
  3. **Country** (dropdown, 6 cols on desktop)
     - Options: UK, USA, Australia, Germany
     - Validation: required
  
  4. **Language** (multi-select dropdown, 6 cols on desktop)
     - Options: English, French, Spanish, Portuguese, Italian, German, Arabic
     - Validation: required, at least one selection

### **Step 3: Social Links**
- Subtitle: "Add Social Links"
- Fields:
  1. **Twitter** (url, 6 cols on desktop)
     - Placeholder: "https://twitter.com/johndoe"
     - Validation: required
  
  2. **Facebook** (url, 6 cols on desktop)
     - Placeholder: "https://facebook.com/johndoe"
     - Validation: required
  
  3. **Google** (url, 6 cols on desktop)
     - Placeholder: "https://google.com/johndoe"
     - Validation: required
  
  4. **LinkedIn** (url, 6 cols on desktop)
     - Placeholder: "https://linkedin.com/johndoe"
     - Validation: required

---

## Validation Schemas (Valibot)

\\\	ypescript
// Account validation
const accountValidationSchema = object({
  username: pipe(string(), nonEmpty('This field is required'), minLength(1)),
  email: pipe(string(), nonEmpty('This field is required'), email('Please enter a valid email address')),
  password: pipe(
    string(),
    nonEmpty('This field is required'),
    minLength(8, 'Password must be at least 8 characters long')
  ),
  confirmPassword: pipe(string(), nonEmpty('This field is required'), minLength(1))
})

// Cross-field validation: passwords must match
const accountSchema = pipe(
  accountValidationSchema,
  forward(
    check(input => input.password === input.confirmPassword, 'Passwords do not match.'),
    ['confirmPassword']
  )
)

// Personal info validation
const personalSchema = object({
  firstName: pipe(string(), nonEmpty('This field is required'), minLength(1)),
  lastName: pipe(string(), nonEmpty('This field is required'), minLength(1)),
  country: pipe(string(), nonEmpty('This field is required'), minLength(1)),
  language: pipe(array(string()), nonEmpty('This field is required'), minLength(1))
})

// Social links validation
const socialSchema = object({
  twitter: pipe(string(), nonEmpty('This field is required'), minLength(1)),
  facebook: pipe(string(), nonEmpty('This field is required'), minLength(1)),
  google: pipe(string(), nonEmpty('This field is required'), minLength(1)),
  linkedIn: pipe(string(), nonEmpty('This field is required'), minLength(1))
})
\\\

---

## State Management

\\\	ypescript
const [activeStep, setActiveStep] = useState(0)              // Current step (0-2)
const [isPasswordShown, setIsPasswordShown] = useState(false)
const [isConfirmPasswordShown, setIsConfirmPasswordShown] = useState(false)
\\\

---

## Form Management (React Hook Form)

**Separate form instances for each step**:

Each step has its own React Hook Form instance with:
- useForm hook with valibotResolver
- Controller wrapper for each field
- Form state: { control, errors, reset, handleSubmit }

Example:
\\\	ypescript
const {
  reset: accountReset,
  control: accountControl,
  handleSubmit: handleAccountSubmit,
  formState: { errors: accountErrors }
} = useForm({
  resolver: valibotResolver(accountSchema),
  defaultValues: {
    username: '',
    email: '',
    password: '',
    confirmPassword: ''
  }
})
\\\

---

## Navigation & Flow

**Button Layout**: Back | Next (reversed on mobile with flex justify-between)

**Step Progression**:
1. Step 1 → (validate) → Step 2 
2. Step 2 → (validate) → Step 3
3. Step 3 → (validate) → Completion

**Button States**:
- Step 1: Back button disabled (can't go back from first step)
- Step 2-3: Back button enabled
- Steps 1-2: Next button (submit form, go to next step)
- Step 3: Submit button with checkmark icon (tabler-check)

**Completion**:
- After final submission, display "All steps are completed!" message
- Reset button allows user to restart from step 1

---

## Features

### **1. Form Validation**
- Real-time validation via React Hook Form + Valibot
- Error display with helperText on each field
- Custom error messages from schema
- Cross-field validation (password match check)

### **2. Error State Management**
- Step indicator shows error state when current step has validation errors
- StepperCustomDot component displays error styling
- labelProps.error set based on current step's form errors

### **3. Password Visibility Toggle**
- Eye icon toggles between password dots and plain text
- End adornment with InputAdornment component
- onMouseDown preventDefault to prevent focus blur

### **4. Directional Icons**
- DirectionalIcon component handles LTR/RTL arrow directions
- Left/Right arrows: tabler-arrow-left / tabler-arrow-right
- Submit: tabler-check icon

### **5. Multi-select Language Field**
- Uses slotProps.select.multiple = true
- Supports array value for multiple selections
- Maps language options from predefined array

### **6. Styled Components**
- Custom Stepper styling with MUI styled() API
- Padding adjustments for responsive design
- Centered stepper with justify-content: center

### **7. Toast Notifications**
- Success toast on final form submission
- Implemented with react-toastify

---

## Custom Components Used

### **StepperCustomDot**
- Custom step indicator component
- Handles error state styling
- Shows step number (01, 02, 03)

### **StepperWrapper**
- Custom styled wrapper for stepper
- Provides theming and layout styling

### **DirectionalIcon**
- Handles RTL/LTR icon direction
- Props: ltrIconClass, rtlIconClass

### **CustomTextField**
- Enhanced Material-UI TextField
- Integrates with React Hook Form Controller
- Supports error state and helper text

---

## Layout Patterns

### **Card Structure**:
- CardContent: Stepper display
- Divider: Separates stepper from form content
- CardContent: Dynamic form content based on activeStep

### **Grid Layout**:
- Container spacing: 6 units
- Fields: 12/12 cols on mobile, 6/6 cols on desktop
- Title/subtitle: Full width (12/12)
- Buttons: Full width with flex justify-between

### **Step Content Rendering**:
- Switch statement on activeStep
- Key prop for each form (remount form on step change)
- Each step is a separate form element

---

## Type Definitions

\\\	ypescript
// Step structure
type Step = {
  title: string
  subtitle: string
}

// Account form data
type AccountData = {
  username: string
  email: string
  password: string
  confirmPassword: string
}

// Personal info form data
type PersonalData = {
  firstName: string
  lastName: string
  country: string
  language: string[]
}

// Social links form data
type SocialData = {
  twitter: string
  facebook: string
  google: string
  linkedIn: string
}
\\\

---

## Complete User Flow

\\\
1. User sees Step 1 (Account Details) form
2. Fills: username, email, password, confirm password
3. Validation checks:
   - All fields required
   - Email format valid
   - Password at least 8 characters
   - Passwords match
4. If valid: Click Next → Step 2
5. If invalid: Errors display, step indicator shows error state

6. User fills Step 2 (Personal Info)
7. Selects: first/last name, country, language(s)
8. Validation checks: all required
9. If valid: Click Next → Step 3
10. If invalid: Back button available to fix Step 1 if needed

11. User fills Step 3 (Social Links)
12. Enters: Twitter, Facebook, Google, LinkedIn URLs
13. Validation checks: all required
14. If valid: Click Submit → Toast "Form Submitted"
15. Form complete: Show "All steps completed!" message
16. Reset button available to restart

If user clicks Back:
- Goes to previous step
- Form data preserved in that step's form state
- Can edit and re-submit
\\\

---

## Related Wizard Variants

The page also includes (not detailed in this snapshot):

1. **StepperAlternativeLabel**: Alternative horizontal label positioning
2. **StepperVerticalWithNumbers**: Vertical layout with numbered steps
3. **StepperVerticalWithoutNumbers**: Vertical layout without step numbers
4. **StepperCustomHorizontal**: Custom styled horizontal stepper
5. **StepperCustomVertical**: Custom styled vertical stepper

---

## Dependencies

### **Form Management**:
- react-hook-form: Form state and validation
- @hookform/resolvers: Adapter for Valibot
- valibot: Schema validation library

### **UI Components**:
- MUI: Stepper, Step, StepLabel, Card, Grid, Button, etc.
- Tabler Icons: Icon library (tabler-*)

### **Custom Components**:
- StepperCustomDot: Custom step indicator
- StepperWrapper: Stepper styling wrapper
- DirectionalIcon: RTL/LTR icon handler
- CustomTextField: Enhanced text input

### **Notifications**:
- react-toastify: Toast notifications

---

## Accessibility Features

- **Semantic HTML**: Form elements with proper structure
- **ARIA Labels**: aria-label on icon buttons (toggle password)
- **Error Association**: Helper text linked to fields
- **Keyboard Navigation**: Full keyboard support for form
- **Tab Navigation**: Sequential tab order through fields
- **Focus Management**: Focus on next field after interaction
- **Color + Text**: Error states use color and text
- **Step Indicator**: Clear visual indication of progress

---

## Performance Notes

- **Separate Form Instances**: Each step has isolated form state
- **Conditional Rendering**: Only active step's form renders
- **Memoization**: Could optimize with React.memo for step components
- **Validation**: Real-time validation with Valibot schema

---

*Last Updated: Phase 3 Ecommerce & Forms, Task kb-79w.3.5*
*File Format: Markdown Snapshot (Architecture Documentation)*
*Implementation: Next.js 15 + React 19 + React Hook Form + Valibot + Material-UI*