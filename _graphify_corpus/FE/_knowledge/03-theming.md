# Theming

## Overview

MUI v7 with full custom theme. Theme config split between static `themeConfig.ts` and runtime `Customizer` panel stored in cookie.

## Key Files

| File | Purpose |
|------|---------|
| `src/configs/themeConfig.ts` | Default theme settings (all overridable at runtime) |
| `src/configs/primaryColorConfig.ts` | Preset primary color options |
| `src/@core/theme/index.ts` | MUI theme creation entry point |
| `src/@core/theme/colorSchemes.ts` | Light/dark color palettes |
| `src/@core/theme/typography.ts` | Font families and sizes |
| `src/@core/theme/shadows.ts` | Shadow definitions |
| `src/@core/theme/customShadows.ts` | Custom shadow tokens |
| `src/@core/theme/overrides/` | Per-MUI-component overrides (one file each) |
| `src/@core/components/customizer/` | Runtime theme customizer panel (sidebar) |

## themeConfig.ts Options

```ts
{
  mode: 'system',        // 'system' | 'light' | 'dark'
  skin: 'default',       // 'default' | 'bordered'
  semiDark: false,       // sidebar dark, content light
  layout: 'vertical',   // 'vertical' | 'collapsed' | 'horizontal'
  layoutPadding: 24,     // px padding for header/content/footer
  navbar: {
    type: 'fixed',       // 'fixed' | 'static'
    contentWidth: 'compact', // 'compact' | 'wide'
    floating: true,
    detached: true,
    blur: true
  },
  contentWidth: 'compact',   // 'compact' | 'wide'
  compactContentWidth: 1440, // max-width in px for compact mode
  footer: {
    type: 'static',      // 'fixed' | 'static'
    contentWidth: 'compact',
    detached: true
  },
  disableRipple: false,
  toastPosition: 'top-right'
}
```

**Important:** mode, skin, semiDark, layout, navbar.contentWidth, contentWidth, footer.contentWidth are stored in cookie and cookie takes precedence over themeConfig. To reset to themeConfig values, clear the cookie named `vuexy-mui-next-demo-1` (or use Customizer reset button).

## Reading/Writing Settings at Runtime

```tsx
import { useSettings } from '@core/hooks/useSettings'

const { settings, updateSettings } = useSettings()
// settings.mode, settings.layout, settings.skin, etc.
updateSettings({ mode: 'dark' })
```

## MUI Component Overrides

Located at `src/@core/theme/overrides/`. Each MUI component has its own file:
- `button.ts`, `card.ts`, `table-pagination.ts`, `dialog.ts`, etc.

To override a component globally, edit the relevant file or add a new one and import it in `src/@core/theme/overrides/index.ts`.

## Custom Colors / Primary Color

Primary color presets defined in `src/configs/primaryColorConfig.ts`. User can switch at runtime via Customizer. To add a new preset, add an entry to that array.

To hard-code a primary color (no presets), modify `src/@core/theme/colorSchemes.ts`.

## Dark Mode

Controlled by `settings.mode`. Values: `'light'`, `'dark'`, `'system'`.
- `InitColorSchemeScript` (in root layout) prevents flash of wrong theme on load.
- MUI v7 uses `colorSchemes` API — both light and dark palettes defined together in `colorSchemes.ts`.

## Icons

Iconify with the **Tabler** icon set. Usage:
```tsx
<i className='tabler-home' />           // via generated CSS
// or
import { Icon } from '@iconify/react'
<Icon icon='tabler:home' />
```
Generated icon CSS: `src/assets/iconify-icons/generated-icons.css` (run `pnpm build:icons` to regenerate after adding new icons).

## RTL Support

Handled by `stylis-plugin-rtl`. Activated automatically when `lang='ar'`. Direction set in `src/configs/i18n.ts` → `langDirection`.
