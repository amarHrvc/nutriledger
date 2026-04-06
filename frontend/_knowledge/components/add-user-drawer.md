# AddUserDrawer Component Snapshot

**Component:** AddUserDrawer  
**Location:** `src/views/apps/user/list/AddUserDrawer.tsx`  
**Epic:** kb-joc.2.3  
**Task:** T018 - Write add-user and edit-user drawers snapshot  
**Status:** Complete

## Component Overview

**Type:** Stateful React Component  
**Purpose:** Modal drawer for creating new user records with form validation

**Architecture Pattern:** Form-in-Drawer with React Hook Form

## Implementation

```typescript
// React Imports
import { useState } from 'react'

// MUI Imports
import Button from '@mui/material/Button'
import Drawer from '@mui/material/Drawer'
import IconButton from '@mui/material/IconButton'
import MenuItem from '@mui/material/MenuItem'
import Typography from '@mui/material/Typography'
import Divider from '@mui/material/Divider'

// Third-party Imports
import { useForm, Controller } from 'react-hook-form'

// Types Imports
import type { UsersType } from '@/types/apps/userTypes'

// Component Imports
import CustomTextField from '@core/components/mui/TextField'

type Props = {
  open: boolean
  handleClose: () => void
  userData?: UsersType[]
  setData: (data: UsersType[]) => void
}

type FormValidateType = {
  fullName: string
  username: string
  email: string
  role: string
  plan: string
  status: string
}

type FormNonValidateType = {
  company: string
  country: string
  contact: string
}

const initialData = {
  company: '',
  country: '',
  contact: ''
}

const AddUserDrawer = (props: Props) => {
  const { open, handleClose, userData, setData } = props

  const [formData, setFormData] = useState<FormNonValidateType>(initialData)

  const {
    control,
    reset: resetForm,
    handleSubmit,
    formState: { errors }
  } = useForm<FormValidateType>({
    defaultValues: {
      fullName: '',
      username: '',
      email: '',
      role: '',
      plan: '',
      status: ''
    }
  })

  const onSubmit = (data: FormValidateType) => {
    const newUser: UsersType = {
      id: (userData?.length && userData?.length + 1) || 1,
      avatar: `/images/avatars/${Math.floor(Math.random() * 8) + 1}.png`,
      fullName: data.fullName,
      username: data.username,
      email: data.email,
      role: data.role,
      currentPlan: data.plan,
      status: data.status,
      company: formData.company,
      country: formData.country,
      contact: formData.contact,
      billing: userData?.[Math.floor(Math.random() * 50) + 1].billing ?? 'Auto Debit'
    }

    setData([...(userData ?? []), newUser])
    handleClose()
    setFormData(initialData)
    resetForm({ fullName: '', username: '', email: '', role: '', plan: '', status: '' })
  }

  const handleReset = () => {
    handleClose()
    setFormData(initialData)
  }

  return (
    <Drawer
      open={open}
      anchor='right'
      variant='temporary'
      onClose={handleReset}
      ModalProps={{ keepMounted: true }}
      sx={{ '& .MuiDrawer-paper': { width: { xs: 300, sm: 400 } } }}
    >
      <div className='flex items-center justify-between plb-5 pli-6'>
        <Typography variant='h5'>Add New User</Typography>
        <IconButton size='small' onClick={handleReset}>
          <i className='tabler-x text-2xl text-textPrimary' />
        </IconButton>
      </div>
      <Divider />
      <div>
        <form onSubmit={handleSubmit(data => onSubmit(data))} className='flex flex-col gap-6 p-6'>
          <Controller
            name='fullName'
            control={control}
            rules={{ required: true }}
            render={({ field }) => (
              <CustomTextField
                {...field}
                fullWidth
                label='Full Name'
                placeholder='John Doe'
                {...(errors.fullName && { error: true, helperText: 'This field is required.' })}
              />
            )}
          />
          <Controller
            name='username'
            control={control}
            rules={{ required: true }}
            render={({ field }) => (
              <CustomTextField
                {...field}
                fullWidth
                label='Username'
                placeholder='johndoe'
                {...(errors.username && { error: true, helperText: 'This field is required.' })}
              />
            )}
          />
          <Controller
            name='email'
            control={control}
            rules={{ required: true }}
            render={({ field }) => (
              <CustomTextField
                {...field}
                fullWidth
                type='email'
                label='Email'
                placeholder='johndoe@gmail.com'
                {...(errors.email && { error: true, helperText: 'This field is required.' })}
              />
            )}
          />
          <Controller
            name='role'
            control={control}
            rules={{ required: true }}
            render={({ field }) => (
              <CustomTextField
                select
                fullWidth
                id='select-role'
                label='Select Role'
                {...field}
                {...(errors.role && { error: true, helperText: 'This field is required.' })}
              >
                <MenuItem value='admin'>Admin</MenuItem>
                <MenuItem value='author'>Author</MenuItem>
                <MenuItem value='editor'>Editor</MenuItem>
                <MenuItem value='maintainer'>Maintainer</MenuItem>
                <MenuItem value='subscriber'>Subscriber</MenuItem>
              </CustomTextField>
            )}
          />
          <Controller
            name='plan'
            control={control}
            rules={{ required: true }}
            render={({ field }) => (
              <CustomTextField
                select
                fullWidth
                id='select-plan'
                label='Select Plan'
                {...field}
                slotProps={{
                  htmlInput: { placeholder: 'Select Plan' }
                }}
                {...(errors.plan && { error: true, helperText: 'This field is required.' })}
              >
                <MenuItem value='basic'>Basic</MenuItem>
                <MenuItem value='company'>Company</MenuItem>
                <MenuItem value='enterprise'>Enterprise</MenuItem>
                <MenuItem value='team'>Team</MenuItem>
              </CustomTextField>
            )}
          />
          <Controller
            name='status'
            control={control}
            rules={{ required: true }}
            render={({ field }) => (
              <CustomTextField
                select
                fullWidth
                id='select-status'
                label='Select Status'
                {...field}
                {...(errors.status && { error: true, helperText: 'This field is required.' })}
              >
                <MenuItem value='pending'>Pending</MenuItem>
                <MenuItem value='active'>Active</MenuItem>
                <MenuItem value='inactive'>Inactive</MenuItem>
              </CustomTextField>
            )}
          />
          <CustomTextField
            label='Company'
            fullWidth
            placeholder='Company PVT LTD'
            value={formData.company}
            onChange={e => setFormData({ ...formData, company: e.target.value })}
          />
          <CustomTextField
            select
            fullWidth
            id='country'
            value={formData.country}
            onChange={e => setFormData({ ...formData, country: e.target.value })}
            label='Select Country'
            slotProps={{
              htmlInput: { placeholder: 'Country' }
            }}
          >
            <MenuItem value='India'>India</MenuItem>
            <MenuItem value='USA'>USA</MenuItem>
            <MenuItem value='Australia'>Australia</MenuItem>
            <MenuItem value='Germany'>Germany</MenuItem>
          </CustomTextField>
          <CustomTextField
            label='Contact'
            type='number'
            fullWidth
            placeholder='(397) 294-5153'
            value={formData.contact}
            onChange={e => setFormData({ ...formData, contact: e.target.value })}
          />
          <div className='flex items-center gap-4'>
            <Button variant='contained' type='submit'>
              Submit
            </Button>
            <Button variant='tonal' color='error' type='reset' onClick={() => handleReset()}>
              Cancel
            </Button>
          </div>
        </form>
      </div>
    </Drawer>
  )
}

export default AddUserDrawer
```

