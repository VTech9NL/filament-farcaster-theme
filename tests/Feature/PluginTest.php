<?php

use Filament\Enums\UserMenuPosition;
use Filament\Panel;
use VTech9NL\PolderTheme\PolderThemePlugin;

it('hides the topbar and moves the user menu into the sidebar', function () {
    $panel = Panel::make()->id('test')->plugin(PolderThemePlugin::make());

    expect($panel->hasTopbar())->toBeFalse()
        ->and($panel->getUserMenuPosition())->toBe(UserMenuPosition::Sidebar)
        ->and($panel->getFontFamily())->toBe('Instrument Sans');
});

it('keeps the topbar and its user menu when asked', function () {
    $panel = Panel::make()->id('test')->plugin(PolderThemePlugin::make()->topbar());

    expect($panel->hasTopbar())->toBeTrue()
        ->and($panel->getUserMenuPosition())->toBe(UserMenuPosition::Topbar);
});

it('leaves the panel font alone when the font is null', function () {
    $panel = Panel::make()->id('test')->font('Inter')->plugin(PolderThemePlugin::make()->font(null));

    expect($panel->getFontFamily())->toBe('Inter');
});
