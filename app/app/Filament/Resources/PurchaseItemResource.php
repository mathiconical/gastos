<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseItemResource\Pages;
use App\Filament\Resources\PurchaseItemResource\RelationManagers;
use App\Models\PurchaseItem;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseItemResource extends Resource
{
    protected static ?string $model = PurchaseItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-numbered-list';

    protected static ?string $label = 'Items das Compras';

    protected static ?string $navigationGroup = 'Compras';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('purchase_id')
                    ->label('Compra')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->label('Preço')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('amount')
                    ->label('Quantidade')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('unit_id')
                    ->label('Unidade')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('purchase_id')
                    ->label('Compra')
                    ->getStateUsing(fn(PurchaseItem $record): string => $record->purchase_id . ' - ' . $record->purchase->date->format('d/m/Y'))
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->money('BRL')
                    ->searchable()
                    ->summarize(
                        Sum::make()
                            ->label('Total Preço')
                            ->money('BRL')
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Quantidade')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_id')
                    ->label('Unidade')
                    ->getStateUsing(fn(PurchaseItem $record): string => ($record->unit->name === '' ? $record->unit->abbr : $record->unit->name))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_item_price')
                    ->label('Total')
                    ->getStateUsing(fn(PurchaseItem $record): string => bcmul($record->price, $record->amount, 2))
                    ->money('BRL')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado Em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado Em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePurchaseItems::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return \Filament\Support\Colors\Color::Rose;
    }
}
