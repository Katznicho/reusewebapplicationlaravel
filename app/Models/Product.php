<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'cover_image',
        'images',
        'pick_up_location',
        'weight',
        'is_delivery_available',
        'is_donation',
        'is_product_new',
        'is_product_available_for_all',
        'is_product_damaged',
        'is_product_rejected',
        'is_product_accepted',
        'reason',
        'damage_description',
        'status',
        'total_amount',
        'user_id',
        'category_id',
        'payment_id',
        'delivery_id',
        'community_id',
        'available',

    ];

    //product belongs to user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //product belongs to category

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    //product belongs to delivery

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }
}
