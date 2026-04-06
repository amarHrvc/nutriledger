# Auth Source Files Analysis

## Overview
Complete analysis of auth-related source files including real production auth pages, demo variants, and shared components.

## Real Auth Pages (Production)

### 1. **Real Auth Pages** (guest-only protected routes)
Location: `src/app/[lang]/(blank-layout-pages)/(guest-only)/`

#### Login Page
- **File**: `login/page.tsx`
- **Type**: Server Component (async)
- **View Component**: `Login` from `@views/Login`
- **Props**: `mode: SystemMode`
- **Features**:
  - Email/Password form validation with Valibot
  - NextAuth.js integration with signIn('credentials') and signIn('google')
  - Form validation schema using Valibot
  - Error state management
  - Password visibility toggle
  - Remember me checkbox
  - Link to forgot password and register
  - Responsive layout with illustration (split view on desktop)
  - Theme-aware images (light/dark mode, bordered variant)
  - Metadata export

#### Register Page
- **File**: `register/page.tsx`
- **Type**: Server Component (async)
- **View Component**: `Register` from `@views/Register`
- **Props**: `mode: SystemMode`
- **Features**:
  - Username, email, password form fields
  - Password visibility toggle
  - Terms & privacy policy checkbox
  - Social signup buttons (Facebook, Twitter, GitHub, Google)
  - Link to login page
  - Responsive layout with illustration
  - Theme-aware images

#### Forgot Password Page
- **File**: `forgot-password/page.tsx`
- **Type**: Server Component (async)
- **View Component**: `ForgotPassword` from `@views/ForgotPassword`
- **Props**: `mode: SystemMode`
- **Features**:
  - Email-only form for reset link request
  - Back to login link
  - Responsive layout with illustration
  - Theme-aware images

---

## View Components (Real Production Implementation)

### Core View Files
Location: `src/views/`

#### 1. **Login.tsx**
- **Props**: `{ mode: SystemMode }`
- **Client Component**: 'use client'
- **Key Dependencies**:
  - next-auth/react: signIn (credentials, google)
  - react-hook-form: useForm, Controller
  - @hookform/resolvers/valibot: valibotResolver
  - valibot: schema validation
  - MUI: Various components (TextField, Button, Checkbox, etc.)
- **State**:
  - isPasswordShown: boolean
  - errorState: ErrorType | null
- **Form Validation**:
  - email: required, valid email format
  - password: required, minimum 5 characters
- **Default Values**: admin@vuexy.com / admin
- **Hooks Used**:
  - useRouter() - For redirects
  - useSearchParams() - Get redirectTo param
  - useParams() - Get locale
  - useSettings() - Get skin setting
  - useTheme() - Theme access
  - useMediaQuery() - Responsive checks
  - useImageVariant() - Mode-aware images

#### 2. **Register.tsx**
- **Props**: `{ mode: SystemMode }`
- **Client Component**: 'use client'
- **Key Dependencies**:
  - MUI components
  - react hooks
- **State**:
  - isPasswordShown: boolean
- **Form Fields**:
  - Username
  - Email
  - Password (with visibility toggle)
  - Terms & privacy policy acceptance
- **Social Options**:
  - Facebook, Twitter, GitHub, Google

#### 3. **ForgotPassword.tsx**
- **Props**: `{ mode: SystemMode }`
- **Client Component**: 'use client'
- **Key Dependencies**:
  - MUI components
  - CustomTextField
- **Form Fields**:
  - Email only
- **Navigation**:
  - Back to login link with directional icon

---

## Demo Auth Variants (13 Total)

### Location
`src/views/pages/auth/` with corresponding pages at:
`src/app/[lang]/(blank-layout-pages)/pages/auth/`

### Demo Variants List

#### V1 Variants (Card-based, AuthIllustrationWrapper):
1. **LoginV1.tsx** - Email/Password, Remember me, Social login
2. **RegisterV1.tsx** - Username/Email/Password, Social signup
3. **ForgotPasswordV1.tsx** - Email-only form
4. **ResetPasswordV1.tsx** - New password, Confirm password
5. **TwoStepsV1.tsx** - OTP input (6 digits), input-otp library
6. **VerifyEmailV1.tsx** - Static email display, Skip/Resend options

