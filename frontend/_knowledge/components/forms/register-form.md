# Register Form Component

User registration form component with multi-field validation, social signup options, and responsive design.

---

## Overview

**Component:** Register Form  
**Type:** Form Component (Client Component with 'use client')  
**Purpose:** Create new user accounts with email, username, and password  
**Framework:** React (useState, form handling)  
**Status:** Demo/Showcase Implementation

---

## Props Interface

interface RegisterFormProps {
  mode?: SystemMode // Optional theme mode for responsive images
}

### Prop Descriptions
- **mode:** Optional system theme mode (light/dark) for image/styling variations

---

## Form Fields

### Username Field
- **Label:** Username
- **Type:** text
- **Placeholder:** "Enter your username"
- **Validation:**
  - Required: Must not be empty
  - Optional: Could include alphanumeric check
- **Features:**
  - Auto-focus on component mount
  - Standard text input

### Email Field
- **Label:** Email
- **Type:** email
- **Placeholder:** "Enter your email"
- **Validation:**
  - Required: Must not be empty
  - Format: Valid email format (optional)
- **Features:**
  - Standard email input
  - Email type validation support

### Password Field
- **Label:** Password
- **Type:** password (toggleable to text)
- **Placeholder:** "••••••••••"
- **Validation:**
  - Required: Must not be empty
  - Min Length: At least 5 characters (recommended)
- **Features:**
  - Visibility toggle with eye icon
  - InputAdornment for icon placement
  - Dynamic type based on visibility state

### Terms and Privacy Checkbox
- **Label:** "I agree to privacy policy and terms"
- **Type:** Checkbox
- **Required:** Yes (for account creation)
- **Link:** Links to privacy policy and terms page
- **Features:**
  - Must be checked to submit
  - Link with preventDefault for demo
  - FormControlLabel wrapper

---

## Form Validation

### Validation Approach
- Client-side validation for user feedback
- Field-level validation checks
- Optional backend validation on submit

### Validation Rules

| Field | Rules | Error Messages |
|-------|-------|----------------|
| **Username** | Required | Implicit (empty field) |
| **Email** | Required | Implicit (empty field) |
| **Password** | Required, Min 5 chars | Implicit (empty field) |
| **Terms** | Must be checked | Required for submission |

### Error Handling
- Form submission prevention if validation fails
- Helper text below fields for user guidance
- Check validation on checkbox
- No explicit error alert in demo version

---

## Form Submission

### Submission Flow
1. User fills all form fields
2. User checks Terms and Privacy checkbox
3. Clicks Create Account/Sign Up button
4. Client validates required fields
5. If valid, submits to backend
6. If invalid, prevents submission

### Submit Button
- **Label:** Create an account or Sign Up
- **Type:** Submit button
- **Features:**
  - Full width button
  - Contained variant (primary color)
  - Submit type triggers form validation

### Non-Functional Features (Demo)
- No actual account creation
- No backend API integration
- preventDefault on form submit
- Demo/showcase purpose only

---

## State Management

### Component State
- isPasswordShown: boolean - Controls password visibility toggle
- formData: Object - Stores form field values (optional)

### State Updates
- Toggle password visibility: setIsPasswordShown
- Update form values: setFormData or onChange handlers

---

## Hooks and Libraries

### React Hooks
- useState(): Password visibility state
- useParams(): Get locale for localization
- useRouter(): Navigation (if used for redirect)
- useTheme(): Theme access
- useMediaQuery(): Responsive checks

### UI Components
- @mui/material: TextField, Button, Checkbox, IconButton, etc.
- CustomTextField: Wrapper around MUI TextField
- InputAdornment: Icon container in password field
- Card (V1 variant): Container component
- Typography: Text and headings

### Custom Components
- AuthIllustrationWrapper: Decorative wrapper (V1 variant)
- Logo: Application logo
- DirectionalIcon: RTL-aware icons

### Custom Hooks
- useImageVariant(): Mode-aware image selection
- useSettings(): Get skin setting (bordered/default)

---

## Features

### Password Visibility Toggle
- Icon button in password endAdornment
- Eye icon (tabler-eye) when password hidden
- Eye-off icon (tabler-eye-off) when password visible
- Click toggles between password and text input types
- onMouseDown preventDefault prevents input blur

### Social Sign-Up Options
- Facebook button with icon
- Twitter button with icon
- GitHub button with icon
- Google button with icon
- Outlined button variant
- Non-functional in demo
- Could integrate with OAuth providers

### Terms and Privacy Acceptance
- Link to privacy policy
- Link to terms and conditions
- Must accept to enable account creation
- Styled with primary color

