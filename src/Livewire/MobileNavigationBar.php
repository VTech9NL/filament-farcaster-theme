<?php

declare(strict_types=1);

namespace VTech9NL\PolderTheme\Livewire;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Livewire\Concerns\HasTenantMenu;
use Filament\Livewire\Concerns\HasUserMenu;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use Livewire\Component;
use VTech9NL\PolderTheme\MobileNavigation\MobileNavigation;
use VTech9NL\PolderTheme\MobileNavigation\MobileNavigationItem;
use VTech9NL\PolderTheme\PolderThemePlugin;

use function Filament\Support\generate_icon_html;

class MobileNavigationBar extends Component implements HasActions, HasSchemas
{
    use HasTenantMenu;
    use HasUserMenu;
    use InteractsWithActions;
    use InteractsWithSchemas;

    public const CENTER_ACTION = 'polderCenter';

    #[Locked]
    public ?string $panelId = null;

    #[Locked]
    public int|string|null $tenantKey = null;

    #[Locked]
    public ?string $routeName = null;

    /**
     * @var array<string, scalar>
     */
    #[Locked]
    public array $routeParameters = [];

    /**
     * Resolved once on the page request, because links and active states depend on the page route.
     *
     * @var list<array<string, mixed>>
     */
    #[Locked]
    public array $items = [];

    /**
     * @param  array<string, scalar>  $routeParameters
     */
    public function mount(?string $routeName = null, array $routeParameters = []): void
    {
        $this->panelId = Filament::getCurrentPanel()?->getId();
        $this->tenantKey = Filament::getTenant()?->getKey();
        $this->routeName = $routeName;
        $this->routeParameters = $routeParameters;
        $this->items = $this->resolveItems();
    }

    /**
     * Livewire update requests skip the panel middleware, so the panel and tenant are bound again here.
     */
    public function boot(): void
    {
        if (filled($this->panelId) && (Filament::getCurrentPanel()?->getId() !== $this->panelId)) {
            Filament::setCurrentPanel($this->panelId);
        }

        $user = Filament::auth()->user();
        $panel = Filament::getCurrentPanel();

        abort_unless($user && $panel && ((! $user instanceof FilamentUser) || $user->canAccessPanel($panel)), 403);

        if (filled($this->tenantKey) && (! Filament::getTenant())) {
            $this->bindTenant($user);
        }

        $navigation = $this->getMobileNavigation();

        if (! $navigation) {
            return;
        }

        if ($centerAction = $navigation->getCenterAction()) {
            $this->cacheAction($centerAction->name(static::CENTER_ACTION));
        }

        foreach ($this->getActionItems($navigation) as $name => $action) {
            $this->cacheAction($action->name($name));
        }
    }

    protected function bindTenant(Authenticatable $user): void
    {
        /** @var class-string<Model>|null $tenantModel */
        $tenantModel = Filament::getCurrentPanel()?->getTenantModel();
        $tenant = $tenantModel ? $tenantModel::query()->find($this->tenantKey) : null;

        abort_unless($tenant && ($user instanceof HasTenants) && $user->canAccessTenant($tenant), 403);

        Filament::setTenant($tenant, isQuiet: true);
    }

