<?php

declare(strict_types=1);

namespace VTech9NL\PolderTheme;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Enums\UserMenuPosition;
use Filament\Panel;
use VTech9NL\PolderTheme\MobileNavigation\MobileNavigation;

class PolderThemePlugin implements Plugin
{
    public const ID = 'polder-theme';

    protected ?string $font = 'Instrument Sans';

    protected bool $hasTopbar = false;

    protected ?string $sidebarWidth = '16rem';

    protected bool $isCompact = false;

    protected MobileNavigation|Closure|null $mobileNavigation = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static */
        return filament(static::ID);
    }

    public function getId(): string
    {
        return static::ID;
    }

    public function font(?string $family): static
    {
        $this->font = $family;

        return $this;
    }

    public function sidebarWidth(?string $width): static
    {
        $this->sidebarWidth = $width;

        return $this;
    }

    public function topbar(bool $condition = true): static
    {
        $this->hasTopbar = $condition;

        return $this;
    }

    public function compact(bool $condition = true): static
    {
        $this->isCompact = $condition;

        return $this;
    }

    public function mobileNavigation(MobileNavigation|Closure|null $navigation): static
    {
        $this->mobileNavigation = $navigation;

        return $this;
    }

    public function getMobileNavigation(): ?MobileNavigation
    {
        return $this->mobileNavigation instanceof Closure
            ? ($this->mobileNavigation)()
            : $this->mobileNavigation;
    }

    public function isCompact(): bool
    {
        return $this->isCompact;
    }

    public function register(Panel $panel): void
    {
        if (filled($this->font)) {
            $panel->font($this->font);
        }

        if (filled($this->sidebarWidth)) {
            $panel->sidebarWidth($this->sidebarWidth);
        }

        $panel
            ->topbar($this->hasTopbar)
            ->userMenu(position: $this->hasTopbar ? UserMenuPosition::Topbar : UserMenuPosition::Sidebar);
    }

    public function boot(Panel $panel): void {}
}
