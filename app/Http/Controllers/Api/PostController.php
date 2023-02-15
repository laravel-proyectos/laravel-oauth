<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
        $this->middleware('scopes:read-post')->only(['index', 'show']);
        $this->middleware('scopes:create-post')->only(['store']);
        $this->middleware('scopes:update-post')->only(['update']);
        $this->middleware('scopes:delete-post')->only(['destroy']);
    }

    public function index()
    {
        $posts = Post::included()
                        ->filter()
                        ->sort()
                        ->getOrPaginate();

        return PostResource::collection($posts);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|max:255|unique:posts',
            'extract' => 'required',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $user = auth()->user();
        $data['user_id'] = $user->id;
        $posts = Post::create($data);
        return PostResource::make($posts);
    }

    public function show($id)
    {
        $post = Post::included()->findOrFail($id);
        return PostResource::make($post);
    }


    public function update(Request $request, Post $post)
    {
        $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|max:255|unique:posts,slug,' . $post->id,
            'extract' => 'required',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $post->update($request->all());
        return PostResource::make($post);
    }


    public function destroy(Post $post)
    {
        $post->delete();
        return PostResource::make($post);
    }
}
