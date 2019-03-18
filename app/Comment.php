<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'user_id',
        'content'
    ];

    /*
     * Each comment belong to user
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
