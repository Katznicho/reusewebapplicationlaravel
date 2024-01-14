<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'front_national_id',
        'back_national_id',
        'passport',
        'status',
        'reason',
        'document_url',
        'passport',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
