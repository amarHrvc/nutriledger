# Demo Auth Pages: 2FA and Email Verification (V1 and V2)

## Overview
Demo showcase pages for account security verification: Two-Step Verification (2FA) and Email Verification, each with two layout variants (Card-based V1 and Split layout V2).

---

## TwoStepsV1: Card-Based 2FA Layout

**Location:** src/views/pages/auth/TwoStepsV1.tsx  
**Route:** src/app/[lang]/(blank-layout-pages)/pages/auth/two-steps-v1/page.tsx  
**Type:** Client Component  
**Props:** None

### Features
- Centered card-based layout
- AuthIllustrationWrapper for decorative styling
- 6-digit OTP (One-Time Password) input using input-otp library
- Masked phone number display
- Resend code link
- Verification button
- Non-functional demo form

### OTP Input Features
- 6 digit security code expected
- OTPInput component from input-otp library
- Custom slot rendering with CSS module styles
- Fake caret visual feedback
- Inline input validation
- containerClassName with flex layout

### Form Structure

| Element | Type | Description |
|---------|------|-------------|
| **Heading** | Typography | "Two Step Verification 💬" |
| **Description** | Typography | "We sent a verification code to your mobile. Enter the code from the mobile in the field below." |
| **Phone Number** | Display | "***1234" (masked display) |
| **OTP Input** | 6-slot input | Custom styled OTP slots |
| **Verify Button** | Button | "Verify my account" (non-functional) |
| **Resend Link** | Typography + Link | "Didn't get the code? Resend" |

### State Management
- otp: string | null - Stores entered OTP value
- Change handler: setOtp

### OTP Input Implementation
- Library: input-otp
- Max length: 6 digits
- Slot component with custom styling
- FakeCaret component for visual feedback
- Custom CSS module: @/libs/styles/inputOtp.module.css

### Styling
- CSS Module for OTP input slots
- Tailwind CSS for layout
- Slot styling: normal and active states
- FakeCaret: vertical line animation/display
- Card container (450px max-width)

### Navigation
- Logo: Home page
- Verify my account: Submit button
- Resend: Non-functional link

### Component Dependencies
- AuthIllustrationWrapper
- Logo
- OTPInput from input-otp
- Custom Slot and FakeCaret components
- CSS Module: inputOtp.module.css
- MUI Typography, Button, Card

---

## TwoStepsV2: Split Layout 2FA

**Location:** src/views/pages/auth/TwoStepsV2.tsx  
**Route:** src/app/[lang]/(blank-layout-pages)/pages/auth/two-steps-v2/page.tsx  
**Type:** Client Component  
**Props:** { mode: SystemMode } - Theme mode

### Features
- Full-screen split layout (illustration left, form right)
- Mode-aware responsive images
- Responsive design (illustration hidden on tablet/mobile)
- 6-digit OTP input with custom styling
- Masked phone number display
- Resend code link
- Theme-aware background masks
- Skin setting support

### Form Structure
- Same as V1: OTP input, verification button, resend link
- Split layout instead of centered card

### Image Sources

**Illustrations:**
- Light: /images/illustrations/auth/v2-two-steps-light.png
- Dark: /images/illustrations/auth/v2-two-steps-dark.png

**Background Masks:**
- Light: /images/pages/auth-mask-light.png
- Dark: /images/pages/auth-mask-dark.png

### State Management
- otp: string | null - Entered OTP value

### Hooks Used
- useState() - OTP value
- useParams() - Get locale
- useTheme() - Theme access
- useMediaQuery() - Responsive (md breakpoint)
- useSettings() - Skin setting
- useImageVariant() - Image selection

---

## VerifyEmailV1: Card-Based Email Verification Layout

**Location:** src/views/pages/auth/VerifyEmailV1.tsx  
**Route:** src/app/[lang]/(blank-layout-pages)/pages/auth/verify-email-v1/page.tsx  
**Type:** Client Component  
**Props:** None

### Features
- Centered card-based layout
- AuthIllustrationWrapper for decorative styling
- Static email display (john.doe@email.com)
- Skip button for immediate access
- Resend email link
- Non-functional demo form
- No input fields required

### Form Structure

| Element | Type | Description |
|---------|------|-------------|
| **Heading** | Typography | "Verify your email ✉️" |
| **Description** | Typography | "Account activation link sent to your email address: john.doe@email.com Please follow the link inside to continue." |
| **Email Address** | Display | john.doe@email.com (styled highlight) |
| **Skip Button** | Button | "Skip For Now" (non-functional) |
| **Resend Link** | Typography + Link | "Didn't get the mail? Resend" |

### State Management
- No state management required
- Static display only