#### V2 Variants (Split layout, full-screen):
7. **LoginV2.tsx** - Split layout with mode-aware illustrations
8. **RegisterV2.tsx** - Split layout auth
9. **ForgotPasswordV2.tsx** - Split layout password reset
10. **ResetPasswordV2.tsx** - Split layout new password form
11. **TwoStepsV2.tsx** - Split layout OTP input
12. **VerifyEmailV2.tsx** - Split layout email verification

#### Special:
13. **RegisterMultiSteps** - 3-step stepper (Account → Personal → Billing)
    - Sub-components: StepAccountDetails, StepPersonalInfo, StepBillingDetails
    - Props: `{ mode: SystemMode }`
    - Full-screen layout with side illustration

---

## Shared Components

### 1. **AuthIllustrationWrapper.tsx**
- **Type**: Styled component (MUI styled)
- **Purpose**: Decorative wrapper for card-based auth variants
- **Features**:
  - Max-width: 450px
  - Pseudo-elements with SVG decorations
  - Primary color theming
  - Responsive: No decorations on mobile

### 2. **DirectionalIcon.tsx**
- **Purpose**: Used in back navigation links
- **Props**: ltrIconClass, rtlIconClass (for RTL language support)

---

## File Structure Summary

Real Pages (Production):
src/app/[lang]/(blank-layout-pages)/(guest-only)/
  - login/page.tsx → Login view
  - register/page.tsx → Register view
  - forgot-password/page.tsx → ForgotPassword view

View Components:
src/views/
  - Login.tsx (client, with NextAuth)
  - Register.tsx (client, form setup)
  - ForgotPassword.tsx (client, email form)
  - pages/auth/
    - LoginV1.tsx, LoginV2.tsx
    - RegisterV1.tsx, RegisterV2.tsx
    - ForgotPasswordV1.tsx, ForgotPasswordV2.tsx
    - ResetPasswordV1.tsx, ResetPasswordV2.tsx
    - TwoStepsV1.tsx, TwoStepsV2.tsx
    - VerifyEmailV1.tsx, VerifyEmailV2.tsx
    - AuthIllustrationWrapper.tsx
    - register-multi-steps/
      - index.tsx, StepAccountDetails.tsx, StepPersonalInfo.tsx, StepBillingDetails.tsx

Demo Pages:
src/app/[lang]/(blank-layout-pages)/pages/auth/
  - login-v1/, login-v2/
  - register-v1/, register-v2/
  - forgot-password-v1/, forgot-password-v2/
  - reset-password-v1/, reset-password-v2/
  - two-steps-v1/, two-steps-v2/
  - verify-email-v1/, verify-email-v2/
  - register-multi-steps/

---

## Key Dependencies

Authentication:
- next-auth/react: signIn for credentials and OAuth (Google)
- next-auth backend: API routes at /api/auth/[...nextauth]

Form Handling:
- react-hook-form: Form state and validation
- @hookform/resolvers/valibot: Form validation resolver
- valibot: Schema validation (pipe, string, email, minLength, etc.)

UI Components:
- @mui/material: All form controls, buttons, typography, etc.
- next/link: Localized navigation
- classnames: Conditional class application

Image Handling:
- Custom Hook: useImageVariant() for mode-aware images
- Next Image: Implicit usage through styled components

Styling:
- @mui/material/styles: styled() function
- Tailwind CSS: Utility classes for spacing and layout
- CSS Modules: Custom styles (e.g., inputOtp.module.css)

---

## Key Patterns

### Layout Patterns
- V1 Variants: Card-based centered layout (450px max-width)
- V2 Variants: Full-screen split layout (illustration left/right)
- Real Pages: Enhanced V2 pattern with actual auth logic

### Styling Patterns
- MUI styled() function for components
- Tailwind + MUI spacing/utilities combination
- Dark/light mode support via useImageVariant
- Mobile-first responsive design
- Bordered skin variant support

### Form Patterns
- Valibot validation (real pages)
- React Hook Form with Controller
- Field-level error display
- Password visibility toggle with icon button
- Form submission handling with redirect

### Component Hierarchy
- Page (Server Component) → View Component (Client) → Form + UI
- Real pages pass mode from server to client
- Demo pages are mostly standalone client components