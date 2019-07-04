<?php
namespace App\Http\Controllers;
use App\Comment;
use App\NewsFeed;
use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class CommentController extends Controller
{
    public function index(Request $request) {
        $comments = Comment::orderBy('created_at', 'asc')->where('post_id', $request->post_id)->paginate(30);
        return $comments;
    }
    public function show($id) {
//        return $comment;
        $comments = Comment::with(['user'=>function($q){
            $q->select('id', 'username', 'email')
                ->with(['profile'=>function($q){
                    $q->select('user_id', 'avatar');
                }])
            ;}])->orderBy('created_at', 'asc')->where('post_id', $id)->paginate(30);
        return $comments;
    }
    public function store(Request $request) {
        $request['user_id'] = $request->user()->id;
        $comment = Comment::create($request->all());
        $post = NewsFeed::where('id', $request->post_id)->select('id', 'user_id')->first();
        $notification = new Notification();
        $notification->post_id = $post->id;
        $notification->author_id = $post->user->id;
        $notification->user_id = $request->user()->id;
        $notification->message = $request->user()->username. " commented on your post";
        $notification->type = 2;
        $notification->save();
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