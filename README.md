# Polder for Filament

*Polder (Dutch): flat, calm land in an ordered grid of fields and water.*

Polder is a theme for [Filament](https://filamentphp.com) v5 panels. It gives the panel an inset layout: the page content floats as a card on a quiet frame, next to a sidebar that holds the user menu. A breadcrumb bar with a sidebar toggle sits at the top of each page.

- Light and dark mode, designed for each mode.
- Your panel colours: Polder uses the `gray` and `primary` palettes that you register on the panel.
- An optional compact density, which you turn on in PHP for each panel.
- All rules are in their own CSS layer, so your own CSS overrides them without `!important`.

## Requirements

- PHP 8.3 or higher
- Filament 5
- A [custom Filament theme](https://filamentphp.com/docs/5.x/styling/overview#creating-a-custom-theme) for the panel (Tailwind CSS v4)

## Installation

1. Install the package:

    ```bash
    composer require vtech9nl/filament-polder-theme
    ```

2. Register the plugin on the panel:

    ```php
    use VTech9NL\PolderTheme\PolderThemePlugin;

    $panel->plugin(PolderThemePlugin::make());
    ```

3. Add the imports to the panel's theme file, for example `resources/css/filament/admin/theme.css`. Keep this order: `layers.css` must come before Filament's CSS.

    ```css
    @import '../../../../vendor/vtech9nl/filament-polder-theme/resources/css/layers.css';
    @import '../../../../vendor/filament/filament/resources/css/theme.css';
    @import '../../../../vendor/vtech9nl/filament-polder-theme/resources/css/theme.css';

    @source '../../../../app/Filament/**/*';
    @source '../../../../resources/views/filament/**/*';
    ```

4. Build the assets:

    ```bash
    npm run build
    ```

To check the import order, run this in the browser console. It must return `ok`:

```js
getComputedStyle(document.body).getPropertyValue('--polder-theme-layer-order')
```

The plugin also writes the layer order at the top of the page `<head>`. So a Filament plugin stylesheet that loads before your theme, and declares Tailwind's layers, cannot push Polder's rules above Tailwind's utilities.

## Configuration

```php
PolderThemePlugin::make()
    ->compact()              // denser spacing on this panel
    ->font('Inter')          // default: Instrument Sans; null keeps the panel's font
    ->topbar()               // keep Filament's topbar; default: hidden
```

### Compact density

`->compact()` makes the panel denser. Form fields and buttons keep their full size, so they stay easy to click. You can set compact on one panel and not on another.

### Tenant switcher

When a panel has tenancy and you turn off Filament's tenant menu with `->tenantMenu(false)`, Polder shows the user's other tenants in the user menu. To change the section label, publish the translations:

```bash
php artisan vendor:publish --tag=polder-translations
```

### Breadcrumb bar

The breadcrumb bar has a slot on its right side. To put content there, for example the global search, register a render hook:

```php
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use VTech9NL\PolderTheme\PolderRenderHook;

FilamentView::registerRenderHook(
    PolderRenderHook::BREADCRUMB_BAR_END,
    fn (): string => Blade::render('@livewire(\Filament\Livewire\GlobalSearch::class)'),
);
```

## Customisation

Polder reads these CSS custom properties. Set them after the imports in your theme file. Set the size tokens on `:root` and the colour tokens on `.fi-body`. Use `.dark .fi-body` for dark mode only.

| Token | Default | Controls |
| --- | --- | --- |
| `--polder-theme-spacing` | `0.25rem` | Spacing scale of the panel |
| `--polder-theme-spacing-compact` | `0.2rem` | Spacing scale when `->compact()` is on |
| `--polder-theme-control-spacing` | `0.25rem` | Spacing scale of fields, buttons and the breadcrumb bar |
| `--polder-theme-radius-md` | `0.25rem` | Small corners: fields, buttons |
| `--polder-theme-radius-lg` | `0.3rem` | Medium corners: dropdowns, navigation items |
| `--polder-theme-radius-xl` | `0.35rem` | Large corners: sections, tables, the content card |
| `--polder-theme-inset` | `2 × --polder-theme-spacing` | Space around the content card |
| `--polder-theme-ground` | `gray-50` / `gray-900` | Frame and sidebar background |
| `--polder-theme-surface` | `white` / `gray-950` | Content card background |
| `--polder-theme-surface-ring` | `gray-950` 5% / `white` 10% | Content card outline |
| `--polder-theme-rule` | `gray-200` 50% / `gray-700` 50% | Breadcrumb bar divider |

```css
:root {
    --polder-theme-radius-xl: 0.75rem;
}

.dark .fi-body {
    --polder-theme-ground: var(--gray-950);
}
```

## Blade views

Polder replaces two Filament views to make its layout: `components/header/index` (the breadcrumb bar) and `components/user-menu`. Each one starts from the Filament v5 view and adds only the theme's changes. CI runs every week against the latest Filament 5 release to find changes in those views early.

## Testing

```bash
composer test
composer analyse
```

## License

MIT. See [LICENSE.md](LICENSE.md).
