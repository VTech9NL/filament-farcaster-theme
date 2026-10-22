<?php

use Filament\Facades\Filament;
use VTech9NL\FarcasterTheme\Tests\Fixtures\Team;
use VTech9NL\FarcasterTheme\Tests\Fixtures\User;

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
        ->assertSee('farcaster-breadcrumb-bar')
        ->assertSee('ada@example.com');
});

it('offers the other tenants in the user menu when the tenant menu is off', function () {
    $this->user->teams()->attach(Team::query()->create(['name' => 'Difference Engine']));

    get('/app/'.$this->team->getKey())
        ->assertOk()
        ->assertSee(__('farcaster::user-menu.switch_tenant'))
        ->assertSee('Difference Engine');
});

it('leaves tenant switching to the tenant menu when the panel shows it', function () {
    Filament::getPanel('app')->tenantMenu();
    $this->user->teams()->attach(Team::query()->create(['name' => 'Difference Engine']));

    get('/app/'.$this->team->getKey())
        ->assertOk()
        ->assertDontSee(__('farcaster::user-menu.switch_tenant'));
});

it('has no tenant switcher when there is no other tenant', function () {
    get('/app/'.$this->team->getKey())
        ->assertOk()
        ->assertDontSee(__('farcaster::user-menu.switch_tenant'));
});

it('switches density to compact only on a compact panel', function () {
    $compactDeclaration = '--farcaster-spacing:var(--farcaster-spacing-compact)';

    get('/compact')->assertOk()->assertSee($compactDeclaration, escape: false);
    get('/app/'.$this->team->getKey())->assertOk()->assertDontSee($compactDeclaration, escape: false);
});
