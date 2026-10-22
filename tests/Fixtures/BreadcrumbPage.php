<?php

declare(strict_types=1);

namespace VTech9NL\PolderTheme\Tests\Fixtures;

use Filament\Pages\Page;

class BreadcrumbPage extends Page
{
    protected static ?string $slug = 'breadcrumb-page';

    protected string $view = 'filament-panels::pages.page';

    public function getBreadcrumbs(): array
    {
        return ['/somewhere' => 'Polder crumb', 'Here'];
    }
}
