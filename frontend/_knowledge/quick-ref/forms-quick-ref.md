# Forms Quick Reference

**Tags**: React Hook Form, Valibot, Validation, Wizards, Multi-step, Layouts

---

## Q&A

### How to Create a Form with Validation?

Use **React Hook Form** + **Valibot** schema validation:

1. Define validation schema with Valibot
2. Create form with `useForm` hook
3. Wrap fields with `Controller` component
4. Display errors via `helperText` prop

**Pattern**: Schema → useForm → Controller → Validation error display

### How to Add Multi-Step Wizard?

Use **MUI Stepper** + separate form instances per step:

1. Create steps array with titles/subtitles
2. Manage `activeStep` state with `useState(0)`
3. Create separate `useForm` hook per step
4. Validate before advancing to next step
5. Handle next/back button logic

**Flow**: Step 1 → Validate → Step 2 → Validate → Step 3 → Submit

### How to Choose Form Layout Variant?

| Variant | Use When | Fields | Example |
|---------|----------|--------|---------|
| Basic | < 5 fields | Simple input | Login, signup |
| Separator | 6-8 fields | Sections needed | Long form |
| Tabbed | 9+ fields | Grouped logically | Profile setup |
| Accordion | Progressive | Mobile-first | Checkout |
| Wizard | Multi-step | Guided flow | Registration |

---

## Types

### FormFieldType

```typescript
type FormFieldType = 
  | 'text'
  | 'email'
  | 'password'
  | 'number'
  | 'tel'
  | 'url'
  | 'select'
  | 'multiselect'
  | 'checkbox'
  | 'radio'
  | 'textarea'
  | 'date'
  | 'file'
```

### ValidationSchemaType

```typescript
// Valibot pipeline schema
type ValidationSchema = {
  field: PipeSchema  // pipe(string(), nonEmpty(), minLength(8), etc)
}

// Common validators
type Validators = 
  | nonEmpty(message?: string)
  | minLength(len, message?)
  | maxLength(len, message?)
  | email(message?)
  | regex(pattern, message?)
  | custom(fn, message?)
```

---

## Pattern Map

### 1. React Hook Form + Valibot Setup

```typescript
// Step 1: Define schema
import { object, string, email, minLength, nonEmpty, pipe, forward, check } from 'valibot'

const loginSchema = object({
  email: pipe(
    string(),
    nonEmpty('Email is required'),
    email('Invalid email format')
  ),
  password: pipe(
    string(),
    nonEmpty('Password is required'),
    minLength(8, 'Min 8 characters')
  )
})

// Step 2: Use in component
import { useForm, Controller } from 'react-hook-form'
import { valibotResolver } from '@hookform/resolvers/valibot'

const {
  control,
  handleSubmit,
  formState: { errors },
  reset
} = useForm({
  resolver: valibotResolver(loginSchema),
  defaultValues: { email: '', password: '' }
})

// Step 3: Render with Controller
<Controller
  name="email"
  control={control}
  render={({ field }) => (
    <CustomTextField
      {...field}
      fullWidth
      label="Email"
      error={!!errors.email}
      helperText={errors.email?.message}
    />
  )}
/>
```

### 2. Layout Variants

**Basic (Vertical Stack)**:
```typescript
<Grid container spacing={6}>
  <Grid size={{ xs: 12 }}><Field /></Grid>
  <Grid size={{ xs: 12 }}><Field /></Grid>
</Grid>
```

**Two-Column**:
```typescript
<Grid container spacing={6}>
  <Grid size={{ xs: 12, sm: 6 }}><Field /></Grid>
  <Grid size={{ xs: 12, sm: 6 }}><Field /></Grid>
</Grid>
```

**Tabbed**:
```typescript
<TabContext value={activeTab}>
  <TabList onChange={(_, val) => setActiveTab(val)}>
    <Tab label="Tab 1" value="tab1" />
    <Tab label="Tab 2" value="tab2" />
  </TabList>
  <TabPanel value="tab1">{/* Fields */}</TabPanel>
  <TabPanel value="tab2">{/* Fields */}</TabPanel>
</TabContext>
```

**Accordion**:
```typescript
<Accordion expanded={expanded === 'panel1'}>
  <AccordionSummary>Section Title</AccordionSummary>
  <AccordionDetails>{/* Fields */}</AccordionDetails>
</Accordion>
```

### 3. Wizard Step Pattern

```typescript
// Steps definition
const steps = [
  { title: 'Account', subtitle: 'Enter details' },
  { title: 'Personal', subtitle: 'Add info' },
  { title: 'Review', subtitle: 'Confirm' }
]

// State management
const [activeStep, setActiveStep] = useState(0)

// Form per step
const accountForm = useForm({ 
  resolver: valibotResolver(accountSchema) 
})
const personalForm = useForm({ 
  resolver: valibotResolver(personalSchema) 
})

// Navigation
const handleNext = async () => {
  const isValid = await (activeStep === 0 
    ? accountForm.trigger() 
    : personalForm.trigger())
  
  if (isValid) {
    setActiveStep(prev => prev + 1)
  }
}

const handleBack = () => setActiveStep(prev => prev - 1)

// Render
<Stepper activeStep={activeStep}>
  {steps.map(step => (
    <Step key={step.title}>
      <StepLabel>{step.title}</StepLabel>
    </Step>
  ))}
</Stepper>

{activeStep === 0 && <AccountStep form={accountForm} />}
{activeStep === 1 && <PersonalStep form={personalForm} />}
{activeStep === 2 && <ReviewStep />}

<Button onClick={handleBack} disabled={activeStep === 0}>Back</Button>
<Button onClick={handleNext}>Next</Button>
```

