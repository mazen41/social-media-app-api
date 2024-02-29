<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Post;
class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::all();
        return response()->json($comments);
    }
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric',
            'post_id' => 'required|numeric',
            'content' =>'string|required',
        ]);
        $comment = new Comment();
        $comment->user_id = $request->user_id;
        $comment->post_id = $request->post_id;
        $comment->content = $request->content;
        $comment->replies_count = 0;
        $comment->likes = 0;
        $comment->dislikes = 0;
        
        $comment->save();
        $comment->load('user'); 
        $comment->load('replies.user');
        
        $comments_count = Comment::where('post_id', $request->post_id)->count();
        Post::where('id', $request->post_id)->update([
            'comments_count' => $comments_count
        ]);
        return response()->json([
            'comment' => $comment,
            'comments_count' => $comments_count
        ], 201);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }
    // public function like(Request $request)
    // {  
       
    // }
    // public function dislike()
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
