@php
    use VTech9NL\PolderTheme\MobileNavigation\MobileNavigationItem;

    $classes = \Illuminate\Support\Arr::toCssClasses([
        $class,
        'polder-theme-active' => $entry['active'] ?? false,
    ]);
@endphp

@if ($entry['type'] === MobileNavigationItem::TYPE_LINK)
    <a href="{{ $entry['url'] }}" class="{{ $classes }}" @if ($entry['active'] ?? false) aria-current="page" @endif>
        @include('polder-theme::livewire.partials.mobile-navigation-entry-content')
    </a>
@elseif ($entry['type'] === MobileNavigationItem::TYPE_ACTION)
    <button type="button" class="{{ $classes }}" x-on:click="sheet = null" wire:click="mountAction(@js($entry['key']))">
        @include('polder-theme::livewire.partials.mobile-navigation-entry-content')
    </button>
@else
    <button type="button" class="{{ $classes }}" x-on:click="sheet = @js($entry['key'])" aria-haspopup="dialog">
        @include('polder-theme::livewire.partials.mobile-navigation-entry-content')
    </button>
@endif
