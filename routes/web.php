<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Test');

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::post('/postss', [PostController::class, 'store'])->name('posts.store');

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/test', [PostController::class, 'test'])->name('posts.test');
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
Route::post('/posts/{post}', [PostController::class, 'delete'])->name('posts.delete');



Route::group(['prefix' => 'test'], function () {
    Route::get('/', [TestController::class, 'test'])->name('posts.index');
    Route::get('/asd', [TestController::class, 'asd'])->name('posts.index');
    Route::post('/upload', [TestController::class, 'upload'])->name('upload');
    Route::get('/posts', [TestController::class, 'index'])->name('posts.index');
    Route::get('/getquestions', [TestController::class, 'getQuestions'])->name('posts.index');
    Route::post('/send', [TestController::class, 'send'])->name('posts.index');
});

require __DIR__.'/auth.php';
