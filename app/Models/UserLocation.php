<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location',
        'lat',
        'long',
        'address',
    ];

    //belongs to the user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
