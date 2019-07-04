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
        if($request->has('tags')){
            $users = $request->tags;
            foreach($users as $user){
                $post->tags()->attach($user, ['mode' => 'is with']);
            }
        }
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
            $notification = new Notification();
            $notification->post_id = $post->id;
            $notification->author_id = $post->user->id;
            $notification->user_id = $request->user()->id;
            $notification->message = $request->user()->username. " likes your post";
            $notification->type = 1;
            if($notification->save()){
                return response()->json(["status"=>true, 'post_count'=>$post->likes()->count()], 200);<?php
namespace App\Http\Controllers;
use App\NewsFeed;
use App\Profile;
use App\Providers\UploadServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index() {
    }

    public function show($id) {
//        $user = $request->user();
        return User::with('profile')->where('id', $id)->first();
    }

    public function store(Request $request) {

        $user = $request->user();
        $profile = new Profile();
        $profile->user_id = $request->user()->id;
        $profile->interest = $request->interest;
        $profile->ages = $request->ages;
        $profile->contribution = $request->contribution;
        if($profile->save()) {
            if(isset($request->foods) && count($request->foods) > 0){
                foreach($request->foods as $food){
                    $profile->foods()->attach($food);
                }
                return response()->json(["success"=>true, "message"=> "Profile has been created successfully", "data"=>$profile]);
            }
//            return response()->json(["success"=>true, "message"=> "Profile has been created successfully", "data"=>$profile]);
        } else {
            return response()->json(["success"=>false, "message"=> "Could not created", "data"=>[]]);
        }

    }
    public function update($id, Request $request) {


//        $profile->update($request->all());
//        return response()->json($profile);
        $username = $request->username;
        $message = $request->message;
        $age = $request->age;
        $location = $request->location;
        $categories = $request->categories;
        $gender = $request->gender;
        $contribution = $request->contribution;
        $is_age_private = $request->is_age_private;

        $user = User::where('id', $id)->first();

        if($user->profile == null){
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->message = $message;
            $profile->age = $age;
            $profile->location = $location;
            $profile->gender = $gender;
            $profile->contribution = $contribution;
            $profile->is_age_private = $is_age_private;

            if($profile->save()){
//                $user['profile'] = $profile;
//                $user_ = $user;
//                if(count($user_->profile->foods) > 0){
//                    $user_->profile->foods;
//                }
                $userData = User::where('id', $id)->first();
                $userData->profile;
//                $profileData = DB::select(DB::raw("SELECT
//  users.id, users.username, users.email, users.phone, users.lat, users.lng,
//   profiles.user_id,
//    profiles.avatar FROM users join profiles on profiles.user_id = users.id where users.id=$user->id;"));

                return response()->json(["success"=>true, "message"=>"Profile has been created successfully", "data"=>$userData]);
            }

        } else {

            $user->username = $username;
//            if(isset($categories) && count($categories) > 0){
//                foreach($categories as $catId){
//                    $user->profile->foods()->attach($catId);
//                }
//            }

            $user->profile->message = $message;
            $user->profile->age = $age;
            $user->profile->location = $location;
            $user->profile->gender = $gender;
            $user->profile->contribution = $contribution;
            $user->profile->is_age_private = $is_age_private;

            if($user->save() && $user->profile->save()){
                $user->profile->foods;
                return response()->json(["success"=>true, "message"=>"Profile has been updated successfully", "data"=>$user]);
            }
        }

        //}
    }
    public function delete(Profile $profile) {
        $profile->delete();
        return response()->json(null, 204);
    }

    public function updatePhoto(Request $request)
    {
        global $uploadedFile;
        $photo = $request->photo;
        $type = $request->type;
        $user = $request->user();
        if($type === "avatar"){
            $uploadedFile = UploadServiceProvider::upload($request, $user, $type);

            $user->profile->avatar = $uploadedFile;
        } else if ($type === "cover") {
            $uploadedFile = UploadServiceProvider::upload($request, $user, $type);
            $user->profile->cover = $uploadedFile;
        }

        if($user->profile->save()){
            return response()->json(["success"=>true, "message"=>"Saved", "type"=>$type, "baseUrl"=>url("/"), "photo"=>"storage/media/$type/".$user->id.'/'.$uploadedFile]);
        }
    }
}
            }
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
}