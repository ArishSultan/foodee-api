<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $with = ['user'];
    protected $fillable = [
        "from_id",
        "to_id"
    ];

    /*
     * Each chat has many messages
     */
    public function messages()
    {
        return $this->hasMany('App\MessageRecipient', 'message_id');
    }



    /*
     * Each chat belongs to user
     */
    public function user()
    {
        return$this->belongsTo('App\User', 'to_id');
    }


    /*
     * Each chat belongs to user
     */
    public function sender()
    {
        return$this->belongsTo('App\User', 'to_id')->select('id', 'username')->with(['profile'=>function($query) { $query->select('user_id', 'avatar');}]);
    }

}
