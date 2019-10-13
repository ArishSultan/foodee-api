<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_id',
        'to_id',
        'feedback',
        'rate'
    ];

     /*
     * Each review belongs to receiver
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'to_id')->select('id', 'username')->with(['profile'=>function($query) { $query->select('user_id', 'avatar');}]);
    }
}
}
