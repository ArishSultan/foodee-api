<?php
namespace App\Http\Controllers;
use App\FoodCategory;
use App\NewsFeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class FoodCategoryController extends Controller
{
    public function index() {
        return FoodCategory::orderByRaw("RAND()")->get();
    }

    public function show(FoodCategory $food) {
        return $food;
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'photo' => 'required|file|max:1024'
        ]);
        $user = $request->user();
        global  $photo_path;
        $photo_path = "";
        $food = new FoodCategory();
        $food->name = $request->name;
        $photo_path = time().'.'.request()->photo->getClientOriginalExtension();
        $request->photo->storeAs('foods',$photo_path);
        $food->photo = $photo_path;
        if($food->save()){
            $hasFood = $user->profile->foods()->where('food_id', $food->id)->exists();
            if($hasFood){
                $user->profile->foods()->attach($food->id);
                return response()->json(["success"=>true, "message"=> "Food has been added successfully", "data"=>$food]);
            } else {
                return response()->json(["success"=>true, "message"=> "Pleas try another, Its already been added", "data"=>$food]);

            }

        } else {
            return response()->json(["success"=>false, "message"=> "Could not added", "data"=>[]]);
        }
    }
    public function update(FoodCategory $food, Request $request) {
        $food->update($request->all());
        return response()->json($food);
    }
    public function delete(FoodCategory $food) {
        $food->delete();
        return response()->json(null, 204);
    }
}