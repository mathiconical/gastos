<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PurchasesPerMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Compras Mensais',
                    'data' => $this->fillValuesIntoMonthsArray(),
                ],
            ],
            'labels' => $this->getMonthsArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function fillValuesIntoMonthsArray(): array
    {
        $purchases = Purchase::query()
            ->selectRaw("SUM(total) AS total, strftime('%m', date) AS monthDate")
            ->groupBy(DB::raw("strftime('%m', date)"))
            ->get()
            ->toArray();

        $monthsArray = $this->getMonthsArray();

        $data = [];

        foreach ($purchases as $purchase) {
            $monthIntIndex = intval($purchase['monthDate']) - 1;
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
