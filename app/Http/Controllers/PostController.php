<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Reponse;
use App\Models\User;
use App\Models\Comment;
class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      

        $posts = Post::with('user', 'comments.user', 'comments.replies.user')->get();  
        // if($posts){
        //     $transformedPosts = $posts->map(function ($post) {
        //         return [
        //             'post' => $post,
        //         ];
        //     });
        // }
        return response()->json([
            'posts' => $posts,
        ]); 

        // $posts = Post::with('user')->get();
        // return response()->json($posts);
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
        $request->validate([
            // 'user_id' => 'required|numeric',
            'title' =>'string|required',
            'accessibility' =>'string',
            'image' =>'image',
            'background_color' => 'string|required',

        ]);
        $accessibility = $request->has('accessibility') ? $request->input('accessibility') : 'public';

        $user = $request->user();
        $imagePath = null;
        if($request->has('image')) {
            $image = $request->image;
            $name = time().'.'.$image->getClientOriginalExtension();
            $path = public_path('upload/posts_images');
            $image->move($path, $name);
            $imagePath = $name;
        }

        $postData = $request->only(['title', 'background_color']);
        $postData['user_id'] = 10;
        $postData['image'] = $imagePath;
        $postData['accessibility'] = $accessibility; // Set the 'accessibility' field

        $post = Post::create($postData);
        $post = Post::with('user', 'comments.user', 'comments.replies.user')->find($post->id);

        return response()->json([
            'message' => 'Post Created Successfully',
            'post' => $post,
        ], 202);
    }

    public function show($id)
    {
            $post = Post::with('user', 'comments.user', 'comments.replies.user')->find($id);
            if($post) {
                return response()->json([
                    'post' => $post,
                ]); 
            } else {
                return response()->json([
                    'message' => "There's No Such A Post",
                ], 404);
            }
        }

    public function search(string $name)
    {
        return Product::where('name', 'like', '%'. $name . '%')->get();
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'user_id' => 'required|numeric',
    //         'title' =>'string|required',
    //         'accessibility' =>'string|required',
    //         'image' =>'image',
    //     ]);

    //     $post = Post::find($id);
    //     if($post) {
    //         $post->update($request->all());
    //         $post = Post::where('id', $id)->where('user_id', $request->user_id)->with('user', 'comments.user', 'comments.replies.user')->get();
    //         return response()->json($post, 200);
    //     }
    //     return response()->json('The Post Not Found My Man');
    // }
    public function edit(Request $request)
    {
        $request->validate([
            'post_id' => 'required|numeric',
            'user_id' => 'numeric',
            'title' => 'string',
            'accessibility' => 'string',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'background_color' => 'string',
        ]);

        $postId = $request->post_id;
        $post = Post::findOrFail($postId);

        // Ensure that the user is authorized to edit the post
        if ($request->user()->id != $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Upload new image and delete old one if a new image is provided
        $newImagePath = $this->uploadImage($request, $post);
        if ($newImagePath && $post->image) {
            $this->deleteImage($post->image);
        }

        // Update post data
        $postData = $request->only(['title', 'accessibility', 'background_color']);
        if ($request->has('user_id')) {
            $postData['user_id'] = $request->user_id;
        }
        if ($newImagePath) {
            $postData['image'] = $newImagePath;
        } elseif ($newImagePath === null) {
            $postData['image'] = null;
        }

        $post->update($postData);

        $post = Post::with('user', 'comments.user', 'comments.replies.user')->find($post->id);

        return response()->json([
            'message' => 'Post Updated Successfully',
            'post' => $post,
        ], 200);
    }

    private function uploadImage(Request $request, $post)
    {
        
        if ($request->has('image')) {
            $image = $request->image;
            $name = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('upload/posts_images');
    
            // Check if the file already exists and generate a unique name if necessary
            while (file_exists($path . '/' . $name)) {
                $name = time() . '_' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
            }
    
            $image->move($path, $name);
            return $name;
        } elseif ($post->image) {
            // If the request doesn't have an image but the post has one, it means the user wants to delete it
            $this->deleteImage($post->image);
            return null; // Return null to indicate that the image should be deleted in the database
        }
    
        // If no new image is provided and the post didn't have an image, return null
        return null;
        
    }

    private function deleteImage($imageName)
    {
        $imagePath = public_path('upload/posts_images/' . $imageName);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }



     public function destroy($id)
     {
         $post = Post::find($id);

         if ($post) {
            // Retrieve the image path
            $imagePath = $post->image;
    
            // Delete the post from the database
            $post->delete();
    
            if ($imagePath) {
                // Delete the associated image from the file system
                $this->deleteImage($imagePath);
            }
    
            return response()->json([
                "message" => "Post deleted successfully",
            ], 200);
        } else {
            return response()->json([
                "message" => "Post not found",
            ], 404);
        }
     }

}
