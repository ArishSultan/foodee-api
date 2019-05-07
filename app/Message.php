<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $with = ["user", 'receiver'];
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
     * Each message belongs to sender
     */
    public function receiver()
    {
        return $this->belongsTo('App\User', 'recipient_id');
    }

    /*
     * Each chat belongs to user
     */
    public function user()
    {
        return$this->belongsTo('App\User', 'to_id');
    }

}
