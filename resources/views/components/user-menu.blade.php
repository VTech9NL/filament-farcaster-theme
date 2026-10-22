@props([
    'position' => null,
])

@php
    use Filament\Actions\Action;
    use Filament\Enums\UserMenuPosition;
    use Filament\Support\Facades\FilamentView;
    use Filament\Support\Icons\Heroicon;
    use Filament\Support\View\ComponentAttributeBag;
    use Filament\View\PanelsIconAlias;
    use Filament\View\PanelsRenderHook;
    use Illuminate\Support\Arr;

    $user = filament()->auth()->user();

    $userName = filament()->getUserName($user);
    $userEmail = data_get($user, 'email');

    $items = collect($this->getUserMenuItems());
    $logoutItem = $items->pull('logout');

    $itemsBeforeAndAfterThemeSwitcher = $items
        ->groupBy(fn (Action $item): bool => $item->getSort() < 0, preserveKeys: true)
        ->all();
    $itemsBeforeThemeSwitcher = $itemsBeforeAndAfterThemeSwitcher[true] ?? collect();
    $itemsAfterThemeSwitcher = $itemsBeforeAndAfterThemeSwitcher[false] ?? collect();

    $hasProfileHeader = $itemsBeforeThemeSwitcher->has('profile') &&
        blank(($item = Arr::first($itemsBeforeThemeSwitcher))->getUrl()) &&
        (! $item->hasAction());

    if ($hasProfileHeader) {
        unset($itemsBeforeThemeSwitcher['profile']);
    } elseif ($itemsBeforeThemeSwitcher->has('profile')) {
        $itemsBeforeThemeSwitcher = $itemsBeforeThemeSwitcher->prepend($itemsBeforeThemeSwitcher->pull('profile'), 'profile');
    }

    $multiGroupAfterTheme = $this->hasMultipleUserMenuItemGroups();
    $afterThemeItemGroups = $multiGroupAfterTheme
        ? array_filter(array_map(fn (array $group): array => Arr::except($group, 'logout'), $this->getUserMenuItemGroupsAfterTheme()))
        : [];

    $currentTenant = filament()->getTenant();
    $switchableTenants = ($currentTenant && (! filament()->hasTenantMenu())) ? $this->getSwitchableTenants() : [];

    $position ??= filament()->getUserMenuPosition();

    $isSidebarCollapsibleOnDesktop = filament()->isSidebarCollapsibleOnDesktop();
@endphp

{{ FilamentView::renderHook(PanelsRenderHook::USER_MENU_BEFORE) }}

<x-filament::dropdown
    :placement="($position === UserMenuPosition::Topbar) ? 'bottom-end' : 'top'"
    :width="($position === UserMenuPosition::Topbar) ? null : 'full'"
    :teleport="$position === UserMenuPosition::Topbar"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-user-menu'])
    "
>
    <x-slot name="trigger">
        <button
            aria-label="{{ filled($userName) ? $userName : __('filament-panels::layout.actions.open_user_menu.label') }}"
            type="button"
            class="fi-user-menu-trigger"
            x-data="{ isOpen: false }"
            x-on:click="isOpen = ! isOpen"
            x-on:click.outside="isOpen = false"
            x-bind:class="{ 'polder-user-menu-trigger-active': isOpen }"
        >
            <x-filament-panels::avatar.user :user="$user" loading="lazy" />

            @if ($position !== UserMenuPosition::Topbar)
                <span
                    @if ($isSidebarCollapsibleOnDesktop)
                        x-show="$store.sidebar.isOpen"
                    @endif
                    class="fi-user-menu-trigger-text"
                >
                    {{ $userName }}
                </span>

                {{
                    \Filament\Support\generate_icon_html(Heroicon::ChevronUpDown, alias: PanelsIconAlias::USER_MENU_TOGGLE_BUTTON, attributes: new ComponentAttributeBag([
                        'x-show' => $isSidebarCollapsibleOnDesktop ? '$store.sidebar.isOpen' : null,
                        'class' => 'polder-user-menu-trigger-icon',
                    ]))
                }}
            @endif
        </button>
    </x-slot>

    {{ FilamentView::renderHook(PanelsRenderHook::USER_MENU_PROFILE_BEFORE) }}

    <div class="polder-user-menu-header">
        <x-filament-panels::avatar.user :user="$user" loading="lazy" />

        <div class="polder-user-menu-header-meta">
            <div class="polder-user-menu-header-name">
                {{ $userName }}
            </div>

            @if (filled($userEmail))
                <div class="polder-user-menu-header-email">
                    {{ $userEmail }}
                </div>
            @endif
        </div>
    </div>

    {{ FilamentView::renderHook(PanelsRenderHook::USER_MENU_PROFILE_AFTER) }}

    @if ($itemsBeforeThemeSwitcher->isNotEmpty())
        <x-filament::dropdown.list>
            @foreach ($itemsBeforeThemeSwitcher as $item)
                {{ $item }}
            @endforeach
        </x-filament::dropdown.list>
    @endif

    @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()) && filament()->hasThemeSwitcher())
        <x-filament::dropdown.list>
            <x-filament-panels::theme-switcher />
        </x-filament::dropdown.list>
    @endif

    @if ($multiGroupAfterTheme && $afterThemeItemGroups !== [])
        @foreach ($afterThemeItemGroups as $afterThemeGroup)
            <x-filament::dropdown.list>
                @foreach ($afterThemeGroup as $item)
                    {{ $item }}
                @endforeach
            </x-filament::dropdown.list>
        @endforeach
    @elseif ($itemsAfterThemeSwitcher->isNotEmpty())
        <x-filament::dropdown.list>
            @foreach ($itemsAfterThemeSwitcher as $item)
                {{ $item }}
            @endforeach
        </x-filament::dropdown.list>
    @endif

    @if (filled($switchableTenants))
        <x-filament::dropdown.list>
            <div class="polder-user-menu-section-label">
                {{ __('polder::user-menu.switch_tenant') }}
            </div>

            <x-filament::dropdown.list.item :icon="Heroicon::Check" disabled>
                {{ filament()->getTenantName($currentTenant) }}
            </x-filament::dropdown.list.item>

            @foreach ($switchableTenants as $tenant)
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

    @if ($logoutItem)
        <x-filament::dropdown.list>
            {{ $logoutItem }}
        </x-filament::dropdown.list>
    @endif
</x-filament::dropdown>

{{ FilamentView::renderHook(PanelsRenderHook::USER_MENU_AFTER) }}
