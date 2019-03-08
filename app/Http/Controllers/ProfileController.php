<?php
namespace App\Http\Controllers;
use App\NewsFeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class ProfileController extends Controller
{
    public function index() {
    }

    public function show(NewsFeed $post) {
        return $post;
    }

    public function store(Request $request) {

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