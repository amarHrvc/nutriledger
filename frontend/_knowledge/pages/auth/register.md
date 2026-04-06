# Real Auth Page: Register

## Overview
Production registration page with form validation, social signup options, and responsive layout.

**Location:** src/app/[lang]/(blank-layout-pages)/(guest-only)/register/page.tsx  
**View Component:** src/views/Register.tsx  
**Type:** Server Component (async) - Client Component (Register view)  
**Props:** { mode: SystemMode }

---

## Page Component Structure

### Route and Server Component
- **Server Component:** egister/page.tsx - Async route handler
- **Props Passed:** { mode: SystemMode } - System theme mode
- **Metadata:** Exported metadata for SEO
- **Layout:** Blank layout for guest-only routes (no header/sidebar)

### View Component Integration
Server component wraps the Register view and passes mode prop.

---

## Form Fields and Validation

### Input Fields

| Field | Type | Validation | Features |
|-------|------|-----------|----------|
| **Username** | Text Input | Required | alphanumeric characters |
| **Email** | Text Input | Required, Valid format | email validation |
| **Password** | Password Input | Required, Min 5 chars | Visibility toggle icon |
| **Terms Acceptance** | Checkbox | Required | Links to policy/terms |

### Default Validation Rules
- Username: Required field
- Email: Required, must be valid email format
- Password: Required, minimum 5 characters
- Terms & Privacy: Must be checked to proceed

### Form Type
- Multi-step registration (Account info → Confirmation)
- Form state managed by React Hook Form
- Inline validation feedback

---

## Social Sign-Up Options

### Supported Providers
| Provider | Icon | Feature |
|----------|------|---------|
| **Facebook** | FB Icon | Social sign-up |
| **Twitter** | X Icon | Social sign-up |
| **GitHub** | GitHub Icon | Social sign-up |
| **Google** | Google Icon | OAuth integration |

### Integration Details
- Display as outlined buttons with provider icons
- Redirect to provider OAuth flow
- Return to register page on cancellation
- Create account on successful OAuth

---

## UI Components and Layout

### MUI Components Used
- TextField (custom CustomTextField)
- IconButton (password visibility toggle)
- Checkbox + FormControlLabel
- Button (primary, outlined variants)
- Divider
- Typography
- InputAdornment

### Layout Structure
- **Desktop (lg+):** Split view layout
  - Left side: Illustration with responsive sizing
  - Right side: Registration form card
  - Max-width: 450px for form area
  
- **Tablet (md):** Hidden illustration, full-width form
- **Mobile:** Full-width form with no illustration

### Responsive Images
- **Illustration Images:**
  - Light mode: /images/illustrations/auth/v2-register-light.png
  - Dark mode: /images/illustrations/auth/v2-register-dark.png
  - Bordered variant available
  
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
- isPasswordShown: boolean - Controls password visibility toggle
- formState: Tracks validation errors and touched fields

### Form State (React Hook Form)
- Managed via useForm() hook
- Form validation schema (Valibot or standard validation)
- Real-time validation feedback
- Controller wrapper for MUI components

---

## Hooks and Dependencies

### React Hooks
- useState() - Password visibility state
- useForm() - Form state management (react-hook-form)
- useRouter() - Navigation after registration
- useParams() - Get locale
- useTheme() - Theme access
- useMediaQuery() - Responsive checks

### Custom Hooks
- useImageVariant() - Mode-aware image selection
- useSettings() - Get skin setting (bordered/default)

### Third-party Libraries
- react-hook-form - Form management
- @hookform/resolvers - Validation resolver
- @mui/material - UI components
- classnames - Conditional CSS classes

---

## Navigation Links

### Available Links
| Link | Destination | Purpose |
|------|-------------|---------|
| **Logo** | Home | Navigate back |
| **Terms and Conditions** | /terms | Privacy and terms |
| **Already have account?** | /login | Sign in page |

---

## Form Submission

### Submission Flow
1. Validate all form fields (client-side)
2. Check terms acceptance checkbox
3. Submit to registration endpoint
4. Receive confirmation (email/account created)
5. Redirect to login or confirmation page

### Success Handling
- Display success message
- Redirect to login page
- Option to auto-login if immediate confirmation

### Error Handling
- Email already exists validation
- Password requirements feedback
- Username availability check
- Display field-level error messages

---

## Accessibility Features

### ARIA Labels
- Form inputs have proper labels and descriptions
- Password visibility toggle has accessible name
- Error messages linked to form fields
- Social provider buttons labeled

### Keyboard Navigation
- Tab through form fields in logical order
- Enter submits form
- Space activates checkboxes
- Icon buttons keyboard accessible
- Social buttons accessible via keyboard

### Semantic HTML
- Proper form element structure
- Input types: text, email, password
- Semantic button elements
- Fieldset grouping for related inputs

---

## Key Features Summary

✓ Username, Email, Password registration  
✓ Form Validation with inline feedback  
✓ Password Visibility Toggle  
✓ Terms and Privacy Policy checkbox  
✓ Social Sign-Up (Facebook, Twitter, GitHub, Google)  
✓ Responsive Design (Split layout on desktop)  
✓ Dark/Light Mode Support  
✓ Error Handling and Display  
✓ Localization Support  
✓ Accessibility Compliant  
✓ Multi-language form labels

---

## Performance Considerations

- Server Component for initial rendering
- Client Component hydration for interactivity
- Image lazy loading with Next Image
- Responsive images with srcset
- Form validation debouncing
- Code splitting for social auth providers
- Memoized styled components

---

## Security Considerations

- Password field with type="password"
- CSRF protection via framework
- Form submission over HTTPS only
- Input sanitization before submission
- No sensitive data in URL params

---

## Dependencies

### Core Dependencies
- react-hook-form - Form management
- @hookform/resolvers - Validation resolver
- @mui/material - Component library

### Next.js Utilities
- next/link - Localized routing
- next/navigation - Client-side navigation
- next/image - Optimized images

### Custom Utilities
- useImageVariant() - Image mode selection
- getLocalizedUrl() - URL localization

---

## Related Pages

- Login: /login - Existing account sign-in
- Password Reset: /forgot-password - Forgot password flow
- Terms: /terms - Terms and conditions
