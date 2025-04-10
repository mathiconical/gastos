<?php

namespace App\Filament\Resources\Widgets;

use Filament\Widgets\ChartWidget;

class PurchasesPerMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
