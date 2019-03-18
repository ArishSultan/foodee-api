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
