<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumComment;
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

    public function posts($categoryId)
    {
        $category = ForumCategory::findOrFail($categoryId);

        $posts = $category->posts()
            ->with('user:id,name,username')
            ->withCount('comments')
            ->latest()
            ->limit(50)
            ->get();

        return response()->json($posts);
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

        $post->load('user:id,name,username');
        $post->comments_count = 0;

        return response()->json($post, 201);
    }

    public function showPost(Request $request, $postId)
    {
        $post = ForumPost::with(['user:id,name,username', 'category:id,name,slug,color,icon'])
            ->findOrFail($postId);

        $userId = Auth::id();

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
            ->with('user:id,name,username')
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

        $comment->load('user:id,name,username');
        $comment->upvotes   = 0;
        $comment->downvotes = 0;
        $comment->user_vote = null;

        return response()->json($comment, 201);
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
