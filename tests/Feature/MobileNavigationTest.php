<?php

use Filament\Actions\Action;
use Filament\Facades\Filament;
use VTech9NL\PolderTheme\Livewire\MobileNavigationBar;
use VTech9NL\PolderTheme\MobileNavigation\MobileNavigation;
use VTech9NL\PolderTheme\PolderThemePlugin;
use VTech9NL\PolderTheme\Tests\Fixtures\Team;
use VTech9NL\PolderTheme\Tests\Fixtures\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::query()->create(['name' => 'Ada Lovelace', 'email' => 'ada@example.com', 'password' => 'secret']);
    $this->team = Team::query()->create(['name' => 'Analytical Engine']);
    $this->user->teams()->attach($this->team);

    actingAs($this->user);

    $this->ran = null;

    Filament::getPanel('app')->getPlugin(PolderThemePlugin::ID)->mobileNavigation(
        MobileNavigation::make()->centerAction(fn (?string $routeName, array $routeParameters): Action => Action::make('logActivity')
            ->action(function () use ($routeName, $routeParameters): void {
                $this->ran = [
                    'panel' => Filament::getCurrentPanel()?->getId(),
                    'tenant' => Filament::getTenant()?->getKey(),
                    'routeName' => $routeName,
                    'routeParameters' => $routeParameters,
                ];
            })),
    );
});

it('renders the bar on a panel page and remembers that page', function () {
    get('/app/'.$this->team->getKey())
        ->assertOk()
        ->assertSee('polder-theme-mobile-navigation')
        ->assertSee('filament.app.pages.dashboard');
});

it('runs the app centre action with the panel, tenant and page it was mounted on', function () {
    Filament::setCurrentPanel('app');
    Filament::setTenant($this->team, isQuiet: true);

    $bar = livewire(MobileNavigationBar::class)
        ->set('routeName', 'filament.app.pages.dashboard')
        ->set('routeParameters', ['tenant' => $this->team->getKey()]);

    Filament::setCurrentPanel('compact');
    Filament::setTenant(null, isQuiet: true);

    $bar->callAction(MobileNavigationBar::CENTER_ACTION);

    expect($this->ran)->toBe([
        'panel' => 'app',
        'tenant' => $this->team->getKey(),
        'routeName' => 'filament.app.pages.dashboard',
        'routeParameters' => ['tenant' => $this->team->getKey()],
    ]);
});

it('renders no bar on a panel without mobile navigation', function () {
    get('/compact')->assertOk()->assertDontSee('polder-theme-mobile-navigation');
});
