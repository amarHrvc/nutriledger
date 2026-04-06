# Demo Auth Pages: Register (V1, V2, and Multi-Steps)

## Overview
Demo showcase pages for registration forms with three different variants: Card-based (V1), Split layout (V2), and Multi-step stepper (RegisterMultiSteps).

---

## RegisterV1: Card-Based Layout

**Location:** src/views/pages/auth/RegisterV1.tsx  
**Route:** src/app/[lang]/(blank-layout-pages)/pages/auth/register-v1/page.tsx  
**Type:** Client Component  
**Props:** None

### Features
- Centered card-based layout
- AuthIllustrationWrapper for decorative styling
- Multi-field registration form
- Password visibility toggle
- Terms and privacy policy acceptance checkbox
- Social signup buttons (Facebook, Twitter, GitHub, Google)
- Sign-in link to login page
- Responsive: Full width on mobile, max-width 450px on desktop

### Form Fields

| Field | Type | Validation |
|-------|------|-----------|
| **Username** | Text Input | Auto-focus |
| **Email** | Text Input | Standard input |
| **Password** | Password Input | Visibility toggle |
| **Terms** | Checkbox | With policy link |

### Social Sign-Up Buttons
- Facebook: Outlined button with icon
- Twitter: Outlined button with icon
- GitHub: Outlined button with icon
- Google: Outlined button with icon
- All buttons non-functional (demo only)

### Navigation Links
- Terms and Conditions: Prevent default (demo)
- Create Account: /pages/auth/register-v1 (self-link)
- Already have account: /pages/auth/login-v1
- Logo: Home page

### Layout Structure
- Card with centered content (450px max-width on desktop)
- Logo at top center
- Heading: "Adventure starts here 🚀"
- Subheading: "Make your app management easy and fun!"
- Form fields in flex column
- Social buttons after divider
- Sign-in link at bottom

### Styling
- Tailwind CSS (flex, gap, spacing)
- MUI Card, CardContent
- Custom TextField wrapper
- Divider for visual separation
- Icons from Tabler set
- Primary color for links

### State Management
- isPasswordShown: boolean - Password visibility

---

## RegisterV2: Split Layout

**Location:** src/views/pages/auth/RegisterV2.tsx  
**Route:** src/app/[lang]/(blank-layout-pages)/pages/auth/register-v2/page.tsx  
**Type:** Client Component  
**Props:** { mode: SystemMode } - Theme mode for images

### Features
- Full-screen split layout (illustration left, form right)
- Mode-aware responsive images (light/dark/bordered)
- Responsive design (hides illustration on tablet/mobile)
- Multi-field registration form
- Password visibility toggle
- Terms and privacy checkbox with link
- Social signup buttons
- Theme-aware background masks
- Skin setting support

### Form Fields

| Field | Type | Validation |
|-------|------|-----------|
| **Username** | Text Input | Standard input |
| **Email** | Text Input | Standard input |
| **Password** | Password Input | Visibility toggle |
| **Terms** | Checkbox | With policy link |

### Image Sources

**Illustrations:**
- Light: /images/illustrations/auth/v2-register-light.png
- Dark: /images/illustrations/auth/v2-register-dark.png
- Bordered Light: /images/illustrations/auth/v2-register-light-border.png
- Bordered Dark: /images/illustrations/auth/v2-register-dark-border.png

**Background Masks:**
- Light: /images/pages/auth-mask-light.png
- Dark: /images/pages/auth-mask-dark.png

### Layout Structure
- Split view on desktop (illustration + form)
- Illustration hidden on tablet/mobile
- Full-width form on mobile
- Max-width form area

### Hooks Used
- useState() - Password visibility
- useParams() - Get locale
- useTheme() - Theme access
- useMediaQuery() - Responsive (md breakpoint)
- useSettings() - Skin setting
- useImageVariant() - Image selection

---

## RegisterMultiSteps: Stepper-Based Multi-Step Form

**Location:** src/views/pages/auth/register-multi-steps/index.tsx  
**Route:** src/app/[lang]/(blank-layout-pages)/pages/auth/register-multi-steps/page.tsx  
**Type:** Client Component  
**Props:** { mode: SystemMode } - Theme mode

### Overview
Complex multi-step registration form with visual stepper progress indicator and conditional step rendering.

### Step Structure

#### Step 1: Account Details
- **Component:** StepAccountDetails
- **Fields:**
  - Username (required)
  - Email (required)
  - Password (required)
