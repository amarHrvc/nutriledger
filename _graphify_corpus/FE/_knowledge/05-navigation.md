# Navigation / Menu

## Menu Data Files

| File | Used by |
|------|---------|
| `src/data/navigation/verticalMenuData.tsx` | Sidebar (vertical + collapsed layout) |
| `src/data/navigation/horizontalMenuData.tsx` | Top nav (horizontal layout) |

Both files export a function that receives the `dictionary` (translations) and returns an array of menu items.

## Menu Item Structure

```ts
// Simple link
{
  label: dictionary['navigation'].myPage,
  icon: 'tabler-circle',
  href: '/my-section/my-page'
}

// Group with children
{
  label: dictionary['navigation'].mySection,
  icon: 'tabler-folder',
  suffix: { label: '3', color: 'error' }, // optional badge
  children: [
    { label: '...', icon: 'tabler-circle', href: '/...' },
  ]
}

// Section header (no link)
{
  label: 'Section Title',
  isSection: true
}

// External link, open in new tab
{
  label: 'Docs',
  href: 'https://...',
  target: '_blank',
  excludeLang: true   // don't prefix with /:lang
}
```

## Adding a Menu Item (step by step)

1. Add translation key to `src/data/dictionaries/en.json` (and fr.json, ar.json) under `"navigation"`:
   ```json
   { "navigation": { "myPage": "My Page" } }
   ```

2. Add entry to `src/data/navigation/verticalMenuData.tsx`:
   ```tsx
   {
     label: dictionary['navigation'].myPage,
     icon: 'tabler-file',
     href: '/my-section/my-page'
   }
   ```

3. Create the route: `src/app/[lang]/(dashboard)/(private)/my-section/my-page/page.tsx`

## Icon Names

All icons use Iconify Tabler set. Format: `tabler-{icon-name}`.
Browse icons at: https://icon-sets.iconify.design/tabler/

Common ones used in Vuexy:
- `tabler-smart-home` (dashboard)
- `tabler-circle` (child menu items)
- `tabler-files`, `tabler-file-text`
- `tabler-users`, `tabler-user`
- `tabler-settings`
- `tabler-chart-bar`
- `tabler-shopping-cart`
- `tabler-mail`

## Active State

Active state (highlight) is automatic — menu item is highlighted when `href` matches the current URL path.
