<?php
namespace App\Http\Controllers;
use App\NewsFeed;
use App\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class ProfileController extends Controller
{
    public function index() {
    }

    public function show(Profile $user) {
//        $user = $request->user();
        return $user;
    }

    public function store(Request $request) {

        $user = $request->user();
        $profile = new Profile();
        $profile->user_id = $request->user()->id;
        $profile->interest = $request->interest;
        $profile->ages = $request->ages;
        $profile->contribution = $request->contribution;
        if($profile->save()){
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
    public function update(Profile $profile, Request $request) {
        $profile->update($request->all());
        return response()->json($profile);
    }
    public function delete(Profile $profile) {
        $profile->delete();
        return response()->json(null, 204);
    }
}