- **Icon:** tabler-file-analytics
- **Subtitle:** "Enter your Account Details"

#### Step 2: Personal Information
- **Component:** StepPersonalInfo
- **Fields:**
  - First Name (required)
  - Last Name (required)
  - Country (dropdown)
  - Language (dropdown)
- **Icon:** tabler-user
- **Subtitle:** "Setup Information"

#### Step 3: Billing Details
- **Component:** StepBillingDetails
- **Fields:**
  - Social Links (multiple)
  - Optional fields for social media
- **Icon:** tabler-credit-card
- **Subtitle:** "Add Social Links"

### Stepper Implementation
- MUI Stepper component with 3 steps
- Horizontal stepper layout
- Custom Step styling with padding
- Step icons with avatar display
- Active step highlighting
- Progress through steps with next/previous buttons

### Features
- Progress tracking across steps
- Conditional rendering of step components
- Next/Previous navigation buttons
- Form validation between steps
- Success state after final step
- Side illustration (responsive)

### Form Management
- React state for active step
- Form data accumulation across steps
- Step completion validation
- Submit on final step

### Layout Structure
- Top: Logo and heading
- Left: Illustration (responsive, hidden on mobile)
- Center: Stepper with step labels
- Right: Current step form
- Bottom: Navigation buttons

### Images
- Light: /images/illustrations/auth/v2-register-light.png
- Dark: /images/illustrations/auth/v2-register-dark.png
- Mask: /images/pages/auth-mask-light.png or dark variant

### Styling
- Stepper with custom step styling
- CustomAvatar for step icons
- MUI Stepper component
- Responsive layout with padding
- Theme-aware colors

### Hooks Used
- useState() - Active step, form data
- useParams() - Get locale
- useTheme() - Theme access
- useMediaQuery() - Responsive checks
- useSettings() - Skin setting
- useImageVariant() - Image selection

---

## Common Features (All Variants)

### Form Behavior
- Non-functional forms (preventDefault)
- Demo/showcase purpose
- No backend integration
- No validation feedback
- Client-side only

### Typography
- V1/V2 Heading: "Adventure starts here 🚀"
- V1/V2 Subheading: "Make your app management easy and fun!"
- MultiStep: Individual step instructions
- Links for policy and terms

### Password Field
- Eye icon for visibility toggle
- Tabler icons: tabler-eye / tabler-eye-off
- Input endAdornment positioning
- Click to toggle visibility

### MUI Components
- Typography - Text content
- Button - Submit and navigation
- Checkbox - Terms acceptance and step navigation
- TextField - Form inputs
- Card (V1) - Container
- Stepper (MultiStep) - Progress display

### Navigation
- Localization-aware URLs
- getLocalizedUrl() utility
- Logo returns to home
- Links between auth demo pages

---

## Comparison Table

| Aspect | V1 | V2 | MultiSteps |
|--------|----|----|-----------|
| **Layout** | Card | Split | Stepper-based |
| **Form Fields** | 4 | 4 | 10+ (across 3 steps) |
| **Illustration** | Decorative wrap | Responsive image | Side image |
| **Props** | None | mode | mode |
| **Steps** | Single | Single | 3 steps with progress |
| **Social Buttons** | Yes | Yes | No |
| **Complexity** | Low | Medium | High |
| **Mobile Experience** | Good | Good | Good with responsive stepper |
| **Visual Feedback** | Minimal | Themed | Stepper progress |

---

## File Organization

### V1
src/app/[lang]/(blank-layout-pages)/pages/auth/register-v1/page.tsx

### V2
src/app/[lang]/(blank-layout-pages)/pages/auth/register-v2/page.tsx

### MultiSteps
src/app/[lang]/(blank-layout-pages)/pages/auth/register-multi-steps/page.tsx  
src/views/pages/auth/register-multi-steps/index.tsx  
src/views/pages/auth/register-multi-steps/StepAccountDetails.tsx  
src/views/pages/auth/register-multi-steps/StepPersonalInfo.tsx  
src/views/pages/auth/register-multi-steps/StepBillingDetails.tsx

---

## Key Patterns

### V1 Pattern
- Simple, self-contained showcase
- Decorative styling wrapper
- Standard form patterns
- Good for basic UI demonstration

### V2 Pattern
- Production-grade responsive design
- Theme and mode awareness
- Responsive image handling
- Skin variant support

### MultiStep Pattern
- Complex form management
- Visual progress indication
- Component composition for steps
- Stepper UI pattern
- Form data accumulation
