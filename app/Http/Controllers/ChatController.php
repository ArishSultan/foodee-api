<?php

namespace App\Api\V1\Controllers;

use App\Message;
use App\MessageRecipient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use League\Fractal\Resource\Collection;
use App\Api\V1\Transformers\UserTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChatController extends Controller
{

    /*
     * sendMessage
     */
    public function send(Request $request)
    {

        $user = $request->user();

        $messageID = $request->message_id;
        $toID = $request->to_id;
        $type = $request->type;
        $messageText = $request->message;

        if($messageID == "-1" || $messageID == -1){
            $message = new Message();
            $message->to_id = $toID;
            $message->from_id = $user->id;
            if($message->save()){
                $messageRecipient = new MessageRecipient();
                $messageRecipient->message_id = $message->id;
                $messageRecipient->recipient_id = $toID;
                $messageRecipient->message = $messageText;
                $messageRecipient->type = $type;
                if($messageRecipient->save()){
                    return response()->json(["success"=>true, "data"=>$messageRecipient]);
                }
            }
        } else {
            $messageRecipient = new MessageRecipient();
            $messageRecipient->message_id = $messageID;
            $messageRecipient->recipient_id = $toID;
            $messageRecipient->message = $messageText;
            $messageRecipient->type = $type;
            if($messageRecipient->save()){
                return response()->json(["success"=>true, "data"=>$messageRecipient]);
            }
        }
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

        $chats = DB::raw(DB::select("SELECT DISTINCT M.id, M.`from_id`, M.`to_id`, u.name, u.id as user_id FROM messages M INNER JOIN (SELECT `from_id` FROM messages)T ON T.`from_id` IN (SELECT `from_id` FROM messages) LEFT JOIN users AS u ON u.id = (CASE WHEN M.from_id='".$user->id."' THEN M.to_id ELSE M.from_id END) WHERE M.from_id = '".$user->id."' || M.to_id='".$user->id."'"))->getValue();

        return $chats;
    }

    /*
     * getMessages
     */
    public function messages($id)
    {
        $message = Message::where("id", $id)->first();
        $messages = $message->messages;
        return $messages;
    }

    public function me(Request $request)
    {
        return $request->user();
    }

}
