<?php

declare(strict_types=1);

namespace VTech9NL\FarcasterTheme;

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class FarcasterThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'farcaster');

        $this->publishes([
            __DIR__.'/../resources/lang' => $this->app->langPath('vendor/farcaster'),
        ], 'farcaster-translations');

        View::prependNamespace('filament-panels', __DIR__.'/../resources/views');

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => $this->isCompactPanel()
                ? '<style>:root{--farcaster-spacing:var(--farcaster-spacing-compact)}</style>'
                : '',
        );
    }

    protected function isCompactPanel(): bool
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel?->hasPlugin(FarcasterThemePlugin::ID)) {
            return false;
        }

        /** @var FarcasterThemePlugin $plugin */
        $plugin = $panel->getPlugin(FarcasterThemePlugin::ID);

        return $plugin->isCompact();
    }
}
