@php
    use Filament\Support\Icons\Heroicon;
    use VTech9NL\PolderTheme\Livewire\MobileNavigationBar;
    use VTech9NL\PolderTheme\MobileNavigation\MobileNavigationItem;

    use function Filament\Support\generate_icon_html;

    $centerAction = $hasCenterAction ? $this->getAction(MobileNavigationBar::CENTER_ACTION) : null;
    $user = filament()->auth()->user();
@endphp

<div
    x-data="{ sheet: null }"
    x-on:keydown.escape.window="sheet = null"
    class="polder-theme-mobile-navigation polder-theme-mobile-navigation-{{ $style }}"
>
    <nav
        aria-label="{{ __('polder::mobile-navigation.label') }}"
        @class([
            'polder-theme-mobile-navigation-bar',
            'polder-theme-mobile-navigation-bar-notched' => $centerAction && ($centerButton === 'raised'),
        ])
    >
        @foreach ($leftItems as $entry)
            @include('polder-theme::livewire.partials.mobile-navigation-entry', ['entry' => $entry, 'class' => 'polder-theme-mobile-navigation-item'])
        @endforeach

        @if ($centerAction)
            <button
                type="button"
                wire:click="mountAction(@js(MobileNavigationBar::CENTER_ACTION))"
                aria-label="{{ $centerAction->getLabel() }}"
                class="polder-theme-mobile-navigation-center polder-theme-mobile-navigation-center-{{ $centerButton }}"
            >
                <span class="polder-theme-mobile-navigation-icon">
                    {{ generate_icon_html($centerAction->getIcon() ?? Heroicon::Plus) }}
                </span>

                @if ($centerButton === 'flat')
                    <span class="polder-theme-mobile-navigation-label">{{ $centerAction->getLabel() }}</span>
                @endif
            </button>
        @endif

        @foreach ($rightItems as $entry)
            @include('polder-theme::livewire.partials.mobile-navigation-entry', ['entry' => $entry, 'class' => 'polder-theme-mobile-navigation-item'])
        @endforeach
    </nav>

    @foreach ($sheets as $sheet)
        <div x-cloak x-show="sheet === @js($sheet['key'])" class="polder-theme-mobile-navigation-sheet">
            <div
                x-show="sheet === @js($sheet['key'])"
                x-transition.opacity
                x-on:click="sheet = null"
                class="polder-theme-mobile-navigation-sheet-backdrop"
            ></div>

            <div
                x-show="sheet === @js($sheet['key'])"
                x-transition:enter-start="polder-theme-mobile-navigation-sheet-panel-hidden"
                x-transition:leave-end="polder-theme-mobile-navigation-sheet-panel-hidden"
                role="dialog"
                aria-modal="true"
                aria-label="{{ $sheet['label'] }}"
                class="polder-theme-mobile-navigation-sheet-panel"
            >
                <div class="polder-theme-mobile-navigation-sheet-handle"></div>

                @if ($sheet['sheet'] === MobileNavigationItem::SHEET_USER_MENU)
                    <div class="polder-theme-mobile-navigation-sheet-user">
                        <x-filament-panels::avatar.user :user="$user" loading="lazy" />

                        <div class="polder-theme-mobile-navigation-sheet-user-meta">
                            <div class="polder-theme-mobile-navigation-sheet-user-name">{{ filament()->getUserName($user) }}</div>

                            @if (filled($email = data_get($user, 'email')))
                                <div class="polder-theme-mobile-navigation-sheet-user-email">{{ $email }}</div>
                            @endif
                        </div>
                    </div>

                    <x-filament::dropdown.list>
                        @foreach ($this->getUserMenuItems() as $key => $menuItem)
                            @if (($key !== 'logout') && (($key !== 'profile') || filled($menuItem->getUrl()) || $menuItem->hasAction()))
                                {{ $menuItem }}
                            @endif
                        @endforeach
                    </x-filament::dropdown.list>

                    @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()) && filament()->hasThemeSwitcher())
                        <x-filament::dropdown.list>
                            <x-filament-panels::theme-switcher />
                        </x-filament::dropdown.list>
                    @endif

                    @if (filament()->hasTenancy() && filled($tenants = $this->getSwitchableTenants()))
                        <x-filament::dropdown.list>
                            <div class="polder-theme-user-menu-section-label">{{ __('polder::user-menu.switch_tenant') }}</div>

                            @foreach ($tenants as $tenant)
                                <x-filament::dropdown.list.item
                                    :href="filament()->getUrl($tenant)"
                                    :image="filament()->getTenantAvatarUrl($tenant)"
                                    tag="a"
                                >
                                    {{ filament()->getTenantName($tenant) }}
                                </x-filament::dropdown.list.item>
                            @endforeach
                        </x-filament::dropdown.list>
                    @endif

                    @if ($logout = ($this->getUserMenuItems()['logout'] ?? null))
                        <x-filament::dropdown.list>
                            {{ $logout }}
                        </x-filament::dropdown.list>
                    @endif
                @elseif ($sheet['sheet'] === MobileNavigationItem::SHEET_VIEW)
                    @include($sheet['view'])
                @else
                    @foreach ($sheet['groups'] as $group)
                        <div class="polder-theme-mobile-navigation-sheet-group">
                            @if (filled($group['label']))
                                <div class="polder-theme-user-menu-section-label">{{ $group['label'] }}</div>
                            @endif

                            <div class="polder-theme-mobile-navigation-sheet-grid">
                                @foreach ($group['items'] as $entry)
                                    @include('polder-theme::livewire.partials.mobile-navigation-entry', ['entry' => $entry, 'class' => 'polder-theme-mobile-navigation-tile'])
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endforeach

    <x-filament-actions::modals />
</div>
