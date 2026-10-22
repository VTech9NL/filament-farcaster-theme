@props([
    'actions' => [],
    'actionsAlignment' => null,
    'breadcrumbs' => [],
    'heading' => null,
    'subheading' => null,
])

@php
    use Filament\Support\Facades\FilamentView;
    use Filament\View\PanelsRenderHook;
    use VTech9NL\PolderTheme\PolderRenderHook;
    use VTech9NL\PolderTheme\PolderThemePlugin;

    use function Filament\Support\is_slot_empty;
@endphp

@if (! filament()->getCurrentPanel()?->hasPlugin(PolderThemePlugin::ID))
    @include('polder-theme::stock.header')
@else
<div class="polder-theme-breadcrumb-bar">
    @if (filament()->hasNavigation() && (! filament()->hasTopbar()))
        <button
            type="button"
            x-data="{}"
            x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open($event.currentTarget)"
            x-bind:aria-label="$store.sidebar.isOpen ? @js(__('filament-panels::layout.actions.sidebar.collapse.label')) : @js(__('filament-panels::layout.actions.sidebar.expand.label'))"
            class="polder-theme-sidebar-toggle"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2" /><path d="M9 3v18" /></svg>
        </button>
    @endif

    @if ($breadcrumbs)
        <x-filament::breadcrumbs :breadcrumbs="$breadcrumbs" />
    @endif

    <div class="polder-theme-breadcrumb-bar-end">
        {{ FilamentView::renderHook(PolderRenderHook::BREADCRUMB_BAR_END, scopes: $this->getRenderHookScopes()) }}
    </div>
</div>

<header
    {{
        $attributes->class([
            'fi-header',
            'fi-header-has-subheading' => filled($subheading),
        ])
    }}
>
    @capture($headingContent)
        {{ FilamentView::renderHook(PanelsRenderHook::PAGE_HEADER_HEADING_BEFORE, scopes: $this->getRenderHookScopes()) }}

        @if (filled($heading))
            <h1 class="fi-header-heading">
                {{ $heading }}
            </h1>
        @endif

        {{ FilamentView::renderHook(PanelsRenderHook::PAGE_HEADER_HEADING_AFTER, scopes: $this->getRenderHookScopes()) }}

        @if (filled($subheading))
            <p class="fi-header-subheading">
                {{ $subheading }}
            </p>
        @endif
    @endcapture

    @php
        $headingContent = $headingContent();
    @endphp

    @if (! is_slot_empty($headingContent))
        <div>{{ $headingContent }}</div>
    @else
        {{ $headingContent }}
    @endif

    @php
        $beforeActions = FilamentView::renderHook(PanelsRenderHook::PAGE_HEADER_ACTIONS_BEFORE, scopes: $this->getRenderHookScopes());
        $afterActions = FilamentView::renderHook(PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER, scopes: $this->getRenderHookScopes());
    @endphp

    @capture($actionsContent)
        {{ $beforeActions }}

        @if ($actions)
            <x-filament::actions
                :actions="$actions"
                :alignment="$actionsAlignment"
            />
        @endif

        {{ $afterActions }}
    @endcapture

    @php
        $actionsContent = $actionsContent();
    @endphp

    @if (! is_slot_empty($actionsContent))
        <div class="fi-header-actions-ctn">
            {{ $actionsContent }}
        </div>
    @else
        {{ $actionsContent }}
    @endif
</header>
@endif
