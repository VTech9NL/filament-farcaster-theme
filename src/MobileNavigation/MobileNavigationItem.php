<?php

declare(strict_types=1);

namespace VTech9NL\PolderTheme\MobileNavigation;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Illuminate\Contracts\Support\Htmlable;
use VTech9NL\PolderTheme\MobileNavigation\Concerns\EvaluatesWithPageContext;

class MobileNavigationItem
{
    use EvaluatesWithPageContext;

    public const TYPE_LINK = 'link';

    public const TYPE_ACTION = 'action';

    public const TYPE_SHEET = 'sheet';

    public const SHEET_ITEMS = 'items';

    public const SHEET_NAVIGATION = 'navigation';

    public const SHEET_USER_MENU = 'userMenu';

    public const SHEET_VIEW = 'view';

    protected string|Closure|null $label = null;

    protected string|BackedEnum|Htmlable|Closure|null $icon = null;

    protected string|Closure|null $url = null;

    protected bool|Closure|null $isActive = null;

    protected Action|Closure|null $action = null;

    protected string $sheet = self::SHEET_ITEMS;

    /**
     * @var array<MobileNavigationItem> | Closure
     */
    protected array|Closure $sheetItems = [];

    protected ?string $sheetView = null;

    final public function __construct(
        protected string $name,
        protected string $type,
    ) {}

    public static function link(string $name): static
    {
        return new static($name, self::TYPE_LINK);
    }

    public static function action(Action|Closure $action): static
    {
        $item = new static('', self::TYPE_ACTION);
        $item->action = $action;

        return $item;
    }

    public static function sheet(string $name): static
    {
        return new static($name, self::TYPE_SHEET);
    }

    public function label(string|Closure|null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function icon(string|BackedEnum|Htmlable|Closure|null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function url(string|Closure|null $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function isActiveWhen(bool|Closure|null $condition): static
    {
        $this->isActive = $condition;

        return $this;
    }

    /**
     * @param  array<MobileNavigationItem> | Closure  $items
     */
    public function items(array|Closure $items): static
    {
        $this->sheet = self::SHEET_ITEMS;
        $this->sheetItems = $items;

        return $this;
    }

    public function navigation(): static
    {
        $this->sheet = self::SHEET_NAVIGATION;

        return $this;
    }

    public function userMenu(): static
    {
        $this->sheet = self::SHEET_USER_MENU;

        return $this;
    }

    public function view(string $view): static
    {
        $this->sheet = self::SHEET_VIEW;
        $this->sheetView = $view;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLabel(): string
    {
        return $this->evaluate($this->label) ?? match ($this->sheet) {
            self::SHEET_NAVIGATION => __('polder::mobile-navigation.more'),
            self::SHEET_USER_MENU => __('polder::mobile-navigation.account'),
            default => str($this->name)->headline()->toString(),
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return $this->evaluate($this->icon) ?? match ($this->sheet) {
            self::SHEET_NAVIGATION => 'heroicon-o-ellipsis-horizontal',
            default => null,
        };
    }

    public function getUrl(): ?string
    {
        return $this->evaluate($this->url);
    }

    public function isActive(): bool
    {
        if ($this->isActive !== null) {
            return (bool) $this->evaluate($this->isActive);
        }

        $url = $this->getUrl();

        return filled($url) && (url()->current() === strtok($url, '?'));
    }

    public function getAction(): ?Action
    {
        return $this->evaluate($this->action);
    }

    public function getSheet(): string
    {
        return $this->sheet;
    }

    /**
     * @return array<MobileNavigationItem>
     */
    public function getSheetItems(): array
    {
        return $this->evaluate($this->sheetItems);
    }

    public function getSheetView(): ?string
    {
        return $this->sheetView;
    }
}
