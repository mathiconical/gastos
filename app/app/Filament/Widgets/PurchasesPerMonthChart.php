<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;

class PurchasesPerMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Ultimos 12 Meses';

    protected static string $color = 'danger';

    protected int|string|array $columnSpan = 'full';

    public function getColumnSpan(): int|string|array
    {
        return 2;
    }

    public static ?int $sort = 5;

    public ?string $filter = 'lastYearInterval';

    protected static ?string $pollingInterval = null;

    protected function getFilters(): ?array
    {
        return Purchase::query()
            ->orderBy('date', 'asc')
            ->get()
            ->map(fn($item): array => ['year' => $item->date->year])
            ->unique()
            ->values()
            ->pluck('year', 'year')
            ->prepend('Todos os Anos', 'allYears')
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

        if ($this->filter === 'allYears') {
            return $purchases;
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
