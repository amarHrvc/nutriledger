# Front-Pages Domain Source Structure

## Overview

The front-pages domain encompasses all public-facing, unauthenticated pages of the Vuexy Admin application. These pages serve as marketing, informational, and onboarding entry points before user authentication. The domain includes landing pages, pricing pages, help centers, payment processing, and checkout flows.

**Key characteristics:**
- Public pages accessible without authentication
- Server Component architecture for data orchestration
- Separate layout system from authenticated dashboard
- Metadata and SEO optimization
- Marketing-focused page designs
- Dynamic data loading via server actions
- Responsive layouts with MUI and Tailwind
- Theme mode support (dark/light)

---

## Page Structure

### Front-Pages Directory Layout

```
/src/app/front-pages/
├── layout.tsx                 # Root layout for all front pages
├── landing-page/
│   └── page.tsx              # Landing page
├── pricing/
│   └── page.tsx              # Pricing plans page
├── help-center/
│   ├── article/
│   │   └── [article-id]/page.tsx  # Individual help articles
│   └── page.tsx              # Help center main page
├── checkout/
│   └── page.tsx              # Checkout flow
└── payment/
    └── page.tsx              # Payment processing
```

### Front-Pages Root Layout (`/src/app/front-pages/layout.tsx`)

**Type:** Server Component (async)

Serves as the unified layout for all front-page routes:

**Key Features:**
- System mode detection via `getSystemMode()`
- InitColorSchemeScript for CSS theme initialization
- IntersectionProvider for scroll observation
- FrontLayout wrapper component
- ScrollToTop button component
- Global CSS and icon imports

**Component Structure:**
```typescript
<html>
  <body>
    <InitColorSchemeScript defaultMode={systemMode} />
    <Providers direction='ltr'>
      <BlankLayout systemMode={systemMode}>
        <IntersectionProvider>
          <FrontLayout>
            {children}
            <ScrollToTop />
          </FrontLayout>
        </IntersectionProvider>
      </BlankLayout>
    </Providers>
  </body>
</html>
```

**Imports:**
- BlankLayout: Minimalist layout (no sidebar, no dashboard chrome)
- FrontLayout: Navigation header + footer
- ScrollToTop: Floating button for scroll-to-top functionality

**Metadata:**
```typescript
{
  title: 'Vuexy - MUI Next.js Admin Dashboard Template',
  description: 'Vuexy - MUI Next.js Admin Dashboard Template - is the most developer friendly & highly customizable Admin Dashboard Template based on MUI v5.'
}
```

---

## Front-Pages Layout Component

Location: `/src/components/layout/front-pages/`

### Layout Structure

**Main Component:** `index.tsx` - FrontLayout wrapper

**Sub-components:**
1. **Header.tsx** - Navigation header with logo, menu, mode toggle
2. **FrontMenu.tsx** - Navigation menu with responsive drawer
3. **Footer.tsx** - Footer with links and copyright
4. **DropdownMenu.tsx** - Dropdown menu for navigation items
5. **styles.module.css** - Layout-specific styles

### Header Component (`Header.tsx`)

**Type:** Client Component (`'use client'`)

**Props:**
```typescript
interface HeaderProps {
  mode: Mode  // 'light' | 'dark'
}
```

**Features:**
- Responsive navigation (toggle drawer on mobile)
- Logo linking to landing page
- ModeDropdown for theme switching
- Scroll trigger detection (changes header style on scroll)
- Mobile drawer menu
- useMediaQuery for responsive behavior
- useScrollTrigger from MUI

**Responsive Behavior:**
- Desktop (lg+): Full horizontal menu with mode dropdown
- Mobile (below lg): Hamburger menu with drawer

**Scroll Detection:**
```typescript
const trigger = useScrollTrigger({
  threshold: 0,
  disableHysteresis: true
})
// Applies 'headerScrolled' class when scrolling detected
```

### Footer Component (`Footer.tsx`)

Standard footer with:
- Company information
- Quick links
- Social media links
- Copyright notice

---

## Front-Pages Views

Location: `/src/views/front-pages/`

### Landing Page (`landing-page/`)

**Page Route:** `/front-pages/landing-page`

**Components:**
1. **HeroSection.tsx** - Main banner with CTA
2. **ProductStat.tsx** - Product statistics/metrics showcase
3. **UsefulFeature.tsx** - Feature highlights
4. **Pricing.tsx** - Pricing plans teaser
5. **OurTeam.tsx** - Team member showcase
6. **CustomerReviews.tsx** - Testimonials/reviews section
7. **ContactUs.tsx** - Contact form
8. **Faqs.tsx** - Frequently asked questions
9. **GetStarted.tsx** - Call-to-action section

