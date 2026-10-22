# Changelog

## v0.1.1 - 2026-10-22

- On phones, Filament's own sidebar button strip is hidden, because the breadcrumb bar's toggle already opens the sidebar. Before, phones showed two toggles.
- With `->topbar()`, the breadcrumb bar shows no sidebar toggle, because the topbar has one.

## v0.1.0 - 2026-10-22

- First version: inset layout, breadcrumb bar, sidebar user menu with tenant switcher, light and dark mode, optional compact density.
- All rules in `@layer polder-theme`. The plugin declares the full layer order at the top of `<head>`, so a plugin stylesheet that loads first cannot push the theme above Tailwind utilities.
