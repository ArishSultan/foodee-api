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

    protected $appends = ['sender'];


    public function getSenderAttribute($value) {
//        $temp = $value.split(",");
            // do stuff
            $message = Message::where('id', $this->message_id)->select('id', 'to_id', 'from_id')->with('sender')->first();
            if ($message){
                return $message->sender;
            } else {
                return false;
            }

    }
    /*
     * Each message belongs To User
     */
//    public function sender()
//    {
//        return $this->belongsTo('App\User', 'recipient_id');
//    }

    /*
     * Each message belongs to receiver
     */
    public function receiver()
    {
        return $this->belongsTo('App\User', 'recipient_id')->select('id', 'username')->with(['profile'=>function($query) { $query->select('user_id', 'avatar');}]);
    }

//    public function sender()
//    {
////        return $this->belongsTo('App\User', 'recipient_id')->select('id', 'username')->with(['profile'=>function($query) { $query->select('user_id', 'avatar');}]);
//        $message = Message::where('id', $this->message_id)->select('id', 'to_id', 'from_id')->first();
//        if ($message){
//            return response()->json($message);
//        } else {
//            return ["success"=>true];
//        }
//    }

//    public function sender()
//    {
//        return $this->getSenderAttribute("sender");
//    }

    /*
    * Each message belongs to sender
    */
//    public function sender()
//    {
//        return $this->belongsTo('App\User', 'recipient_id')->select('id', 'username')->with(['profile'=>function($query) { $query->select('user_id', 'avatar');}]);
//    }


}