### Layout Structure
- Card container (450px max-width)
- Logo centered at top
- Heading and description
- Static email display
- Skip button
- Resend link
- Simple, minimal form

### Styling
- Tailwind CSS for layout
- MUI Card, CardContent
- Typography for email highlight
- Primary color for Resend link

### Navigation
- Logo: Home page
- Skip For Now: Non-functional button
- Resend: Non-functional link

### Component Dependencies
- AuthIllustrationWrapper
- Logo
- MUI Typography, Button, Card
- Custom Link component

---

## VerifyEmailV2: Split Layout Email Verification

**Location:** src/views/pages/auth/VerifyEmailV2.tsx  
**Route:** src/app/[lang]/(blank-layout-pages)/pages/auth/verify-email-v2/page.tsx  
**Type:** Client Component  
**Props:** { mode: SystemMode } - Theme mode

### Features
- Full-screen split layout (illustration left, form right)
- Mode-aware responsive images
- Responsive design (illustration hidden on tablet/mobile)
- Static email display
- Skip and Resend functionality
- Theme-aware background masks
- Skin setting support

### Form Structure
- Same as V1: Email display, skip button, resend link
- Split layout instead of centered card

### Image Sources

**Illustrations:**
- Light: /images/illustrations/auth/v2-verify-email-light.png
- Dark: /images/illustrations/auth/v2-verify-email-dark.png

**Background Masks:**
- Light: /images/pages/auth-mask-light.png
- Dark: /images/pages/auth-mask-dark.png

### Hooks Used
- useParams() - Get locale
- useTheme() - Theme access
- useMediaQuery() - Responsive
- useSettings() - Skin setting
- useImageVariant() - Image selection

---

## Common Features (All Variants)

### Form Behavior
- Non-functional demo forms
- Demo/showcase purpose
- No backend integration
- No validation feedback
- Client-side only

### Typography
- 2FA Heading: "Two Step Verification 💬"
- 2FA Description: "We sent a verification code to your mobile..."
- Email Heading: "Verify your email ✉️"
- Email Description: "Account activation link sent to your email address..."

### Interaction Patterns
- 2FA: OTP input with 6 digits
- Email: Skip and Resend links
- Both: Resend functionality option

### MUI Components
- Typography - All text content
- Button - Action buttons
- Card (V1) - Container
- Link components - Navigation

### Accessibility
- Semantic form elements
- Typography hierarchy
- Link components for navigation
- Button accessibility

---

## Comparison Table: 2FA Variants

| Aspect | V1 | V2 |
|--------|----|----|
| **Layout** | Card | Split |
| **Max Width** | 450px | Full-screen |
| **Illustration** | Wrapped | Responsive |
| **OTP Input** | Custom slots | Custom slots |
| **Mobile** | Full-width | Form only |
| **Props** | None | mode |
| **Responsive Images** | None | Yes (light/dark) |
| **Theme Support** | Basic | Full |

---

## Comparison Table: Email Verification Variants

| Aspect | V1 | V2 |
|--------|----|----|
| **Layout** | Card | Split |
| **Max Width** | 450px | Full-screen |
| **Illustration** | Wrapped | Responsive |
| **Email Display** | Static | Static |
| **Mobile** | Full-width | Form only |
| **Props** | None | mode |
| **Responsive Images** | None | Yes (light/dark) |
| **Theme Support** | Basic | Full |

---

## File Organization

### Two Steps V1
src/app/[lang]/(blank-layout-pages)/pages/auth/two-steps-v1/page.tsx

### Two Steps V2
src/app/[lang]/(blank-layout-pages)/pages/auth/two-steps-v2/page.tsx

### Verify Email V1
src/app/[lang]/(blank-layout-pages)/pages/auth/verify-email-v1/page.tsx

### Verify Email V2
src/app/[lang]/(blank-layout-pages)/pages/auth/verify-email-v2/page.tsx

---

## Key Libraries and Dependencies

### OTP Input
- Library: input-otp
- Usage: 6-digit security code input
- Custom slot rendering
- CSS module for styling

### Styling
- CSS Modules: @/libs/styles/inputOtp.module.css
- Tailwind CSS: Layout and spacing
- MUI styled components: Image positioning

### Components
- input-otp: OTPInput component
- Custom Slot and FakeCaret: OTP display
- AuthIllustrationWrapper: Decorative styling
- Logo, Typography, Button: Core UI

---

## Key Patterns

### 2FA Pattern (Two-Step Verification)
- Mobile verification code input
- 6-digit OTP requirement
- Masked phone number display
- Resend functionality
- Verification confirmation

### Email Verification Pattern
- Email confirmation flow
- Static email display
- Skip option for alternative flow
- Resend email capability
- Account activation link requirement

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
