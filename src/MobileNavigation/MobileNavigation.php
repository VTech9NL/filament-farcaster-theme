<?php

declare(strict_types=1);

namespace VTech9NL\PolderTheme\MobileNavigation;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Concerns\EvaluatesClosures;

class MobileNavigation
{
    use EvaluatesClosures;

    protected Action|Closure|null $centerAction = null;

    /**
     * @var array<string, mixed>
     */
    protected array $pageContext = [];

    public static function make(): static
    {
        return app(static::class);
    }

    public function centerAction(Action|Closure|null $action): static
    {
        $this->centerAction = $action;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $pageContext
     */
    public function getCenterAction(array $pageContext = []): ?Action
    {
        $this->pageContext = $pageContext;

        return $this->evaluate($this->centerAction);
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'routeName' => [$this->pageContext['routeName'] ?? null],
            'routeParameters' => [$this->pageContext['routeParameters'] ?? []],
            default => [],
        };
    }
}
