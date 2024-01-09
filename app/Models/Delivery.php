<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'status',
        'pickup_date',
        'delivery_date',
        'owner_status',
        'user_id',
        'category_id',
        'product_id',
        'proof',
        'community_id',
        'payment_id'
    ];

    //cast proof to an array
    protected $casts = [
        'proof' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    //delivery belongs to user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //delivert has a category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    //delivery has a payment
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    //delivery belongs to a community using community_id
    public function community(): BelongsTo
    {
        return $this->belongsTo(User::class, 'community_id', 'id');
    }
}
