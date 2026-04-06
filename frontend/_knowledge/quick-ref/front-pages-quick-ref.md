# Front Pages Quick Reference

## Q&A

### How to Add a Marketing Page?

1. Create folder: `/src/app/front-pages/my-page/`
2. Create `page.tsx` with server component pattern
3. Create view component in `/src/views/front-pages/my-page/`
4. Export metadata object with title/description
5. FrontLayout already handles header/footer

```typescript
// /src/app/front-pages/my-page/page.tsx
import { getServerMode } from '@core/utils/serverHelpers'
import MyPageView from '@views/front-pages/my-page'

const MyPage = async () => {
  const mode = await getServerMode()
  return <MyPageView mode={mode} />
}

export const metadata = {
  title: 'My Page - Vuexy',
  description: 'Page description for SEO'
}

export default MyPage
```

### How to Set Up Landing Page?

Landing page is the main entry point at `/front-pages/landing-page`:
- Server component fetches system mode
- Displays HeroSection, Features, Pricing teaser, Testimonials
- Includes CTAs linking to auth pages
- Fully responsive with MUI Grid + Tailwind

### How to Create Public Page?

Public pages require no authentication:
1. Create under `/src/app/front-pages/[page-name]/`
2. Use `'use client'` for interactive sections only
3. Server component handles data fetching
4. Leverage BlankLayout (no sidebar)
5. Add metadata for SEO

### How to Set Up SEO Metadata?

Each page exports `metadata` object:

```typescript
export const metadata = {
  title: 'Page Title - Vuexy',
  description: 'Clear description under 160 chars',
  openGraph: {
    title: 'OG Title',
    description: 'OG description',
    url: 'https://vuexy.com/page',
    type: 'website'
  },
  canonical: 'https://vuexy.com/page'
}
```

---

## Types

### `PageMetaType`

```typescript
interface PageMetadata {
  title: string
  description: string
  openGraph?: {
    title: string
    description: string
    url: string
    type: string
    image?: string
  }
  canonical?: string
  keywords?: string[]
}
```

### `RouteType`

```typescript
type PublicRoute = 
  | '/front-pages/landing-page'
  | '/front-pages/pricing'
  | '/front-pages/help-center'
  | '/front-pages/help-center/article/[article-id]'
  | '/front-pages/checkout'
  | '/front-pages/payment'

type Mode = 'light' | 'dark' | 'system'

interface PricingPlan {
  id: string
  name: string
  price: number
  currency: string
  billingPeriod: 'month' | 'year'
  description: string
  features: string[]
  cta: { text: string; href: string }
  highlighted?: boolean
}

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
```

---

## Page Map - All Public Pages

| Route | Component | Purpose | Auth |
|-------|-----------|---------|------|
| `/front-pages/landing-page` | LandingPageView | Hero, features, testimonials | No |
| `/front-pages/pricing` | PricingView | Plan selection, comparison | No |
| `/front-pages/help-center` | HelpCenterView | Knowledge base hub | No |
| `/front-pages/help-center/article/[id]` | ArticleView | Individual articles | No |
| `/front-pages/checkout` | CheckoutView | Cart & billing | No |
| `/front-pages/payment` | PaymentView | Payment processing | No |

**Layout Structure:**
```
/src/app/front-pages/
├── layout.tsx              # Root (header, footer, theme)
├── landing-page/page.tsx
├── pricing/page.tsx
├── help-center/page.tsx
├── help-center/article/[id]/page.tsx
├── checkout/page.tsx
└── payment/page.tsx
```

---

## Code Snippets

### Page Component Template

```typescript
'use client'

import type { Mode } from '@/types'

interface PageProps {
  mode: Mode
}

const PageComponent = ({ mode }: PageProps) => {
  return (
    <div className="min-h-screen">
      {/* Content */}
    </div>
  )
}

export default PageComponent
```

### Metadata Setup

```typescript
import type { Metadata } from 'next'

export const metadata: Metadata = {
  title: 'Page Title - Vuexy',
  description: 'Concise description',
  openGraph: {
    title: 'OG Title',
    description: 'OG description',
    url: 'https://vuexy.com/page',
    type: 'website'
  }
}

const Page = async () => {
  return <PageView />
}

export default Page
```

### Public Route Pattern

```typescript
import { getServerMode } from '@core/utils/serverHelpers'
import PageView from '@views/front-pages/page-name'

const Page = async () => {
  const mode = await getServerMode()
  return <PageView mode={mode} />
}

export const metadata = {
  title: 'Page - Vuexy',
  description: 'Description'
}

export default Page
```

### Form Server Action

```typescript
'use server'

export async function submitContactForm(data: ContactFormData) {
  // Validate, process, send email
  return { success: true, message: 'Form submitted' }
}
```

---

## Key Layout Components

- **FrontLayout** - Header + footer wrapper
- **BlankLayout** - Minimal layout (no sidebar)
- **InitColorSchemeScript** - Theme initialization
- **Header.tsx** - Nav, logo, theme toggle
- **Footer.tsx** - Links, copyright

---

## Key Dependencies

- `@core/utils/serverHelpers` → getServerMode()
- `@components/layout/front-pages` → Header, Footer, FrontLayout
- `@layouts/BlankLayout` → Minimal layout
- `@core/components/scroll-to-top` → ScrollToTop button
- `@mui/material` → Button, Card, Grid, Dialog
- `tailwindcss` → Utility CSS

**All front-pages routes are public - no authentication required.**