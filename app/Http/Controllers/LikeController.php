<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;
use App\Models\LikedTo;
class LikeController extends Controller
{
    public function like(Request $request, $id) {
        $post = Post::find($id);
    
        if ($post) {
            $user_id = $request->input('userId'); 
            $liked_to_id = $request->input('LikedTo');

            $existing_like = Like::where('user_id', $user_id)->where('post_id', $id)->first();
            if ($existing_like) {
                $existing_like->delete();
                $existing_liked_to = LikedTo::where('user_id', $user_id)->where('post_id', $id)->where('liked_to_id',  $liked_to_id)->first();
                if($existing_liked_to) {
                    $existing_liked_to->delete();
                }
            } else {
                $new_like = new Like();
                $new_like->user_id = $user_id;
                $new_like->post_id = $id;
                $new_like->save();
                $liked_to = new LikedTo();
                $liked_to->user_id = $user_id;
                $liked_to->post_id = $id;
                $liked_to->liked_to_id = $liked_to_id;

                $liked_to->save();
            }
    
            $likes_count = Like::where('post_id', $id)->count();
            $post->likes = $likes_count;
            $post->save();
    
            return response()->json([
                'likes_count' => $likes_count,
                'likedTo' => $liked_to_id,
            ], 202);
        }
    
        return response()->json(['message' => 'Post not found'], 404);
    }
    
    
    
}
