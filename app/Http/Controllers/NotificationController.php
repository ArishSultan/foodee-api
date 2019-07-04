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
        return $user->notifications;
    }

}