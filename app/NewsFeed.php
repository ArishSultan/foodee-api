<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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

    protected $appends = ['is_liked'];

//    protected $appends = '';

//    protected $casts = [
//        'photos' => 'array',
//    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /*
     * A post may Belongs-To-Many Users
     */
    public function tags()
    {
        return $this->belongsToMany('App\User', 'post_tags', 'post_id', 'user_id')->withPivot(['post_id', 'user_id', 'mode']);
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
        return $this->hasMany('App\Comment', 'post_id');
    }

    /*
     * Post has many likes
     */
//    public function likes()
//    {
//        return $this->hasMany('App\Like', 'post_id');
//    }

    public function getIsLikedAttribute($value) {
//        $temp = $value.split(",");
        if (Auth::user()) {   // Check is user logged in
            // do stuff
            $isLiked = Like::where('user_id', Auth::user()->id)->where('post_id', $this->id)->first();
            if ($isLiked){
                return true;
            } else {
                return false;
            }
//            return true;
        }

    }

    public function getPhotosAttribute($value) {
//        $temp = $value.split(",");
        if($value){
            return explode(',', $value);
        } else {
            return null;
        }

    }


    public function getCreatedAtAttribute($value) {
//        $now = new Carbon();
//        $dt = new Carbon($this->created_at);
//        $dt->setLocale('es');
        //return Carbon::create($value)->diffForHumans();
//        Carbon::now()->setTimezone(Auth::user()->timezone)->diffForHumans($financing_date)
        $carbonDate = new Carbon($value);
        return $carbonDate->diffForHumans();
        //return $this->created_at = $this->created_at->diffForHumans();
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
