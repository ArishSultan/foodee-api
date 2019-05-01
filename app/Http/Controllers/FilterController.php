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

        $users = User::whereHas('profile')->where('id', '!=', $request->user()->id)->get();
        return $users;

    }
}