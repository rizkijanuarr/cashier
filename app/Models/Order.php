<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    // /App/Enum
    protected $casts = [
        'status' => \App\Enums\OrderStatus::class,
        'payment_method' => \App\Enums\PaymentMethod::class,
    ];

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // user_id terisi otomatis berdasarkan yang login saat ini
    protected static function booted(): void
    {
        static::creating(function (self $order) {
            $order->user_id = auth()->id();
            $order->total = 0;
        });

        static::saving(function ($order) {
            if ($order->isDirty('total')) {
                $order->loadMissing('orderDetails.product');

                $profitCalculation = $order->orderDetails->reduce(function ($carry, $detail) {
                    $productProfit = ($detail->price - $detail->product->cost_price) * $detail->quantity;
                    return $carry + $productProfit;
                }, 0);

                $order->attributes['profit'] = $profitCalculation;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function markAsComplete(): void
    {
        $this->status = \App\Enums\OrderStatus::COMPLETED;
        $this->save();
    }

    public function costPrice(): Attribute
    {
        return Attribute::make(
            set: fn($value) => str($value)->replace(',', '')
        );
    }

    public function price(): Attribute
    {
        return Attribute::make(
            set: fn($value) => str($value)->replace(',', '')
        );
    }
}
