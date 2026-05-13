<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\PomodoroController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/login',    [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/logout',   [AuthController::class, 'logout']);
    Route::get('/user',      [AuthController::class, 'user']);
});

// Public API routes
Route::prefix('api')->group(function () {
    Route::get('/forum/categories',                [ForumController::class, 'categories']);
    Route::get('/forum/categories/{id}/posts',     [ForumController::class, 'posts']);
    Route::get('/forum/posts/{id}',                [ForumController::class, 'showPost']);
    Route::post('/contact',                        [ContactController::class, 'send'])->middleware('throttle:5,10');
});

// Auth-required API routes
Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('/tasks',              [TaskController::class, 'index']);
    Route::post('/tasks',             [TaskController::class, 'store']);
    Route::put('/tasks/{task}',       [TaskController::class, 'update']);
    Route::delete('/tasks/{task}',    [TaskController::class, 'destroy']);

    Route::get('/columns',             [ColumnController::class, 'index']);
    Route::post('/columns',            [ColumnController::class, 'store']);
    Route::put('/columns/{column}',    [ColumnController::class, 'update']);
    Route::delete('/columns/{column}', [ColumnController::class, 'destroy']);

    Route::post('/forum/categories/{id}/posts',   [ForumController::class, 'storePost']);
    Route::put('/forum/posts/{id}',               [ForumController::class, 'updatePost']);
    Route::delete('/forum/posts/{id}',            [ForumController::class, 'deleteOwnPost']);
    Route::post('/forum/posts/{id}/comments',     [ForumController::class, 'storeComment']);
    Route::put('/forum/comments/{id}',            [ForumController::class, 'updateComment']);
    Route::delete('/forum/comments/{id}',         [ForumController::class, 'deleteOwnComment']);
    Route::post('/forum/comments/{id}/vote',      [ForumController::class, 'vote']);
    Route::post('/forum/posts/{id}/favorite',     [ForumController::class, 'toggleFavorite']);

    Route::put('/user/profile',                   [UserController::class, 'updateProfile']);
    Route::put('/user/password',                  [UserController::class, 'updatePassword']);

    Route::post('/user/avatar',              [UserController::class, 'updateAvatar']);
    Route::get('/pomodoro/settings',         [PomodoroController::class, 'getSettings']);
    Route::post('/pomodoro/settings',        [PomodoroController::class, 'saveSettings']);
});

// Admin-only API routes
Route::prefix('api/admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/users',                  [AdminController::class, 'users']);
    Route::post('/users/{id}/toggle-ban', [AdminController::class, 'toggleBan']);
    Route::get('/posts',                  [AdminController::class, 'posts']);
    Route::delete('/posts/{id}',          [AdminController::class, 'deletePost']);
    Route::get('/comments',               [AdminController::class, 'comments']);
    Route::delete('/comments/{id}',       [AdminController::class, 'deleteComment']);
    Route::get('/categories',             [AdminController::class, 'categories']);
    Route::post('/categories',            [AdminController::class, 'storeCategory']);
    Route::put('/categories/{id}',        [AdminController::class, 'updateCategory']);
    Route::delete('/categories/{id}',     [AdminController::class, 'deleteCategory']);
});

