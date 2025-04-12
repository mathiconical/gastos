<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsExpenseOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    public static bool $isLazy = true;

    public static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        $total = Purchase::query()->select('total', 'date')->get();

        $total = $total->map(fn($item) => ['total' => $item->total, 'date' => $item->date->format('Y-m-d')]);

        return [
            Stat::make('TOTAL', 'R$ ' . $total->sum('total'))
                ->chart($total->pluck('total', 'date')->toArray())
                ->chartColor('danger')
                ->description('GASTO LINEAR')
                ->chartColor('danger')
                ->descriptionColor('danger')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::After),
        ];
    }
}