**Page Component:**
```typescript
// Async server component
const LandingPage = async () => {
  const mode = await getServerMode()
  return <LandingPageWrapper mode={mode} />
}
```

**Page Structure:**
- Full-width hero banner with gradient
- Feature showcase with grid layout
- Pricing comparison section
- Social proof (testimonials)
- Team showcase
- FAQ accordion
- Contact form
- CTA button for signup/login

**Data Flow:**
- Mode from server (dark/light)
- Static content (no backend data loading)
- Form submissions handled via client-side handlers

### Pricing Page (`pricing/`)

**Page Route:** `/front-pages/pricing`

**Components:**
1. **PricingSection.tsx** - Page header/title
2. **Plans.tsx** - Pricing plan cards (multiple tiers)
3. **FreeTrial.tsx** - Free trial promotion
4. **Faqs.tsx** - Pricing-specific FAQs

**Page Component:**
```typescript
const PricingPage = async () => {
  const data = await getPricingData()  // Server action
  return <PricingWrapper data={data} />
}
```

**Data Requirements:**
```typescript
// Server action: getPricingData()
type PricingData = {
  plans: PricingPlan[]
  features: Feature[]
  trial: TrialOffer
}

type PricingPlan = {
  id: string
  name: string
  price: number
  currency: string
  billingPeriod: 'month' | 'year'
  description: string
  features: string[]
  cta: {
    text: string
    href: string
  }
  highlighted?: boolean
}
```

**Plans Component Features:**
- Multiple pricing tiers (e.g., Starter, Professional, Enterprise)
- Feature comparison per tier
- CTA button for each plan
- Highlighted "popular" plan option
- Responsive card grid

**Free Trial Section:**
- Trial duration display
- Trial CTA button
- No credit card required messaging

---

### Help Center (`help-center/`)

**Page Routes:**
- `/front-pages/help-center` - Main help center hub
- `/front-pages/help-center/article/[article-id]` - Individual articles

**Components:**
1. **HelpCenterWrapper** - Main hub component
   - Search bar
   - Category/article listing
   - Featured articles

**Article Structure:**
```typescript
type HelpArticle = {
  id: string
  title: string
  category: string
  content: string
  keywords: string[]
  relatedArticles: HelpArticle[]
}
```

**Features:**
- Category-based browsing
- Search functionality
- Related articles sidebar
- Breadcrumb navigation
- Last updated timestamp

---

### Checkout Page (`checkout/`)

**Page Route:** `/app/front-pages/checkout`

**Component:** `CheckoutPage.tsx`

**Features:**
- Product selection
- Cart management
- Billing information form
- Order summary
- Payment method selection

**Data Integration:**
- Cart data from context or props
- Product information from backend
- Pricing calculation

---

### Payment Page (`payment/`)

**Page Route:** `/app/front-pages/payment`

**Component:** `Payment.tsx`

**Features:**
- Payment form (credit card, digital wallets)
- Order confirmation
- Invoice generation
- Receipt handling

---

## Routing & Navigation

### Public Routes (No Auth Required)

```
/front-pages/landing-page    → Landing page
/front-pages/pricing         → Pricing plans
/front-pages/help-center     → Help center hub
/front-pages/help-center/article/[id]  → Help articles
/front-pages/checkout        → Checkout flow
/front-pages/payment         → Payment processing
```

### Route Characteristics

- **No authentication required** - Accessible without login
- **Public SEO pages** - Included in sitemap, indexed by search
- **Separate layout** - Different from authenticated dashboard
- **Marketing optimized** - Call-to-action buttons, conversions

### Navigation Patterns

**Front Navigation Menu:**
- Link to landing page
- Link to pricing
- Link to help center
- Login/signup buttons
- Theme mode toggle

**Footer Navigation:**
- Links to help center articles
- Links to pricing page
- Link to landing page
- Company/legal links

---

## Server Actions & Data Flow

### Server Actions Used

#### `getServerMode()`
- Retrieves current theme mode (light/dark)
- Used in layout for InitColorSchemeScript
- Used in header for theme dropdown

**Location:** `@core/utils/serverHelpers`

#### `getPricingData()`
- Fetches pricing plans and features
- Called in pricing page server component
- Returns structured pricing data

**Location:** `@/app/server/actions`

**Return Type:**
```typescript
interface PricingData {
  plans: PricingPlan[]
  features: Feature[]
  trial: TrialOffer
}
```

### Data Caching

- Static pages (landing, pricing) cached at build time
- Help center articles cacheable with ISR (Incremental Static Regeneration)
- Checkout/payment pages dynamic (no caching)

---

## Metadata & SEO

### Page Metadata

