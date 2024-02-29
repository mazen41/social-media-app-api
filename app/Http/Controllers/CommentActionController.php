<?php

namespace App\Http\Controllers;

use App\Models\CommentAction;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\CommentLiked;
class CommentActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function createAction(Request $request)
    {
        $request->validate([
            'post_id' => 'required',
            'user_id' => 'required',
            'comment_id' => 'required',
            'action_type' => 'string|required'
        ]);
        $commentType = $request->action_type;
        
        $existing_action = CommentAction::where('post_id', $request->post_id)
            ->where('user_id', $request->user_id)
            ->where('parent_comment_id', $request->comment_id)
            ->where('action_type', $request->action_type)
            ->first();

        if ($existing_action) {
            $existing_action->delete();
            $comment_action_count = CommentAction::where('post_id', $request->post_id)
                ->where('parent_comment_id', $request->comment_id)
                ->where('action_type', $request->action_type)
                ->count();
            Comment::where('post_id', $request->post_id)->where('id', $request->comment_id)->update([
                $commentType => $comment_action_count,    
            ]);
            return response()->json($comment_action_count, 200);
        } else {
            $new_action = CommentAction::create([
                'post_id' => $request->post_id,
                'user_id' => $request->user_id,
                'parent_comment_id' => $request->comment_id, 
                'action_type' => $request->action_type,
            ]);

            if ($new_action) {
                $comment_action_count = CommentAction::where('post_id', $request->post_id)
                    ->where('parent_comment_id', $request->comment_id)
                    ->where('action_type', $request->action_type)
                    ->count();
                Comment::where('post_id', $request->post_id)->where('id', $request->comment_id)->update([
                    $commentType => $comment_action_count,    
                ]);
                return response()->json($comment_action_count, 200);
            }
        }

        return response()->json('Something Went Wrong!', 500);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CommentAction $commentAction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CommentAction $commentAction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CommentAction $commentAction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CommentAction $commentAction)
    {
        //
    }
}
