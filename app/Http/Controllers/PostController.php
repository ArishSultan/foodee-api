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

class PostController extends Controller
{
    public function index(Request $request) {

//        return NewsFeed::withCount('comments')
//            ->withCount('likes')
//            ->with(['user'=>function($q){
//                $q->select('id', 'username', 'email')
//                    ->with(['profile'=>function($q){
//                        $q->select('user_id', 'avatar');
//                    }]);
//                }, 'comments'=>function($query) {
//                $query->with(['user'=>function($q){
//                    $q->select('id', 'username', 'email')
//                        ->with(['profile'=>function($q){
//                            $q->select('user_id', 'avatar');
//                        }]);}])->take(3);
//            }])->orderBy('created_at', 'desc')->paginate(6);



        $posts =  NewsFeed::
        with(['comments'=>function($query) {
                $query->with(['user'=>function($q){
                    $q->select('id', 'username', 'email', 'device_token')
                        ->with(['profile'=>function($q){
                            $q->select('user_id', 'avatar');
                        }])
                    ;}]);
            }])
            ->with(['user'=>function($q){
                $q->select('id', 'username', 'email', 'device_token')
                    ->with(['profile'=>function($q){
                        $q->select('user_id', 'avatar');
                    }]);
            }])
            ->with(['tags'=>function($query){
                $query->select('username');
            }])
            ->withCount('likes')->withCount('comments')->orderBy('created_at', 'desc')->paginate(6);

//        CustomBroadcaster::fire(1, 'news_feed', $posts);

        return $posts;

//        $lat = $request->query('lat');
//        $lng = $request->query('lng');
//        $newsFeeds = NewsFeed::distance($lat, $lng, 10)->simplePaginate(10);
//        return $newsFeeds;
    }
    public function show(NewsFeed $post) {

        $postObj = NewsFeed::where('id', $post->id)->
            with(['comments'=>function($query) {
                $query->with(['user'=>function($q){
                    $q->select('id', 'username', 'email', 'device_token')
                        ->with(['profile'=>function($q){
                            $q->select('user_id', 'avatar');
                        }])
                    ;}]);
            }])
                ->with(['user'=>function($q){
                    $q->select('id', 'username', 'email', 'device_token')
                        ->with(['profile'=>function($q){
                            $q->select('user_id', 'avatar');
                        }]);
                }])->with(['tags'=>function($query){
            $query->select('username');
        }])->withCount('likes')->withCount('comments')->first();
        return $postObj;
//        return $post;
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


        $post = NewsFeed::create(["user_id"=>$request->user()->id, "lat"=>$request['lat'], "lng"=>$request['lng'], "content"=>$request['content'], "photos"=>(isset($request->photos) ? $photos_string : null)]);
        if($request->has('tags')){
            $users = $request->tags;
            foreach($users as $user){
                $post->tags()->attach($user, ['mode' => 'is with']);

//                if($request->user()->id != $post->user->id) {

                    $notification = new Notification();
                    $notification->post_id = $post->id;
                    $notification->author_id = $user;
                    $notification->user_id = $request->user()->id;
                    $notification->message = " tagged you in a post";
                    $notification->type = 3;
                    $notification->save();
                    CustomBroadcaster::fire($user, 'new_notification', $notification);
//                }


//                CustomBroadcaster::fire($user, 'new_notification', $post);
            }
        }
        return response()->json($post, 201);
    }
    public function update(NewsFeed $post, Request $request) {

//        return $request->photos;
//        $post->update($request->all());
        global  $photos_string;

        if($request->hasFile('photos')){
            $photos = UploadServiceProvider::multiUploads($request, 'post');
//                return $photos;
            $photos_string = implode(",", $photos);
//            return dd(implode(",", $photos));
        }

        $data = $post;
        $data->lat = $request->lat;
        $data->lng = $request->lng;
        $data->content = $request['content'];

        if($request->hasFile('photos')){
            $imagesArray = $post->photos;

            array_push($imagesArray, $photos_string);
            $photos_string = implode(",", $imagesArray);
            $data->photos = $photos_string;
        }


        if($data->save()){

            if($request->has('tags')){
                $post->tags()->detach();
                $users = $request->tags;
                foreach($users as $user){
                    $post->tags()->attach($user, ['mode' => 'is with']);
                }
            }

            return response()->json($data, 200);
        }
        return response()->json($post);
    }
    public function destroy(NewsFeed $post) {
        if($post->delete()){
            return response()->json(['success'=>true, 'message'=>'Post deleted successfully!'], 200);
        } else {
            return response()->json(['success'=>false, 'message'=>'No query results found for this model'], 204);
        }

    }

    public function myTimeline(Request $request, $id)
    {
        $user = User::where('id', $id)->select('id')->first();
        $posts =  NewsFeed::
        with(['comments'=>function($query) {
            $query->with(['user'=>function($q){
                $q->select('id', 'username', 'email')
                    ->with(['profile'=>function($q){
                        $q->select('user_id', 'avatar');
                    }])
                ;}]);
        }])
            ->with(['user'=>function($q){
                $q->select('id', 'username', 'email')
                    ->with(['profile'=>function($q){
                        $q->select('user_id', 'avatar');
                    }]);
            }])
//            ->whereDoesntHave('tags')
            ->with(['tags'=>function($query){
                $query->select('username');
            }])
            ->where('user_id', $user->id)
            ->withCount('likes')
            ->withCount('comments')
            ->orderBy('created_at', 'desc')
            ->paginate(6);
        return $posts;
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
        $post = NewsFeed::where('id', $id)->select('id', 'user_id')->first();
        $existing_like = Like::where('post_id', $id)->where('user_id',$request->user()->id)->first();

        if (is_null($existing_like)) {
            Like::create([
                'post_id' => $id,
                'user_id' => $request->user()->id
            ]);
            if($request->user()->id !== $post->user->id) {
                $notification = new Notification();
                $notification->post_id = $post->id;
                $notification->author_id = $post->user->id;
                $notification->user_id = $request->user()->id;
                $notification->message = " likes your post";
                $notification->type = 1;
                $notification->save();
                CustomBroadcaster::fire($post->user->id, 'new_notification', $notification);
            }

//            if($notification->save()){
                return response()->json(["status"=>true, 'post_count'=>$post->likes()->count()], 200);
//            }
        } else {
            if (is_null($existing_like->deleted_at)) {
                $existing_like->delete();
                Notification::where('author_id', $post->user->id)
                    ->where('post_id', $id)
                    ->where('user_id', $request->user()->id)
                    ->delete();
                return response()->json(["status"=>false, 'post_count'=>$post->likes()->count()], 200);
            } else {
                $existing_like->restore();
                Notification::where('author_id', $post->user->id)
                    ->where('post_id', $id)
                    ->where('user_id', $request->user()->id)
                    ->delete();
                return response()->json(["status"=>false, 'post_count'=>$post->likes()->count()], 200);
            }
        }
    }

    public function deletePostImages(Request $request)
    {
        $post = NewsFeed::where('id', $request->id)->select('id', 'photos')->first();

        if(isset($post)){
            $photos = $post->photos;
            $arr = array_diff($photos, array($request->photo));
            $photos_string = implode(",", $arr);
            $post->photos = $photos_string;
            if($post->save()){
                return response()->json(["success"=>true, "message"=>"File has been deleted successfully"]);
            }
        }
    }

    public function whoLikedMyPost ($id) {
        $post = NewsFeed::select('id', 'user_id')->where('id', $id)->first();
        if($post){
            return $post->likes;
        }
    }
}