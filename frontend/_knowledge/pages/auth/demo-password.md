# Demo Auth Pages: Password Reset (V1 and V2)

## Overview
Demo showcase pages for password management flows: Forgot Password and Reset Password, each with two layout variants (Card-based V1 and Split layout V2).

---

## ForgotPasswordV1: Card-Based Layout

**Location:** src/views/pages/auth/ForgotPasswordV1.tsx  
**Route:** src/app/[lang]/(blank-layout-pages)/pages/auth/forgot-password-v1/page.tsx  
**Type:** Client Component  
**Props:** None

### Features
- Centered card-based layout
- AuthIllustrationWrapper for decorative styling
- Single email input field
- Simple form submission
- Back to login link with directional icon
- RTL support for back link
- Non-functional demo form

### Form Fields

| Field | Type | Validation |
|-------|------|-----------|
| **Email** | Text Input | Standard email input |

### Navigation
- Logo: Home page
- Send Reset Link: Submit button (non-functional)
- Back to Login: /pages/auth/login-v1 with RTL support

### Back Navigation
- DirectionalIcon component for RTL support
- LTR: tabler-chevron-left
- RTL: tabler-chevron-right
- Text: "Back to login"
- Link styling with primary color

### Layout Structure
- Card container (450px max-width on desktop)
- Logo centered at top
- Heading: "Forgot Password 🔒"
- Description: "Enter your email and we'll send you instructions to reset your password"
- Email input field
- Send Reset Link button
- Back to login link below

### State Management
- No state management (demo only)

### Styling
- Tailwind CSS (flex, gap, spacing)
- MUI Card, CardContent
- Custom TextField
- Button (fullWidth, contained variant)
- Primary color for links

### Component Dependencies
- AuthIllustrationWrapper
- DirectionalIcon - RTL-aware back arrow
- Logo
- CustomTextField
- MUI Typography, Button, Card

---

## ForgotPasswordV2: Split Layout

**Location:** src/views/pages/auth/ForgotPasswordV2.tsx  
**Route:** src/app/[lang]/(blank-layout-pages)/pages/auth/forgot-password-v2/page.tsx  
**Type:** Client Component  
**Props:** { mode: SystemMode } - Theme mode

### Features
- Full-screen split layout (illustration left, form right)
- Mode-aware responsive images
- Responsive design (illustration hidden on tablet/mobile)
- Single email input field
- Back to login link with RTL support
- Theme-aware background masks
- Skin setting support

### Form Fields

| Field | Type | Validation |
|-------|------|-----------|
| **Email** | Text Input | Standard email input |

### Image Sources

**Illustrations:**
- Light: /images/illustrations/auth/v2-forgot-password-light.png
- Dark: /images/illustrations/auth/v2-forgot-password-dark.png

**Background Masks:**
- Light: /images/pages/auth-mask-light.png
- Dark: /images/pages/auth-mask-dark.png

### Hooks Used
- useParams() - Get locale
- useTheme() - Theme access
- useMediaQuery() - Responsive (md breakpoint)
- useSettings() - Skin setting
- useImageVariant() - Image selection

---

## ResetPasswordV1: Card-Based Layout

**Location:** src/views/pages/auth/ResetPasswordV1.tsx  
**Route:** src/app/[lang]/(blank-layout-pages)/pages/auth/reset-password-v1/page.tsx  
**Type:** Client Component  
**Props:** None

### Features
- Centered card-based layout
- AuthIllustrationWrapper for decorative styling
- Two password input fields (New Password, Confirm Password)
- Individual password visibility toggles
- Form submission button
- Back to login link with RTL support
- Non-functional demo form

### Form Fields

| Field | Type | Validation | Features |
|-------|------|-----------|----------|
| **New Password** | Password Input | Auto-focus | Visibility toggle |
| **Confirm Password** | Password Input | Standard | Visibility toggle |

### Password Visibility
- Separate state for each password field
- Individual eye icons for each field
- Independent toggle functionality
- Tabler icons: tabler-eye / tabler-eye-off

### Navigation
- Logo: Home page
- Set New Password: Submit button
- Back to Login: /pages/auth/login-v1 with RTL support

### Layout Structure
- Card container (450px max-width on desktop)
- Logo centered at top
- Heading: "Reset Password 🔒"
- Description: "Your new password must be different from previously used passwords"
- New Password input with toggle
- Confirm Password input with toggle
- Set New Password button
- Back to login link

