# Demo Auth Pages: Login (V1 and V2)

## Overview
Demo showcase pages for login form with two different layout variants: Card-based (V1) and Split layout (V2).

---

## LoginV1: Card-Based Layout

**Location:** src/views/pages/auth/LoginV1.tsx  
**Route:** src/app/[lang]/(blank-layout-pages)/pages/auth/login-v1/page.tsx  
**Type:** Client Component  
**Props:** None (no mode prop required)

### Features
- Centered card-based layout
- AuthIllustrationWrapper component for decorative styling
- Standard email/password form
- Remember me checkbox
- Password visibility toggle
- Forgot password link
- Sign-up link to register page
- Responsive: Full width on mobile, max-width 450px on desktop

### Form Fields

| Field | Type | Validation |
|-------|------|-----------|
| **Email or Username** | Text Input | Auto-focus on mount |
| **Password** | Password Input | Visibility toggle |
| **Remember Me** | Checkbox | Optional |

### Navigation Links
- Forgot Password: /pages/auth/forgot-password-v1
- Create Account: /pages/auth/register-v1
- Logo: Home page

### Layout Structure
- AuthIllustrationWrapper with decorative SVG elements
- Card container with padding
- Logo centered at top
- Welcome heading and subheading
- Form fields in flex column layout
- Action buttons and links at bottom
- Divider between sections

### Styling Approach
- Tailwind CSS (flex, gap, spacing)
- MUI Card component
- CustomTextField wrapper
- Responsive padding: sm:!p-12
- Icons from Tabler icon set
- Primary color for links

### State Management
- isPasswordShown: boolean - Password visibility toggle

### Component Dependencies
- AuthIllustrationWrapper - Decorative styled component
- Logo - Application logo
- CustomTextField - MUI TextField wrapper
- MUI Card, CardContent, Typography, Button, Checkbox, etc.

---

## LoginV2: Split Layout

**Location:** src/views/pages/auth/LoginV2.tsx  
**Route:** src/app/[lang]/(blank-layout-pages)/pages/auth/login-v2/page.tsx  
**Type:** Client Component  
**Props:** { mode: SystemMode } - Theme mode for image selection

### Features
- Full-screen split layout (illustration left, form right)
- Mode-aware responsive images (light/dark/bordered variants)
- Responsive design (hides illustration on tablet/mobile)
- Standard email/password form
- Remember me checkbox
- Password visibility toggle
- Forgot password link
- Sign-up link to register page
- Theme-aware background masks
- Skin setting support (bordered/default)

### Form Fields

| Field | Type | Validation |
|-------|------|-----------|
| **Email or Username** | Text Input | Standard text input |
| **Password** | Password Input | Visibility toggle |
| **Remember Me** | Checkbox | Optional |

### Image Sources

**Illustrations:**
- Light: /images/illustrations/auth/v2-login-light.png
- Dark: /images/illustrations/auth/v2-login-dark.png
- Bordered Light: /images/illustrations/auth/v2-login-dark-border.png
- Bordered Dark: /images/illustrations/auth/v2-login-dark-border.png

**Background Masks:**
- Light: /images/pages/auth-mask-light.png
- Dark: /images/pages/auth-mask-dark.png

### Layout Structure
- **Desktop (lg+):** Split view (illustration + form side-by-side)
- **Tablet (md):** Illustration hidden, form full-width
- **Mobile:** Full-width form, minimal spacing

### Responsive Image Sizing
- Max block size: 680px on desktop
- Max block size: 550px on 1536px breakpoint
- Max block size: 450px on lg breakpoint
- Mask image: max 355px height
- Absolute positioning for background mask

### Styling Approach
- Tailwind CSS for layout (flex, min-bs-[100dvh], p-6)
- MUI styled() for LoginIllustration and MaskImg components
- Theme-aware spacing and breakpoints
- Conditional classes for border-ie on bordered skin
- useMediaQuery for responsive behavior

### State Management
- isPasswordShown: boolean - Password visibility toggle

### Hooks Used
- useState() - Password visibility
- useParams() - Get locale
- useTheme() - Theme access
- useMediaQuery() - Responsive checks (md breakpoint)
- useSettings() - Get skin setting
- useImageVariant() - Mode-aware image selection

### Component Dependencies
- Logo - Application logo
- CustomTextField - MUI TextField wrapper
- useImageVariant hook - Image mode selection
- useSettings hook - Skin/border setting
- MUI components - Typography, Button, Checkbox, etc.

---

## Form Features (Both Variants)

### Form Behavior
- No real form submission (preventDefault on submit)
- Client-side only, no backend integration
- Demo/showcase purpose
- No validation feedback
- No error states

### Password Visibility Toggle
- Eye icon indicates password state
- Click toggles between text and password input types
- Tabler icons: tabler-eye / tabler-eye-off
- Positioned in input endAdornment

### Remember Me
- Standard HTML checkbox
- No functional backend storage
- UX demonstration only

### Navigation
- Links to demo pages, not real authentication
- Localization-aware URLs via getLocalizedUrl()
- Logo link returns to home

---

## Common Elements (Both Variants)

### Typography
- Heading: "Welcome to {{templateName}}! 👋🏻"
- Subheading: "Please sign-in to your account and start the adventure"
- Bottom text: "New on our platform?" with link to register

### MUI Components
- Typography - All text content
- Button - Submit button (fullWidth, variant='contained')
- Checkbox - Remember me
- IconButton - Password visibility toggle
- InputAdornment - Icon in password field
- Divider - Visual separation (in some variants)

### Accessibility
- Form element with noValidate
- AutoComplete off (security demo)
- Icon buttons with onClick and onMouseDown
- Link components with proper href

---

## File Organization

### V1 Directory
src/app/[lang]/(blank-layout-pages)/pages/auth/login-v1/page.tsx
- Routes to LoginV1 component
- No special page configuration

### V2 Directory  
src/app/[lang]/(blank-layout-pages)/pages/auth/login-v2/page.tsx
- Routes to LoginV2 component
- Passes mode prop from layout/params

---

## Comparison: V1 vs V2

| Aspect | V1 | V2 |
|--------|----|----|
| **Layout** | Card-centered | Full-screen split |
| **Max Width** | 450px | No limit |
| **Illustration** | Wrapped in AuthIllustrationWrapper | Responsive positioned image |
| **Background** | Implicit | Explicit mask images |
| **Mobile** | Visible form + hidden illustration | Form only |
| **Props** | None | mode: SystemMode |
| **Hooks** | useParams only | useParams, useTheme, useMediaQuery, useSettings, useImageVariant |
| **Styling** | Tailwind + MUI Card | Tailwind + MUI styled() |
| **Theme Support** | Basic | Full with bordered variant |

---

## Key Takeaways

- **V1:** Simple, self-contained, decorative styling for demonstration
- **V2:** Full-featured, production-grade patterns with responsive images and theme support
- **Both:** Non-functional forms intended for UI/UX showcase
- **Navigation:** Links to other demo pages in auth showcase
- **Localization:** Both support multi-language routes
