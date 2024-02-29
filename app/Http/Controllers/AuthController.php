<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{

    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'image' => 'image',
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);
        $imagePath = null;
        if($request->has('image')) {
            $image = $request->image;
            $name = time().'.'.$image->getClientOriginalExtension();
            $path = public_path('upload/users_images');
            $image->move($path, $name);
            $imagePath = $name;
            $user->image = $name;
            $user->save();
        }
        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,

        ];

        return response(['imagePath' => $imagePath ? $imagePath : null, 'response' => $response], 201);
    }


    public function login (Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $fields['email'])->first();
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
              'message' => "The Email Or Pssword Dosen't Match !"
            ], 401);
        }

        $token =  $user->createToken('myapptoken')->plainTextToken;
        $imagePath = $user->image;
        $response = [
            'user' => $user,
            'token' => $token,
            'imageUrl' => $imagePath
        ];
        return response($response, 201);
    }
    public function logout(Request $request) {
        // try {
        //     auth()->user()->tokens()->delete();
        //     return ['message' => "Logged Out"];
        // } catch (\Exception $e) {
        //     return response(['error' => $e->getMessage()], 500);
        // }
        // try {
        //     $userId = $request->user_id;
        //     // Assuming you have validated or ensured the user_id is present and valid.
    
        //     User::find($userId)->tokens->each(function ($token, $key) {
        //         $token->revoke();
        //     });
    
        //     return ['message' => 'Logged Out'];
        // } catch (\Exception $e) {
        //     return response(['error' => $e->getMessage()], 500);
        // }
        try {
            // Check if there is an authenticated user
            $user = auth()->user();
            if (!$user) {
                return response()->json(['message' => 'No user authenticated'], 401);
            }
    
            // Revoke and delete tokens
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });
    
            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    
    }
}