    protected function getMobileNavigation(): ?MobileNavigation
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel?->hasPlugin(PolderThemePlugin::ID)) {
            return null;
        }

        /** @var PolderThemePlugin $plugin */
        $plugin = $panel->getPlugin(PolderThemePlugin::ID);

        return $plugin->getMobileNavigation()?->pageContext([
            'routeName' => $this->routeName,
            'routeParameters' => $this->routeParameters,
        ]);
    }

    /**
     * @return array<string, Action>
     */
    protected function getActionItems(MobileNavigation $navigation): array
    {
        $actions = [];

        foreach ($navigation->getItems() as $index => $item) {
            if ($item->getType() === MobileNavigationItem::TYPE_ACTION && ($action = $item->getAction())) {
                $actions["polderItem{$index}"] = $action;
            }

            if (($item->getType() === MobileNavigationItem::TYPE_SHEET) && ($item->getSheet() === MobileNavigationItem::SHEET_ITEMS)) {
                foreach ($item->getSheetItems() as $sheetIndex => $sheetItem) {
                    $sheetItem->pageContext(['routeName' => $this->routeName, 'routeParameters' => $this->routeParameters]);

                    if (($sheetItem->getType() === MobileNavigationItem::TYPE_ACTION) && ($action = $sheetItem->getAction())) {
                        $actions["polderItem{$index}_{$sheetIndex}"] = $action;
                    }
                }
            }
        }

        return $actions;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function resolveItems(): array
    {
        $navigation = $this->getMobileNavigation();

        if (! $navigation) {
            return [];
        }

        $items = [];

        foreach ($navigation->getItems() as $index => $item) {
            $resolved = $this->resolveEntry($item, "polderItem{$index}");

            if ($item->getType() === MobileNavigationItem::TYPE_SHEET) {
                $resolved['sheet'] = $item->getSheet();
                $resolved['view'] = $item->getSheetView();
                $resolved['groups'] = match ($item->getSheet()) {
                    MobileNavigationItem::SHEET_NAVIGATION => $this->resolvePanelNavigation(),
                    MobileNavigationItem::SHEET_ITEMS => [[
                        'label' => null,
                        'items' => array_map(
                            fn (MobileNavigationItem $sheetItem, int $sheetIndex): array => $this->resolveEntry(
                                $sheetItem->pageContext(['routeName' => $this->routeName, 'routeParameters' => $this->routeParameters]),
                                "polderItem{$index}_{$sheetIndex}",
                            ),
                            $item->getSheetItems(),
                            array_keys($item->getSheetItems()),
                        ),
                    ]],
                    default => [],
                };
            }

            $items[] = $resolved;
        }

        return $items;
    }

    /**
     * @return array<string, mixed>
     */
    protected function resolveEntry(MobileNavigationItem $item, string $key): array
    {
        $action = ($item->getType() === MobileNavigationItem::TYPE_ACTION) ? $item->getAction() : null;

        return [
            'key' => $key,
            'type' => $item->getType(),
            'label' => $this->labelText($action?->getLabel() ?? $item->getLabel()),
            'icon' => $this->iconHtml($item->getIcon() ?? $action?->getIcon()),
            'url' => ($item->getType() === MobileNavigationItem::TYPE_LINK) ? $item->getUrl() : null,
            'active' => ($item->getType() === MobileNavigationItem::TYPE_LINK) && $item->isActive(),
        ];
    }

    /**
     * @return array<int, array{label: ?string, items: array<int, array<string, mixed>>}>
     */
    protected function resolvePanelNavigation(): array
    {
        return collect(Filament::getCurrentPanel()?->getNavigation() ?? [])
            ->map(fn (NavigationGroup $group): array => [
                'label' => filled($group->getLabel()) ? $this->labelText($group->getLabel()) : null,
                'items' => collect($group->getItems())
                    ->reject(fn (NavigationItem $item): bool => $item->isHidden())
                    ->map(fn (NavigationItem $item): array => [
                        'key' => null,
                        'type' => MobileNavigationItem::TYPE_LINK,
                        'label' => $this->labelText($item->getLabel()),
                        'icon' => $this->iconHtml($item->getIcon() ?? $group->getIcon()),
                        'url' => $item->getUrl(),
                        'active' => $item->isActive(),
                    ])
                    ->values()
                    ->all(),
            ])
            ->filter(fn (array $group): bool => filled($group['items']))
            ->values()
            ->all();
    }

    protected function labelText(string|Htmlable $label): string
    {
        return $label instanceof Htmlable ? strip_tags($label->toHtml()) : $label;
    }

    protected function iconHtml(string|BackedEnum|Htmlable|null $icon): ?string
    {
        return filled($icon) ? generate_icon_html($icon)?->toHtml() : null;
    }

    public function render(): View
    {
        $navigation = $this->getMobileNavigation();
        $hasCenterAction = array_key_exists(static::CENTER_ACTION, $this->cachedActions);
        $leftCount = $hasCenterAction ? (int) ceil(count($this->items) / 2) : count($this->items);

        return view('polder-theme::livewire.mobile-navigation-bar', [
            'style' => $navigation?->getStyle()->value,
            'centerButton' => $navigation?->getCenterButton()->value,
            'hasCenterAction' => $hasCenterAction,
            'leftItems' => array_slice($this->items, 0, $leftCount),
            'rightItems' => array_slice($this->items, $leftCount),
            'sheets' => array_values(array_filter($this->items, fn (array $item): bool => $item['type'] === MobileNavigationItem::TYPE_SHEET)),
        ]);
    }
}