## Props Interface

```typescript
type Props = {
  open: boolean              // Drawer visibility
  handleClose: () => void    // Callback to close drawer
  userData?: UsersType[]     // Current users array (optional)
  setData: (data: UsersType[]) => void  // Callback to update users
}
```

## Form Structure

### Two-Part Form Model

#### Part 1: Validated Fields
```typescript
type FormValidateType = {
  fullName: string   // Required
  username: string   // Required
  email: string      // Required
  role: string       // Required
  plan: string       // Required
  status: string     // Required
}
```

#### Part 2: Unvalidated Fields
```typescript
type FormNonValidateType = {
  company: string    // Optional
  country: string    // Optional
  contact: string    // Optional
}
```

## Form Fields

### Required Fields (React Hook Form Validated)

1. **Full Name**
   - Type: Text input
   - Placeholder: "John Doe"
   - Validation: Required
   - Error Message: "This field is required."

2. **Username**
   - Type: Text input
   - Placeholder: "johndoe"
   - Validation: Required
   - Error Message: "This field is required."

3. **Email**
   - Type: Email input
   - Placeholder: "johndoe@gmail.com"
   - Validation: Required (no format validation)
   - Error Message: "This field is required."

4. **Role** (Select)
   - Options: admin, author, editor, maintainer, subscriber
   - Validation: Required
   - Error Message: "This field is required."

