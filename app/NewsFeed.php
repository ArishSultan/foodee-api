<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'lat',
        'lng'
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

    public function scopeDistance($query, $lat, $lng, $radius = 100, $unit = "km")
    {
        $unit = ($unit === "km") ? 6378.10 : 3963.17;
        $lat = (float) $lat;
        $lng = (float) $lng;
        $radius = (double) $radius;
        return $query->having('distance','<=',$radius)
            ->select(DB::raw("*,
                            ($unit * ACOS(COS(RADIANS($lat))
                                * COS(RADIANS(latitude))
                                * COS(RADIANS($lng) - RADIANS(longitude))
                                + SIN(RADIANS($lat))
                                * SIN(RADIANS(latitude)))) AS distance")
            )->orderBy('distance','asc');
    }
}
