<?php

namespace App\Filament\Resources\PurchaseItemResource\Pages;

use App\Filament\Resources\PurchaseItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePurchaseItems extends ManageRecords
{
    protected static string $resource = PurchaseItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
