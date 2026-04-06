# Forms & Wizards Domain Source Structure

## Overview

The Vuexy Admin forms and wizards domain provides comprehensive form pattern implementations with validation, layout options, and multi-step wizard workflows. Organized under `/src/views/forms/` and `/src/views/pages/wizard-examples/`, it demonstrates best practices for client-side form handling, validation schemas, and step-based workflows using React Hook Form, Valibot validation, and MUI Stepper components.

**Key characteristics:**
- React Hook Form integration for efficient form state management
- Valibot schema validation for type-safe validation
- MUI Stepper for multi-step workflows
- Comprehensive input component patterns
- Client-side form validation with real-time feedback
- Password visibility toggles and input adornments
- Tab-based form organization
- Responsive form layouts

---

## Component Structure

### 1. Form Layouts Domain

**Location:** `/src/views/forms/form-layouts/`

Demonstrates various form organization patterns and input component styles.

#### FormLayoutsBasic

Simple registration form with no external validation:

```typescript
const FormLayoutsBasic = () => {
  const [isPasswordShown, setIsPasswordShown] = useState(false)
  const [isConfirmPasswordShown, setIsConfirmPasswordShown] = useState(false)

  return (
    <Card>
      <CardHeader title='Basic' />
      <CardContent>
        <form onSubmit={e => e.preventDefault()}>
          <Grid container spacing={6}>
            <Grid size={{ xs: 12 }}>
              <CustomTextField
                fullWidth
                label='Name'
                placeholder='John Doe'
              />
            </Grid>
            <Grid size={{ xs: 12 }}>
              <CustomTextField
                fullWidth
                type='email'
                label='Email'
                placeholder='johndoe@gmail.com'
                helperText='You can use letters, numbers & periods'
              />
            </Grid>
            <Grid size={{ xs: 12 }}>
              <CustomTextField
                fullWidth
                label='Password'
                type={isPasswordShown ? 'text' : 'password'}
                helperText='Use 8 or more characters'
                slotProps={{
                  input: {
                    endAdornment: (
                      <InputAdornment position='end'>
                        <IconButton
                          onClick={handleClickShowPassword}
                          onMouseDown={e => e.preventDefault()}
                          aria-label='toggle password visibility'
                        >
                          <i className={isPasswordShown ? 'tabler-eye-off' : 'tabler-eye'} />
                        </IconButton>
                      </InputAdornment>
                    )
                  }
                }}
              />
            </Grid>
          </Grid>
        </form>
      </CardContent>
    </Card>
  )
}
```

**Key patterns:**
- Password visibility toggle using state
- InputAdornment for icons/buttons in inputs
- Helper text for validation guidance
- Grid-based responsive layout

#### FormLayoutsAlignment

Demonstrates various input layouts:
- **Horizontal alignment:** Label-value pairs side-by-side
- **Vertical alignment:** Labels above inputs (default)
- **Inline alignment:** Label and input inline

#### FormLayoutsCollapsible

Form sections with expand/collapse functionality:
- Accordion patterns with MUI Collapse
- Section headers with collapse toggle
- State management for expand/collapse state

#### FormLayoutsIcons

Input fields with leading/trailing icons:
- Icon placement in InputAdornment
- Color-coded icons for different field types
- Accessible icon usage

#### FormLayoutsSeparator

Sections separated by Divider components:
- Visual grouping of form fields
- Section headers
- Improved readability for long forms

#### FormLayoutsTabs

Form organized into tabs:
- MUI TabContext for tab management
- Tab switching without page reload
- Validation per tab

---

### 2. Form Validation Domain

**Location:** `/src/views/forms/form-validation/`

React Hook Form integration with validation schemas.

#### FormValidationBasic

Basic validation with React Hook Form:

```typescript
type FormValues = {
  firstName: string
  lastName: string
  email: string
  password: string
  dob: Date | null | undefined
  select: string
  textarea: string
  radio: boolean
  checkbox: boolean
}

const FormValidationBasic = () => {
  const [isPasswordShown, setIsPasswordShown] = useState(false)

  const {
    control,
    reset,
    handleSubmit,
    formState: { errors }
  } = useForm<FormValues>({
    defaultValues: {
      firstName: '',
      lastName: '',
      email: '',
      password: '',
      dob: null,
      select: '',
      textarea: '',
      radio: false,
      checkbox: false
    }
  })

  const onSubmit = () => toast.success('Form Submitted')

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <Grid container spacing={6}>
        <Grid size={{ xs: 12, sm: 6 }}>
          <Controller
            name='firstName'
            control={control}
            rules={{ required: true }}
            render={({ field }) => (
              <CustomTextField
                {...field}
                fullWidth
                label='First Name'
                placeholder='John'
                {...(errors.firstName && {
                  error: true,
                  helperText: 'This field is required.'
                })}
              />
            )}
          />
        </Grid>
      </Grid>
    </form>
  )
}
```

