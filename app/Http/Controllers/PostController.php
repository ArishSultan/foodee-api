<?php
namespace App\Http\Controllers;
use App\Like;
use App\NewsFeed;
use App\Providers\UploadServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class PostController extends Controller
{
    public function index(Request $request) {

        return NewsFeed::withCount('comments')->with('user.profile', 'comments')->orderBy('created_at', 'desc')->paginate(6);
//        $lat = $request->query('lat');
//        $lng = $request->query('lng');
//        $newsFeeds = NewsFeed::distance($lat, $lng, 10)->simplePaginate(10);
//        return $newsFeeds;
    }
    public function show(NewsFeed $post) {
        return $post;
    }
    public function store(Request $request) {
        global  $photos_string;
        $request['user_id'] = $request->user()->id;
//        $photos = ['http://www.erro-shop.de/WebRoot/Store7/Shops/61191284/4B6C/0809/A63D/3582/ECF1/C0A8/28BC/0D81/72370_thunfisch_salat_ohne_teller_ml.jpg', 'https://ae01.alicdn.com/kf/HTB1ico.a9YTBKNjSZKbq6xJ8pXaC/LeadingStar-6pcs-Pack-Cute-Imitation-Food-Erasers-Hamburger-Eraser-Office-Study-Correction-Supplies-for-Students-OL.jpg_640x640.jpg'];
//        $request['photos'] = implode(",", $photos);
        if($request->hasFile('photos')){
            $photos = UploadServiceProvider::multiUploads($request, 'post');
//                return $photos;
            $photos_string = implode(",", $photos);
//            return dd(implode(",", $photos));
        }

        $post = NewsFeed::create(["user_id"=>$request->user()->id, "lat"=>$request['lat'], "lng"=>$request['lng'], "content"=>$request['content'], "photos"=>$photos_string]);
        return response()->json($post, 201);
    }
    public function update(NewsFeed $post, Request $request) {
        $post->update($request->all());
        return response()->json($post);
    }
    public function delete(NewsFeed $post) {
        $post->delete();
        return response()->json(null, 204);
    }

    public function isLikedByMe(Request $request, $id)
    {

        $post = NewsFeed::where('id', $id)->first();
        if(isset($post)){
            $isLiked = Like::where('user_id', $request->user()->id)->where('post_id', $post->id)->first();
            if ($isLiked){
                return response()->json(["status"=>true], 200);
            } else {
                return response()->json(["status"=>false], 200);
            }
        } else {
            return response()->json(["message"=>"No content were found!"], 204);
        }

    }

    public function like(Request $request, $id)
    {
        $post = NewsFeed::where('id', $id)->select('id')->first();
        $existing_like = Like::where('post_id', $id)->where('user_id',$request->user()->id)->first();

        if (is_null($existing_like)) {
            Like::create([
                'post_id' => $id,
                'user_id' => $request->user()->id
            ]);
            return response()->json(["status"=>true, 'post_count'=>$post->likes()->count()], 200);
        } else {
            if (is_null($existing_like->deleted_at)) {
                $existing_like->delete();
                return response()->json(["status"=>false, 'post_count'=>$post->likes()->count()], 200);
            } else {
                $existing_like->restore();
                return response()->json(["status"=>false, 'post_count'=>$post->likes()->count()], 200);
            }
        }
    }
}