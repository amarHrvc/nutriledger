# Components, Libraries & Hooks

## Core Components (`src/@core/components/`)

| Component | Path | Usage |
|-----------|------|-------|
| `Customizer` | `customizer/` | Floating theme settings panel. Already in dashboard layout. |
| `CustomInputHorizontal` | `custom-inputs/` | Card-style radio/checkbox input (horizontal) |
| `CustomInputVertical` | `custom-inputs/` | Card-style radio/checkbox input (vertical) |
| `CustomInputImage` | `custom-inputs/` | Image-based selectable card |
| `OptionMenu` | `option-menu/` | Dropdown menu button (3-dot menu) |
| `ScrollToTop` | `scroll-to-top/` | Floating scroll-to-top button |
| MUI wrappers | `mui/` | Extended MUI components with Vuexy defaults |

## App-Level Components (`src/components/`)

| Component | Purpose |
|-----------|---------|
| `Providers.tsx` | Root provider: Redux, MUI theme, NextAuth, Settings |
| `AuthRedirect.tsx` | Redirects unauthenticated users to login |
| `layout/vertical/Navigation.tsx` | Sidebar navigation |
| `layout/vertical/Navbar.tsx` | Top bar (vertical layout) |
| `layout/vertical/Footer.tsx` | Footer (vertical layout) |
| `layout/horizontal/Header.tsx` | Top nav (horizontal layout) |
| `layout/horizontal/Footer.tsx` | Footer (horizontal layout) |

## Key Third-Party Libraries

### UI Components (MUI v7)
All standard MUI components available. Import from `@mui/material`:
```tsx
import Button from '@mui/material/Button'
import { Grid, Card, CardContent, Typography } from '@mui/material'
```

### Charts
```tsx
// ApexCharts
import ReactApexcharts from 'react-apexcharts'
<ReactApexcharts type='bar' series={[...]} options={{...}} />

// Recharts
import { LineChart, Line, XAxis, YAxis } from 'recharts'
```
Examples in: `src/views/charts/apex/` and `src/views/charts/recharts/`

### Data Tables (TanStack Table v8)
```tsx
import { useReactTable, getCoreRowModel, flexRender } from '@tanstack/react-table'
```
Examples in: `src/views/react-table/` and `src/views/apps/user/list/`

### Forms (React Hook Form + Valibot)
```tsx
import { useForm, Controller } from 'react-hook-form'
import { valibotResolver } from '@hookform/resolvers/valibot'
import * as v from 'valibot'
```
Examples in: `src/views/forms/form-validation/`

### Rich Text Editor (Tiptap v3)
```tsx
import { useEditor, EditorContent } from '@tiptap/react'
import StarterKit from '@tiptap/starter-kit'
```
Used in: `src/views/apps/email/`, `src/views/apps/kanban/`

### Date Picker
```tsx
import DatePicker from 'react-datepicker'
import 'react-datepicker/dist/react-datepicker.css'
```

### File Upload
```tsx
import { useDropzone } from 'react-dropzone'
```

### Calendar (FullCalendar v6)
```tsx
import FullCalendar from '@fullcalendar/react'
import dayGridPlugin from '@fullcalendar/daygrid'
```
Used in: `src/views/apps/calendar/`

### Maps (Mapbox)
```tsx
import Map from 'react-map-gl'
```
Requires `NEXT_PUBLIC_MAPBOX_TOKEN` env var. Used in: `src/views/apps/logistics/`

### Toast Notifications
```tsx
import { toast } from 'react-toastify'
toast.success('Saved!')
toast.error('Error!')
```
`<ToastContainer>` already mounted in `Providers.tsx`.

### Drag & Drop
```tsx
import { useDragAndDrop } from '@formkit/drag-and-drop/react'
```
Used in: `src/views/apps/kanban/`

### Video Player
```tsx
import ReactPlayer from 'react-player'
```

### Slider / Carousel
```tsx
import { useKeenSlider } from 'keen-slider/react'
```

## Core Hooks

| Hook | Location | Purpose |
|------|----------|---------|
| `useSettings` | `@core/hooks/useSettings` | Read/write theme settings (mode, layout, skin, etc.) |
| `useImageVariant` | `@core/hooks/useImageVariant` | Return correct image src for current mode (light/dark) |
| `useLayoutInit` | `@core/hooks/useLayoutInit` | Initialize layout direction, called in layouts |
| `useObjectCookie` | `@core/hooks/useObjectCookie` | Read/write cookie as object |
| `useIntersection` | `src/hooks/useIntersection` | Intersection Observer wrapper |

### useSettings example
```tsx
import { useSettings } from '@core/hooks/useSettings'

const { settings, updateSettings } = useSettings()
// Read
const isDark = settings.mode === 'dark'
// Write
updateSettings({ layout: 'horizontal' })
```

### useImageVariant example
```tsx
import { useImageVariant } from '@core/hooks/useImageVariant'

const imgSrc = useImageVariant(mode, '/images/logo-light.png', '/images/logo-dark.png')
```

## HOCs (`src/hocs/`)

| HOC | Purpose |
|-----|---------|
| `AuthGuard` | Server component. Checks session, redirects to login if none. |
| `TranslationWrapper` | Provides i18n dictionary to client components. |
