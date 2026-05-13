<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users()
    {
        return response()->json(
            User::select('id', 'name', 'surname', 'username', 'email', 'role', 'banned_at', 'created_at')
                ->orderByDesc('created_at')
                ->get()
        );
    }

    public function toggleBan($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot ban another admin.'], 422);
        }

        $user->banned_at = $user->banned_at ? null : now();
        $user->save();

        return response()->json(['banned_at' => $user->banned_at]);
    }

    public function posts(Request $request)
    {
        $query = ForumPost::with(['user:id,name,username', 'category:id,name'])
            ->withCount('comments')
            ->latest();

        if ($search = trim($request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        return response()->json($query->limit(200)->get());
    }

    public function deletePost($id)
    {
        ForumPost::findOrFail($id)->delete();
        return response()->json(['deleted' => true]);
    }

    public function comments(Request $request)
    {
        $query = ForumComment::with(['user:id,name,username', 'post:id,title'])
            ->latest();

        if ($search = trim($request->query('search', ''))) {
            $query->where('body', 'like', "%{$search}%");
        }

        return response()->json($query->limit(200)->get());
    }

    public function deleteComment($id)
    {
        ForumComment::findOrFail($id)->delete();
        return response()->json(['deleted' => true]);
    }

    public function categories()
    {
        return response()->json(ForumCategory::withCount('posts')->orderBy('name')->get());
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:forum_categories',
            'description' => 'nullable|string|max:500',
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:20',
        ]);
        return response()->json(ForumCategory::create($data), 201);
    }

    public function updateCategory(Request $request, $id)
    {
        $cat  = ForumCategory::findOrFail($id);
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:forum_categories,slug,' . $id,
            'description' => 'nullable|string|max:500',
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:20',
        ]);
        $cat->update($data);
        return response()->json($cat);
    }

    public function deleteCategory($id)
    {
        ForumCategory::findOrFail($id)->delete();
        return response()->json(['deleted' => true]);
    }
}