**Key patterns:**
- useForm hook for form state management
- Controller for input field integration
- Error display via helperText prop
- Type-safe form values with TypeScript
- Reset functionality for clearing form

#### FormValidationSchema

Validation using Valibot schema:

```typescript
import { valibotResolver } from '@hookform/resolvers/valibot'
import { email, object, minLength, string, pipe, nonEmpty } from 'valibot'

const validationSchema = object({
  email: pipe(
    string(),
    nonEmpty('Email is required'),
    email('Please enter valid email')
  ),
  password: pipe(
    string(),
    nonEmpty('Password is required'),
    minLength(8, 'Must be 8+ characters')
  )
})

const form = useForm({
  resolver: valibotResolver(validationSchema),
  defaultValues: { email: '', password: '' }
})
```

Valibot advantages:
- Type-safe validation schemas
- Custom error messages
- Composable validators
- No runtime overhead
- Better tree-shaking

#### FormValidationAsyncSubmit

Async form submission:
- Loading state during submission
- Toast notifications for success/error
- Debounced submission to prevent duplicates
- Error recovery

---

### 3. Form Wizard Domain

**Location:** `/src/views/forms/form-wizard/`

Multi-step form workflows with MUI Stepper.

#### StepperLinearWithValidation

Linear stepper with per-step validation:

```typescript
type StepperProps = {
  activeStep: number
  setActiveStep: (step: number) => void
}

const steps = [
  {
    title: 'Account Details',
    subtitle: 'Enter your account details'
  },
  {
    title: 'Personal Info',
    subtitle: 'Setup Information'
  },
  {
    title: 'Social Links',
    subtitle: 'Add Social Links'
  }
]

const accountValidationSchema = object({
  username: pipe(string(), nonEmpty('This field is required')),
  email: pipe(
    string(),
    nonEmpty('This field is required'),
    email('Please enter a valid email address')
  ),
  password: pipe(
    string(),
    nonEmpty('This field is required'),
    minLength(8, 'Password must be at least 8 characters long')
  ),
  confirmPassword: pipe(string(), nonEmpty('This field is required'))
})

const personalSchema = object({
  firstName: pipe(string(), nonEmpty('This field is required')),
  lastName: pipe(string(), nonEmpty('This field is required')),
  country: pipe(string(), nonEmpty('This field is required')),
  language: pipe(array(string()), nonEmpty('This field is required'))
})
```

**Key patterns:**
- Separate form instances per step
- Step-specific validation schemas
- Previous/Next navigation with validation
- Form state reset per step
- Stepper component styling

#### StepperCustomHorizontal

Horizontal stepper with custom styling:
- Custom step icons/indicators
- Alternative label display
- Icon-based step tracking

#### StepperCustomVertical

Vertical stepper layout:
- Sidebar-style step navigation
- Collapsible step content
- Mobile-friendly layout option

#### StepperVerticalWithNumbers

Number-based step indicators

#### StepperVerticalWithoutNumbers

Icon-based step indicators

---

### 4. Wizard Examples Domain

**Location:** `/src/views/pages/wizard-examples/`

Real-world wizard implementations.

#### Checkout Wizard (4 steps)

**Architecture:**
```typescript
const steps = [
  { title: 'Cart', icon: <CartIcon /> },
  { title: 'Address', icon: <AddressIcon /> },
  { title: 'Payment', icon: <PaymentIcon /> },
  { title: 'Confirmation', icon: <CheckIcon /> }
]

const CheckoutWizard = () => {
  const [activeStep, setActiveStep] = useState(0)

  const handleNext = () => {
    if (activeStep === steps.length - 1) {
      // Complete checkout
    }
    setActiveStep(prev => prev + 1)
  }

  const handleBack = () => setActiveStep(prev => prev - 1)

  return (
    <Card>
      <Stepper activeStep={activeStep}>
        {steps.map(step => (
          <Step key={step.title}>
            <StepLabel icon={step.icon}>{step.title}</StepLabel>
          </Step>
        ))}
      </Stepper>

      {activeStep === 0 && <StepCart handleNext={handleNext} />}
      {activeStep === 1 && <StepAddress handleNext={handleNext} />}
      {activeStep === 2 && <StepPayment handleNext={handleNext} />}
      {activeStep === 3 && <StepConfirmation />}

      <Button onClick={handleBack} disabled={activeStep === 0}>Back</Button>
      <Button onClick={handleNext} disabled={activeStep === steps.length - 1}>Next</Button>
    </Card>
  )
}
```

**StepCart component:**
- Product list with quantity controls
- Price calculations
- Discount application
- Alert dismissal pattern

