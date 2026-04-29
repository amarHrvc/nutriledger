# Layout System

## Three Layout Modes

Controlled by `settings.layout` (set in themeConfig or Customizer):

| Value | Description |
|-------|-------------|
| `vertical` | Sidebar on left, content on right (default) |
| `collapsed` | Sidebar collapsed to icons only |
| `horizontal` | Top navigation bar instead of sidebar |

## Layout Component Tree

```
(dashboard)/(private)/layout.tsx
└── Providers (Redux, MUI theme, Settings context, NextAuth)
    └── AuthGuard (server — checks session)
        └── LayoutWrapper (switches between vertical/horizontal based on settings)
            ├── VerticalLayout
            │   ├── Navigation (sidebar, uses verticalMenuData)
            │   ├── Navbar (top bar with search, notifications, user menu)
            │   ├── {children} (page content)
            │   └── VerticalFooter
            └── HorizontalLayout
                ├── Header (top nav, uses horizontalMenuData)
                ├── {children}
                └── HorizontalFooter
        └── ScrollToTop (floating button)
        └── Customizer (floating settings panel, top-right)
```

## Key Files

| File | Purpose |
|------|---------|
| `src/@layouts/LayoutWrapper.tsx` | Switches vertical/horizontal based on settings |
| `src/@layouts/VerticalLayout.tsx` | Vertical layout shell |
| `src/@layouts/HorizontalLayout.tsx` | Horizontal layout shell |
| `src/components/layout/vertical/Navigation.tsx` | Sidebar component |
| `src/components/layout/vertical/Navbar.tsx` | Top bar (vertical mode) |
| `src/components/layout/horizontal/Header.tsx` | Top nav (horizontal mode) |
| `src/components/Providers.tsx` | Wraps Redux, MUI theme, Auth, Settings |
| `src/@menu/` | All menu rendering components |

## Blank Layout (no sidebar)

For auth pages, error pages — uses `(blank-layout-pages)/layout.tsx` which renders children directly without `LayoutWrapper`.

## Front Pages Layout

`src/app/front-pages/layout.tsx` — completely separate, no admin chrome.

## Adding a New Layout Section

To wrap a group of pages in a custom layout:
1. Create a new route group folder: `src/app/[lang]/(dashboard)/(private)/your-section/`
2. Optionally add a `layout.tsx` inside for section-specific wrapping
3. Pages in this folder inherit the parent `(private)` AuthGuard automatically

## Customizer Panel

`src/@core/components/customizer/` — floating panel users can use to switch mode/layout/skin at runtime. Settings saved to cookie. Disable by removing `<Customizer>` from `(dashboard)/(private)/layout.tsx:73`.
