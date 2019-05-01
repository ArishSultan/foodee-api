<?php
namespace App\Http\Controllers;
use App\FoodCategory;
use App\NewsFeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class FilterController extends Controller
{
    public function index(Request $request) {


        $food = $request->query('food');
        $contribution = $request->query('contribution');

        $users = User::whereHas(['profile'=> function($query) use ($food, $contribution) {
            $query->where('contribution', 'LIKE', "%{$contribution}%");
//                  ->where('name', 'LIKE', "%{$food}%");
        }, 'profile.foods', function($query) use ($food, $contribution) {

            $query->where('name', 'LIKE', "%{$food}%");
        }])
//            ->whereHas('profile.foods', function($query) use ($food, $contribution) {
//
//                    $query->where('name', 'LIKE', "%{$food}%");
//            })
//            ->whereHas('profile.foods', function($query) use ($food, $contribution) {
//            $query->where('name', 'LIKE', "%{$food}%");
//        })
            ->with('profile.foods')
//            ->with(['profile.foods' => function ($query) use ($food, $contribution) {
//                $query->where('name', 'LIKE', "%{$food}%");
//            }])
            ->where('id', '!=', $request->user()->id)->get();
        return $users;

    }
}