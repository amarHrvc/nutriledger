# Forgot Password Form Component

Email-based password recovery form component with simple validation and recovery flow support.

---

## Overview

**Component:** Forgot Password Form  
**Type:** Form Component (Client Component with 'use client')  
**Purpose:** Initiate password reset flow by requesting recovery email  
**Framework:** React (useState, form handling)  
**Status:** Production/Demo Implementation

---

## Props Interface

interface ForgotPasswordFormProps {
  mode?: SystemMode // Optional theme mode for responsive images
}

### Prop Descriptions
- **mode:** Optional system theme mode for image/styling variations

---

## Form Fields

### Email Field
- **Label:** Email
- **Type:** email
- **Placeholder:** "Enter your email"
- **Validation:**
  - Required: Must not be empty
  - Format: Valid email format (recommended)
- **Features:**
  - Auto-focus on component mount
  - Email type input for browser validation
  - Standard text input

### Characteristics
- Single field form for simplicity
- Minimal required information
- Quick submission flow

---

## Form Validation

### Validation Approach
- Client-side validation for user feedback
- Email format checking (browser native + custom)
- Simple validation flow (one field)

### Validation Rules

| Field | Rules | Error Messages |
|-------|-------|----------------|
| **Email** | Required | Implicit (empty field) |

### Error Handling
- Simple inline validation
- Helper text below field
- No complex error states
- Visual feedback on empty submission

---

## Form Submission

### Submission Flow
1. User enters email address
2. Clicks Send Reset Link button
3. Form validates email is not empty
4. If valid, submits to backend password recovery endpoint
5. Backend sends password reset email
6. Shows success message to user
7. User receives reset link in email

### Submit Button
- **Label:** Send Reset Link
- **Type:** Submit button
- **Features:**
  - Full width button
  - Contained variant (primary color)
  - Submit type triggers form validation

### Success Flow
- Display confirmation message
- "Check your email for reset instructions"
- Option to return to login
- Email confirmation status

---

## State Management

### Component State
- formData: Object - Stores email field value
- isSubmitting: boolean - Shows loading state during submission
- successMessage: string or null - Confirmation message

### State Updates
- Email input onChange handler
- Submit handler sets loading state
- Success state triggers message display

---

## Hooks and Libraries

### React Hooks
- useState(): Form state, loading, success messages
- useParams(): Get locale for localization
- useRouter(): Navigation after reset
- useTheme(): Theme access
- useMediaQuery(): Responsive checks

### UI Components
- @mui/material: TextField, Button, Typography, etc.
- CustomTextField: Wrapper around MUI TextField
- Card (V1 variant): Container component
- Typography: Text and headings

### Custom Components
- AuthIllustrationWrapper: Decorative wrapper (V1 variant)
- Logo: Application logo
- DirectionalIcon: RTL-aware back navigation icon

### Custom Hooks
- useImageVariant(): Mode-aware image selection
- useSettings(): Get skin setting (bordered/default)

---

## Features

### Simple Single-Field Form
- Minimal complexity
- Fast user interaction
- Quick submission
- Easy validation

### Back to Login Navigation
- Back link with directional icon
- RTL support via DirectionalIcon component
- Text: "Back to login"
- Link styling with primary color
- Navigates to /login page

### Email Confirmation
- Display masked email in response
- Show "Check your email" message
- Resend option after timeout
- Confirmation of email delivery

### Responsive Design
- Mobile: Single column, full-width
- Tablet: Optimized spacing
- Desktop: Standard form layout
- Max-width 450px for V1 card variant

---

## Navigation Elements

### Links
- **Back to Login:** /login - Return to sign in
- **Logo:** Home page - Navigate away
- **Resend Link:** Resend recovery email option

### Navigation Support
- Localization-aware URLs
- getLocalizedUrl() for multi-language support
- RTL-aware directional icon

---

## Accessibility Features

### ARIA Attributes
- Form input has proper label
- Error states clearly marked
- Back link with descriptive text
- DirectionalIcon with aria-hidden

### Keyboard Navigation
- Tab to email input
- Tab to Send Reset Link button
- Tab to Back to Login link
- Enter submits form

### Semantic HTML
- Proper form element
- Input type="email" for email field
- Semantic button elements
- Link components for navigation

---

## Variants

### ForgotPasswordV1
- Card-based centered layout (450px max-width)
- AuthIllustrationWrapper for decoration
- Mobile: Full-width, no illustration
- Simple email form

### ForgotPasswordV2
- Split layout (illustration left, form right)
- Responsive images (light/dark)
- Illustration hidden on tablet/mobile
- Full-width form on mobile
- Theme-aware background masks

---

## Component Dependencies

### Shared Components
- Logo - Application logo
- CustomTextField - MUI TextField wrapper
- AuthIllustrationWrapper - Decorative wrapper (V1)
- DirectionalIcon - RTL-aware back arrow

### MUI Components
- Card, CardContent (V1 variant)
- Typography - Headings and descriptions
- Button - Form submission
- TextField (via CustomTextField)
- Alert - Success/error messages

---

## Styling

### Layout
- Tailwind CSS for overall layout
- MUI components for form controls
- CustomTextField wrapper for consistent styling
- Responsive padding and margins

### Theme Support
- Dark/light mode via useTheme()
- useImageVariant for theme-aware images
- useSettings for skin variants
- Primary colors from MUI theme

### Component Styling
- TextField: fullWidth, outlined variant
- Button: fullWidth, contained variant
- Typography: Heading variants for hierarchy
- Icons: Tabler icon set

---

## API Integration

### Backend Endpoint
- POST /api/auth/forgot-password
- Request body: { email: string }
- Response: { success: boolean, message: string }

### Email Delivery
- Backend sends password reset email
- Email contains secure reset link
- Link valid for 24-48 hours
- One-time use reset token

### Error Handling
- Email not found validation
- Server error handling
- Retry capability
- User-friendly error messages

---

## Security Considerations

- Email field type for validation
- CSRF protection via framework
- Form submission over HTTPS only
- No sensitive data in URLs
- Reset tokens time-limited
- One-time use links
- Rate limiting on email sending (recommended)

---

## Performance Considerations

- Lightweight component with minimal state
- Single form field for quick submission
- No complex validation library
- Image lazy loading
- Responsive images with srcset
- Memoized styled components

---

## Dependencies

### NPM Packages
- @mui/material - UI components
- classnames - Conditional CSS classes

### Next.js APIs
- next/navigation - useParams, useRouter
- next/link - Navigation

### Custom Utilities
- useImageVariant() - Image mode selection
- useSettings() - Get application settings
- getLocalizedUrl() - URL localization

---

## Error States

### Validation Errors
- Empty email: Prevents form submission
- Invalid email format: Shows error (optional)
- Server error: Displays error message
- Email not found: Shows friendly message

---

## Success States

### Submission Success
- Display confirmation message
- Show masked email address
- Resend link option
- Back to login option
- Auto-dismiss or clear form

---

## Testing Considerations

### Unit Tests
- Email validation
- Form submission handling
- Loading state management
- Success message display

### Integration Tests
- Form submission with valid email
- Form submission with invalid email
- Backend API error handling
- Redirect/navigation after submission

### E2E Tests
- Complete password reset flow
- Email delivery simulation
- Reset link validation
- Password change confirmation

---

## Future Enhancements

- Email existence verification
- Rate limiting display
- Resend cooldown timer
- SMS recovery option
- Security questions fallback
- Account unlock option
