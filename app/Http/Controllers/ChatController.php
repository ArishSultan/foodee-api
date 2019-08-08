<?php

namespace App\Http\Controllers;

use App\Helpers\CustomBroadcaster;
use App\Message;
use App\MessageRecipient;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChatController extends Controller
{

    /*
     * sendMessage
     */
    public function send(Request $request)
    {

        global $initialMessageId;
        $initialMessageId = -1;

        $user = $request->user();

        $messageID = $request->message_id;
        $toID = $request->to_id;
        $type = $request->type;
        $messageText = $request->message;

        $checkInbox = Message::where(function($q) use ($user, $request, $toID){
            $q->where('from_id', $user->id);
            $q->where('to_id', $toID)->first();

        })->orWhere(function($q) use ($user, $request, $toID){
            $q->where('from_id', $toID);
            $q->where('to_id', $user->id)->first();
        })->first();

        if(isset($checkInbox)){
            $messageRecipient = new MessageRecipient();
            $messageRecipient->message_id = $checkInbox->id;
            $messageRecipient->recipient_id = $toID;
            $messageRecipient->sender_id = $user->id;
            $messageRecipient->message = $messageText;
            $messageRecipient->type = $type;
            if($messageRecipient->save()){
//                $messageRecipient = Message::select('id', 'from_id', 'to_id')->first();
                $payload = MessageRecipient::where('id', $messageRecipient->id)->first();
                CustomBroadcaster::fire($toID, 'new_message', $payload);
                return response()->json(["success"=>true, "data"=>$messageRecipient]);
            }
        } else {
            $message = new Message();
            $message->to_id = $toID;
            $message->from_id = $user->id;
            if($message->save()){
                $messageRecipient = new MessageRecipient();
                $messageRecipient->message_id = $message->id;
                $messageRecipient->recipient_id = $toID;
                $messageRecipient->sender_id = $user->id;
                $messageRecipient->message = $messageText;
                $messageRecipient->type = $type;
                if($messageRecipient->save()){
                    $payload = MessageRecipient::where('id', $messageRecipient->id)->with(['sender', 'receiver'])->first();
                    CustomBroadcaster::fire($toID, 'new_message', $payload);
                    return response()->json(["success"=>true, "data"=>$messageRecipient]);
                }
            }
        }


//        if($messageID == "-1" || $messageID == -1){

//            $checkInbox = Message::where(function($q) use ($user, $request, $toID){
//                    $q->where('from_id', $user->id);
//                    $q->where('to_id', $toID)->first();
//
//                    })->orWhere(function($q) use ($user, $request, $toID){
//                        $q->where('from_id', $toID);
//                        $q->where('to_id', $user->id)->first();
//                    })->first();

            // if inbox exist (if two users sending a new message with -1 at a time so in this case it does not create duplicate instance)
//            if($checkInbox !== null){
//                $messageRecipient = new MessageRecipient();
//                $messageRecipient->message_id = $checkInbox->id;
//                $messageRecipient->recipient_id = $toID;
//                $messageRecipient->message = $messageText;
//                $messageRecipient->type = $type;
//                if($messageRecipient->save()){
//                    return response()->json(["success"=>true, "data"=>$messageRecipient]);
//                }
//            } else {
//                $message = new Message();
//                $message->to_id = $toID;
//                $message->from_id = $user->id;
//                if($message->save()){
//                    $messageRecipient = new MessageRecipient();
//                    $messageRecipient->message_id = $message->id;
//                    $messageRecipient->recipient_id = $toID;
//                    $messageRecipient->message = $messageText;
//                    $messageRecipient->type = $type;
//                    if($messageRecipient->save()){
//                        return response()->json(["success"=>true, "data"=>$messageRecipient]);
//                    }
//                }
//            }
//            $message = new Message();
//            $message->to_id = $toID;
//            $message->from_id = $user->id;
//            if($message->save()){
//                $messageRecipient = new MessageRecipient();
//                $messageRecipient->message_id = $message->id;
//                $messageRecipient->recipient_id = $toID;
//                $messageRecipient->message = $messageText;
//                $messageRecipient->type = $type;
//                if($messageRecipient->save()){
//                    return response()->json(["success"=>true, "data"=>$messageRecipient]);
//                }
//            }
//        } else {
//            $messageRecipient = new MessageRecipient();
//            $messageRecipient->message_id = $messageID;
//            $messageRecipient->recipient_id = $toID;
//            $messageRecipient->message = $messageText;
//            $messageRecipient->type = $type;
//            if($messageRecipient->save()){
//                return response()->json(["success"=>true, "data"=>$messageRecipient]);
//            }
//        }
    }

    /*
     * getConversations
     */
    public function chats(Request $request)
    {
        $user = $request->user();
        //return $user;
        // raq query
        // SELECT * FROM `messages` WHERE (from_id=23 && to_id IN ( SELECT recipient_id from message_recipients )) || (to_id=23 && recipient_id IN ( SELECT from_id from message_recipients )) || (from_id=23 && to_id NOT IN ( SELECT from_id from message_recipients ))
        // SELECT M.id, M.`from_id`, M.`to_id` FROM messages M INNER JOIN (SELECT `from_id`, max(id) as maxId FROM messages WHERE `to_id` = 25 || `from_id` = 25 GROUP BY `from_id`)T ON M.id = T.maxId
        // correct query -> SELECT M.id, M.`from_id`, M.`to_id`, u.name, u.id as user_id FROM messages M INNER JOIN (SELECT `from_id`, max(id) as maxId FROM messages WHERE `to_id` = 24 || `from_id` = 24 GROUP BY `from_id`)T ON M.id = T.maxId LEFT JOIN users AS u ON u.id = (CASE WHEN M.from_id=24 THEN M.to_id ELSE M.from_id END)
        // SELECT DISTINCT M.id, M.`from_id`, M.`to_id`, u.name, u.id as user_id FROM messages M INNER JOIN (SELECT `from_id` FROM messages)T ON T.`from_id` IN (SELECT `from_id` FROM messages) LEFT JOIN users AS u ON u.id = (CASE WHEN M.from_id=25 THEN M.to_id ELSE M.from_id END) WHERE M.from_id = 25 || M.to_id=25
//        $chats = Message::where(function($q) use ($user) {
//           $q->where("from_id", $user->id)
//               ->whereIn("to_id", MessageRecipient::select("recipient_id")->pluck('from_id'));
//        })->orWhere(function($q) use ($user) {
//            $q->where("to_id", $user->id)
//                ->whereIn("from_id", MessageRecipient::select("recipient_id")->pluck('from_id'));
//        })->get();

//        $chats = DB::raw(DB::select("SELECT DISTINCT M.id, M.`from_id`, M.`to_id`, p.avatar, u.username, u.id as user_id FROM messages M INNER JOIN (SELECT `from_id` FROM messages)T ON T.`from_id` IN (SELECT `from_id` FROM messages) LEFT JOIN users AS u ON u.id = (CASE WHEN M.from_id='".$user->id."' THEN M.to_id ELSE M.from_id END) inner join profiles AS p ON p.user_id = u.id WHERE M.from_id = '".$user->id."' || M.to_id='".$user->id."'"))->getValue();
        $chats = DB::raw(DB::select("SELECT DISTINCT M.id, M.`from_id`, M.`to_id`, p.avatar, u.username, (SELECT message from message_recipients WHERE message_id=M.id order BY created_at desc LIMIT 1) as message, (SELECT COUNT(is_read) from message_recipients where message_id=M.id && is_read = 0) as message_count, (SELECT created_at from message_recipients WHERE message_id=M.id order BY created_at desc LIMIT 1) as created_at, u.id as user_id, MR.`message_id` FROM messages M INNER JOIN (SELECT `from_id` FROM messages)T ON T.`from_id` IN (SELECT `from_id` FROM messages) LEFT JOIN users AS u ON u.id = (CASE WHEN M.from_id='".$user->id."' THEN M.to_id ELSE M.from_id END) INNER JOIN message_recipients as MR ON M.id = MR.message_id inner join profiles AS p ON p.user_id = u.id WHERE M.from_id = '".$user->id."' || M.to_id = '".$user->id."' order by created_at desc"))->getValue();

        return $chats;
    }

    /*
     * getMessages
     */
    public function messages($to_id, $from_id)
    {
        $collection = collect();
//        $inbox = Message::where("from_id", $from_id)->where('to_id', $to_id)->first();
        $inbox = Message::where(function($q) use ($to_id, $from_id){
            $q->where('from_id', $from_id);
            $q->where('to_id', $to_id)->first();

        })->orWhere(function($q) use ($to_id, $from_id){
            $q->where('from_id', $to_id);
            $q->where('to_id', $from_id)->first();
        })->first();

//        if(isset($inbox)) {
//            $ids = $inbox->messages->where('is_read', 0)->get()->pluck('id');
//            $collection->push($ids);
//        }

        $messages = $inbox->messages;
        return $messages;
    }

    public function me(Request $request)
    {
        return $request->user();
    }

}
