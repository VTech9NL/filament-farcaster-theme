<?php

declare(strict_types=1);

namespace VTech9NL\FarcasterTheme\Tests\Fixtures;

use Filament\Http\Middleware\Authenticate;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use VTech9NL\FarcasterTheme\FarcasterThemePlugin;

class CompactPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('compact')
            ->path('compact')
            ->pages([Dashboard::class])
            ->middleware([EncryptCookies::class, StartSession::class, ShareErrorsFromSession::class])
            ->authMiddleware([Authenticate::class])
            ->plugin(FarcasterThemePlugin::make()->compact());
    }
}
