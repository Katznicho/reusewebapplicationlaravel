<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'purpose',
        'location',
        'longitude',
        'latitude',
        'community_category_id',
        'user_id',
        'contact_person',
        'contact_number',
        'contact_person_email',
        'contact_person_role',
        'website',
        'total_members',
        'total_members_women',
        'total_members_men',
        'year_started',
        'leader_name',
        'leader_role',
        'leader_email',
        'leader_contact',
        'images',
    ];

    //cast iamges to array
    protected $casts = [
        'images' => 'array',
    ];

    //relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //relationships
    public function communityCategory()
    {
        return $this->belongsTo(CommunityCategory::class);
    }
}
