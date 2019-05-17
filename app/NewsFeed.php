<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsFeed extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'content',
        'photos',
    ];

//    protected $casts = [
//        'photos' => 'array',
//    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'id')->select('id, username');
    }

    public function likes()
    {
        return $this->belongsToMany('App\User', 'likes', 'post_id', 'user_id');
    }

    /*
     * Each post has many comments
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function getPhotosAttribute($value) {
//        $temp = $value.split(",");
        return explode(',', $value);
    }
}
