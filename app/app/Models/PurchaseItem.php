<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $purchase_id
 * @property int $product_id
 * @property string $price
 * @property string $amount
 * @property int $unit_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Purchase|null $purchase
 * @property-read \App\Models\Unit|null $unit
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem wherePurchaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin IdeHelperPurchaseItem
 */
class PurchaseItem extends Model
{
    protected $fillable = [
        'product_id',
        'price',
        'amount',
        'unit_id',
        'purchase_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
}