### Responsive Design
- Mobile: Single column, full-width
- Tablet: Optimized spacing
- Desktop: Standard form layout
- Max-width 450px for V1 card variant
- Full-width split layout for V2 variant

---

## Navigation Elements

### Links
- **Privacy Policy & Terms:** Navigates to terms page
- **Already have account?:** /login - Sign in page
- **Logo:** Home page - Navigate away

### Navigation Support
- Localization-aware URLs
- getLocalizedUrl() for multi-language support
- Proper href attributes for links

---

## Accessibility Features

### ARIA Attributes
- Form inputs have proper labels
- Checkboxes associated with labels
- Icon buttons have aria-label
- Error states clearly marked

### Keyboard Navigation
- Tab through form fields: Username - Email - Password - Terms - Button
- Enter submits form
- Space activates checkbox
- Icon button accessible via keyboard
- Social buttons keyboard accessible

### Semantic HTML
- Proper form element
- Input type="email" for email field
- Input type="password" for password field
- Semantic button elements
- Fieldset grouping (optional)

---

## Variants

### RegisterV1
- Card-based centered layout (450px max-width)
- AuthIllustrationWrapper for decoration
- Mobile: Full-width, no illustration
- Social buttons included

### RegisterV2
- Split layout (illustration left, form right)
- Responsive images (light/dark/bordered)
- Illustration hidden on tablet/mobile
- Full-width form on mobile
- Social buttons included

### RegisterMultiSteps
- 3-step stepper form
- Step 1: Account Details (username, email, password)
- Step 2: Personal Info (name, country, language)
- Step 3: Billing/Social Links
- Progress tracking
- Next/Previous navigation
- Conditional step rendering

---

## Component Dependencies

### Shared Components
- Logo - Application logo (top of form)
- CustomTextField - MUI TextField wrapper
- AuthIllustrationWrapper - Decorative wrapper (V1)

### MUI Components
- Card, CardContent (V1 variant)
- Typography - Headings and text
- Button - Form submission and social buttons
- Checkbox, FormControlLabel - Terms acceptance
- TextField (via CustomTextField)
- IconButton - Password visibility toggle
- InputAdornment - Icon positioning
- Divider - Visual separation

### Icons
- Tabler icons: tabler-eye, tabler-eye-off, social icons
- Icon buttons for password toggle

---

## Styling

### Layout
- Tailwind CSS for overall layout (flex, gap, spacing)
- MUI components for form controls
- CustomTextField wrapper for consistent styling
- Responsive padding and margins

### Theme Support
- Dark/light mode via useTheme()
- useImageVariant for theme-aware images
- useSettings for skin variants
- Primary colors from MUI theme

### Component Styling
- TextField: fullWidth, outlined variant (default)
- Button: fullWidth, contained variant, primary color
- Social buttons: outlined variant
- Checkbox: size medium (default)
- Icons: Tabler icon set via i.className

---

## Security Considerations

- Password field type prevents visibility in input
- Eye icon toggle only visual, doesn't change security
- Terms acceptance required for account creation
- Password minimum length requirement (5+ characters)
- Form submission validation
- No sensitive data in URLs
- CSRF protection via framework

---

## Performance Considerations

- Lightweight component with minimal state
- No complex form validation library (demo version)
- Image lazy loading with Next Image
- Responsive images with srcset
- Memoized styled components
- Efficient password toggle implementation
- No unnecessary re-renders

---

## Dependencies

### NPM Packages
- @mui/material - UI components
- classnames - Conditional CSS classes
- react-hook-form - Form management (if used)

### Next.js APIs
- next/navigation - useParams, useRouter
- next/link - Localized navigation

### Custom Utilities
- useImageVariant() - Image mode selection
- useSettings() - Get application settings
- getLocalizedUrl() - URL localization

---

## Error States

### Validation Errors
- Empty username: Prevents form submission
- Empty email: Prevents form submission
- Empty password: Prevents form submission
- Terms not checked: Prevents form submission

### Display Behavior
- Inline helper text for guidance
- Field highlighting on error (optional)
- Button disabled if validation fails (optional)

---

## Testing Considerations

### Unit Tests
- Password visibility toggle
- Form field updates
- Social button clicks
- Terms checkbox validation

### Integration Tests
- Form submission with valid data
- Form submission with missing fields
- Social sign-up button clicks
- Navigation to login page

### E2E Tests
- Complete registration flow
- Social sign-up flows
- Password visibility toggle
- Terms acceptance requirement
- Navigation after registration (if implemented)

---

## Future Enhancements

- Email verification flow
- Password strength meter
- Username availability check
- Multi-step registration (as shown in RegisterMultiSteps)
- Backend API integration
- Real OAuth provider integration
- Email confirmation requirement
- CAPTCHA integration
