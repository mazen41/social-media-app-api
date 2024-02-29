<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\LikedToController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\CommentActionController;
use App\Http\Controllers\DislikeController;
use App\Http\Controllers\ReplyActionController;
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::get('/posts/search/{name}', [PostController::class, 'search']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/posts/{id}/liked-status', [CheckLikedStatus::class, 'likedStatus']);
Route::get('/users/{id}/liked-to', [LikedToController::class, 'index']);
Route::get('/users/{id}/profile',  [UserController::class, 'show']);
Route::get('/user/{id}', [UserController::class, 'getUser']);
Route::get('/user/search/{query}', [UserController::class, 'search']);
Route::post('/posts/{id}/like', [LikeController::class, 'like']);
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/posts', [PostController::class, 'store']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::post('/posts/{id}/dislike', [DislikeController::class, 'dislike']);
    Route::post('/posts/comment', [CommentController::class, 'store']);
    Route::post('/posts/comment/action', [CommentActionController::class, 'createAction']);
    Route::post('/posts/reply/action', [ReplyActionController::class, 'createAction']);
    Route::post('/posts/comment/reply', [ReplyController::class, 'store']);
    Route::post('user/update/email', [UserController::class, 'updateEmail']);
    Route::post('user/update/name', [UserController::class, 'updateUsername']);
    Route::post('user/update/password', [UserController::class, 'updatePassword']);
    Route::post('user/update/image', [UserController::class, 'updateUserImage']);
    Route::post('post/edit', [PostController::class, 'edit']);
    Route::post('post/destroy/{id}', [PostController::class, 'destroy']);
});
