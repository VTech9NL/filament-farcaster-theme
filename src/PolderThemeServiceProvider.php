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
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'polder');

        $this->publishes([
            __DIR__.'/../resources/lang' => $this->app->langPath('vendor/polder'),
        ], 'polder-translations');

        View::prependNamespace('filament-panels', __DIR__.'/../resources/views');

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => $this->isCompactPanel()
                ? '<style>:root{--polder-theme-spacing:var(--polder-theme-spacing-compact)}</style>'
                : '',
        );
    }

    protected function isCompactPanel(): bool
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel?->hasPlugin(PolderThemePlugin::ID)) {
            return false;
        }

        /** @var PolderThemePlugin $plugin */
        $plugin = $panel->getPlugin(PolderThemePlugin::ID);

        return $plugin->isCompact();
    }
}
