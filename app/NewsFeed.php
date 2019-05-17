<?php

namespace App;

use Carbon\Carbon;
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

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

//    protected $appends = '';

//    protected $casts = [
//        'photos' => 'array',
//    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
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


    public function getCreatedAtAttribute($value) {
//        $now = new Carbon();
//        $dt = new Carbon($this->created_at);
//        $dt->setLocale('es');
        return $this->created_at = "132546789";
    }

    public function scopeDistance($query, $lat, $lng, $radius = 100, $unit = "km")
    {
        $unit = ($unit === "km") ? 6378.10 : 3963.17;
        $lat = (float) $lat;
        $lng = (float) $lng;
        //$radius = $radius;
        return $query->having('distance','<=',$radius)
            ->select(DB::raw("*,
                            ($unit * ACOS(COS(RADIANS($lat))
                                * COS(RADIANS(lat))
                                * COS(RADIANS($lng) - RADIANS(lng))
                                + SIN(RADIANS($lat))
                                * SIN(RADIANS(lat)))) AS distance")
            )->orderBy('created_at','desc');
    }
}
