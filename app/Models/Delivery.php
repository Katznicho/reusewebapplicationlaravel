<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    //cast proof to an array
    protected $casts = [
        'proof' => 'array',
    ];

    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    //delivery belongs to user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
