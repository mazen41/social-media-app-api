<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Comment;
class ReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'user_id' => 'required|numeric',
        //     'post_id' => 'required|numeric',
        //     'comment_id' => 'required|numeric',
        //     'content' =>'string|required',
        // ]);
        // $reply = new Reply();
        // $reply->user_id = $request->user_id;
        // $reply->post_id = $request->post_id;
        // $reply->content = $request->content;
        // $reply->comment_id = $request->comment_id;
        // $replies_count = Reply::where('post_id', $request->post_id)->where('comment_id', $request->comment_id)->count();
        // Comment::where('post_id', $request->post_id)->where('id', $request->comment_id)->update([
        //     'replies_count' => $replies_count,
        // ]);

        // $user = User::findOrFail($request->user_id);
        // $reply->user()->associate($user);
        // $reply->save();

        // return response()->json([
        //     'reply' => $reply,
        //     'replies_count' => $replies_count
        // ], 201);
        $request->validate([
            'user_id' => 'required|numeric',
            'post_id' => 'required|numeric',
            'comment_id' => 'required|numeric',
            'content' => 'string|required',
        ]);
        
        $reply = new Reply();
        $reply->user_id = $request->user_id;
        $reply->post_id = $request->post_id;
        $reply->content = $request->content;
        $reply->comment_id = $request->comment_id;
        $reply->likes = 0;
        $reply->dislikes = 0;
        
        $user = User::findOrFail($request->user_id);
        $reply->user()->associate($user);
        $reply->save();
        
        // Update replies_count after saving the new reply
        $replies_count = Reply::where('post_id', $request->post_id)
            ->where('comment_id', $request->comment_id)
            ->count();
        
        Comment::where('post_id', $request->post_id)
            ->where('id', $request->comment_id)
            ->update([
                'replies_count' => $replies_count,
            ]);
        
        return response()->json([
            'reply' => $reply,
            'replies_count' => $replies_count,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reply $reply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reply $reply)
    {
        //
    }

    
    public function update(Request $request, Reply $reply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reply $reply)
    {
        //
    }
}
