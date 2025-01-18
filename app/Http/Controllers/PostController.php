<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'page' => 'integer|min:0',
            'size' => 'integer|min:1'
        ]);

        return response()->json(Post::with(['attachments', 'user'])->paginate(
            $validated['size'] ?? 10,
            ['*'],
            'page',
            $validated['page'] ?? 0
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'caption' => 'required|string|max:255',
            'attachments' => 'required|array',
            'attachments.*' => ['required', File::types(["jpg", "jpeg", "webp", "png", "gif"])->max(1024)],
        ]);

        /**
         * @var \App\Models\User
         */
        $user = Auth::user();

        $post = $user->posts()->create([
            'caption' => $validated['caption']
        ]);

        foreach ($request->file('attachments') as $file) {
            $path = $file->store('posts', 'public');

            $post->attachments()->create([
                'storage_path' => $path
            ]);
        }

        return response()->json([
            "message" => "Create post success"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return response()->json($post->load('attachments'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Post $post)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (Auth::id() !== $post->user_id) {
            return response([
                'message' => 'Unauthorized'
            ], 401);
        }

        foreach ($post->attachments() as $attachment) {
            if (Storage::exists($attachment->storage_path)) {
                Storage::delete($attachment->storage_path);
            }

            $attachment->delete();
        }

        $post->delete();

        return response([], 204);
    }
}
