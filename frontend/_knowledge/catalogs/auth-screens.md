# Auth Screens Catalog

Complete catalog of all authentication-related screens in the application, including production pages and demo variants.

---

## Catalog Table

| # | Screen | Type | View File | Layout | Features | Form Fields | Status |
|---|--------|------|-----------|--------|----------|-------------|--------|
| 1 | **Login** | Real | src/views/Login.tsx | Split (V2) | NextAuth.js, email/password, remember me, forgot password link | Email, Password, Remember Me | Production |
| 2 | **Register** | Real | src/views/Register.tsx | Split (V2) | Multi-field form, social signup, password visibility toggle | Username, Email, Password, Terms | Production |
| 3 | **Forgot Password** | Real | src/views/ForgotPassword.tsx | Split (V2) | Email recovery, simple single-field form | Email | Production |
| 4 | **LoginV1** | Demo | src/views/pages/auth/LoginV1.tsx | Card (V1) | Email/password form, remember me, password visibility, social signin | Email/Username, Password, Remember Me | Showcase |
| 5 | **LoginV2** | Demo | src/views/pages/auth/LoginV2.tsx | Split (V2) | Split layout, responsive images, theme-aware, bordered skin support | Email/Username, Password, Remember Me | Showcase |
| 6 | **RegisterV1** | Demo | src/views/pages/auth/RegisterV1.tsx | Card (V1) | Card-based, social signup buttons, multi-field form | Username, Email, Password, Terms | Showcase |
| 7 | **RegisterV2** | Demo | src/views/pages/auth/RegisterV2.tsx | Split (V2) | Split layout, responsive images, theme-aware, social buttons | Username, Email, Password, Terms | Showcase |
| 8 | **RegisterMultiSteps** | Demo | src/views/pages/auth/register-multi-steps/ | Stepper | 3-step form with progress, account/personal/billing steps | Account, Personal, Billing | Showcase |
| 9 | **ForgotPasswordV1** | Demo | src/views/pages/auth/ForgotPasswordV1.tsx | Card (V1) | Card-based, single email field, back to login link | Email | Showcase |
| 10 | **ForgotPasswordV2** | Demo | src/views/pages/auth/ForgotPasswordV2.tsx | Split (V2) | Split layout, responsive images, theme-aware | Email | Showcase |
| 11 | **ResetPasswordV1** | Demo | src/views/pages/auth/ResetPasswordV1.tsx | Card (V1) | Card-based, two password fields with toggles, back to login | New Password, Confirm Password | Showcase |
| 12 | **ResetPasswordV2** | Demo | src/views/pages/auth/ResetPasswordV2.tsx | Split (V2) | Split layout, responsive images, theme-aware, password toggles | New Password, Confirm Password | Showcase |
| 13 | **TwoStepsV1** | Demo | src/views/pages/auth/TwoStepsV1.tsx | Card (V1) | Card-based, 6-digit OTP input, resend link, masked phone | OTP (6-digit) | Showcase |
| 14 | **TwoStepsV2** | Demo | src/views/pages/auth/TwoStepsV2.tsx | Split (V2) | Split layout, responsive images, OTP input with custom slots | OTP (6-digit) | Showcase |
| 15 | **VerifyEmailV1** | Demo | src/views/pages/auth/VerifyEmailV1.tsx | Card (V1) | Card-based, static email display, skip/resend options | None (display only) | Showcase |
| 16 | **VerifyEmailV2** | Demo | src/views/pages/auth/VerifyEmailV2.tsx | Split (V2) | Split layout, responsive images, skip/resend options | None (display only) | Showcase |

---

## Screen Type Summary

### Real Production Screens (3)

**Real pages are fully functional production implementations with actual authentication logic.**

1. **Login** - Production login with NextAuth.js credentials and OAuth
2. **Register** - Production registration with email signup
3. **Forgot Password** - Production password recovery flow

**Characteristics:**
- Located in: src/views/ (root level)
- Server Components at: src/app/[lang]/(blank-layout-pages)/(guest-only)/
- Props: { mode: SystemMode }
- Features: Real authentication, form validation, error handling
- Status: Production-ready, active implementation

### Demo Showcase Screens (13)

