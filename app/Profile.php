<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    protected $casts = [
        'is_age_private' => 'boolean',
    ];

    public function foods()
    {
        return $this->belongsToMany(FoodCategory::class, 'food_profile', 'profile_id', 'food_id')->withPivot('profile_id', 'food_id');
    }
}
