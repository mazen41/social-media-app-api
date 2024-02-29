<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LikedTo;
use App\Models\User;
class LikedToController extends Controller
{
    public function index($id)  {
        $check_user = User::where('id', $id)->first();
        
        if($check_user) {
            $users_liked = likedTo::where('user_id', $id)->get();

            if($users_liked->isNotEmpty()) {
                $likedUserIds = $users_liked->pluck('liked_to_id')->toArray();
                $likedUsers = User::whereIn('id', $likedUserIds)->get();
                return response()->json(['users_liked' => $likedUsers]);
            } else {
                return response()->json(['message' => 'No liked users found for this user.']);
            }
        } else {
            return response()->json(['message' => 'User not found.']);
        }
    }
}
