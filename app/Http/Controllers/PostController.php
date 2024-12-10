<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        return Post::with('attachment', 'user')->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $data = $request->validated();

        $image = $request->file('attachments')->store('attachments');
        $data['image'] = $image;

        $user = $request->user();

        $post = $user->posts()->create($data);
        $post->attachment()->create(['file_path' => $image]);

        return response()->json([
            'message' => 'Create post success',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        if ($post->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Forbidden access',
            ], 403);
        }

        Storage::delete($post->attachment->file_path);
        $post->delete();

        return response()->json([
            'message' => 'Delete post success',
        ], 204);
    }
}
