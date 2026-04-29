# State Management & Data Layer

## Redux Toolkit

### Setup

Store configured at `src/redux-store/index.ts`. Wrapped in `<Providers>` at `src/components/Providers.tsx`.

### Slices (`src/redux-store/slices/`)

| Slice | State managed |
|-------|---------------|
| `calendar.ts` | Calendar events |
| `chat.ts` | Chat rooms, messages, contacts |
| `email.ts` | Emails, folders, labels |
| `kanban.ts` | Kanban boards, columns, tasks |

### Using Redux in a component

```tsx
import { useDispatch, useSelector } from 'react-redux'
import type { RootState, AppDispatch } from '@/redux-store'

const dispatch = useDispatch<AppDispatch>()
const data = useSelector((state: RootState) => state.calendar)
```

### Adding a New Slice

1. Create `src/redux-store/slices/myFeature.ts` using `createSlice`
2. Import and add to `src/redux-store/index.ts` reducer map

## API Routes (Next.js)

Located at `src/app/api/`. These are Next.js Route Handlers (not Express).

| API Endpoint | Serves data for |
|-------------|-----------------|
| `/api/apps/academy` | Academy app |
| `/api/apps/ecommerce` | eCommerce app |
| `/api/apps/invoice` | Invoice app |
| `/api/apps/logistics` | Logistics app |
| `/api/apps/permissions` | Permissions app |
| `/api/apps/user-list` | User list app |
| `/api/pages/faq` | FAQ page |
| `/api/pages/pricing` | Pricing page |
| `/api/pages/profile` | User profile page |
| `/api/pages/widget-examples` | Widget examples |
| `/api/login` | Auth (Credentials provider) |
| `/api/auth/[...nextauth]` | NextAuth handler |

Each route handler reads from `src/fake-db/` and returns JSON.

## Fake DB (`src/fake-db/`)

Mock data files. Structure mirrors the apps:
```
fake-db/
├── apps/     # academy, ecommerce, invoice, etc.
└── pages/    # faq, pricing, profile, widget-examples
```

**To replace with real data:** swap the fetch call in the view or API route to point to your real backend. The API route handler pattern is:

```ts
// src/app/api/apps/my-feature/route.ts
import { db } from '@/fake-db/apps/my-feature'
export async function GET() {
  return Response.json(db)
}
```

## Prisma

Used only for **auth session storage** (User, Session, Account, VerificationToken tables).

- Schema: `src/prisma/schema.prisma`
- Default DB: SQLite (`file:./dev.db`)
- Migrations: `pnpm migrate`
- Generate client: `pnpm postinstall` (runs automatically after install)

To switch to PostgreSQL/MySQL: change `provider` in schema.prisma and update `DATABASE_URL` in `.env`.

## Data Fetching Patterns Used in This Template

1. **Server Component + API route** — page.tsx fetches from `/api/...` on the server
2. **Client Component + useEffect/axios** — view component fetches on mount
3. **Redux slice** — data loaded into Redux, components read from store
4. **Inline data** — static data imported directly from fake-db in views
