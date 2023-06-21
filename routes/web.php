<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [UserController::class, "correctHomePage"])->name('login');
Route::post('/register', [UserController::class, "register"])->middleware('guest');
Route::post('/login', [UserController::class, "login"])->middleware('guest');
Route::post('/logout', [UserController::class, "logout"])->middleware('MustBeLoggedIn');

// Manage Avatars
Route::get(
  '/manage-avatar',
  [UserController::class, "showManageAvatarForm"]
)->middleware('MustBeLoggedIn');

Route::post(
  '/upload-avatar',
  [UserController::class, "uploadAvatar"]
)->middleware('MustBeLoggedIn');

// Blog related routes

Route::get(
  '/create-post',
  [PostController::class, "showCreatePostForm"]
)->middleware('MustBeLoggedIn');

Route::post(
  '/create-post',
  [PostController::class, "createNewPost"]
)->middleware('MustBeLoggedIn');

Route::get(
  '/post/{post}',
  [PostController::class, "viewSinglePost"]
)->middleware('MustBeLoggedIn');

// Delete a post
Route::delete(
  '/delete/{post}',
  [PostController::class, "deletePost"]
)->middleware('can:delete,post');

// Update a post
Route::get(
  '/edit-post/{post}',
  [PostController::class, "showEditPostForm"]
)->middleware('can:update,post');

Route::put(
  '/post/{post}',
  [PostController::class, "updatePost"]
)->middleware('can:update,post');

// Profile related routes
// Access the profile by username or id
Route::get(
  '/profile/{user:username}',
  [UserController::class, "viewProfile"]
)->middleware('MustBeLoggedIn');

/*
  * Use Gates to ensure only an admin can view a page
*/
Route::get('/admins-only', function () {
  return view('admins-dashboard');
})->middleware('can:adminsViewOnly');
