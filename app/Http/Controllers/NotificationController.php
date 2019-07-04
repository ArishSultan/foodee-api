<?php
namespace App\Http\Controllers;
use App\NewsFeed;
use App\Profile;
use App\Providers\UploadServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();
    }
}