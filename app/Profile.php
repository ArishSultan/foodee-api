<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    public function foods()
    {
        return $this->belongsToMany(FoodCategory::class, 'food_profile');
    }
}
