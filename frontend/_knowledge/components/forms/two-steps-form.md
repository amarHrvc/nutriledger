# Two-Steps/OTP Form Component

Two-factor authentication form component with 6-digit OTP input and verification flow.

---

## Overview

**Component:** Two-Steps Form / OTP Form  
**Type:** Form Component (Client Component with 'use client')  
**Purpose:** Verify user identity with one-time password (OTP)  
**Framework:** React + input-otp library  
**Status:** Demo/Showcase Implementation

---

## Props Interface

interface TwoStepsFormProps {
  mode?: SystemMode // Optional theme mode for responsive images
}

### Prop Descriptions
- **mode:** Optional system theme mode for image/styling variations

---

## Form Fields

### OTP Input Field
- **Label:** Type your 6 digit security code
- **Type:** OTP Input (6 digits)
- **Placeholder:** Displays as 6 empty input slots
- **Validation:**
  - Required: All 6 digits must be entered
  - Format: Numeric only
- **Features:**
  - input-otp library integration
  - Custom slot rendering with CSS module
  - Fake caret visual indicator
  - Automatic focus management
  - Numeric-only input validation
  - Max length: 6 characters

### OTP Slot Component
- **Visual Design:** Individual digit input slots
- **Active State:** Highlighted when focused
- **Caret Indicator:** Visual cursor in active slot
- **Spacing:** Gap between slots for clarity
- **Style:** CSS module styling (inputOtp.module.css)

### Additional Display Elements

#### Phone Number Display (Masked)
- **Format:** ******1234
- **Style:** Monospace font, medium weight
- **Purpose:** Show which phone number received code
- **Label:** "We sent a verification code to your mobile"

#### Instruction Text
- **Text:** "Type your 6 digit security code"
- **Style:** Body text, secondary color
- **Purpose:** Guide user on what to enter

---

## Form Validation

### Validation Approach
- Real-time numeric validation
- Input-otp library handles validation
- All 6 digits required before submission
- Automatic slot progression

### Validation Rules

| Element | Rules | Error Handling |
|---------|-------|----------------|
| **OTP Input** | 6 digits required, numeric only | Input-otp prevents non-numeric chars |
| **Form Submission** | All slots must be filled | Submit button disabled until filled |

### Validation Flow
1. User types digit in first slot
2. input-otp auto-advances to next slot
3. Process repeats for all 6 digits
4. Final digit triggers submission readiness
5. Submit button becomes enabled

---

## Form Submission

### Submission Flow
1. User receives OTP via SMS/Mobile
2. User enters 6-digit code into form
3. Automatic slot progression as digits entered
4. User clicks Verify Account button (or auto-submits)
5. Form sends OTP to backend verification endpoint
6. Backend validates OTP matches
7. Account verified on success
8. Redirect to dashboard or next step

### Submit Button
- **Label:** Verify my account
- **Type:** Submit button
- **Features:**
  - Full width button
  - Contained variant (primary color)
  - Submit type triggers verification
  - Disabled until all 6 digits entered

---

## State Management

### Component State

otp: string or null - Stores entered OTP value
isVerifying: boolean - Loading state during submission
error: string or null - Error message display

### State Updates
- OTP input onChange handler updates value
- Submit handler triggers verification
- Error state displays feedback
- Success state triggers redirect

### Form State
- Managed via input-otp library
- Automatic validation and focus management
- No external form library needed

---

## Hooks and Libraries

### React Hooks
- useState(): OTP state, loading, error
- useParams(): Get locale for localization
- useRouter(): Navigation after verification
- useTheme(): Theme access
- useMediaQuery(): Responsive checks

### OTP Library
- input-otp: OTPInput component and Slot management
- Custom slot rendering with props
- Automatic numeric validation
- Focus management between slots

### UI Components
- @mui/material: Button, Typography, Card, etc.
- CustomSlot: Custom styled OTP slot component
- FakeCaret: Visual caret indicator
- Card (V1 variant): Container component

### Custom Components
- AuthIllustrationWrapper: Decorative wrapper (V1 variant)
- Logo: Application logo

### Custom Hooks
- useImageVariant(): Mode-aware image selection
- useSettings(): Get skin setting

---

## Features

### 6-Digit OTP Input
- Input-otp library integration
- Six individual slot components
- Custom styling with CSS module
- Automatic slot advancement
- Numeric validation
- Flexible slot rendering

### Slot Component
- Custom Slot function with SlotProps
- Active state styling
- Fake caret indicator
- Character display
- Gap spacing between slots

### Fake Caret
- Visual indicator in active slot
- Animated or static display
- Vertical line styling
- Text primary color
- Height: 20 pixels

### Masked Phone Number
- Format: ******1234
- Display font-medium styling
- Primary text color
- Context: "We sent a verification code to..."

### Resend Code Option
- "Didn't get the code?" text
- "Resend" link (non-functional in demo)
- Could implement resend cooldown timer
- Rate limiting for security

