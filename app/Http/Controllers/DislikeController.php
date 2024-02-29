<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dislike;
use App\Models\Post;
use App\Models\DislikedTo;
class DislikeController extends Controller
{
    // public function dislike(Request $request, $id) {
    //     $post = Post::find($id);
    
    //     if ($post) {
    //         $user_id = $request->input('userId'); // Assuming userId is sent in the request payload
    
    //         // Check if the user has already liked the post
    //         $existing_dislike = Dislike::where('user_id', $user_id)->where('post_id', $id)->first();
    //         if ($existing_dislike) {
    //             $existing_dislike->delete();
    //         } else {
    //             // Like the post
    //             $new_dislike = new Dislike();
    //             $new_dislike->user_id = $user_id;
    //             $new_dislike->post_id = $id;
    //             $new_dislike->save();
    //         }
    
    //         // Get the updated count of likes for the post
    //         $dislikes_count = Dislike::where('post_id', $id)->count();
    //         $post->dislikes = $dislikes_count;
    //         $post->save();
    
    //         return response()->json([
    //             'dislikes_count' => $dislikes_count,
    //         ], 202);
    //     }
    
    //     return response()->json(['message' => 'Post not found'], 404);
    // } 

    public function dislike(Request $request, $id) {
        $post = Post::find($id);
    
        if ($post) {
            $user_id = $request->input('userId'); 
            // $disliked_to_id = $request->input('dislikedTo');

            $existing_dislike = Dislike::where('user_id', $user_id)->where('post_id', $id)->first();
            if ($existing_dislike) {
                $existing_dislike->delete();
                
            } else {
                $new_dislike = new Dislike();
                $new_dislike->user_id = $user_id;
                $new_dislike->post_id = $id;
                $new_dislike->save();
              

                // $disliked_to->save();
            }
    
            $dislikes_count = Dislike::where('post_id', $id)->count();
            $post->dislikes = $dislikes_count;
            $post->save();
    
            return response()->json([
                'dislikes_count' => $dislikes_count,

            ], 202);
        }
    
        return response()->json(['message' => 'Post not found'], 404);
    }
}