**Demo pages are non-functional UI showcase variants for presentation and design reference.**

**V1 Variants (Card-based, 6 pages):**
1. LoginV1 - Card-centered login
2. RegisterV1 - Card-centered registration
3. ForgotPasswordV1 - Card-centered password recovery
4. ResetPasswordV1 - Card-centered password reset
5. TwoStepsV1 - Card-centered 2FA
6. VerifyEmailV1 - Card-centered email verification

**V2 Variants (Split layout, 6 pages):**
1. LoginV2 - Split layout with illustrations
2. RegisterV2 - Split layout with illustrations
3. ForgotPasswordV2 - Split layout with illustrations
4. ResetPasswordV2 - Split layout with illustrations
5. TwoStepsV2 - Split layout with illustrations
6. VerifyEmailV2 - Split layout with illustrations

**Special Multi-Step:**
1. RegisterMultiSteps - 3-step stepper form with progress tracking

**Characteristics:**
- Located in: src/views/pages/auth/
- Routes at: src/app/[lang]/(blank-layout-pages)/pages/auth/
- Props: V1 (none), V2 (mode: SystemMode)
- Features: UI showcase, responsive design, theme support
- Status: Demo/reference implementations

---

## Layout Type Distribution

### V1: Card-Based Layout (6 screens)
- AuthIllustrationWrapper component
- Max-width: 450px on desktop
- Centered card style
- Decorative SVG pseudo-elements
- Mobile-responsive
- Simple self-contained design

**Screens:** LoginV1, RegisterV1, ForgotPasswordV1, ResetPasswordV1, TwoStepsV1, VerifyEmailV1

### V2: Split Layout (6 screens + Real)
- Full-screen split view
- Left: Responsive illustration
- Right: Form area
- Illustration hidden on tablet/mobile
- Theme-aware images (light/dark/bordered)
- Production-grade responsive design
- Skin variant support

**Screens:** LoginV2, RegisterV2, ForgotPasswordV2, ResetPasswordV2, TwoStepsV2, VerifyEmailV2, Real Login/Register/Forgot Password

### Stepper Layout (1 screen)
- MUI Stepper component
- 3-step progress tracking
- Step icons with avatars
- Conditional step rendering
- Form data accumulation
- Visual progress indication

**Screens:** RegisterMultiSteps

---

## Feature Comparison Matrix

### Authentication Features

| Feature | Real Login | Real Register | Real Forgot | Demo V1 | Demo V2 | Demo Multi |
|---------|-----------|---------------|-------------|---------|---------|-----------|
| NextAuth.js | Yes | No | No | No | No | No |
| Email/Password | Yes | Yes | Email | Yes | Yes | Yes |
| Form Validation | Yes (Valibot) | No | No | No | No | No |
| OAuth (Google) | Yes | No | No | No | No | No |
| Social Buttons | No | No | No | Optional | Optional | No |
| Responsive Images | Yes | Yes | Yes | Limited | Yes | Yes |
| Theme Support | Yes | Yes | Yes | Limited | Yes | Yes |
| Error Handling | Yes | No | No | No | No | No |
| Loading States | Yes | No | No | No | No | No |

### Form Features

| Feature | Count | Pages |
|---------|-------|-------|
| **Single Field Forms** | 2 | Forgot Password V1/V2 |
| **2-Field Forms** | 2 | Login V1/V2 (with remember me) |
| **3-Field Forms** | 4 | Register V1/V2, Two Steps V1/V2 (OTP) |
| **4-Field Forms** | 4 | Register V1/V2 (with terms), Login Real, Register Real |
| **Multi-Step Forms** | 1 | RegisterMultiSteps (3 steps) |
| **Display-Only** | 2 | Verify Email V1/V2 |
| **OTP Input** | 2 | Two Steps V1/V2 (6-digit) |
| **Password Toggle** | 4 | Login (real), Reset Password V1/V2, Register (real) |

---

## Mobile Responsiveness

### Mobile-First Design
All screens include mobile responsiveness:

**Desktop (lg+):** Full layout with all features
**Tablet (md):** Adaptive layout
**Mobile:** Optimized single-column layout

