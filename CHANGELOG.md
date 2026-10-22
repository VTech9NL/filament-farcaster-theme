# Changelog

## v0.3.0 - 2026-10-22

- `MobileNavigation::visible()` hides the mobile bar on the pages you choose. The closure can receive `$routeName` and `$routeParameters`.

## v0.2.1 - 2026-10-22

- The replaced page header and user menu now apply only to panels that register the plugin. Other panels in the same app render Filament's own views. Before, they showed Polder's breadcrumb bar and user menu without Polder's CSS.
- Removed a style rule for `.fi-select-wrp`, a class that Filament does not have.

## v0.2.0 - 2026-10-22

First public release.

- Inset layout: the content card floats on the frame, next to a 16rem sidebar with the user menu at the bottom. `->sidebarWidth()` changes the width.
- Breadcrumb bar with a sidebar toggle and a slot for your own content (`PolderRenderHook::BREADCRUMB_BAR_END`). On phones, and with `->topbar()`, the panel shows one sidebar toggle only.
- Light and dark mode. Dark mode has four distinct layers (frame, card, containers, fields), with clear table lines and outlines.
- Optional compact density with `->compact()`, set for each panel.
- Tenant switcher in the user menu when the panel turns Filament's tenant menu off.
- Mobile navigation: an optional bottom bar for phones, floating or docked, with links, actions, bottom sheets (your own items, the panel navigation, the user menu or a Blade view) and a raised or flat centre action. Closures can read the page the bar was opened on.
- All rules in `@layer polder-theme`. The plugin declares the full layer order at the top of `<head>`, so a plugin stylesheet that loads first cannot push the theme above Tailwind utilities.
