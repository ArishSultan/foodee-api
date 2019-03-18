<?php
namespace App\Http\Controllers;
use App\NewsFeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class PostController extends Controller
{
    public function index() {
        return NewsFeed::withCount('comments')->orderBy('created_at', 'desc')->paginate(6);
    }
    public function show(NewsFeed $post) {
        return $post;
    }
    public function store(Request $request) {
        $request['user_id'] = $request->user()->id;
        $photos = ['http://www.erro-shop.de/WebRoot/Store7/Shops/61191284/4B6C/0809/A63D/3582/ECF1/C0A8/28BC/0D81/72370_thunfisch_salat_ohne_teller_ml.jpg', 'https://ae01.alicdn.com/kf/HTB1ico.a9YTBKNjSZKbq6xJ8pXaC/LeadingStar-6pcs-Pack-Cute-Imitation-Food-Erasers-Hamburger-Eraser-Office-Study-Correction-Supplies-for-Students-OL.jpg_640x640.jpg'];
        $request['photos'] = implode(",", $photos);
        $post = NewsFeed::create($request->all());
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
}