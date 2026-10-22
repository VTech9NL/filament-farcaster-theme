# Polder for Filament

[![Latest version](https://img.shields.io/packagist/v/vtech9nl/filament-polder-theme.svg?style=flat-square)](https://packagist.org/packages/vtech9nl/filament-polder-theme)
[![Tests](https://img.shields.io/github/actions/workflow/status/VTech9NL/filament-polder-theme/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/VTech9NL/filament-polder-theme/actions/workflows/tests.yml)
[![Total downloads](https://img.shields.io/packagist/dt/vtech9nl/filament-polder-theme.svg?style=flat-square)](https://packagist.org/packages/vtech9nl/filament-polder-theme)
[![License](https://img.shields.io/packagist/l/vtech9nl/filament-polder-theme.svg?style=flat-square)](LICENSE.md)

Polder is a theme for [Filament](https://filamentphp.com) v5 panels. The page content floats as a card on a quiet frame, next to a narrow sidebar that holds the user menu. A breadcrumb bar with a sidebar toggle sits at the top of each page, and phones get a bottom navigation bar.

<picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/dashboard-dark.png">
    <img alt="A Filament dashboard with the Polder theme" src="https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/dashboard-light.png">
</picture>

## Features

- **Inset layout.** The content card floats on the frame, and the user menu sits at the bottom of a 16rem sidebar.
- **Light and dark mode.** Each mode has its own values. In dark mode, the frame, the card, the containers and the fields are four separate layers, so tables and fields stay easy to see.
- **Your colours.** Polder uses the `gray` and `primary` palettes that you register on the panel.
- **Compact density.** One method makes a panel denser. Fields and buttons keep their full size.
- **Mobile navigation.** A bottom bar for phones, floating or docked, with links, actions, bottom sheets and a centre button.
- **Tenant switcher in the user menu,** when you turn Filament's tenant menu off.
- **Safe to customise.** All rules sit in their own CSS layer, so your CSS overrides them without `!important`. Every colour and size is a `--polder-theme-*` setting.

## Screenshots

| Light | Dark |
| --- | --- |
| ![Orders list, light](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/orders-light.png) | ![Orders list, dark](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/orders-dark.png) |
| ![Edit form, light](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/product-edit-light.png) | ![Edit form, dark](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/product-edit-dark.png) |
| ![User menu, light](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/user-menu-light.png) | ![User menu, dark](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/user-menu-dark.png) |

### Compact

| Light | Dark |
| --- | --- |
| ![Compact dashboard, light](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/dashboard-compact-light.png) | ![Compact dashboard, dark](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/dashboard-compact-dark.png) |
| ![Compact orders list, light](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/orders-compact-light.png) | ![Compact orders list, dark](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/orders-compact-dark.png) |

### Mobile navigation

| Floating, light | Floating, dark | More sheet | Account sheet | Docked, flat button |
| --- | --- | --- | --- | --- |
| ![Floating bar, light](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/mobile-floating-light.png) | ![Floating bar, dark](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/mobile-floating-dark.png) | ![More sheet](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/mobile-more-light.png) | ![Account sheet](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/mobile-account-dark.png) | ![Docked bar](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/mobile-docked-light.png) |

### With your own primary colour

The same form with `'primary' => Color::Indigo`:

| Light | Dark |
| --- | --- |
| ![Indigo form, light](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/product-edit-indigo-light.png) | ![Indigo form, dark](https://raw.githubusercontent.com/VTech9NL/filament-polder-theme/main/art/product-edit-indigo-dark.png) |

## Requirements

- PHP 8.3 or higher
- Filament 5
- Tailwind CSS v4, through a custom Filament theme (step 3 makes one if you have none)

## Installation

1. Install the package:

    ```bash
    composer require vtech9nl/filament-polder-theme
    ```

2. Register the plugin on the panel, for example in `app/Providers/Filament/AdminPanelProvider.php`:

    ```php
    use VTech9NL\PolderTheme\PolderThemePlugin;

    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->plugin(PolderThemePlugin::make());
    }
    ```

3. If the panel has no custom theme yet, make one. Filament creates the CSS file, adds it to Vite and registers it on the panel:

    ```bash
    php artisan make:filament-theme admin
    ```

4. Open the theme file, for example `resources/css/filament/admin/theme.css`, and put Polder's imports around Filament's. The order matters: `layers.css` comes first.

    ```css
    @import '../../../../vendor/vtech9nl/filament-polder-theme/resources/css/layers.css';
    @import '../../../../vendor/filament/filament/resources/css/theme.css';
    @import '../../../../vendor/vtech9nl/filament-polder-theme/resources/css/theme.css';

    @source '../../../../app/Filament/**/*';
    @source '../../../../resources/views/filament/**/*';
    ```

5. Build the assets:

    ```bash
    npm run build
    ```

To check the setup, run this in the browser console on a panel page. It must return `ok`:

```js
getComputedStyle(document.body).getPropertyValue('--polder-theme-layer-order')
```

## Configuration

All options are methods on the plugin:

```php
PolderThemePlugin::make()
    ->compact()                // denser spacing on this panel
    ->font('Inter')            // default: Instrument Sans; null keeps the panel's font
    ->sidebarWidth('18rem')    // default: 16rem; null keeps the panel's width
    ->topbar()                 // keep Filament's topbar; default: hidden
    ->mobileNavigation(...)    // a bottom bar for phones, see below
```

### Compact density

`->compact()` makes the panel denser. Form fields and buttons keep their full size, so they stay easy to click. Each panel has its own setting, so an admin panel can be compact while a customer panel is not.

### Colours

Polder has no palette of its own. It uses the panel's colours, so you set them as usual:

```php
use Filament\Support\Colors\Color;

$panel->colors([
    'primary' => Color::Indigo,
    'gray' => Color::Zinc,
]);
```

## Mobile navigation

Below the `lg` breakpoint, Polder can show a bottom navigation bar. You build it from items, and you can put an action in the centre:

```php
use App\Filament\Actions\NewOrderAction;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;
use VTech9NL\PolderTheme\MobileNavigation\MobileNavigation;
use VTech9NL\PolderTheme\MobileNavigation\MobileNavigationButton;
use VTech9NL\PolderTheme\MobileNavigation\MobileNavigationItem;
use VTech9NL\PolderTheme\MobileNavigation\MobileNavigationStyle;

PolderThemePlugin::make()
    ->mobileNavigation(
        MobileNavigation::make()
            ->style(MobileNavigationStyle::Floating)
            ->items([
                MobileNavigationItem::link('home')
                    ->label('Home')
                    ->icon(Heroicon::OutlinedHome)
                    ->url(fn (): string => Dashboard::getUrl()),
                MobileNavigationItem::link('orders')
                    ->label('Orders')
                    ->icon(Heroicon::OutlinedShoppingCart)
                    ->url(fn (): string => OrderResource::getUrl()),
                MobileNavigationItem::sheet('more')->navigation(),
                MobileNavigationItem::sheet('account')->userMenu(),
            ])
            ->centerAction(fn (): Action => NewOrderAction::make(), MobileNavigationButton::Raised),
    );
```

The bar puts the first half of the items to the left of the centre button and the rest to the right.

### Styles

| Setting | Options |
| --- | --- |
| `->style()` | `MobileNavigationStyle::Floating`: a rounded bar above the content. `MobileNavigationStyle::Docked`: a full-width bar on the bottom edge. |
| Second argument of `->centerAction()` | `MobileNavigationButton::Raised`: a round button that rises above the bar. `MobileNavigationButton::Flat`: a filled button in the row, with its label. |

### Items

| Item | What a tap does |
| --- | --- |
| `MobileNavigationItem::link('name')->url(...)` | Opens a URL. The item is active when the URL is the current page, or when the `->isActiveWhen()` closure returns `true`. |
| `MobileNavigationItem::action(fn (): Action => ...)` | Runs a Filament action: a modal, a slide-over, or a direct action. The label and icon come from the action, unless you set them on the item. |
| `MobileNavigationItem::sheet('name')->items([...])` | Opens a bottom sheet with your own links and actions. |
| `MobileNavigationItem::sheet('name')->navigation()` | Opens a bottom sheet with the panel's full navigation. |
| `MobileNavigationItem::sheet('name')->userMenu()` | Opens a bottom sheet with the user menu: profile, theme, other tenants and sign out. |
| `MobileNavigationItem::sheet('name')->view('my.view')` | Opens a bottom sheet with your own Blade view. |

All items take `->label()` and `->icon()`.

### Using the current page

The bar's closures can receive the page the bar was opened on. The action runs in a later Livewire request, so the bar keeps this for you:

```php
->centerAction(fn (?string $routeName, array $routeParameters): Action => LogActivityAction::make()
    ->fillForm(['animal_id' => $routeName === 'filament.app.resources.animals.view' ? $routeParameters['record'] : null]))
```

The page values come from the server, so a browser cannot change them. Still authorise any record that you load from them, as you would in any action.

## Tenant switcher

When a panel has tenancy and you turn off Filament's tenant menu with `->tenantMenu(false)`, Polder lists the user's other tenants in the user menu. To change the section label, publish the translations:

```bash
php artisan vendor:publish --tag=polder-translations
```

## Breadcrumb bar

The breadcrumb bar has a slot on its right side. To put content there, register a render hook. This example moves the global search into the bar:

```php
use Filament\Enums\GlobalSearchPosition;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use VTech9NL\PolderTheme\PolderRenderHook;

// In the panel provider: stop Filament from putting the search in the sidebar.
$panel->globalSearch(position: GlobalSearchPosition::Topbar);

// In a service provider's boot() method:
FilamentView::registerRenderHook(
    PolderRenderHook::BREADCRUMB_BAR_END,
    fn (): string => Blade::render('@livewire(\Filament\Livewire\GlobalSearch::class)'),
);
```

## Customisation

Polder reads these CSS custom properties. Set them in your theme file, after the imports. Set the size settings on `:root` and the colour settings on `.fi-body`. Use `.dark .fi-body` to change dark mode only.

| Setting | Default (light / dark) | Controls |
| --- | --- | --- |
| `--polder-theme-spacing` | `0.25rem` | Spacing scale of the panel |
| `--polder-theme-spacing-compact` | `0.2rem` | Spacing scale when `->compact()` is on |
| `--polder-theme-control-spacing` | `0.25rem` | Spacing scale of fields, buttons and the breadcrumb bar |
| `--polder-theme-radius-md` | `0.25rem` | Small corners: fields, buttons |
| `--polder-theme-radius-lg` | `0.3rem` | Medium corners: dropdowns, navigation items |
| `--polder-theme-radius-xl` | `0.35rem` | Large corners: sections, tables, the content card |
| `--polder-theme-inset` | `2 × --polder-theme-spacing` | Space around the content card |
| `--polder-theme-ground` | `gray-50` / `gray-950` | Frame and sidebar background |
| `--polder-theme-surface` | `white` / between `gray-900` and `gray-950` | Content card and mobile bar background |
| `--polder-theme-surface-ring` | `gray-950` 5% / `white` 8% | Content card outline |
| `--polder-theme-raised` | dark only: between `gray-800` and `gray-900` | Sections, tables, stats, dropdowns, modals and sheets |
| `--polder-theme-raised-ring` | dark only: `white` 10% | Outline of raised containers |
| `--polder-theme-field` | dark only: `white` 6% | Form field background |
| `--polder-theme-divider` | dark only: `white` 10% | Table row lines |
| `--polder-theme-rule` | `gray-200` 50% / `white` 10% | Breadcrumb bar and docked bar divider |
| `--polder-theme-mobile-navigation-height` | `4rem` | Height of the mobile bar |

For example, rounder corners everywhere and lighter containers in dark mode:

```css
:root {
    --polder-theme-radius-xl: 0.75rem;
}

.dark .fi-body {
    --polder-theme-raised: var(--gray-800);
}
```

## How Polder works

- **CSS layers.** All rules sit in `@layer polder-theme`, between Filament's components and Tailwind's utilities. Your own CSS, outside any layer, always wins. The plugin also writes the layer order at the top of the page `<head>`, so a plugin stylesheet that loads before your theme cannot change it.
- **Two Blade views.** Polder replaces `components/header/index` (the breadcrumb bar) and `components/user-menu`, only in panels that register the plugin; other panels keep Filament's own views. Each one starts from the Filament 5 view and adds only Polder's changes. A test fails when Filament changes either original view, and CI runs every week against the latest Filament 5 release.
- **No extra build step.** Polder's own views use only `polder-theme-*` classes, which the theme CSS styles. You do not need an `@source` line for the package.

## Testing

```bash
composer test
composer analyse
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

MIT. See [LICENSE.md](LICENSE.md).
