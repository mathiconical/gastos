<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;

class ManageCoupons extends ManageRecords
{
    protected static string $resource = CouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            /*
                ->mutateFormDataUsing(function (array $data): array {
                    $default = [
                        'key' => '',
                        'visible' => false,
                        'processed' => false,
                        'processed_timestamp' => '',
                        'user_id' => Auth::user()->id,
                    ];

                    return [...$default, ...$data];
                }),
            */
        ];
    }
}