```typescript
const products = [
  {
    imgSrc: '/images/pages/google-home.png',
    productName: 'Google - Google Home - White',
    soldBy: 'Google',
    inStock: true,
    rating: 4,
    count: 1,
    price: 299,
    originalPrice: 359
  }
]

const StepCart = ({ handleNext }: { handleNext: () => void }) => {
  const [openCollapse, setOpenCollapse] = useState<boolean>(true)

  return (
    <Grid container spacing={6}>
      <Alert icon={<i className='tabler-percentage' />}>
        Discount applied!
      </Alert>
      {products.map(product => (
        <ProductCard key={product.productName} {...product} />
      ))}
    </Grid>
  )
}
```

**StepAddress component:**
- Billing address form
- Shipping address form
- Address type selection (home/office/other)
- Save address for future use checkbox

**StepPayment component:**
- Payment method selection
- Card details input
- Billing information
- Payment gateway integration

**StepConfirmation component:**
- Order summary
- Itemized pricing
- Delivery timeline
- Success message

#### Create Deal Wizard (4 steps)

Real estate/deal management:

**Steps:**
1. Deal Type - Select deal category
2. Deal Details - Property/product information
3. Deal Usage - Intended use case
4. Review - Confirm and submit

**Components:**
- StepDealType - Radio buttons for deal type selection
- StepDealDetails - Form with property fields
- StepDealUsage - Usage pattern selection
- StepReview - Summary with edit capability

#### Property Listing Wizard (5 steps)

Real estate property creation:

**Steps:**
1. Personal Details - Owner information
2. Property Details - Property characteristics
3. Property Area - Location and boundaries
4. Price Details - Pricing and terms
5. Property Features - Amenities and features

**Components:**
- StepPersonalDetails - Owner/agent information
- StepPropertyDetails - Property type, size, bedrooms
- StepPropertyArea - Land area, boundaries, maps
- StepPriceDetails - Price, terms, financing options
- StepPropertyFeatures - Amenities, utilities, features

---

## Type Definitions

### Form Types

```typescript
// Basic form values
type FormValues = {
  firstName: string
  lastName: string
  email: string
  password: string
  dob: Date | null | undefined
  select: string
  textarea: string
  radio: boolean
  checkbox: boolean
}

// Validation form values
type ValidationFormValues = {
  username: string
  email: string
  password: string
  confirmPassword: string
}

// Personal info form
type PersonalFormValues = {
  firstName: string
  lastName: string
  country: string
  language: string[]
}

// Social links form
type SocialFormValues = {
  twitter: string
  facebook: string
  google: string
  linkedIn: string
}
```

### Wizard Step Types

```typescript
type WizardStep = {
  title: string
  subtitle?: string
  icon?: ReactNode
}

type CheckoutStep = {
  title: 'Cart' | 'Address' | 'Payment' | 'Confirmation'
  icon: ReactNode
}

type ProductItem = {
  imgSrc: string
  imgAlt: string
  productName: string
  soldBy: string
  inStock: boolean
  rating: number
  count: number
  price: number
  originalPrice: number
}

type AddressInfo = {
  firstName: string
  lastName: string
  email: string
  phone: string
  address: string
  city: string
  state: string
  zipCode: string
  country: string
}

type PaymentInfo = {
  cardHolder: string
  cardNumber: string
  expiryDate: string
  cvv: string
  cardType: 'visa' | 'mastercard' | 'amex'
}
```

---

## Patterns & Validation Strategies

### 1. React Hook Form Pattern

**Controller pattern for custom inputs:**

```typescript
<Controller
  name='email'
  control={control}
  rules={{
    required: 'Email is required',
    pattern: {
      value: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
      message: 'Invalid email address'
    }
  }}
  render={({ field, fieldState: { error } }) => (
    <CustomTextField
      {...field}
      fullWidth
      label='Email'
      error={!!error}
      helperText={error?.message}
    />
  )}
/>
```

**Advantages:**
- Minimal re-renders
- Flexible component integration
- Built-in validation
- Error state management

### 2. Valibot Validation Schema

**Schema composition:**

```typescript
import {
  object,
  string,
  email,
  minLength,
  nonEmpty,
  pipe,
  forward,
  check,
  array
} from 'valibot'

const passwordSchema = pipe(
  string(),
  nonEmpty('Password is required'),
  minLength(8, 'Must be 8+ characters')
)

const loginSchema = object({
  email: pipe(
    string(),
    nonEmpty('Email is required'),
    email('Invalid email')
  ),
  password: passwordSchema
})

const registrationSchema = pipe(
  object({
    password: passwordSchema,
    confirmPassword: string()
  }),
  forward(
    check(
      input => input.password === input.confirmPassword,
      'Passwords must match'
    ),
    ['confirmPassword']
  )
)
```

### 3. Stepper State Management

