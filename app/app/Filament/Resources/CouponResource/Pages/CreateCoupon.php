<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Services\ProcessCouponService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $default = [
            'user_id' => Auth::user()->id,
            'visible' => false,
            'processed' => false,
            'processed_timestamp' => '',
        ];

        $merged = [
            ...$default,
            ...$data
        ];

        $coupon = static::getModel()::where('key', $data['key'])->first();
        if ($coupon) {
            $coupon->purchase->items->delete();
            $coupon->purchase->delete();
            $coupon->delete();
        }

        $instance = static::getModel()::create($merged);

        // comment this if are running whithout fpm
        defer(function () use ($instance) {
            new ProcessCouponService()->execute($instance);
        });

        return $instance;
    }
}
