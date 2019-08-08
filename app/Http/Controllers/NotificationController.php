<?php
namespace App\Http\Controllers;
use App\Helpers\CustomBroadcaster;
use App\Like;
use App\NewsFeed;
use App\Notification;
use App\Providers\UploadServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class NotificationController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();
        $collection = collect();
        $un_read_notifications = $user->unReadNotifications()->pluck('id');
        $collection->push($un_read_notifications);
        Notification::where('author_id', $user->id)
            ->whereIn('id', $collection[0])
            ->update(["is_read"=>1]);
//        return $collection;
        return $user->notifications;
    }

}