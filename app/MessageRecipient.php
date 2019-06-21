<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageRecipient extends Model
{

    protected $with = ["receiver"];
    protected $fillable = [
        "message_id",
        "recipient_id",
        "message",
        "type"
    ];

    /*
     * Each message belongs To User
     */
//    public function sender()
//    {
//        return $this->belongsTo('App\User', 'recipient_id');
//    }

    /*
     * Each message belongs to sender
     */
    public function receiver()
    {
        return $this->belongsTo('App\User', 'recipient_id')->select('id', 'username')->with('profile');
    }


}
