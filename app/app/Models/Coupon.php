<?php

namespace App\Models;

use App\Models\Scopes\CouponVisibleScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(CouponVisibleScope::class)]
/**
 * @property int $id
 * @property string $key
 * @property int $user_id
 * @property int $processed
 * @property int $visible
 * @property string|null $processed_timestamp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereProcessedTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereVisible($value)
 * @mixin \Eloquent
 * @mixin IdeHelperCoupon
 */
class Coupon extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'processed',
        'visible',
        'processed-timestamp',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
}
