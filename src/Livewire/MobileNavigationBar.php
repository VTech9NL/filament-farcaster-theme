<?php

declare(strict_types=1);

namespace VTech9NL\PolderTheme\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasTenants;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use VTech9NL\PolderTheme\MobileNavigation\MobileNavigation;
use VTech9NL\PolderTheme\PolderThemePlugin;

class MobileNavigationBar extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public const CENTER_ACTION = 'polderCenter';

    public ?string $panelId = null;

    public int|string|null $tenantKey = null;

    public ?string $routeName = null;

    /**
     * @var array<string, scalar>
     */
    public array $routeParameters = [];

    public function mount(): void
    {
        $route = request()->route();

        $this->panelId = Filament::getCurrentPanel()?->getId();
        $this->tenantKey = Filament::getTenant()?->getKey();
        $this->routeName = $route?->getName();
        $this->routeParameters = collect($route?->parameters() ?? [])
            ->map(fn (mixed $parameter): mixed => $parameter instanceof Model ? $parameter->getKey() : $parameter)
            ->filter(fn (mixed $parameter): bool => is_scalar($parameter))
            ->all();
    }

    /**
     * Livewire update requests skip the panel middleware, so the panel and tenant are bound again here.
     */
    public function boot(): void
    {
        if (filled($this->panelId) && (Filament::getCurrentPanel()?->getId() !== $this->panelId)) {
            Filament::setCurrentPanel($this->panelId);
        }

        if (filled($this->tenantKey) && (! Filament::getTenant())) {
            $this->bindTenant();
        }

        $centerAction = $this->getMobileNavigation()?->getCenterAction([
            'routeName' => $this->routeName,
            'routeParameters' => $this->routeParameters,
        ]);

        if ($centerAction) {
            $this->cacheAction($centerAction->name(static::CENTER_ACTION));
        }
    }

    protected function bindTenant(): void
    {
        /** @var class-string<Model>|null $tenantModel */
        $tenantModel = Filament::getCurrentPanel()?->getTenantModel();
        $tenant = $tenantModel ? $tenantModel::query()->find($this->tenantKey) : null;
        $user = Filament::auth()->user();

        if ($tenant && ($user instanceof HasTenants) && $user->canAccessTenant($tenant)) {
            Filament::setTenant($tenant, isQuiet: true);
        }
    }

    protected function getMobileNavigation(): ?MobileNavigation
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel?->hasPlugin(PolderThemePlugin::ID)) {
            return null;
        }

        /** @var PolderThemePlugin $plugin */
        $plugin = $panel->getPlugin(PolderThemePlugin::ID);

        return $plugin->getMobileNavigation();
    }

    public function render(): View
    {
        return view('polder-theme::livewire.mobile-navigation-bar', [
            'hasCenterAction' => array_key_exists(static::CENTER_ACTION, $this->cachedActions),
        ]);
    }
}