### State Management
- isPasswordShown: boolean - New password visibility
- isConfirmPasswordShown: boolean - Confirm password visibility

### Component Dependencies
- AuthIllustrationWrapper
- DirectionalIcon - Back navigation
- Logo
- CustomTextField
- MUI Typography, Button, Card, IconButton

---

## ResetPasswordV2: Split Layout

**Location:** src/views/pages/auth/ResetPasswordV2.tsx  
**Route:** src/app/[lang]/(blank-layout-pages)/pages/auth/reset-password-v2/page.tsx  
**Type:** Client Component  
**Props:** { mode: SystemMode } - Theme mode

### Features
- Full-screen split layout (illustration left, form right)
- Mode-aware responsive images
- Responsive design (illustration hidden on tablet/mobile)
- Two password fields with independent toggles
- Back to login link with RTL support
- Theme-aware background masks
- Skin setting support

### Form Fields

| Field | Type | Features |
|-------|------|----------|
| **New Password** | Password Input | Individual visibility toggle |
| **Confirm Password** | Password Input | Individual visibility toggle |

### Image Sources

**Illustrations:**
- Light: /images/illustrations/auth/v2-reset-password-light.png
- Dark: /images/illustrations/auth/v2-reset-password-dark.png

**Background Masks:**
- Light: /images/pages/auth-mask-light.png
- Dark: /images/pages/auth-mask-dark.png

### State Management
- isPasswordShown: boolean - New password visibility
- isConfirmPasswordShown: boolean - Confirm password visibility

### Hooks Used
- useState() - Two password visibility states
- useParams() - Get locale
- useTheme() - Theme access
- useMediaQuery() - Responsive
- useSettings() - Skin setting
- useImageVariant() - Image selection

---

## Common Features (All Variants)

### Form Behavior
- Non-functional forms (preventDefault)
- Demo/showcase purpose
- No validation feedback
- No backend integration
- Client-side only

### Typography
- Forgot Password Heading: "Forgot Password 🔒"
- Forgot Password Description: "Enter your email and we'll send you instructions to reset your password"
- Reset Password Heading: "Reset Password 🔒"
- Reset Password Description: "Your new password must be different from previously used passwords"

### Password Visibility Toggles
- Eye icons in endAdornment
- Toggle between text and password input types
- Click handler with preventDefault
- Tabler icon set

### MUI Components
- Typography - All text content
- Button - Submit buttons (fullWidth, contained variant)
- TextField - Form inputs
- Card (V1) - Container
- IconButton - Visibility toggle buttons
- InputAdornment - Icon positioning

### Navigation
- Localization-aware URLs
- getLocalizedUrl() utility
- Logo returns to home
- Back to Login with RTL support
- DirectionalIcon for directional awareness

---

## Comparison Table

| Aspect | Forgot V1 | Forgot V2 | Reset V1 | Reset V2 |
|--------|-----------|-----------|----------|----------|
| **Layout** | Card | Split | Card | Split |
| **Fields** | Email (1) | Email (1) | Password (2) | Password (2) |
| **Visibility Toggles** | None | None | 2 | 2 |
| **Illustration** | Wrapped | Responsive | Wrapped | Responsive |
| **Props** | None | mode | None | mode |
| **Height** | Min | Full-screen | Min | Full-screen |
| **Responsive** | Mobile-friendly | Responsive hidden | Mobile-friendly | Responsive hidden |

---

## File Organization

### Forgot Password V1
src/app/[lang]/(blank-layout-pages)/pages/auth/forgot-password-v1/page.tsx

### Forgot Password V2
src/app/[lang]/(blank-layout-pages)/pages/auth/forgot-password-v2/page.tsx

### Reset Password V1
src/app/[lang]/(blank-layout-pages)/pages/auth/reset-password-v1/page.tsx

### Reset Password V2
src/app/[lang]/(blank-layout-pages)/pages/auth/reset-password-v2/page.tsx

---

## Key Patterns

### Forgot Password Pattern
- Single email field for recovery initiation
- Simple, streamlined form
- Minimal required information
- Back navigation for easy exit

### Reset Password Pattern
- Two password fields for confirmation
- Password validation readiness
- Visibility toggles for UX
- Secure password entry

### V1 Pattern
- Card-based showcase
- Decorative wrapper styling
- Self-contained component
- Good for basic UI demos

### V2 Pattern
- Production-grade responsive design
- Theme and mode awareness
- Split layout for larger screens
- Responsive image handling
- Skin variant support
