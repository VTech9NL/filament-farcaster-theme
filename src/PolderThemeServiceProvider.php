<?php

declare(strict_types=1);

namespace VTech9NL\PolderTheme;

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use VTech9NL\PolderTheme\Livewire\MobileNavigationBar;

class PolderThemeServiceProvider extends ServiceProvider
{
    public const LAYER_ORDER = 'properties, theme, base, components, polder-theme, utilities';

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'polder');

        $this->publishes([
            __DIR__.'/../resources/lang' => $this->app->langPath('vendor/polder'),
        ], 'polder-translations');

        View::prependNamespace('filament-panels', __DIR__.'/../resources/views');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'polder-theme');

        Livewire::component('polder-theme.mobile-navigation-bar', MobileNavigationBar::class);

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_START,
            fn (): string => $this->getPlugin() instanceof PolderThemePlugin
                ? '<style>@layer '.self::LAYER_ORDER.';</style>'
                : '',
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => $this->getPlugin()?->isCompact()
                ? '<style>:root{--polder-theme-spacing:var(--polder-theme-spacing-compact)}</style>'
                : '',
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            function (): string {
                $navigation = $this->getPlugin()?->getMobileNavigation();
                $pageRoute = $this->getPageRoute();

                if (! Filament::auth()->check() || ! $navigation?->pageContext($pageRoute)->isVisible()) {
                    return '';
                }

                return Blade::render(
                    "@livewire('polder-theme.mobile-navigation-bar', ['routeName' => \$routeName, 'routeParameters' => \$routeParameters])",
                    $pageRoute,
                );
            },
        );
    }

    /**
     * @return array{routeName: ?string, routeParameters: array<string, scalar>}
     */
    protected function getPageRoute(): array
    {
        $route = request()->route();

        return [
            'routeName' => $route?->getName(),
            'routeParameters' => collect($route?->parameters() ?? [])
                ->map(fn (mixed $parameter): mixed => $parameter instanceof Model ? $parameter->getKey() : $parameter)
                ->filter(fn (mixed $parameter): bool => is_scalar($parameter))
                ->all(),
        ];
    }

    protected function getPlugin(): ?PolderThemePlugin
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel?->hasPlugin(PolderThemePlugin::ID)) {
            return null;
        }

        /** @var PolderThemePlugin */
        return $panel->getPlugin(PolderThemePlugin::ID);
    }
}