5. **Plan** (Select)
   - Options: basic, company, enterprise, team
   - Validation: Required
   - Error Message: "This field is required."

6. **Status** (Select)
   - Options: pending, active, inactive
   - Validation: Required
   - Error Message: "This field is required."

### Optional Fields (Unvalidated)

1. **Company**
   - Type: Text input
   - Placeholder: "Company PVT LTD"
   - Validation: None
   - Managed via local state

2. **Country** (Select)
   - Options: India, USA, Australia, Germany
   - Validation: None
   - Managed via local state

3. **Contact**
   - Type: Number input
   - Placeholder: "(397) 294-5153"
   - Validation: None
   - Managed via local state

## UI Layout

### Drawer Configuration
```typescript
{
  anchor: 'right'                    // Slides in from right
  variant: 'temporary'               // Temporary overlay
  width: { xs: 300, sm: 400 }        // Responsive width
  keepMounted: true                  // Keeps DOM mounted
}
```

### Header Section
- Typography: "Add New User" (h5)
- Close button: X icon on right
- Divider below header

### Form Section
- Flex column layout with gap-6 (24px)
- Padding: 6 (24px)
- Input fields span full width

### Action Buttons
- **Submit:** Contained variant, triggers form validation
- **Cancel:** Tonal error variant, closes drawer

## Form Handling

### Validation Pattern

Uses **React Hook Form** with `Controller` wrapper:
- Only validates required fields
- No complex validation rules
- Shows error state and helper text for missing fields
- No email format validation

### Form Submission
```typescript
const onSubmit = (data: FormValidateType) => {
  // Create new user object
  const newUser: UsersType = {
    id: (userData?.length && userData?.length + 1) || 1,
    avatar: `/images/avatars/${Math.floor(Math.random() * 8) + 1}.png`,
    // ... mapped fields from data + formData
  }
  
  // Append to existing users
  setData([...(userData ?? []), newUser])
  
  // Close and reset
  handleClose()
  setFormData(initialData)
  resetForm()
}
```

### Field Mapping
```
Validated Fields (RHF) → Unvalidated Fields (Local State) → New User Object
├── fullName          ├── company
├── username          ├── country  
├── email             └── contact
├── role
├── plan
└── status
```

## State Management

### React Hook Form State
- **defaultValues:** All form fields empty strings
- **Control:** Manages field state
- **Errors:** Tracks validation errors
- **Reset:** Clears all validated fields

### Local State (formData)
```typescript
const [formData, setFormData] = useState<FormNonValidateType>(initialData)
```
- Managed separately from RHF
- Updated via onChange handlers
- Reset via setFormData(initialData)

## Data Creation Logic

### New User Object Structure
```typescript
{
  id: nextId,                                    // Auto-generated
  avatar: `/images/avatars/${random}.png`,       // Random 1-8
  fullName, username, email, role,              // From validated form
  currentPlan: plan,                            // Mapped from plan field
  status,                                       // From validated form
  company, country, contact,                    // From unvalidated form
  billing: randomBillingFromUserData            // From existing users
}
```

