<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;

class PurchasesPerMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Relaçao de Gasto Ano / Mes';

    protected static string $color = 'danger';

    protected int|string|array $columnSpan = 'full';

    public function getColumnSpan(): int|string|array
    {
        return 2;
    }

    public static ?int $sort = 5;

    public ?string $filter = 'lastYearInterval';

    protected static ?string $pollingInterval = null;

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
        ];
    }

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
                    'label' => 'Compras Mensais R$ ',
                    'data' => $this->fillValuesIntoMonthsArray(),
                    'borderColor' => '#dc2662',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function applyFilter(Collection $purchases): Collection
    {
        if ($this->filter === 'lastYearInterval') {
            $purchases = Purchase::query()->select('total', 'date')->where('date', '>=', now()->subYear()->format('Y-m-d'))->get();
            return $purchases->filter(function ($item) {
                return $item->date->format('Y-m') >= now()->subYear()->format('Y-m');
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
            ->orderBy('date', 'ASC')
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
            $yearIndex = substr($purchase['date'], 0, 4);
            $data[$yearIndex . '/' . $monthAbbrKey] = $purchase['total'];
        }

        return $data;
    }

    private function getMonthsArray(): array
    {
        return explode(',', 'Jan,Fev,Mar,Abr,Mai,Jun,Jul,Ago,Set,Out,Nov,Dez');
    }
}
