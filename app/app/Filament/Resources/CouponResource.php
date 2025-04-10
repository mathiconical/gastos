<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Filament\Resources\CouponResource\RelationManagers;
use App\Models\Coupon;
use App\Models\Scopes\CouponVisibleScope;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Entrada';

    protected static ?string $navigationLabel = 'Chave NFce';

    protected static ?string $label = 'Cupons';

    protected static ?int $navigationSort = 0;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([CouponVisibleScope::class]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->label('Chave'),
                Forms\Components\Select::make('user_id')
                    ->label('Usuario')
                    ->disabled()
                    ->default(Auth::user()->id)
                    ->options(User::where('id', Auth::user()->id)->pluck('name', 'id')),
                Forms\Components\Toggle::make('processed')
                    ->label('Processado')
                    ->disabled()
                    ->required(),
                Forms\Components\Toggle::make('visible')
                    ->label('Visivel')
                    ->required(),
                Forms\Components\DateTimePicker::make('processed_timestamp')
                    ->format('d/m/Y H:i:s')
                    ->disabled()
                    ->label('Processado Em'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Chave')
                    ->searchable(),
                Tables\Columns\IconColumn::make('processed')
                    ->label('Processado')
                    ->searchable()
                    ->boolean(),
                Tables\Columns\ToggleColumn::make('visible')
                    ->label('Visivel'),
                Tables\Columns\TextColumn::make('processed_timestamp')
                    ->label('Processado Em')
                    ->dateTime('d/m/Y H:i:s')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado Em')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado Em')
                    ->dateTime()
                    ->searchable()
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

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return \Filament\Support\Colors\Color::Rose;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
        ];
    }
}
