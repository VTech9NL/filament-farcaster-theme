<span class="polder-theme-mobile-navigation-icon">
    @if (filled($entry['icon']))
        {!! $entry['icon'] !!}
    @elseif (($entry['sheet'] ?? null) === \VTech9NL\PolderTheme\MobileNavigation\MobileNavigationItem::SHEET_USER_MENU)
        <x-filament-panels::avatar.user :user="filament()->auth()->user()" loading="lazy" />
    @endif
</span>

<span class="polder-theme-mobile-navigation-label">{{ $entry['label'] }}</span>
