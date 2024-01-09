<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function getImagesAttribute($value)
    {
        // Decode the JSON string
        $decodedImages = json_decode($value, true);

        // If decoding fails, return an empty array or handle accordingly
        return $decodedImages ? $decodedImages : [];
    }

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

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    //a product has a payment
    public function payment(): HasOne
    {

        return $this->hasOne(Payment::class);
    }

    // a product id a attached to a community using community_id
    public function community(): BelongsTo
    {
        return $this->belongsTo(User::class, 'community_id', 'id');
    }
}
