<?php

namespace App\Models;

use App\Models\Scopes\CouponVisibleScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ScopedBy(CouponVisibleScope::class)]
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
        return $this->belongsTo(purchase::class);
    }
}
