<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:passport', function () {
//     Route::get('profile', [UserController::class, 'profile']);
// });

Route::group(['middleware'  =>  ['auth:api']], function () {

    // Profile
    Route::get('profile', [UserController::class, 'profile']);
    Route::post('profileUpdate', [UserController::class, 'profileUpdate']);
    Route::get('getFollower', [UserController::class, 'getFollower']);
    Route::get('getFollowing', [UserController::class, 'getFollowing']);
    
    // Post
    Route::get('getStory', [PostController::class, 'getStory']);
    Route::post('addStory', [PostController::class, 'addStory']);
    Route::delete('deleteStory', [PostController::class, 'deleteStory']);

    // Post Like
    Route::post('likeStory', [PostController::class, 'likeStory']);

    // Post Comments
    Route::post('addStoryComment', [PostController::class, 'addStoryComment']);
    Route::delete('deleteStoryComment', [PostController::class, 'deleteStoryComment']);
    
    // User Serach
    Route::get('userSearch', [UserController::class, 'userSearch']);

    // Follow / Unfollow
    Route::post('userFollow', [UserController::class, 'userFollow']);
    
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);