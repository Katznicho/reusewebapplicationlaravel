<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'phone_number',
        'payment_mode',
        'payment_method',
        'description',
        'is_annyomous',
        'reference',
        'status',
        'order_tracking_id',
        'OrderNotificationType',
        'user_id',
        'product_id',
        'type',

    ];

    //payment belongs to user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //payment belongs to product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    //payment belongs to delivery

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    //
}