**Landing Page:**
```typescript
{
  title: 'Vuexy Admin Dashboard - Home',
  description: 'Modern, responsive admin dashboard template...',
  openGraph: {
    title: 'Vuexy Admin Dashboard',
    description: '...',
    url: 'https://vuexy.com',
    type: 'website'
  }
}
```

**Pricing Page:**
```typescript
{
  title: 'Pricing - Vuexy Admin Dashboard',
  description: 'Choose the perfect plan for your needs...'
}
```

**Help Center:**
```typescript
{
  title: '[Article Title] - Help Center | Vuexy',
  description: '[Article excerpt]'
}
```

### SEO Elements

- Page titles with keywords
- Meta descriptions for search results
- Open Graph tags for social sharing
- Canonical URLs
- Structured data (schema.org)
- Mobile-friendly responsive design

---

## Component Patterns

### 1. Server Component Page Pattern

```typescript
// async page component with data fetching
const FrontPage = async () => {
  const mode = await getServerMode()
  const data = await fetchPageData()

  return <PageWrapper mode={mode} data={data} />
}
```

### 2. Client Component Wrapper Pattern

```typescript
// wrapper delegates to client component for interactivity
'use client'

const PageWrapper = ({ mode, data }: Props) => {
  return (
    <div>
      <Header mode={mode} />
      <Content data={data} />
      <Footer />
    </div>
  )
}
```

### 3. Responsive Card Grid Pattern

```typescript
// Pricing/feature cards in responsive grid
<Grid container spacing={6}>
  {plans.map(plan => (
    <Grid size={{ xs: 12, md: 6, lg: 4 }} key={plan.id}>
      <PricingCard plan={plan} />
    </Grid>
  ))}
</Grid>
```

### 4. Form with Action Pattern

```typescript
// Contact form with server action
const handleSubmit = async (formData: FormData) => {
  const result = await submitContactForm(formData)
  // Handle result
}

<form action={handleSubmit}>
  <input name="email" required />
  <textarea name="message" required />
  <button type="submit">Send</button>
</form>
```

### 5. Modal/Dialog Pattern

```typescript
// Modal for checkout, login, etc.
const [open, setOpen] = useState(false)

return (
  <>
    <Button onClick={() => setOpen(true)}>Get Started</Button>
    <Dialog open={open} onClose={() => setOpen(false)}>
      {/* Modal content */}
    </Dialog>
  </>
)
```

---

## Styling Approach

### CSS Framework

- **Tailwind CSS** - Utility-first styling
- **MUI Material** - Component library and theming
- **CSS Modules** - Scoped styles for layout components

### Key CSS Classes

**Layout utilities:**
- `frontLayoutClasses.header` - Header container
- `frontLayoutClasses.navbar` - Navigation bar
- `frontLayoutClasses.navbarContent` - Nav content wrapper
- `frontLayoutClasses.footer` - Footer container

**Common utilities:**
- `is-full` - Full width (inline-size)
- `bs-full` - Full height (block-size)
- `flex`, `flex-col`, `flex-auto` - Flexbox
- `gap-2`, `gap-4`, `gap-6` - Spacing
- `rounded-full` - Rounded corners

### Dark Mode Support

- Managed via CSS variables
- Theme detection via `getSystemMode()`
- InitColorSchemeScript handles theme initialization
- Components use CSS custom properties for colors

---

## Type Definitions

### Front-Page Types

```typescript
// Mode type
type Mode = 'light' | 'dark' | 'system'

// Pricing types
interface PricingPlan {
  id: string
  name: string
  price: number
  currency: string
  billingPeriod: 'month' | 'year'
  description: string
  features: string[]
  cta: {
    text: string
    href: string
  }
  highlighted?: boolean
}

// Help article types
interface HelpArticle {
  id: string
  title: string
  category: string
  content: string
  keywords: string[]
  relatedArticles: HelpArticle[]
  publishedAt: Date
  updatedAt: Date
}

// Form submission types
interface ContactFormData {
  name: string
  email: string
  subject: string
  message: string
}

interface CheckoutData {
  items: CartItem[]
  subtotal: number
  tax: number
  total: number
  shippingAddress: Address
  billingAddress: Address
}
```

---

## Authentication Integration

### Public vs Authenticated

**Public Pages (No Auth):**
- Landing page
- Pricing page
- Help center
- Contact page

**Requires Authentication After:**
- Clicking "Get Started" → Sign up/login modal
- Clicking "Start Trial" → Sign up form
- After purchase → Redirect to dashboard login

### Login/Signup Redirects

```typescript
// CTA buttons redirect to auth pages
const handleGetStarted = () => {
  router.push('/auth/login?redirect=/dashboard')
}

const handleSignup = () => {
  router.push('/auth/register')
}
```

---

## Dependency Map

