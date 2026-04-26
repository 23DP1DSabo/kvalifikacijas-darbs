<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    protected $fillable = ['user_id', 'post_id', 'body'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'post_id');
    }

    public function votes()
    {
        return $this->hasMany(ForumVote::class, 'comment_id');
    }
}
