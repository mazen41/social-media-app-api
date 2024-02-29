<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function show ($id) {
        $user = User::where('id', $id)->get();

        if($user) {
            $user_posts = Post::where('user_id',  $id)->get();
            if($user_posts)  {
                return response()->json([
                    'user' => $user,
                    'posts' => $user_posts,
                ]);
            }
            return response()->json(['user' => $user]);
        };
    }
    public function getUser($id) {
        $user = User::find($id);
        if($user) {
            
            $posts = Post::where('user_id', $id)->with('user', 'comments.user', 'comments.replies.user')->get();  

            return response()->json([
                'user' => $user,
                'posts' => $posts,
            ]);
        }
    }
    // public function updateEmail(Request $request) {
    //     $request->validate([
    //         'email' => 'required|string|unique:users,email',
    //         'password' => 'required|string|confirmed',
    //         'user_id' => 'required|numeric',
    //     ]);
    //     $user_id = $request->user_id;
        
    //     $user = User::find($user_id);
    //     if ($user && Hash::check($request->password, $user->password)) {
    //         User::where('id', $user_id)->update(['email' => $request->email]);
    //         $updatedUser = User::find($user_id);
    //         return response()->json(['message' => 'User Email Updated Successfully','updated_user' => $updatedUser], 201);
    //     }else {
    //         return response()->json(['error_message' => 'something went Wrong'], 404);
    //     }
    // }
    public function updateEmail(Request $request) {
       
        
    
        $request->validate([
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'user_id' => 'required|numeric',
        ]);
    
        $user_id = $request->user_id;
        $user = User::find($user_id);
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Incorrect password'], 401);
        }
    
        User::where('id', $user_id)->update(['email' => $request->email]);
        $updatedUser = User::find($user_id);
    
        return response()->json(['message' => 'User Email Updated Successfully', 'updated_user' => $updatedUser], 201);
    }

    public function updateUsername(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string|confirmed',
            'user_id' => 'required|numeric',
        ]);
        $user_id = $request->user_id;
        $user = User::find($user_id);
        if ($user && Hash::check($request->password, $user->password)) {

            User::where('id', $user_id)->update(['name' =>  $request->name]);
            $updatedUser = User::find($user_id);
            return response()->json(['message' => 'Username Updated Successfully','updated_user' => $updatedUser], 201);
        }else {

            return response()->json(['password_error' => 'Something Went Wrong '], 404);
        }
    }
    public function updatePassword (Request $request) {
        $request->validate([
            'old_password' => 'required|string',
            'password' => 'required|string|confirmed',
            'user_id' => 'required|numeric',        
        ]);
        $user_id = $request->user_id;
        $user = User::find($user_id);
        if($user && Hash::check($request->old_password, $user->password)){
            $password_hashed = bcrypt($request->password);
            User::where('id', $user_id)->update(['password' => $password_hashed]);
            return response()->json(['message' => 'password Updated Successfully'], 201);
            
        }else {
            return response()->json(['incorccet_pass' => 'The Old Password Is Wrong Try Another Password'], 404);
        }

    }

    public function updateUserImage(Request $request) {
        $user = User::find($request->user_id);
    
        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }
    
        $fields = $request->validate([
            'image' => 'image|required',
        ]);
    
        $imagePath = null;
        if ($request->has('image')) {
            $image = $request->image;
            $name = time().'.'.$image->getClientOriginalExtension();
            $path = public_path('upload/users_images');
            $image->move($path, $name);
            $imagePath = $name;
    
            // Delete the previous image if exists
            if ($user->image) {
                $previousImagePath = public_path('upload/users_images/' . $user->image);
                if (file_exists($previousImagePath)) {
                    unlink($previousImagePath);
                }
            }
    
            $user->image = $name;
            $user->save();
        }
    
        return response(['imagePath' => $imagePath ? $imagePath : null, 'user' => $user], 200);
    }
    
    public function search(string $query) {
        // $query = $request->input('query');
        $users = User::where('name', 'like', "%$query%")->get();
        return response()->json($users);
    }
}

