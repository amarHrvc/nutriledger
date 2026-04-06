# Utilities Quick Reference

## Common Questions

**Q: How do I format a date for display?**
- Read: ../source-analysis/misc-utilities-domain.md (Utility Functions section)
- Key: Use `formatDate()` utility or native `Intl.DateTimeFormat`
- Time: 2 min
- Quick: `formatDate(new Date(), 'MM/dd/yyyy')` → "01/15/2024"

**Q: How do I generate user initials from a name?**
- Read: ../source-analysis/misc-utilities-domain.md (Text Processing section)
- Key: Use `getInitials()` pure function
- Time: 1 min
- Quick: `getInitials('John Doe')` → "JD"

**Q: How do I detect and use theme mode (light/dark)?**
- Read: ../source-analysis/misc-utilities-domain.md (Server-Side Helpers section)
- Key: Server: `getServerMode()`, Client: `useSettings()` hook
- Time: 3 min
- Setup: Server: `const mode = await getServerMode()`, Client: `const { settings } = useSettings()`

**Q: How do I access custom hooks for common tasks?**
- Read: ../08-components.md (Core Hooks section)
- Key: `useSettings`, `useImageVariant`, `useIntersection`, `useObjectCookie`
- Time: 5 min
- Common: `const { settings, updateSettings } = useSettings()`

**Q: How do I handle form submissions and validation?**
- Read: ../source-analysis/misc-utilities-domain.md (Form Components section)
- Key: Use `Form` component + React Hook Form + Valibot
- Time: 10 min
- Pattern: Wrap with `<Form>`, use `useForm()`, provide `onSubmit` handler

---

## Essential Utilities

| Utility | Location | Purpose | Usage |
|---------|----------|---------|-------|
| **formatDate** | `@utils/` | Format dates to string | `formatDate(date, 'MM/dd/yyyy')` |
| **getInitials** | `@utils/getInitials` | Extract initials from name | `getInitials('John Doe')` → "JD" |
| **getDictionary** | `@utils/getDictionary` | Load i18n dictionaries (server-only) | `await getDictionary('en')` |
| **ensurePrefix** | `@utils/string` | Add prefix if missing | `ensurePrefix('icon', 'mdi-')` |
| **withoutPrefix** | `@utils/string` | Remove prefix if present | `withoutPrefix('mdi-icon', 'mdi-')` |
| **withoutSuffix** | `@utils/string` | Remove suffix if present | `withoutSuffix('doc.pdf', '.pdf')` |

---

## Essential Hooks

| Hook | Location | Purpose | Returns |
|------|----------|---------|---------|
| **useSettings** | `@core/hooks/useSettings` | Read/write theme settings | `{ settings, updateSettings }` |
| **useImageVariant** | `@core/hooks/useImageVariant` | Get image URL by mode | `string (image path)` |
| **useIntersection** | `@hooks/useIntersection` | Intersection Observer wrapper | `IntersectionContext` |
| **useObjectCookie** | `@core/hooks/useObjectCookie` | Read/write object from cookie | `{ value, updateCookie }` |
| **useLayoutInit** | `@core/hooks/useLayoutInit` | Initialize layout direction | `void` |

---

## Hook Signatures & Examples

### useSettings Hook

```typescript
import { useSettings } from '@core/hooks/useSettings'

export function ThemeToggle() {
  const { settings, updateSettings } = useSettings()
  
  // settings: { mode: 'light' | 'dark' | 'system', skin: string, layout: string }
  const isDark = settings.mode === 'dark'
  
  const toggleTheme = () => {
    updateSettings({ mode: isDark ? 'light' : 'dark' })
  }
  
  return <button onClick={toggleTheme}>Toggle Theme</button>
}
```

### useImageVariant Hook

```typescript
import { useImageVariant } from '@core/hooks/useImageVariant'
import { useSettings } from '@core/hooks/useSettings'

export function Logo() {
  const { settings } = useSettings()
  
  const imgSrc = useImageVariant(
    settings.mode,
    '/images/logo-light.png',
    '/images/logo-dark.png'
  )
  
  return <img src={imgSrc} alt="Logo" />
}
```

### useIntersection Hook

```typescript
import { useIntersection } from '@hooks/useIntersection'

export function LazyImage() {
  const context = useIntersection()
  // Use context for visibility tracking
  return <img src="..." alt="Lazy loaded" />
}
```

