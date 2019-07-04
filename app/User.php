<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;
class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'phone',
        'password',
    ];

    protected $appends = ['notifications'];

    public function getNotificationsAttribute($value) {

        if (Auth::user()) {   // Check is user logged in
            // do stuff
            $notifications = Auth::user()->notifications;
            if ($notifications){
                return $notifications;
            } else {
                return [];
            }
        }

    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'author_id');
    }

    public function likes()
    {
        return $this->belongsToMany('App\Post', 'likes', 'user_id', 'post_id');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