### Illustration Handling
- **V1 Variants:** Always visible (part of card)
- **V2 Variants:** Hidden on tablet/mobile (useMediaQuery)
- **Real Pages:** Hidden on tablet/mobile
- **Stepper:** Responsive stepper, side illustration hidden on mobile

---

## Theme and Styling Support

### Theme Modes
All screens support:
- **Light Mode:** Light illustrations and background
- **Dark Mode:** Dark illustrations and background
- **Bordered Variant:** Alternative bordered skin for select screens

### Image Variants
Screens with responsive images include:
- Light illustration
- Dark illustration
- Bordered light variant (optional)
- Bordered dark variant (optional)
- Background mask (light/dark)

**Screens with Images:**
- Real Login, Register, Forgot Password
- LoginV2, RegisterV2, ForgotPasswordV2, ResetPasswordV2
- TwoStepsV2, VerifyEmailV2
- RegisterMultiSteps

---

## Route Organization

### Production Routes
`
src/app/[lang]/(blank-layout-pages)/(guest-only)/
  ├── login/page.tsx → Login.tsx
  ├── register/page.tsx → Register.tsx
  └── forgot-password/page.tsx → ForgotPassword.tsx
`

### Demo Routes
`
src/app/[lang]/(blank-layout-pages)/pages/auth/
  ├── login-v1/page.tsx → LoginV1.tsx
  ├── login-v2/page.tsx → LoginV2.tsx
  ├── register-v1/page.tsx → RegisterV1.tsx
  ├── register-v2/page.tsx → RegisterV2.tsx
  ├── forgot-password-v1/page.tsx → ForgotPasswordV1.tsx
  ├── forgot-password-v2/page.tsx → ForgotPasswordV2.tsx
  ├── reset-password-v1/page.tsx → ResetPasswordV1.tsx
  ├── reset-password-v2/page.tsx → ResetPasswordV2.tsx
  ├── two-steps-v1/page.tsx → TwoStepsV1.tsx
  ├── two-steps-v2/page.tsx → TwoStepsV2.tsx
  ├── verify-email-v1/page.tsx → VerifyEmailV1.tsx
  ├── verify-email-v2/page.tsx → VerifyEmailV2.tsx
  └── register-multi-steps/page.tsx → index.tsx
`

---

## Component Dependencies Summary

### Shared Components
- **Logo** - Application logo (all screens)
- **CustomTextField** - MUI TextField wrapper (form inputs)
- **DirectionalIcon** - RTL-aware icon component (back links)
- **AuthIllustrationWrapper** - Decorative wrapper (V1 variants)

### MUI Components
- Typography, Button, Card, CardContent
- TextField, Checkbox, FormControlLabel
- IconButton, InputAdornment, Divider
- Stepper, Step, StepLabel (RegisterMultiSteps)

### Third-Party Libraries
- **react-hook-form** - Form management (real pages)
- **valibot** - Schema validation (real pages)
- **input-otp** - OTP input (Two Steps variants)
- **next-auth** - Authentication (real login)

### Styling
- **Tailwind CSS** - Layout and utilities
- **MUI styled()** - Component customization
- **CSS Modules** - inputOtp.module.css (OTP styling)

---

## Navigation Map

### Inter-Page Links

**From Login (Real/V1/V2):**
- Register: /register or /pages/auth/register-v1|v2
- Forgot Password: /forgot-password or /pages/auth/forgot-password-v1|v2
- Home: Logo link

**From Register (Real/V1/V2):**
- Login: /login or /pages/auth/login-v1|v2
- Terms & Privacy: External links

**From Forgot Password (Real/V1/V2):**
- Login: /login or /pages/auth/login-v1|v2
- Home: Logo link

**From Reset Password (V1/V2):**
- Login: /pages/auth/login-v1|v2
- Home: Logo link

**From Two Steps (V1/V2):**
- Resend: Optional link
- Home: Logo link

**From Verify Email (V1/V2):**
- Skip: Optional
- Resend: Optional link
- Home: Logo link

---

## Next Steps

- Refer to individual page snapshots for detailed implementation
- Check component documentation for usage patterns
- Review form implementation guide for validation
- See styling guide for theme customization
