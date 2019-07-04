<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Notification extends Model
{

    protected $fillable = [
        'post_id',
        'author_id',
        'user_id',
        'message',
        'type'
    ];

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
