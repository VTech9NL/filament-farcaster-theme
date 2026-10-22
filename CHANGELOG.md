# Changelog

## v0.2.0 - 2026-10-22

- Mobile navigation: an optional bottom bar for phones, floating or docked, with links, actions, bottom sheets (your own items, the panel navigation, the user menu or a Blade view) and a raised or flat centre action. Closures can read the page the bar was opened on.
- Dark mode has four distinct layers (frame, card, containers, fields), with stronger table lines and outlines. Avatars get an outline in dark mode.
- The sidebar is 16rem by default; `->sidebarWidth()` changes it.

## v0.1.1 - 2026-10-22

- On phones, Filament's own sidebar button strip is hidden, because the breadcrumb bar's toggle already opens the sidebar. Before, phones showed two toggles.
- With `->topbar()`, the breadcrumb bar shows no sidebar toggle, because the topbar has one.

## v0.1.0 - 2026-10-22

- First version: inset layout, breadcrumb bar, sidebar user menu with tenant switcher, light and dark mode, optional compact density.
- All rules in `@layer polder-theme`. The plugin declares the full layer order at the top of `<head>`, so a plugin stylesheet that loads first cannot push the theme above Tailwind utilities.
