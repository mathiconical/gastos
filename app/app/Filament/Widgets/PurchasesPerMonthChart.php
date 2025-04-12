<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PurchasesPerMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Ultimos 12 Meses';

    protected static string $color = 'danger';

    public ?string $filter = '2024';

    protected static ?string $pollingInterval = '10s';

    protected function getFilters(): ?array
    {
        return Purchase::query()
            ->get()
            ->map(fn($item): array => ['year' => $item->date->year])
            ->unique()
            ->values()
            ->pluck('year', 'year')
            ->prepend('Ultimos 12 Meses', 'lastYearInterval')
            ->toArray();
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Compras Mensais',
                    'data' => $this->fillValuesIntoMonthsArray(),
                    'borderColor' => '#dc2662',
                ],
            ],
            'labels' => $this->getMonthsArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function applyFilter(Collection $purchases): Collection
    {
        if ($this->filter === 'lastYearInterval') {
            return $purchases->filter(function ($item) {
                return $item['date'] >= now()->subYear();
            });
        }

        return $purchases->filter(function ($item) {
            return substr($item['date'], 0, 4) == $this->filter;
        });
    }

    private function fillValuesIntoMonthsArray(): array
    {
        $purchases = Purchase::query()
            ->selectRaw("total, date")
            ->get()
            ->map(function ($item) {
                return [
                    'total' => $item->total,
                    'date' => $item->date->format('Y-m-d')
                ];
            });

        $purchases = $this->applyFilter($purchases);

        $monthsArray = $this->getMonthsArray();

        $data = [];

        foreach ($purchases as $purchase) {
            $monthIntIndex = intval(substr($purchase['date'], 5, 2)) - 1;
            $monthAbbrKey = $monthsArray[$monthIntIndex];
            $data[$monthAbbrKey] = $purchase['total'];
        }

        return $data;
    }

    private function getMonthsArray(): array
    {
        return explode(',', 'Jan,Fev,Mar,Abr,Mai,Jun,Jul,Ago,Set,Out,Nov,Dez');
    }
}
