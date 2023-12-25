<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'is_read',
        'user_id',
    ];

    // notification belongs to user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
