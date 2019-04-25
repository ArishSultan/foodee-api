<?php
namespace App\Http\Controllers;
use App\NewsFeed;
use App\Profile;
use App\Providers\UploadServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

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

        $user = User::where('id', $id)->first();

        $user->username = $username;
        //if($user->save()){

            if(isset($categories) && count($categories) > 0){
                foreach($categories as $catId){
                    $user->profile->foods()->attach($catId);
                }
            }

            $user->profile->message = $message;
            $user->profile->age = $age;
            $user->profile->location = $location;
            $user->profile->gender = $gender;
            $user->profile->contribution = $contribution;
            if($user->save() && $user->profile->save()){
                $user->profile->foods;
                return response()->json(["success"=>true, "message"=>"Profile has been updated successfully", "data"=>$user]);
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