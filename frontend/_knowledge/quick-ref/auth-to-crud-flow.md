# Auth-to-CRUD Flow Navigation Map

## Overview

This document maps how the authentication system flows into CRUD domains. Each authentication action triggers specific CRUD operations and domain responsibilities.

---

## 1. Authentication → Dashboard Redirect

**Flow:**
\\\
Login (credentials) → Auth verification → Session created → User object returned → Redirect to Dashboard
                                                                                  ↓
                                                          Load user dashboard data (from User CRUD)
\\\

**Key Points:**
- Auth system validates credentials via /api/auth/[...nextauth]
- Successful auth creates session with user ID
- NextAuth callback redirects to /dashboard
- Dashboard fetches user profile from User CRUD domain

**Related Files:**
- See [Auth Quick Reference](./auth-quick-ref.md) for auth flow details
- User data structure: \pages/dashboard\ uses \getUserProfile()\ from User CRUD

---

## 2. User Creation (Auth) → User CRUD Domain

**Flow:**
\\\
Registration Form → Auth provider creates user → User record stored → User CRUD initializes
                   (social OAuth or email/password)
                                                         ↓
                                          User CRUD operations available:
                                          - Read profile (GET /api/users/:id)
                                          - Update profile (PUT /api/users/:id)
                                          - Manage settings (preferences, security)
\\\

**Key Points:**
- Registration triggers auth provider account creation (OAuth/Email)
- User CRUD domain becomes active after auth registration
- User profile is primary resource for personalization
- Updates to user data affect auth session metadata

**Related Files:**
- [User CRUD Quick Reference](./user-crud-quick-ref.md)
- Registration endpoint: \pages/api/auth/register\
- User CRUD endpoints: \pages/api/users/[id]\

---

## 3. Role Assignment (Auth) → Roles Domain

**Flow:**
\\\
User authenticated → User row in DB → Admin assigns role → Role metadata added to User record
                                      (via Roles CRUD)         ↓
                                                       Role applied to session → Permissions available
\\\

**Key Points:**
- Roles are assigned AFTER user auth, not during registration
- Role assignment is Roles domain responsibility
- Admin operations manage user-role relationships
- Roles CRUD domain fully controls role lifecycle

**Related Files:**
- Roles domain: \_knowledge/01-architecture.md#Roles Domain\
- Role assignment endpoint: \pages/api/roles/assign\

---

## 4. Permission Checks → Roles/Permissions Domain

**Flow:**
\\\
User requests resource → Auth middleware checks session → Fetch user roles → Check role permissions
                                                                      ↓
                                              Query Permissions domain (Role → Permissions)
                                                      ↓
                                        Grant/Deny access + visible features/actions
\\\

**Key Points:**
- Auth system validates session; Permissions domain validates capabilities
- Permissions are tied to Roles (not directly to Users)
- Middleware applies permission checks: \middleware.ts\ → checks roles → guards routes
- UI rendering uses permission context for conditional rendering

---

## Navigation Map (Table Format)

| Auth Stage | Trigger | CRUD Domain | Operation | Result |
|-----------|---------|-------------|-----------|--------|
| **Signup** | User registers | User CRUD | CREATE user record | User account ready |
| **Login** | Credentials validated | Session | READ user + roles | Session created |
| **Post-Auth** | Auth complete | User CRUD | READ user profile | Dashboard loads |
| **Assign Role** | Admin action | Roles CRUD | CREATE user-role link | Role active |
| **Check Permission** | Route/action access | Permissions | READ role permissions | Access allowed/denied |
| **Update Profile** | User edit | User CRUD | UPDATE user record | Changes persist |
| **Logout** | User action | Session | DELETE session | Auth cleared |

---

## Cross-Domain Dependencies

\\\
┌─────────────┐
│  Auth Core  │  ← Entry point (login, register, session)
│  NextAuth   │
└────┬────────┘
     │
     ├─→ [User CRUD Domain]     ← Create/read/update user records
     │   - User profiles
     │   - User settings
     │   - User preferences
     │
     ├─→ [Roles CRUD Domain]     ← Manage role assignments
     │   - Assign/revoke roles
     │   - User-role relationships
     │
     └─→ [Permissions Domain]    ← Check capabilities
         - Role → Permission mapping
         - Feature access control
         - Route protection
\\\

---

## Implementation Checklist

- [ ] Auth system validates credentials (NextAuth)
- [ ] User CRUD creates/reads user records
- [ ] Roles CRUD manages role assignments
- [ ] Permissions domain checks access rights
- [ ] Session includes user roles (for fast permission checks)
- [ ] Middleware applies permission guards
- [ ] UI respects permission context for rendering

---

## Quick Links

- **Auth Details:** [Authentication Quick Reference](./auth-quick-ref.md)
- **User CRUD:** [User CRUD Quick Reference](./user-crud-quick-ref.md)
- **Roles Domain:** [Architecture: Roles Domain](_knowledge/01-architecture.md#Roles-Domain)
- **Permissions:** [Architecture: Permissions](_knowledge/01-architecture.md#Permissions-System)

---

*Last Updated: 2024*
*Part of Knowledge Base: Quick Reference Series*
