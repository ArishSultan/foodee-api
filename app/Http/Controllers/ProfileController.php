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
        $profile->user_id = $user->id;
        $profile->interest = $request->interest;
        $profile->ages = $request->ages;
        $profile->contribution = $request->contribution;

        if ($profile->save()) {
            if (isset($request->foods) && count($request->foods) > 0) {
                foreach($request->foods as $food) {
                    $profile->foods()->attach($food);
                }
                return response()->json(["success"=>true, "message"=> "Profile has been created successfully", "data"=>$profile]);
            }
        } else {
            return response()->json(["success"=>false, "message"=> "Could not created", "data"=>[]]);
        }
    }

    public function update($id, Request $request) {
        $username = $request->username;
        $message = $request->message;
        $age = $request->age;
        $location = $request->location;
        $categories = $request->categories;
        $gender = $request->gender;
        $contribution = $request->contribution;
        $is_age_private = $request->is_age_private;

        $user = User::where('id', $id)->first();

        if ($user->profile == null) {
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


//            return $message;

            if($user->save() && $user->profile->save()){
                $user->profile->foods;
                return response()->json(["success"=>true, "message"=>"Profile has been updated successfully", "data"=>$user]);
            }
        }
    }

    public function delete(Request $request, $id) {
        $profile = Profile::where('id', $id);
	$_profile = $profile->get();

	if (count($_profile) > 0) {

            $user_id = $_profile[0]->user_id;

            $profile->delete();
            User::where('id', $user_id)->delete();

            return response()->json(null, 204);
	} else {
            return response()->json(null, 404);
        }
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
