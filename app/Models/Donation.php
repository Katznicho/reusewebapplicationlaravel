<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'user_id',
        'product_id',
        'is_annyomous',
        'status'
    ];
    
}
