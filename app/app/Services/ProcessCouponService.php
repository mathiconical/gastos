<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Unit;
use App\Models\Utils\ParseHTMLTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ProcessCouponService
{
    private string $sefazLink;
    private ProcessSefazPageService $processSefazPageService;

    public function __construct()
    {
        $this->sefazLink = 'https://portalsped.fazenda.mg.gov.br/portalnfce/sistema/qrcode.xhtml?p=CHANGE_ME|2|1|1';
        $this->processSefazPageService = new ProcessSefazPageService();
    }

    public function execute(Coupon $coupon): void
    {
        $url = str_replace('CHANGE_ME', $coupon->key, $this->sefazLink);
        $table = $this->processSefazPageService->get($url);

        $table = collect($table);

        if ($table->count() === 0) {
            return;
        }

        $purchase = Purchase::query()->create([
            'coupon_id' => $coupon->id,
            'total' => $table->sum('price'),
            'items' => $table->count(),
            'date' => Carbon::now()->toDateTimeString(),
        ]);

        $table->each(function ($row) {
            $unit = Unit::where('abbr', $row['unit'])->first();
            if (!$unit) {
                Unit::query()->insert([
                    'abbr' => $row['unit'],
                    'name' => $row['unit'],
                ]);
            }
        });

        $table->each(function ($row) use ($purchase) {
            PurchaseItem::query()->insert([
                'price' => bcdiv($row['price'], $row['amount'], 2),
                'amount' => $row['amount'],
                'unit_id' => Unit::query()->where('abbr', $row['unit'])->first()->id,
                'name' => $row['name'],
                'purchase_id' => $purchase->id,
            ]);
        });

        $coupon->update([
            'visible' => true,
            'processed' => true,
            'processed_timestamp' => Carbon::now()->toDateTimeString(),
        ]);
    }
}
