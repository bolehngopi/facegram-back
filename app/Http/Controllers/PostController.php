<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'min:0',
            'size' => 'integer'
        ]);

        return response()->json(Post::paginate(
            $request->input('size')
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'caption' => 'required',
            'attachment.*' => 'required|image|file|max:2400'
        ]);

        /**
         * @var \App\Models\User
         */
        $user = Auth::user();

        $post = $user->posts()->create([
            $request->input('caption')
        ]);

        foreach ($request->file('attachment') as $file) {
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
        return response()->json($post);
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
