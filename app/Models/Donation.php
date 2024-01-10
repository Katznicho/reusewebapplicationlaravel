<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'product_id',
        'is_annyomous',
        'status',
        'payment_id',
        'amount',
    ];

    //a donation belongs to a user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // a donation has a payment
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'donation_id', 'id');
    }

    //a donation belongs to a product
    public function product(): BelongsTo
    {

        return $this->belongsTo(Product::class);
    }
}
