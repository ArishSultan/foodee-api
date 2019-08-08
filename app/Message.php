<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $with = ['user', 'unreadMessages'];
    protected $fillable = [
        "from_id",
        "to_id"
    ];

//    protected $appends = ['new_msg_count'];


//    public function getNewMsgCountAttribute($value) {
////        $temp = $value.split(",");
//            // do stuff
//            $unreadCount = MessageRecipient::where('message_id', $this->id)->where('is_read', 0)->count();
//            if (count($unreadCount) > 0){
//                return $unreadCount;
//            } else {
//                return 0;
//            }
//
//    }

    /*
     * Each chat has many messages
     */
    public function messages()
    {
        return $this->hasMany('App\MessageRecipient', 'message_id');
    }

    public function unreadMessages()
    {
        return $this->hasMany('App\MessageRecipient', 'message_id')->where('is_read', 0);
    }

    public function unReadNotifications()
    {
        return $this->hasMany(Notification::class, 'author_id')->where('is_read', 0);
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
