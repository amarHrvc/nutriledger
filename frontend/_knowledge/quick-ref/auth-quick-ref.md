# Auth Quick Reference

## Common Questions

**Q: How do I implement a login system?**
- Read: `../pages/auth/login.md` + `../02-auth.md`
- Key types: Email, password, NextAuth.js providers
- Time: 10 min
- Quick: Use `signIn('credentials')` with email/password

**Q: How do I protect a route from unauthorized access?**
- Read: `../02-auth.md` (Auth System section)
- Key: Place page under `(private)` group - `AuthGuard` handles it automatically
- Time: 2 min
- Setup: Move page to `[lang]/(dashboard)/(private)/` folder

**Q: How do I add OAuth (Google login)?**
- Read: `../02-auth.md` (Auth Providers section)
- Key: GoogleProvider in `src/libs/auth.ts`
- Time: 5 min
- Setup: Add `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` env vars

**Q: How do I handle form validation on login/register?**
- Read: `../pages/auth/login.md` (Form Validation section)
- Key: Use Valibot schema + React Hook Form
- Time: 5 min
- Validation: Email required, valid format; Password min 5 chars

---

## Essential Types & Patterns

| Concept | File | Key Details |
|---------|------|-------------|
| **Login Flow** | 02-auth.md | Email/Password → Credentials Provider → getServerSession() |
| **Protected Routes** | 02-auth.md | AuthGuard HOC in (private) group layout |
| **Session Management** | 02-auth.md | Server: getServerSession(), Client: useSession() |
| **OAuth Providers** | 02-auth.md | Google, Facebook, Twitter, GitHub supported |
| **Form Validation** | pages/auth/login.md | Valibot pipe() validators + React Hook Form |
| **Auth Screens** | catalogs/auth-screens.md | 3 real screens + 13 demo variants available |

---

## Pattern Map: Auth Screens

| Need | Use | File | Demo Routes |
|------|-----|------|-------------|
| **Login (Production)** | Login.tsx | views/Login.tsx | /login (real), /pages/auth/login-v1, login-v2 |
| **Registration** | Register.tsx | views/Register.tsx | /register (real), /pages/auth/register-v1, register-v2 |
| **Forgot Password** | ForgotPassword.tsx | views/ForgotPassword.tsx | /forgot-password (real), v1/v2 variants |
| **2FA/OTP** | TwoStepsV1/V2 | views/pages/auth/ | /pages/auth/two-steps-v1, two-steps-v2 |
| **Multi-Step Register** | RegisterMultiSteps | views/pages/auth/register-multi-steps/ | /pages/auth/register-multi-steps |

---

## Quick Setup: Login Form

```typescript
// src/views/Login.tsx (Client Component)
'use client'

import { useForm } from 'react-hook-form'
import { valibotResolver } from '@hookform/resolvers/valibot'
import { signIn } from 'next-auth/react'
import { pipe, string, email, minLength } from 'valibot'

const loginSchema = {
  email: pipe(string(), email('Invalid email')),
  password: pipe(string(), minLength(5, 'Min 5 characters'))
}

export function Login() {
  const { control, handleSubmit } = useForm({
    resolver: valibotResolver(loginSchema),
    defaultValues: { email: 'admin@vuexy.com', password: 'admin' }
  })

  const onSubmit = async (data) => {
    const result = await signIn('credentials', {
      email: data.email,
      password: data.password,
      redirect: true,
      callbackUrl: '/dashboard'
    })
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <button type="submit">Sign In</button>
    </form>
  )
}
```

---

## Quick Setup: Protected Route (AuthGuard)

```typescript
// src/app/[lang]/(dashboard)/(private)/page.tsx
// Route automatically protected by AuthGuard in (private) layout

export default function ProtectedPage() {
  // If authenticated, render; if not, AuthGuard redirects to /login
  return <div>Protected content</div>
}
```

---

## Quick Setup: OAuth Integration

```typescript
// src/libs/auth.ts - NextAuth config
import GoogleProvider from 'next-auth/providers/google'

export const authOptions = {
  providers: [
    GoogleProvider({
      clientId: process.env.GOOGLE_CLIENT_ID!,
      clientSecret: process.env.GOOGLE_CLIENT_SECRET!
    })
  ]
}

// Usage: const result = await signIn('google', { redirect: true })
```

---

## Session Usage Examples

```typescript
// Server-side (in Server Components)
import { getServerSession } from 'next-auth'

export default async function ServerPage() {
  const session = await getServerSession()
  return <div>User: {session?.user?.email}</div>
}

// Client-side
'use client'
import { useSession } from 'next-auth/react'

export function ClientComponent() {
  const { data: session } = useSession()
  return <div>User: {session?.user?.email}</div>
}
```

---

## Environment Variables

```bash
NEXTAUTH_SECRET=your-secret-key
NEXTAUTH_URL=http://localhost:3000
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
DATABASE_URL=file:./dev.db
```

---

## Auth File Structure

```
src/
├── hocs/
│   └── AuthGuard.tsx          # Server component, checks session
├── components/
│   └── AuthRedirect.tsx       # Redirects to login
├── libs/
│   └── auth.ts                # NextAuth configuration
├── app/api/auth/
│   └── [...nextauth]/
│       └── route.ts           # NextAuth handler
├── app/api/login/
│   ├── route.ts               # Credentials provider endpoint
│   └── users.ts               # Demo users (replace with DB)
└── views/
    ├── Login.tsx              # Production login
    ├── Register.tsx           # Production register
    └── pages/auth/
        ├── LoginV1.tsx        # Demo variants
        └── ... (13 more)
```

---

## See Also

- **Architecture**: ../01-architecture.md (Auth System section)
- **Detailed Auth**: ../02-auth.md (Auth System overview)
- **Login Page**: ../pages/auth/login.md (Form validation, NextAuth integration)
- **Register Page**: ../pages/auth/register.md (Registration flow)
- **All Screens**: ../catalogs/auth-screens.md (Complete catalog of 16 screens)
- **Auth Analysis**: ../auth-source-analysis.md (Source file breakdown)
