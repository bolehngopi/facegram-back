<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
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

        return response()->json(User::paginate(
            $validated['size'] ?? 10,
            ['*'],
            'page',
            $validated['page'] ?? 0
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json($user->load(['posts', 'posts.attachments']));
    }
}