```typescript
const [activeStep, setActiveStep] = useState(0)

const handleNext = async () => {
  // Validate current step form
  const isValid = await trigger() // React Hook Form
  
  if (isValid) {
    setActiveStep(prev => Math.min(prev + 1, steps.length - 1))
  }
}

const handleBack = () => {
  setActiveStep(prev => Math.max(prev - 1, 0))
}

const handleReset = () => {
  setActiveStep(0)
  reset() // React Hook Form reset
}
```

### 4. Input Component Patterns

**Password input with visibility toggle:**

```typescript
<CustomTextField
  type={isPasswordShown ? 'text' : 'password'}
  label='Password'
  slotProps={{
    input: {
      endAdornment: (
        <InputAdornment position='end'>
          <IconButton
            onClick={() => setIsPasswordShown(!isPasswordShown)}
            onMouseDown={e => e.preventDefault()}
          >
            <i className={isPasswordShown ? 'tabler-eye-off' : 'tabler-eye'} />
          </IconButton>
        </InputAdornment>
      )
    }
  }}
/>
```

**Select with custom options:**

```typescript
<Controller
  name='country'
  control={control}
  rules={{ required: 'Country is required' }}
  render={({ field }) => (
    <CustomTextField
      {...field}
      select
      fullWidth
      label='Country'
      error={!!errors.country}
      helperText={errors.country?.message}
    >
      {countries.map(country => (
        <MenuItem key={country.code} value={country.code}>
          {country.name}
        </MenuItem>
      ))}
    </CustomTextField>
  )}
/>
```

**Checkbox group:**

```typescript
<FormGroup>
  <FormControlLabel
    control={<Checkbox {...field} />}
    label='I agree to terms'
  />
  <FormHelperText error={!!error}>{error?.message}</FormHelperText>
</FormGroup>
```

---

## Dependency List

### Core Dependencies
- React 18+ (hooks, form state)
- Next.js 14+ (navigation, client components)
- @mui/material - Form components (TextField, Select, Checkbox, etc.)
- @mui/lab - Stepper components (Stepper, Step, StepLabel)
- react-hook-form - Form state management
- @hookform/resolvers - Validation schema integration
- valibot - Schema validation
- react-toastify - Toast notifications
- classnames - Conditional styling

### Internal Dependencies
- @core/components/mui/TextField - Custom TextField wrapper
- @core/styles/stepper - Stepper styling
- @components/stepper-dot - Custom stepper indicator
- @components/DirectionalIcon - RTL-aware icon component
- @/libs/styles/AppReactDatepicker - Date picker styling

### Import Patterns

```typescript
// React Hook Form
import { useForm, Controller } from 'react-hook-form'
import { valibotResolver } from '@hookform/resolvers/valibot'

// Valibot
import {
  object,
  string,
  email,
  minLength,
  nonEmpty,
  pipe,
  forward,
  check,
  array
} from 'valibot'

// MUI Form Components
import TextField from '@mui/material/TextField'
import Select from '@mui/material/Select'
import Checkbox from '@mui/material/Checkbox'
import Radio from '@mui/material/Radio'
import MenuItem from '@mui/material/MenuItem'
import FormControlLabel from '@mui/material/FormControlLabel'
import InputAdornment from '@mui/material/InputAdornment'

// MUI Stepper
import Stepper from '@mui/material/Stepper'
import Step from '@mui/material/Step'
import StepLabel from '@mui/material/StepLabel'
import StepContent from '@mui/material/StepContent'
```

---

## Validation Strategies Summary

### Real-time Validation
- Validates on blur or change
- Immediate user feedback
- Shows validation errors as users type

### On-Submit Validation
- Validates entire form on submit
- Useful for multi-field dependencies
- Reduces validation noise

### Schema-based Validation
- Centralized validation logic
- Type-safe schemas with Valibot
- Reusable across components
- Server-side compatibility

### Custom Validators
- Domain-specific validation rules
- Async validation (email uniqueness)
- Cross-field validation
- Conditional validation rules

---

## Statistics

**Total Form/Wizard Components:** 15+
- Form Layouts: 6 variations
- Form Validation: 3 approaches
- Form Wizards: 6 stepper patterns
- Wizard Examples: 3 real-world flows

**Lines of Code:** ~2,500 LOC
**Reusability:** High (patterns applicable across projects)
**Accessibility:** WCAG compliant with proper labels and error messages

---

## Summary

Forms & Wizards domain demonstrates **production-ready form patterns** using:
- React Hook Form for efficient state management
- Valibot for type-safe schema validation
- MUI components for consistent UI
- Stepper component for multi-step workflows
- Comprehensive validation strategies
- Accessible form inputs
- Real-world wizard implementations

**Perfect for:** Building registration forms, checkout flows, multi-step surveys, property listing tools, deal management systems, and any complex form workflows.