---

## Common Code Snippets

### Snippet 1: Format Date Utility

```typescript
// Usage in component
import { formatDate } from '@utils/'

export function EventCard({ eventDate }: { eventDate: Date }) {
  const formattedDate = formatDate(eventDate, 'MMM dd, yyyy')
  
  return (
    <div>
      <h3>Event</h3>
      <p className="text-gray-600">{formattedDate}</p>
    </div>
  )
}
```

### Snippet 2: Generate User Initials for Avatar

```typescript
import { getInitials } from '@utils/getInitials'

export function UserAvatar({ name, color }: { name: string; color?: string }) {
  const initials = getInitials(name)
  
  return (
    <div className={`flex items-center justify-center w-10 h-10 rounded-full ${color || 'bg-primary'}`}>
      <span className="text-white font-bold text-sm">{initials}</span>
    </div>
  )
}

// Usage: <UserAvatar name="John Doe" color="bg-blue-500" />
// Renders: "JD" in blue circle
```

### Snippet 3: Theme Mode Detection & Application

```typescript
// Server Component
import { getServerMode } from '@core/utils/serverHelpers'

export async function ServerComponent() {
  const mode = await getServerMode() // 'light' | 'dark'
  
  return (
    <div className={mode === 'dark' ? 'dark' : 'light'}>
      Content adapts to server-detected theme
    </div>
  )
}

// Client Component
'use client'
import { useSettings } from '@core/hooks/useSettings'

export function ClientComponent() {
  const { settings } = useSettings()
  
  return (
    <div className={settings.mode === 'dark' ? 'dark-mode' : 'light-mode'}>
      Content adapts to user theme choice
    </div>
  )
}
```

### Snippet 4: Form with Validation & Submission

```typescript
'use client'
import { Form } from '@components/Form'
import { useForm } from 'react-hook-form'
import { valibotResolver } from '@hookform/resolvers/valibot'
import { pipe, string, email, minLength } from 'valibot'

const formSchema = {
  email: pipe(string(), email('Invalid email')),
  message: pipe(string(), minLength(10, 'Min 10 chars'))
}

export function MyForm() {
  const { control, handleSubmit, formState: { errors } } = useForm({
    resolver: valibotResolver(formSchema)
  })
  
  const onSubmit = async (data) => {
    console.log('Submitted:', data)
  }
  
  return (
    <Form onSubmit={handleSubmit(onSubmit)}>
      <input {...control.register('email')} placeholder="Email" />
      {errors.email && <span>{errors.email.message}</span>}
      <textarea {...control.register('message')} placeholder="Message" />
      {errors.message && <span>{errors.message.message}</span>}
      <button type="submit">Submit</button>
    </Form>
  )
}
```

---

## Quick Setup Guide

### Setup: String Manipulation

```typescript
import { ensurePrefix, withoutPrefix, withoutSuffix } from '@utils/string'

const icon = ensurePrefix(userIconName, 'mdi-')
const cleanIcon = withoutPrefix(icon, 'mdi-')
const fileName = withoutSuffix('document.pdf', '.pdf')
```

### Setup: Initials for Avatars

```typescript
import { getInitials } from '@utils/getInitials'

const userName = 'Mary Jane Watson'
const initials = getInitials(userName)  // 'MJW'
<Avatar initials={initials} />
```

### Setup: Server-Side i18n Dictionary

```typescript
import { getDictionary } from '@utils/getDictionary'
import type { Locale } from '@configs/i18n'

export default async function Page({ params }: { params: { lang: string } }) {
  const dict = await getDictionary(params.lang as Locale)
  return <h1>{dict.common.welcome}</h1>
}
```

---

## Type Definitions

```typescript
type ThemeColor = 'primary' | 'secondary' | 'success' | 'error' | 'warning' | 'info'
type SystemMode = 'light' | 'dark'
type ThemeMode = 'light' | 'dark' | 'system'
interface Settings { mode: ThemeMode; skin: string; layout: string }
type Locale = 'en' | 'fr' | 'ar'
```

---

## See Also

- **Detailed Utilities**: ../source-analysis/misc-utilities-domain.md
- **Components Guide**: ../08-components.md
- **Architecture**: ../01-architecture.md
- **Theming**: ../03-theming.md