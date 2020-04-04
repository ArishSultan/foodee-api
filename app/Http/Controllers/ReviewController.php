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
use App\Review;

class ReviewController extends Controller
{

    public function post (Request $request)
    {
        $user = $request->user();
        $to_id = $request->to_id;
        $feedback = $request->feedback;
        $rate = $request->rate;
        $post = Review::create([
            'from_id' => $user->id,
            'to_id' => $to_id,
            'feedback' => $feedback,
            'rate' => $rate
        ]);
        if($post) {
            return response()->json(['success'=>true, 'data'=>$post]);
        }
    }

    public function delete(Request $request, $id) {
        Review::where('id', $id)->delete();

	return response()->json(null, 204);
    }

    public function reviews (Request $request, $userId)
    {
        $user = $request->user();
        $reviews = Review::where('to_id', $userId)->with('user')->get();
        return response()->json(['data'=>$reviews]);
    }

}
