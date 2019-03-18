<?php
namespace App\Http\Controllers;
use App\Comment;
use App\NewsFeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class CommentController extends Controller
{
    public function index(Request $request) {
        $comments = Comment::orderBy('created_at', 'asc')->where('post_id', $request->post_id)->paginate(6);
        return $comments;
    }
    public function show($id) {
//        return $comment;
        $comments = Comment::with('user')->orderBy('created_at', 'asc')->where('post_id', $id)->paginate(6);
        return $comments;
    }
    public function store(Request $request) {
        $request['user_id'] = $request->user()->id;
        $comment = Comment::create($request->all());
        return response()->json($comment, 201);
    }
    public function update(Comment $comment, Request $request) {
        $comment->update($request->all());
        return response()->json($comment);
    }
    public function delete(Comment $comment) {
        $comment->delete();
        return response()->json(null, 204);
    }
}