### Responsive Design
- Mobile: Full-width OTP input
- Tablet: Optimized spacing
- Desktop: Standard form layout
- Slots adapt to screen size

---

## Navigation Elements

### Links
- **Logo:** Home page - Navigate away
- **Resend:** Resend OTP code option
- **Verify my account:** Submit verification

### Success Navigation
- Redirect to dashboard on verification success
- Redirect to next step (confirm password, etc.)
- Localization-aware navigation

---

## Accessibility Features

### ARIA Attributes
- Form inputs have proper labels
- Button has descriptive text
- Instructions clear and visible
- Error messages associated with input

### Keyboard Navigation
- Tab to first OTP slot
- Type digits (auto-advances between slots)
- Tab to Verify button
- Enter key submits form
- Tab to Resend link

### Semantic HTML
- Proper form element
- Number input type (numeric validation)
- Semantic button element
- Link elements for navigation

---

## Variants

### TwoStepsV1
- Card-based centered layout (450px max-width)
- AuthIllustrationWrapper for decoration
- Mobile: Full-width, no illustration
- Simple OTP form

### TwoStepsV2
- Split layout (illustration left, form right)
- Responsive images (light/dark)
- Illustration hidden on tablet/mobile
- Full-width form on mobile
- Theme-aware background masks

---

## Component Dependencies

### Shared Components
- Logo - Application logo
- AuthIllustrationWrapper - Decorative wrapper (V1)

### OTP Libraries
- input-otp: OTPInput component
- Custom Slot component wrapper
- FakeCaret component for visual feedback

### MUI Components
- Card, CardContent (V1 variant)
- Typography - Text, labels, instructions
- Button - Form submission
- InputAdornment - Icon positioning (if used)

### Styling
- CSS Module: @/libs/styles/inputOtp.module.css
- Tailwind CSS: Utility classes
- MUI styled: Component styling

---

## Styling

### OTP Slot Styling

**CSS Module Classes:**
- slot: Base slot styling
- slotActive: Highlight when focused
- fakeCaret: Animated caret indicator

**Visual Design:**
- Border: Input-like borders
- Padding: Adequate spacing
- Min-width: Ensure slot visibility
- Text alignment: Center digit
- Font: Monospace for numbers

### Layout
- Tailwind CSS for overall layout
- MUI components for form controls
- Responsive padding and margins
- Gap between slots for clarity

### Theme Support
- Dark/light mode via useTheme()
- useImageVariant for theme-aware images
- Primary colors from MUI theme

---

## API Integration

### Backend Endpoint
- POST /api/auth/verify-otp
- Request body: { otp: string (6 digits) }
- Response: { success: boolean, message: string }

### OTP Delivery
- Sent via SMS to verified phone number
- 6-digit numeric code
- Time-limited validity (usually 5-10 minutes)
- Resend option with rate limiting

### Error Handling
- Invalid OTP: Show error message
- Expired OTP: Prompt for resend
- Rate limit exceeded: Show cooldown message
- Server error: Display friendly message

---

## Security Considerations

- OTP sent via secure SMS channel
- Time-limited validity (expires quickly)
- Rate limiting on verification attempts
- Resend attempt limiting
- No OTP logging or display in logs
- HTTPS form submission only
- Session validation before verification

---

## Performance Considerations

- input-otp library: Lightweight (~5KB)
- CSS module styling: Optimized classes
- No external form validation library
- Minimal JavaScript overhead
- Fast slot advancement
- Responsive input handling
- No unnecessary re-renders

---

## Dependencies

### NPM Packages
- input-otp - OTP input component
- @mui/material - UI components
- classnames - Conditional CSS classes

### CSS
- inputOtp.module.css - Custom OTP styling

### Next.js APIs
- next/navigation - useParams, useRouter
- next/link - Navigation

### Custom Utilities
- useImageVariant() - Image mode selection
- useSettings() - Get application settings

---

## Error States

### Validation Errors
- Incomplete OTP: Prevents submission
- Invalid digits: input-otp prevents non-numeric
- Expired OTP: Shows resend prompt
- Wrong OTP: Shows "Invalid code" message

### Recovery Options
- Resend OTP: Request new code
- Try again: Clear form and retry
- Contact support: If repeated failures

---

## Success States

### Verification Success
- Clear success message
- Auto-redirect to next step
- Account marked as verified
- Session established
- Confirmation display (optional)

---

## Testing Considerations

### Unit Tests
- OTP slot rendering
- Numeric validation
- Slot advancement
- Form state management

### Integration Tests
- Full OTP input and submission
- Backend verification API call
- Error handling and retry
- Resend OTP flow
- Redirect after success

### E2E Tests
- Complete 2FA verification flow
- Timeout/expiration scenarios
- Multiple resend attempts
- Rate limiting verification
- Success redirect confirmation

---

## Future Enhancements

- Resend cooldown timer
- Backup verification methods
- QR code for authenticator apps
- SMS and email OTP options
- Biometric fallback
- Security token integration
