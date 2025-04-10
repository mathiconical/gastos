<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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

        $date = $table['date'];
        unset($table['date']);
        $purchase = Purchase::query()->create([
            'coupon_id' => $coupon->id,
            'total' => $table->sum('price'),
            'items' => $table->count(),
            'date' => Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d'),
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
            $product = Product::query()->where('name', $row['name'])->first();
            if (!$product) {
                $product = Product::query()->create(['name' => $row['name']]);
            }

            PurchaseItem::query()->insert([
                'price' => bcdiv($row['price'], $row['amount'], 2),
                'amount' => $row['amount'],
                'unit_id' => Unit::query()->where('abbr', $row['unit'])->first()->id,
                'product_id' => $product->id,
                'purchase_id' => $purchase->id,
            ]);
        });

        $coupon->update([
            'visible' => true,
            'processed' => true,
            'processed_timestamp' => Carbon::now()->toDateTimeString(),
        ]);
    }

    public function executeBatch(array|Collection $coupons): void
    {
        foreach ($coupons as $coupon) {
            $this->execute($coupon);
        }
    }
}