### Avatar Generation
```typescript
`/images/avatars/${Math.floor(Math.random() * 8) + 1}.png`
// Randomly selects between avatars 1-8
```

### ID Generation
```typescript
(userData?.length && userData?.length + 1) || 1
// If users exist: length + 1
// If no users: 1
```

### Billing Assignment
```typescript
userData?.[Math.floor(Math.random() * 50) + 1].billing ?? 'Auto Debit'
// Random from existing users (1-50)
// Fallback: 'Auto Debit'
```

## User Interaction Flow

### Opening the Drawer
```
UserListTable.setAddUserOpen(true)
  ↓
AddUserDrawer.open = true
  ↓
Drawer slides in from right
```

### Filling Form
1. User fills required fields
2. Optional fields can be left empty
3. Form validation runs on submit

### Submitting
1. Click Submit button
2. RHF validates required fields
3. If valid:
   - Create new user object
   - Append to userData array
   - Trigger setData callback
   - Close drawer
   - Reset form

### Canceling
1. Click Cancel button
2. OR click X button in header
3. Close drawer
4. Reset form
5. Discard any changes

## Error Handling

### Validation Errors
- **Trigger:** Submit with empty required field
- **Display:** Error state + red border + helper text
- **Text:** "This field is required."
- **No Removal:** Error persists until field is filled

### No Business Logic Validation
- No duplicate checking for email/username
- No format validation for email
- No numeric validation for contact
- No dependent field validation

## Integration with Parent

### Parent: UserListTable

**Props Passed from UserListTable:**
```typescript
<AddUserDrawer
  open={addUserOpen}                    // boolean state
  handleClose={() => setAddUserOpen(!addUserOpen)}
  userData={data}                       // current users
  setData={setData}                     // update users function
/>
```

**Callback Flow:**
```
AddUserDrawer.setData(newUsersArray)
  ↓
UserListTable.setData(newUsersArray)
  ↓
data state updated
  ↓
filteredData updated via useEffect
  ↓
Table re-renders with new user
```

## Styling Details

### Tailwind Classes
- `flex items-center justify-between` - Header layout
- `plb-5 pli-6` - Padding (block/inline)
- `flex flex-col gap-6` - Form layout
- `flex items-center gap-4` - Button group

### MUI Props
- `fullWidth` - Input fills container
- `size='small'` - Close button size
- `variant='contained'` - Solid button
- `variant='tonal'` - Ghost button

### CustomTextField Props
- `select` - Converts to dropdown
- `type='number'` - Number input
- `type='email'` - Email input
- `placeholder` - Hint text
- `helperText` - Error message display

## Accessibility Features

- **Native Inputs:** Uses standard form controls
- **Labels:** Each input has label
- **Error Messaging:** Helper text for validation errors
- **Button Focus:** Proper focus states
- **Keyboard:** Standard form keyboard navigation
- **Close Button:** Visible X button for accessibility

## Known Limitations

1. **No Email Validation:** Accepts any string in email field
2. **No Duplicate Prevention:** Can add users with same email/username
3. **No Delete Confirmation:** Add happens immediately on submit
4. **No Loading State:** No indication while data is being saved
5. **No Success Feedback:** Silent success, no toast/snackbar
6. **Auto Avatar:** Random avatar, no upload capability
7. **Limited Billing:** Only copies existing or uses default
8. **No Edit Mode:** Cannot edit existing users with this component

## Related Components

### Parent
- **UserListTable:** Opens/manages drawer state

### Alternatives
- **Edit User Drawer:** Similar component for editing (if exists)
- **Bulk Import:** For importing multiple users

## Summary

AddUserDrawer is a **modal form component** that:
- **Captures user data** via form inputs
- **Validates required fields** with React Hook Form
- **Creates new user objects** with auto-generated fields
- **Appends to existing data** via callback
- **Provides clean UX** with drawer animation
- **Handles reset** on close

**Best Practices:**
- ✅ Two-part form (validated + unvalidated)
- ✅ React Hook Form integration
- ✅ Clear error messaging
- ✅ Auto-generated data (avatar, ID)
- ✅ Proper state cleanup on close