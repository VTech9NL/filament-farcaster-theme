<?php

declare(strict_types=1);

namespace VTech9NL\PolderTheme\MobileNavigation\Concerns;

use Filament\Support\Concerns\EvaluatesClosures;

trait EvaluatesWithPageContext
{
    use EvaluatesClosures;

    /**
     * @var array{routeName?: ?string, routeParameters?: array<string, scalar>}
     */
    protected array $pageContext = [];

    /**
     * @param  array{routeName?: ?string, routeParameters?: array<string, scalar>}  $pageContext
     */
    public function pageContext(array $pageContext): static
    {
        $this->pageContext = $pageContext;

        return $this;
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
