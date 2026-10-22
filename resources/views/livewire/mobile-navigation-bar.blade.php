<nav class="polder-theme-mobile-navigation">
    @if ($hasCenterAction)
        {{ $this->getAction(\VTech9NL\PolderTheme\Livewire\MobileNavigationBar::CENTER_ACTION) }}
    @endif

    <x-filament-actions::modals />
</nav>