### External Dependencies
- **React 18+** - Functional components, hooks
- **Next.js 14+** - Server components, dynamic routes, metadata
- **@mui/material** - UI components (Button, Grid, Dialog, etc.)
- **Tailwind CSS** - Utility styling

### Internal Dependencies
- `@core/utils/serverHelpers` - getServerMode()
- `@/app/server/actions` - Server actions (getPricingData, etc.)
- `@components/layout/front-pages` - Header, footer, layout
- `@layouts/BlankLayout` - Minimal layout wrapper
- `@core/components/scroll-to-top` - Scroll-to-top button
- `@core/types` - Type definitions (Mode, etc.)

---

## Code Examples

### Creating a New Front Page

```typescript
// 1. Create page file
// /src/app/front-pages/my-page/page.tsx

import { getServerMode } from '@core/utils/serverHelpers'
import MyPageWrapper from '@views/front-pages/my-page'

const MyPage = async () => {
  const mode = await getServerMode()
  
  return <MyPageWrapper mode={mode} />
}

export const metadata = {
  title: 'My Page - Vuexy',
  description: 'Description for my page'
}

export default MyPage
```

### Creating a Pricing Card Component

```typescript
'use client'

import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Typography from '@mui/material/Typography'
import type { PricingPlan } from '@/types'

interface PricingCardProps {
  plan: PricingPlan
  highlighted?: boolean
}

const PricingCard = ({ plan, highlighted }: PricingCardProps) => {
  return (
    <Card 
      variant={highlighted ? 'elevation' : 'outlined'}
      sx={{ height: '100%' }}
    >
      <CardContent>
        <Typography variant="h5">{plan.name}</Typography>
        <Typography variant="h3" sx={{ my: 2 }}>
          ${plan.price}
          <Typography component="span" variant="body2">
            /{plan.billingPeriod}
          </Typography>
        </Typography>
        
        <Typography variant="body2" color="textSecondary" sx={{ mb: 3 }}>
          {plan.description}
        </Typography>

        <ul sx={{ mb: 3 }}>
          {plan.features.map(feature => (
            <li key={feature}>{feature}</li>
          ))}
        </ul>

        <Button variant="contained" fullWidth href={plan.cta.href}>
          {plan.cta.text}
        </Button>
      </CardContent>
    </Card>
  )
}

export default PricingCard
```

### Creating a Contact Form

```typescript
'use client'

import { submitContactForm } from '@/app/server/actions'
import Button from '@mui/material/Button'
import TextField from '@mui/material/TextField'
import { useState } from 'react'
import type { ContactFormData } from '@/types'

const ContactForm = () => {
  const [loading, setLoading] = useState(false)
  const [submitted, setSubmitted] = useState(false)

  const handleSubmit = async (formData: FormData) => {
    setLoading(true)
    try {
      const result = await submitContactForm(
        Object.fromEntries(formData) as ContactFormData
      )
      if (result.success) {
        setSubmitted(true)
      }
    } finally {
      setLoading(false)
    }
  }

  if (submitted) {
    return <Typography>Thank you for contacting us!</Typography>
  }

  return (
    <form action={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
      <TextField
        name="name"
        label="Full Name"
        required
        fullWidth
      />
      <TextField
        name="email"
        label="Email"
        type="email"
        required
        fullWidth
      />
      <TextField
        name="message"
        label="Message"
        multiline
        rows={4}
        required
        fullWidth
      />
      <Button
        type="submit"
        variant="contained"
        disabled={loading}
      >
        {loading ? 'Sending...' : 'Send Message'}
      </Button>
    </form>
  )
}

export default ContactForm
```

---

## Summary

The front-pages domain provides:

- **Landing Page** - Hero, features, testimonials, contact, CTAs
- **Pricing Page** - Plan selection, comparison, free trial offer
- **Help Center** - Knowledge base, articles, search
- **Checkout & Payment** - Purchase flow, order management
- **Unified Layout** - Header, footer, theme support
- **SEO Optimization** - Metadata, schema, structured data
- **Public Access** - No authentication required
- **Server-Side Rendering** - Fast initial load, data pre-fetching
- **Responsive Design** - Mobile-first, adaptive layouts
- **Theme Integration** - Dark/light mode support

**Key Statistics:**
- 6+ main public pages
- 20+ page components
- 100+ sub-components
- ~5,000 LOC front-pages views
- Full responsive design
- Server action integration
- Database-driven pricing data

**Benefits:**
- **Marketing optimized** - Conversion-focused design
- **SEO friendly** - Meta tags, structured data
- **Fast loading** - Server-side rendering, static generation
- **Accessible** - Semantic HTML, ARIA support
- **Themeable** - Dark/light mode, CSS variables
- **Scalable** - Easy to add new pages and sections
