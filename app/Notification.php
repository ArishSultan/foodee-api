<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Notification extends Model
{

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function getCreatedAtAttribute($value) {
        $carbonDate = new Carbon($value);
        return $carbonDate->diffForHumans();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post()
    {
        return $this->belongsTo(NewsFeed::class, 'post_id');
    }
}
