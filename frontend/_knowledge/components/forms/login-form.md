# Login Form Component

Core form component for user authentication with email/password fields, validation, and NextAuth.js integration.

---

## Overview

**Component:** Login Form  
**Type:** Form Component (Client Component with 'use client')  
**Purpose:** Authenticate users with email and password credentials  
**Framework:** React Hook Form + Valibot + NextAuth.js  
**Status:** Production Implementation

---

## Props Interface

interface LoginFormProps {
  mode: SystemMode // 'light' or 'dark' for theme-aware rendering
}

### Prop Descriptions
- **mode:** System theme mode (light/dark) passed from server component for image/styling variations

---

## Form Fields

### Email Field
- **Label:** Email or Username
- **Type:** text
- **Input Type:** email
- **Placeholder:** "Enter your email or username"
- **Validation:**
  - Required: Must not be empty
  - Format: Must be valid email format
  - Error: "Email is invalid"
- **Valibot Schema:**
  - minLength(1, 'This field is required')
  - email('Email is invalid')

### Password Field
- **Label:** Password
- **Type:** password (toggleable to text)
- **Placeholder:** "••••••••••"
- **Validation:**
  - Required: Must not be empty
  - Min Length: At least 5 characters
  - Error: "Password must be at least 5 characters"
- **Features:**
  - Visibility toggle with eye icon
  - InputAdornment for icon placement
  - Dynamic type based on visibility state
- **Valibot Schema:**
  - minLength(1, 'This field is required')
  - minLength(5, 'Password must be at least 5 characters')

### Remember Me Checkbox
- **Label:** Remember me
- **Type:** Checkbox
- **Optional:** Yes
- **State:** isChecked (boolean)
- **Purpose:** Maintains user session preference

---

## Form Validation

### Validation Library
**Valibot** - Schema-based validation with pipe function

### Validation Rules

| Field | Rules | Error Messages |
|-------|-------|----------------|
| **Email** | Required, Valid email format | "This field is required", "Email is invalid" |
| **Password** | Required, Min 5 characters | "This field is required", "Password must be at least 5 characters" |

### Error Handling
- Field-level inline error display
- Error state managed by React Hook Form
- Controller wrapper for MUI components
- Valibot resolver for schema validation
- Helper text under fields shows validation errors

---

## Form Submission

### Submission Flow
1. User fills email and password fields
2. Optionally checks "Remember me"
3. Clicks Login button
4. React Hook Form validates schema
5. If valid, triggers authentication handler
6. If invalid, displays field-level errors

### Authentication Methods

#### Credentials Provider
- Submits email and password to NextAuth credentials provider
- Backend validates against database
- Returns success/error response

#### Google OAuth Provider
- Initiates Google OAuth flow
- Redirects to Google for authentication
- Returns to app after consent

### Error States

| Scenario | Error Display | Message |
|----------|---------------|---------|
| Invalid email format | Below email field | "Email is invalid" |
| Empty email | Below email field | "This field is required" |
| Empty password | Below password field | "This field is required" |
| Password less than 5 chars | Below password field | "Password must be at least 5 characters" |
| Auth failed | Alert component at top | Dynamic error from server |
| Google OAuth error | Alert component | "OAuth sign-in failed" |

---

## State Management

### Component State

- isPasswordShown: boolean - Password visibility toggle
- errorState: ErrorType or null - Stores validation/auth errors

### Form State (React Hook Form)
- useForm(): Manages form data, validation, submission
- watch(): Monitor field changes (optional)
- formState: Access errors, isDirty, isSubmitting
- handleSubmit(): Wraps form submission with validation

### Error Type
type ErrorType = {
  message: string[]
}

---

## Hooks and Libraries

### React Hooks
- useState(): Password visibility, error state
- useForm(): Form state management
- useRouter(): Navigation after authentication
- useSearchParams(): Get redirectTo param
- useParams(): Get locale for localization
- useTheme(): Theme access
- useMediaQuery(): Responsive checks

### Form Libraries
- react-hook-form: Form state and submission handling
- @hookform/resolvers/valibot: Valibot validation integration
- valibot: Schema validation

### Authentication
- next-auth/react: signIn function for credentials and OAuth

### UI Components
- @mui/material: TextField, Button, Checkbox, IconButton, etc.
- CustomTextField: Wrapper around MUI TextField
- InputAdornment: Icon container in password field

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

### Remember Me Checkbox
- MUI Checkbox component with FormControlLabel
- Non-functional in demo (UX only)
- Could integrate with NextAuth.js session config

### Test Credentials
- Email: admin@vuexy.com
- Password: admin
- Used for demo/testing purposes

### Error Display
- MUI Alert component for auth errors
- Inline helper text for field validation
- Error state cleared on successful submission

### Responsive Design
- Mobile: Single column, full-width
- Tablet: Optimized spacing
- Desktop: Standard form layout
- Max-width form area on split layout variant

---

## Navigation Elements

### Links
- **Forgot Password:** /forgot-password - Password recovery
- **Create Account:** /register - New user registration
- **Logo:** Home page - Navigate away

### Redirect Support
- Gets redirectTo param from URL search params
- Navigates to original requested page after login
- Falls back to home page if no redirect specified
- Preserves language locale in navigation

---

## Accessibility Features

### ARIA Attributes
- Form inputs have proper aria-label or label association
- Error messages linked to inputs via aria-describedby
- Icon buttons have aria-label for screen readers

### Keyboard Navigation
- Tab order: Email - Password - Remember Me - Login
- Enter submits form
- Space activates checkbox
- Icon button accessible via keyboard

### Semantic HTML
- Proper form element
- Input type="email" for email validation
- Input type="password" for password input
- Semantic button element

---

## Security Features

- Password field type prevents visibility
- Eye icon toggle only visual, doesn't change actual security
- CSRF protection via NextAuth.js framework
- Form submission over HTTPS only
- No sensitive data logged
- Session tokens stored securely in HTTP-only cookies

---

## Dependencies

### NPM Packages
- react-hook-form - Form management
- @hookform/resolvers - Validation resolver
- valibot - Schema validation
- next-auth - Authentication
- @mui/material - UI components
- classnames - Conditional classes

### Next.js APIs
- next/navigation - useRouter, useSearchParams, useParams
- next/link - Localized navigation

### Custom Utilities
- useImageVariant() - Image mode selection
- useSettings() - Get application settings
- getLocalizedUrl() - URL localization

---

## Performance Considerations

- React Hook Form: Efficient form state management
- Valibot: Lightweight schema validation
- Debounced validation on blur
- Controller prevents unnecessary re-renders
- Memoized callbacks for password toggle
- Lazy loading of auth providers

---

## Testing Considerations

### Unit Tests
- Schema validation tests
- Error state management
- Password toggle functionality

### Integration Tests
- Form submission with valid credentials
- Form submission with invalid credentials
- Redirect on successful authentication
- Error display on failed authentication

### E2E Tests
- Complete login flow
- OAuth sign-in flow
- Error recovery flow
- Redirect after login
