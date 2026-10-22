# Changelog

## v0.2.0 - 2026-10-22

First public release.

- Inset layout: the content card floats on the frame, next to a 16rem sidebar with the user menu at the bottom. `->sidebarWidth()` changes the width.
- Breadcrumb bar with a sidebar toggle and a slot for your own content (`PolderRenderHook::BREADCRUMB_BAR_END`). On phones, and with `->topbar()`, the panel shows one sidebar toggle only.
- Light and dark mode. Dark mode has four distinct layers (frame, card, containers, fields), with clear table lines and outlines.
- Optional compact density with `->compact()`, set for each panel.
- Tenant switcher in the user menu when the panel turns Filament's tenant menu off.
- Mobile navigation: an optional bottom bar for phones, floating or docked, with links, actions, bottom sheets (your own items, the panel navigation, the user menu or a Blade view) and a raised or flat centre action. Closures can read the page the bar was opened on.
- All rules in `@layer polder-theme`. The plugin declares the full layer order at the top of `<head>`, so a plugin stylesheet that loads first cannot push the theme above Tailwind utilities.
