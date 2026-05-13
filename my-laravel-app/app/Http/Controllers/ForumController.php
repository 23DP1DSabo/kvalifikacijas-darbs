<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumComment;
use App\Models\ForumFavorite;
use App\Models\ForumPost;
use App\Models\ForumVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function categories()
    {
        return response()->json(
            ForumCategory::withCount('posts')->get()
        );
    }

    public function posts(Request $request, $categoryId)
    {
        $category = ForumCategory::findOrFail($categoryId);
        $userId   = Auth::id();

        $query = $category->posts()
            ->with('user:id,name,username,profile_picture,role')
            ->withCount('comments');

        // Search
        if ($search = trim($request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                    ->orWhere('username', 'like', "%{$search}%"));
            });
        }

        // Favorites filter (auth-only)
        if ($userId && $request->boolean('favorites')) {
            $query->whereHas('favorites', fn($q) => $q->where('user_id', $userId));
        }

        // Sort
        match ($request->query('sort', 'latest')) {
            'oldest'  => $query->oldest(),
            'popular' => $query->orderByDesc('comments_count'),
            default   => $query->latest(),
        };

        $posts = $query->limit(100)->get();

        // Attach is_favorited flag per post without N+1
        if ($userId && $posts->isNotEmpty()) {
            $favoritedIds = ForumFavorite::where('user_id', $userId)
                ->whereIn('post_id', $posts->pluck('id'))
                ->pluck('post_id')
                ->flip();

            $posts->each(function ($post) use ($favoritedIds) {
                $post->is_favorited = $favoritedIds->has($post->id);
            });
        } else {
            $posts->each(fn($post) => $post->is_favorited = false);
        }

        return response()->json($posts);
    }

    public function toggleFavorite(Request $request, $postId)
    {
        $post   = ForumPost::findOrFail($postId);
        $userId = $request->user()->id;

        $existing = ForumFavorite::where('user_id', $userId)->where('post_id', $post->id)->first();

        if ($existing) {
            $existing->delete();
            $favorited = false;
        } else {
            ForumFavorite::create(['user_id' => $userId, 'post_id' => $post->id]);
            $favorited = true;
        }

        return response()->json(['is_favorited' => $favorited]);
    }

    public function storePost(Request $request, $categoryId)
    {
        $category = ForumCategory::findOrFail($categoryId);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string|max:10000',
        ]);

        $post = $category->posts()->create([
            'user_id' => $request->user()->id,
            'title'   => $data['title'],
            'body'    => $data['body'],
        ]);

        $post->load('user:id,name,username,profile_picture,role');
        $post->comments_count = 0;

        return response()->json($post, 201);
    }

    public function showPost(Request $request, $postId)
    {
        $post = ForumPost::with(['user:id,name,username,profile_picture,role', 'category:id,name,slug,color,icon'])
            ->findOrFail($postId);

        $userId = Auth::id();

        $post->is_favorited = $userId
            ? ForumFavorite::where('user_id', $userId)->where('post_id', $post->id)->exists()
            : false;

        // Load user votes in one query
        $commentIds = $post->comments()->pluck('id');
        $userVotes  = collect();
        if ($userId && $commentIds->isNotEmpty()) {
            $userVotes = ForumVote::where('user_id', $userId)
                ->whereIn('comment_id', $commentIds)
                ->get()
                ->keyBy('comment_id');
        }

        $comments = $post->comments()
            ->with('user:id,name,username,profile_picture,role')
            ->withCount([
                'votes as upvotes'   => fn($q) => $q->where('value', 1),
                'votes as downvotes' => fn($q) => $q->where('value', -1),
            ])
            ->latest()
            ->get()
            ->map(function ($comment) use ($userVotes) {
                $comment->user_vote = $userVotes->get($comment->id)?->value ?? null;
                return $comment;
            });

        return response()->json(['post' => $post, 'comments' => $comments]);
    }

    public function storeComment(Request $request, $postId)
    {
        $post = ForumPost::findOrFail($postId);

        $data = $request->validate(['body' => 'required|string|max:5000']);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $data['body'],
        ]);

        $comment->load('user:id,name,username,profile_picture,role');
        $comment->upvotes   = 0;
        $comment->downvotes = 0;
        $comment->user_vote = null;

        return response()->json($comment, 201);
    }

    public function updatePost(Request $request, $postId)
    {
        $post = ForumPost::findOrFail($postId);
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string|max:10000',
        ]);
        $post->update($data);
        return response()->json($post);
    }

    public function deleteOwnPost(Request $request, $postId)
    {
        $post = ForumPost::findOrFail($postId);
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        $post->delete();
        return response()->json(['deleted' => true]);
    }

    public function updateComment(Request $request, $commentId)
    {
        $comment = ForumComment::findOrFail($commentId);
        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        $data = $request->validate(['body' => 'required|string|max:5000']);
        $comment->update($data);
        return response()->json($comment);
    }

    public function deleteOwnComment(Request $request, $commentId)
    {
        $comment = ForumComment::findOrFail($commentId);
        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        $comment->delete();
        return response()->json(['deleted' => true]);
    }

    public function vote(Request $request, $commentId)
    {
        $comment  = ForumComment::findOrFail($commentId);
        $data     = $request->validate(['value' => 'required|in:-1,1']);
        $value    = (int) $data['value'];
        $userId   = $request->user()->id;
        $existing = $comment->votes()->where('user_id', $userId)->first();

        if (!$existing) {
            $comment->votes()->create(['user_id' => $userId, 'value' => $value]);
        } elseif ($existing->value === $value) {
            $existing->delete();
            $value = null;
        } else {
            $existing->update(['value' => $value]);
        }

        return response()->json([
            'upvotes'   => $comment->votes()->where('value', 1)->count(),
            'downvotes' => $comment->votes()->where('value', -1)->count(),
            'user_vote' => $value,
        ]);
    }
}
