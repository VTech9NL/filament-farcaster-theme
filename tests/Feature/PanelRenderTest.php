<?php

use Filament\Facades\Filament;
use Illuminate\Support\Str;
use VTech9NL\PolderTheme\PolderThemeServiceProvider;
use VTech9NL\PolderTheme\Tests\Fixtures\Team;
use VTech9NL\PolderTheme\Tests\Fixtures\User;

use function Orchestra\Testbench\package_path;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->user = User::query()->create(['name' => 'Ada Lovelace', 'email' => 'ada@example.com', 'password' => 'secret']);
    $this->team = Team::query()->create(['name' => 'Analytical Engine']);
    $this->user->teams()->attach($this->team);

    actingAs($this->user);
});

it('renders the breadcrumb bar and the user menu header from the theme views', function () {
    get('/app/'.$this->team->getKey())
        ->assertOk()
        ->assertSee('polder-theme-breadcrumb-bar')
        ->assertSee('ada@example.com');
});

it('offers the other tenants in the user menu when the tenant menu is off', function () {
    $this->user->teams()->attach(Team::query()->create(['name' => 'Difference Engine']));

    get('/app/'.$this->team->getKey())
        ->assertOk()
        ->assertSee(__('polder::user-menu.switch_tenant'))
        ->assertSee('Difference Engine');
});

it('leaves tenant switching to the tenant menu when the panel shows it', function () {
    Filament::getPanel('app')->tenantMenu();
    $this->user->teams()->attach(Team::query()->create(['name' => 'Difference Engine']));

    get('/app/'.$this->team->getKey())
        ->assertOk()
        ->assertDontSee(__('polder::user-menu.switch_tenant'));
});

it('has no tenant switcher when there is no other tenant', function () {
    get('/app/'.$this->team->getKey())
        ->assertOk()
        ->assertDontSee(__('polder::user-menu.switch_tenant'));
});

it('switches density to compact only on a compact panel', function () {
    $compactDeclaration = '--polder-theme-spacing:var(--polder-theme-spacing-compact)';

    get('/compact')->assertOk()->assertSee($compactDeclaration, escape: false);
    get('/app/'.$this->team->getKey())->assertOk()->assertDontSee($compactDeclaration, escape: false);
});

it('fixes the layer order before any stylesheet can declare it', function () {
    $html = get('/app/'.$this->team->getKey())->assertOk()->getContent();

    $layerOrderPosition = strpos($html, '@layer '.PolderThemeServiceProvider::LAYER_ORDER.';');

    expect($layerOrderPosition)->not->toBeFalse()
        ->and($layerOrderPosition)->toBeLessThan(strpos($html, '<link'));
});

it('leaves the sidebar toggle to the topbar when the panel keeps it', function () {
    get('/app/'.$this->team->getKey())->assertSee('polder-theme-sidebar-toggle');

    Filament::getPanel('app')->topbar();

    get('/app/'.$this->team->getKey())->assertDontSee('polder-theme-sidebar-toggle');
});

it('leaves panels without the plugin on Filament\'s own views', function () {
    $html = get('/plain/breadcrumb-page')->assertOk()->getContent();
    $header = Str::betweenFirst($html, 'class="fi-header', '</header>');

    expect($html)->not->toContain('polder-theme-')
        ->and($header)->toContain('fi-breadcrumbs')
        ->and($html)->toContain('fi-header-has-breadcrumbs')
        ->and($html)->toContain('fi-user-menu-trigger');
});

it('moves the breadcrumbs into the bar on panels with the plugin', function () {
    $html = get('/app/'.$this->team->getKey().'/breadcrumb-page')->assertOk()->getContent();
    $bar = Str::betweenFirst($html, 'class="polder-theme-breadcrumb-bar"', 'class="fi-header');
    $header = Str::betweenFirst($html, 'class="fi-header', '</header>');

    expect($bar)->toContain('Polder crumb')
        ->and($header)->not->toContain('fi-breadcrumbs');
});

it('still matches the Filament views it replaces', function (string $view, string $hash) {
    expect(hash_file('sha256', package_path("vendor/filament/filament/resources/views/{$view}")))->toBe($hash);
})->with([
    'header' => ['components/header/index.blade.php', '3b82f6e042cfacb6f086b71f1190ca143432a04206b3d535b74d9fe0b75d033d'],
    'user menu' => ['components/user-menu.blade.php', 'd4ee9665fe2378fce86f2993cedc0f6257a618bad8eda043b7def336e71006d8'],
]);
