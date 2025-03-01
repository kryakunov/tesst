<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index() {
        $posts = Post::all();
        $posts = PostResource::collection($posts)->resolve();
        return inertia('Post/index', compact('posts'));
    }

    public function test() {
        return inertia('Post/test');
    }

    public function create() {
        return inertia('Post/create');
    }

    public function show(Post $post) {
        return inertia('Post/show', compact('post'));
    }

    public function edit(Post $post) {
        return inertia('Post/edit', compact('post'));
    }

    public function delete(Post $post) {
        $post->delete();
        return redirect()->route('posts.index');
    }

    public function store(StoreRequest $request) {
        Post::create($request->validated());

      // return redirect()->route('posts.index');
    }
}
