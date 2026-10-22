<?php

declare(strict_types=1);

namespace VTech9NL\PolderTheme;

use Filament\Contracts\Plugin;
use Filament\Enums\UserMenuPosition;
use Filament\Panel;

class PolderThemePlugin implements Plugin
{
    public const ID = 'polder-theme';

    protected ?string $font = 'Instrument Sans';

    protected bool $hasTopbar = false;

    protected bool $isCompact = false;

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

    public function isCompact(): bool
    {
        return $this->isCompact;
    }

    public function register(Panel $panel): void
    {
        if (filled($this->font)) {
            $panel->font($this->font);
        }

        $panel
            ->topbar($this->hasTopbar)
            ->userMenu(position: $this->hasTopbar ? UserMenuPosition::Topbar : UserMenuPosition::Sidebar);
    }

    public function boot(Panel $panel): void {}
}
