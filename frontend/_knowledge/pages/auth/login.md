# Real Auth Page: Login

## Overview
Production login page with real authentication, form validation, and NextAuth.js integration.

**Location:** src/app/[lang]/(blank-layout-pages)/(guest-only)/login/page.tsx  
**View Component:** src/views/Login.tsx  
**Type:** Server Component (async) - Client Component (Login view)  
**Props:** { mode: SystemMode }

---

## Page Component Structure

### Route and Server Component
- **Server Component:** login/page.tsx - Async route handler
- **Props Passed:** { mode: SystemMode } - System theme mode
- **Metadata:** Exported metadata for SEO
- **Layout:** Blank layout for guest-only routes (no header/sidebar)

### View Component Integration
Server component wraps the Login view and passes mode prop.

---

## Form Fields and Validation

### Input Fields

| Field | Type | Validation | Features |
|-------|------|-----------|----------|
| **Email** | Text Input | Required, Valid email format | Valibot validation |
| **Password** | Password Input | Required, Min 5 characters | Visibility toggle icon |
| **Remember Me** | Checkbox | Optional | State management |

### Validation Schema (Valibot)

- Email: Required, must be valid email format
- Password: Required, minimum 5 characters
- Schema validation using Valibot pipe and validators

### Default Test Credentials
- **Email:** admin@vuexy.com
- **Password:** admin

---

## Authentication Features

### NextAuth.js Integration
- **Providers:**
  - Credentials provider (email/password)
  - Google OAuth provider
  
- **Sign In Methods:**
  - Credentials: signIn('credentials', { email, password, redirect: true })
  - Google: signIn('google', { redirect: true })

### Error Handling
- Form validation errors displayed inline
- Authentication errors shown in Alert component
- Error type: { message: string[] }
- Supports multiple error messages per field

### Redirect Logic
- Retrieves redirectTo parameter from URL search params
- Navigates to dashboard after successful login
- Respects language locale in navigation

---

## UI Components and Layout

### MUI Components Used
- TextField (custom CustomTextField)
- IconButton (password visibility toggle)
- Checkbox + FormControlLabel
- Button (primary, outlined variants)
- Divider
- Alert (error display)
- Typography
- InputAdornment

### Layout Structure
- **Desktop (lg+):** Split view layout
  - Left side: Illustration with responsive sizing
  - Right side: Login form card with centered content
  - Max-width: 450px for form area
  
- **Tablet (md):** Hidden illustration, full-width form
- **Mobile:** Full-width form with no illustration

### Responsive Images
- **Illustration Images:**
  - Light mode: /images/illustrations/auth/v2-login-light.png
  - Dark mode: /images/illustrations/auth/v2-login-dark.png
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
- isPasswordShown: boolean - Controls password visibility
- errorState: ErrorType | null - Stores validation/auth errors

### Form State (React Hook Form)
- Managed via useForm() hook
- Valibot resolver for validation
- Real-time validation on blur
- Controller wrapper for MUI TextField

---

## Hooks and Dependencies

### React Hooks
- useState() - Password visibility, error state
- useForm() - Form state management (react-hook-form)
- useRouter() - Navigation after login
- useSearchParams() - Get redirectTo param
- useParams() - Get locale
- useTheme() - Theme access
- useMediaQuery() - Responsive checks

### Custom Hooks
- useImageVariant() - Mode-aware image selection
- useSettings() - Get skin setting (bordered/default)

### Third-party Libraries
- next-auth/react - Authentication
- react-hook-form - Form management
- @hookform/resolvers/valibot - Validation resolver
- valibot - Schema validation
- @mui/material - UI components
- classnames - Conditional CSS classes

---

## Navigation Links

### Available Links
| Link | Destination | Purpose |
|------|-------------|---------|
| **Logo** | Home | Navigate back |
| **Register** | /register | New account signup |
| **Forgot Password** | /forgot-password | Password recovery |

---

## Accessibility Features

### ARIA Labels
- Password input visibility toggle has aria-label
- Form inputs have proper labels/placeholders
- Error messages associated with inputs

### Keyboard Navigation
- Tab order: Email - Password - Remember Me - Sign In
- Enter key submits form
- Icon buttons keyboard accessible

### Semantic HTML
- Proper form element
- Input types: email, password
- Semantic button elements

---

## Key Features Summary

✓ Email/Password Authentication  
✓ NextAuth.js Integration (Credentials + Google OAuth)  
✓ Form Validation with Valibot  
✓ Password Visibility Toggle  
✓ Remember Me Checkbox  
✓ Responsive Design (Split layout on desktop)  
✓ Dark/Light Mode Support  
✓ Error Handling and Display  
✓ Localization Support  
✓ Redirect Parameter Support  
✓ Accessibility Compliant

---

## Performance Considerations

- Server Component for initial rendering
- Client Component hydration for interactivity
- Image lazy loading with Next Image
- Responsive images with srcset
- Debounced form validation
- Memoized styled components

---

## Dependencies

### Core Dependencies
- next-auth - Authentication
- react-hook-form - Form handling
- valibot - Schema validation
- @mui/material - Component library

### Next.js Utilities
- next/link - Localized routing
- next/navigation - Client-side navigation
- next/image - Optimized images

### Custom Utilities
- useImageVariant() - Image mode selection
- getLocalizedUrl() - URL localization
