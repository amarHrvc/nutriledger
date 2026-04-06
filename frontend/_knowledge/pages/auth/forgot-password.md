# Real Auth Page: Forgot Password

## Overview
Production password reset page with email-based recovery flow and responsive layout.

**Location:** src/app/[lang]/(blank-layout-pages)/(guest-only)/forgot-password/page.tsx  
**View Component:** src/views/ForgotPassword.tsx  
**Type:** Server Component (async) - Client Component (ForgotPassword view)  
**Props:** { mode: SystemMode }

---

## Page Component Structure

### Route and Server Component
- **Server Component:** orgot-password/page.tsx - Async route handler
- **Props Passed:** { mode: SystemMode } - System theme mode
- **Metadata:** Exported metadata for SEO
- **Layout:** Blank layout for guest-only routes (no header/sidebar)

### View Component Integration
Server component wraps the ForgotPassword view and passes mode prop.

---

## Form Fields and Validation

### Input Fields

| Field | Type | Validation | Features |
|-------|------|-----------|----------|
| **Email** | Text Input | Required, Valid email | Standard email validation |

### Validation Rules
- Email field is required
- Must be valid email format
- Case-insensitive email handling
- No special validation beyond standard email rules

### Form Type
- Single-field email recovery form
- Simple and streamlined user experience
- Minimal friction for password recovery

---

## Password Reset Flow

### Recovery Process
1. User enters email address
2. Form validates email format
3. Submits to backend password reset endpoint
4. Backend sends reset email to user
5. User receives email with reset link
6. Clicking link navigates to reset password page
7. User enters new password and confirms
8. Password updated in system

### Email Contents
- Reset link with secure token
- Link valid for 24-48 hours
- Instructions for resetting password
- Account recovery and security information

### Success Messages
- Display confirmation message: "Check your email"
- Brief explanation: "We sent a password reset link to your email"
- Option to resend email if not received

---

## UI Components and Layout

### MUI Components Used
- TextField (custom CustomTextField)
- Button (primary variant)
- Typography (headings and descriptions)
- Divider (visual separation)

### Layout Structure
- **Desktop (lg+):** Split view layout
  - Left side: Illustration with responsive sizing
  - Right side: Password reset form card
  - Max-width: 450px for form area
  
- **Tablet (md):** Hidden illustration, full-width form
- **Mobile:** Full-width form with no illustration

### Responsive Images
- **Illustration Images:**
  - Light mode: /images/illustrations/auth/v2-forgot-password-light.png
  - Dark mode: /images/illustrations/auth/v2-forgot-password-dark.png
  
- **Background Mask:**
  - Light: /images/pages/auth-mask-light.png
  - Dark: /images/pages/auth-mask-dark.png

### Styling
- Tailwind CSS for layout (flex, min-bs-[100dvh], spacing)
- MUI styled() for component customization
- Theme-aware colors and breakpoints
- Dark/light mode support via useImageVariant hook

---

## State Management

### Component State
- formState: Tracks form submission and success state
- loading: Indicates email sending in progress
- submitted: Shows success message after submission

### Form State
- Email input value
- Validation error state
- Submission status

---

## Hooks and Dependencies

### React Hooks
- useState() - Form state and loading indicators
- useRouter() - Navigation after submission
- useParams() - Get locale for localization
- useTheme() - Theme access
- useMediaQuery() - Responsive checks

### Custom Hooks
- useImageVariant() - Mode-aware image selection
- useSettings() - Get skin setting (bordered/default)

### Third-party Libraries
- @mui/material - UI components
- classnames - Conditional CSS classes
- next/link - Navigation

---

## Navigation and Links

### Available Links
| Link | Destination | Purpose |
|------|-------------|---------|
| **Logo** | Home | Navigate back |
| **Back to Login** | /login | Return to login page |
| **Don't have account?** | /register | Registration page |

### Back Navigation
- Directional icon (arrow) with RTL support
- Text link "Back to Login"
- Returns to login page
- Preserves locale in navigation

---

## Form Submission

### Submission Flow
1. User enters email address
2. Client validates email format
3. Submit button triggers API call
4. Show loading state during submission
5. Receive response from backend
6. Display success message
7. Option to return to login

### API Integration
- Endpoint: POST /api/auth/forgot-password
- Request body: { email: string }
- Response: { success: boolean, message: string }
- Error handling: Display error message if email not found

### Success Handling
- Confirmation message displayed
- Email sent notification
- Auto-dismiss after delay (optional)
- Option to return to login page

### Error Handling
- Invalid email format feedback
- Email not found in system
- Server error messages displayed
- Retry option if submission fails

---

## Accessibility Features

### ARIA Labels
- Email input with proper label
- Error messages linked to input field
- Button has descriptive text
- Form instructions clearly stated

### Keyboard Navigation
- Tab to email input
- Tab to submit button
- Enter submits form
- Tab to back link for navigation

### Semantic HTML
- Proper form element structure
- Input type: email
- Semantic button element
- Clear heading hierarchy

---

## Key Features Summary

✓ Email-based password recovery  
✓ Simple single-field form  
✓ Email validation (format checking)  
✓ Loading state during submission  
✓ Success confirmation message  
✓ Error handling with user feedback  
✓ Responsive Design (Split layout on desktop)  
✓ Dark/Light Mode Support  
✓ Localization Support  
✓ Back to Login Navigation  
✓ Accessibility Compliant  
✓ RTL Support

---

## Performance Considerations

- Server Component for initial rendering
- Client Component hydration for interactivity
- Image lazy loading with Next Image
- Responsive images with srcset
- Minimal JavaScript (form submission only)
- Memoized styled components
- Debounced form validation

---

## Security Considerations

- Email field with type="email"
- CSRF protection via framework
- Form submission over HTTPS only
- No sensitive data in URLs
- Backend validates email before sending reset link
- Reset tokens time-limited (24-48 hours)
- One-time use reset tokens

---

## Dependencies

### Core Dependencies
- @mui/material - Component library
- next/link - Navigation

### Next.js Utilities
- next/navigation - Client-side navigation
- next/image - Optimized images

### Custom Utilities
- useImageVariant() - Image mode selection
- getLocalizedUrl() - URL localization
- DirectionalIcon - RTL-aware icon component

---

## Related Pages

- Login: /login - Sign in page
- Register: /register - New account registration
- Reset Password: /reset-password - Confirm new password
- Two-Factor Auth: /two-factor - Account security

---

## Email Template Integration

### Password Reset Email
- Contains secure reset link
- Includes account username/email
- Expiration time stated
- Instructions for resetting
- Support contact information
- Company branding and logo
