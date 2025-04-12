<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsRandomItemOverview extends BaseWidget
{
    public static bool $isLazy = true;

    public static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        $minMax = PurchaseItem::query()->selectRaw('MIN(id) AS minid, MAX(id) AS maxid')->first();

        $item = PurchaseItem::query()->find(rand($minMax->minid, $minMax->maxid));

        $total_purchase_item = PurchaseItem::query()
            ->where('product_id', $item->product->id)
            ->selectRaw('amount * price AS total')
            ->get()
            ->pluck('total');

        return [
            Stat::make('ITEM ALEATORIO', $item->product->name)
                ->chart($total_purchase_item->toArray())
                ->chartColor('info')
                ->description('Quantidade: ' . $item->amount . ' ' . $item->unit->abbr . ', Valor Medio R$ ' . number_format($total_purchase_item->average(), 2, '.', ','))
                ->chartColor('success')
                ->descriptionColor('success')
                ->descriptionIcon('heroicon-o-shopping-cart', IconPosition::Before),
        ];
    }
}
