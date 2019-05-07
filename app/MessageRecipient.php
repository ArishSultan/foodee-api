<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageRecipient extends Model
{

    protected $with = ["sender"];
    protected $fillable = [
        "message_id",
        "recipient_id",
        "message",
        "type"
    ];

    /*
     * Each message belongs To User
     */
    public function sender()
    {
        return $this->belongsTo('App\User', 'recipient_id');
    }

}
