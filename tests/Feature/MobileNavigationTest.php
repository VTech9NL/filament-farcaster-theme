<?php

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use VTech9NL\PolderTheme\Livewire\MobileNavigationBar;
use VTech9NL\PolderTheme\MobileNavigation\MobileNavigation;
use VTech9NL\PolderTheme\MobileNavigation\MobileNavigationItem;
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
        MobileNavigation::make()
            ->items([
                MobileNavigationItem::link('home')->label('Home')->url(fn (): string => Dashboard::getUrl()),
                MobileNavigationItem::action(fn (): Action => Action::make('scan')->label('Scan')->action(fn () => $this->ran = 'scan')),
                MobileNavigationItem::sheet('tools')->label('Tools')->items([
                    MobileNavigationItem::action(fn (): Action => Action::make('export')->label('Export')->action(fn () => $this->ran = 'export')),
                ]),
                MobileNavigationItem::sheet('more')->navigation(),
                MobileNavigationItem::sheet('account')->userMenu(),
            ])
            ->centerAction(fn (?string $routeName, array $routeParameters): Action => Action::make('logActivity')
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

    $bar = livewire(MobileNavigationBar::class, [
        'routeName' => 'filament.app.pages.dashboard',
        'routeParameters' => ['tenant' => (string) $this->team->getKey()],
    ]);

    Filament::setCurrentPanel('compact');
    Filament::setTenant(null, isQuiet: true);

    $bar->callAction(MobileNavigationBar::CENTER_ACTION);

    expect($this->ran)->toBe([
        'panel' => 'app',
        'tenant' => $this->team->getKey(),
        'routeName' => 'filament.app.pages.dashboard',
        'routeParameters' => ['tenant' => (string) $this->team->getKey()],
    ]);
});

it('does not let the browser change the panel, tenant or page it was mounted on', function () {
    Filament::setCurrentPanel('app');
    Filament::setTenant($this->team, isQuiet: true);

    livewire(MobileNavigationBar::class)->set('tenantKey', 999);
})->throws(CannotUpdateLockedPropertyException::class);

it('refuses a tenant the user does not belong to', function () {
    Filament::setCurrentPanel('app');
    Filament::setTenant(Team::query()->create(['name' => 'Difference Engine']), isQuiet: true);

    $bar = livewire(MobileNavigationBar::class);
    Filament::setTenant(null, isQuiet: true);

    $bar->call('mountAction', MobileNavigationBar::CENTER_ACTION)->assertForbidden();

    expect($this->ran)->toBeNull();
});

it('refuses a signed-out request', function () {
    Filament::setCurrentPanel('app');
    Filament::setTenant($this->team, isQuiet: true);

    $bar = livewire(MobileNavigationBar::class);
    auth()->logout();

    $bar->call('mountAction', MobileNavigationBar::CENTER_ACTION)->assertForbidden();

    expect($this->ran)->toBeNull();
});

it('shows the configured items and marks the current page', function () {
    get('/app/'.$this->team->getKey())
        ->assertOk()
        ->assertSeeInOrder(['Home', 'Scan', 'Tools', 'More', 'Account'])
        ->assertSee('aria-current="page"', escape: false);
});

it('runs an action item from the bar and from a custom sheet', function () {
    Filament::setCurrentPanel('app');
    Filament::setTenant($this->team, isQuiet: true);

    livewire(MobileNavigationBar::class)->callAction('polderItem1');
    expect($this->ran)->toBe('scan');

    livewire(MobileNavigationBar::class)->callAction('polderItem2_0');
    expect($this->ran)->toBe('export');
});

it('lists the panel navigation in the More sheet', function () {
    Filament::setCurrentPanel('app');
    Filament::setTenant($this->team, isQuiet: true);

    $more = collect(livewire(MobileNavigationBar::class)->get('items'))->firstWhere('label', 'More');

    expect(collect($more['groups'])->pluck('items')->flatten(1)->pluck('label'))->toContain('Dashboard');
});

it('offers the user menu and the other tenants in the Account sheet', function () {
    $this->user->teams()->attach(Team::query()->create(['name' => 'Difference Engine']));

    $html = get('/app/'.$this->team->getKey())->assertOk()->getContent();
    $bar = substr($html, strpos($html, 'class="polder-theme-mobile-navigation '));

    expect($bar)
        ->toContain(__('filament-panels::layout.actions.logout.label'))
        ->toContain('Difference Engine');
});

it('hides the bar where the app says so', function () {
    $navigation = Filament::getPanel('app')->getPlugin(PolderThemePlugin::ID)->getMobileNavigation();

    $navigation->visible(fn (?string $routeName): bool => $routeName !== 'filament.app.pages.dashboard');
    get('/app/'.$this->team->getKey())->assertOk()->assertDontSee('polder-theme-mobile-navigation');

    $navigation->visible(fn (?string $routeName): bool => $routeName === 'filament.app.pages.dashboard');
    get('/app/'.$this->team->getKey())->assertOk()->assertSee('polder-theme-mobile-navigation');
});

it('accepts file uploads only into the fields of its actions', function () {
    Filament::setCurrentPanel('app');
    Filament::setTenant($this->team, isQuiet: true);

    livewire(MobileNavigationBar::class)->call('_startUpload', 'notAField', [], false)->assertForbidden();
});

it('renders no bar on a panel without mobile navigation', function () {
    get('/compact')->assertOk()->assertDontSee('polder-theme-mobile-navigation');
});