---

## Snippets

### RHF Setup (Complete Example)

```typescript
'use client'
import { Controller, useForm } from 'react-hook-form'
import { valibotResolver } from '@hookform/resolvers/valibot'
import { object, string, email, minLength, nonEmpty, pipe } from 'valibot'
import { Button, Card, CardContent, Grid } from '@mui/material'
import CustomTextField from '@/core/components/mui/TextField'

type FormData = { email: string; password: string }

const schema = object({
  email: pipe(string(), nonEmpty('Required'), email('Invalid email')),
  password: pipe(string(), nonEmpty('Required'), minLength(8, 'Min 8 chars'))
})

export function LoginForm() {
  const { control, handleSubmit, formState: { errors } } = useForm<FormData>({
    resolver: valibotResolver(schema),
    defaultValues: { email: '', password: '' }
  })

  const onSubmit = (data: FormData) => console.log(data)

  return (
    <Card>
      <CardContent>
        <form onSubmit={handleSubmit(onSubmit)}>
          <Grid container spacing={6}>
            <Grid size={{ xs: 12 }}>
              <Controller
                name="email"
                control={control}
                render={({ field }) => (
                  <CustomTextField
                    {...field}
                    fullWidth
                    label="Email"
                    error={!!errors.email}
                    helperText={errors.email?.message}
                  />
                )}
              />
            </Grid>
            <Grid size={{ xs: 12 }}>
              <Controller
                name="password"
                control={control}
                render={({ field }) => (
                  <CustomTextField
                    {...field}
                    fullWidth
                    type="password"
                    label="Password"
                    error={!!errors.password}
                    helperText={errors.password?.message}
                  />
                )}
              />
            </Grid>
            <Grid size={{ xs: 12 }}>
              <Button type="submit" fullWidth variant="contained">
                Login
              </Button>
            </Grid>
          </Grid>
        </form>
      </CardContent>
    </Card>
  )
}
```

### Valibot Schema Patterns

```typescript
// Password match validation
const registrationSchema = pipe(
  object({
    password: pipe(string(), nonEmpty(), minLength(8)),
    confirmPassword: pipe(string(), nonEmpty())
  }),
  forward(
    check(
      input => input.password === input.confirmPassword,
      'Passwords must match'
    ),
    ['confirmPassword']
  )
)

// Multi-field with custom message
const schema = object({
  username: pipe(
    string(),
    nonEmpty('Username required'),
    minLength(3, 'Min 3 characters'),
    maxLength(20, 'Max 20 characters')
  ),
  country: pipe(string(), nonEmpty('Select country')),
  languages: pipe(array(string()), minLength(1, 'Pick at least 1 language'))
})
```

### Wizard Boilerplate

```typescript
'use client'
import { useState } from 'react'
import { useForm } from 'react-hook-form'
import { valibotResolver } from '@hookform/resolvers/valibot'
import { Stepper, Step, StepLabel, Button, Card, CardContent, Grid } from '@mui/material'
import { object, string, nonEmpty, pipe } from 'valibot'

const steps = ['Account', 'Personal', 'Review']

const schema1 = object({
  username: pipe(string(), nonEmpty('Required'))
})

const schema2 = object({
  firstName: pipe(string(), nonEmpty('Required'))
})

export function Wizard() {
  const [activeStep, setActiveStep] = useState(0)
  
  const form1 = useForm({ resolver: valibotResolver(schema1) })
  const form2 = useForm({ resolver: valibotResolver(schema2) })
  
  const currentForm = activeStep === 0 ? form1 : form2

  const handleNext = async () => {
    const valid = await currentForm.trigger()
    if (valid) setActiveStep(prev => prev + 1)
  }

  const handleBack = () => setActiveStep(prev => prev - 1)

  return (
    <Card>
      <CardContent>
        <Stepper activeStep={activeStep}>
          {steps.map(label => (
            <Step key={label}>
              <StepLabel>{label}</StepLabel>
            </Step>
          ))}
        </Stepper>

        <form>
          {activeStep === 0 && (
            <Grid container spacing={6}>
              {/* Step 1 Fields */}
            </Grid>
          )}
          {activeStep === 1 && (
            <Grid container spacing={6}>
              {/* Step 2 Fields */}
            </Grid>
          )}
          {activeStep === 2 && (
            <Grid container spacing={6}>
              {/* Review Step */}
            </Grid>
          )}
        </form>

        <Button onClick={handleBack} disabled={activeStep === 0}>Back</Button>
        <Button onClick={handleNext} disabled={activeStep === steps.length - 1}>
          {activeStep === steps.length - 1 ? 'Submit' : 'Next'}
        </Button>
      </CardContent>
    </Card>
  )
}
```

---

## Implementation Checklist

- [ ] Install: `react-hook-form @hookform/resolvers valibot`
- [ ] Import: `useForm, Controller, valibotResolver`
- [ ] Define schema with Valibot pipes
- [ ] Create form with resolver
- [ ] Wrap fields with Controller
- [ ] Map errors to fields
- [ ] Test validation
- [ ] Add success/error handling
- [ ] Style errors with helperText

---

## Key Dependencies

- `react-hook-form` - Form state management
- `@hookform/resolvers` - Valibot adapter
- `valibot` - Schema validation
- `@mui/material` - UI components
- `react-toastify` - Toast notifications (optional)

---

*Quick Ref Version: 1.0*
