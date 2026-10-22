<?php

declare(strict_types=1);

namespace VTech9NL\PolderTheme;

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
