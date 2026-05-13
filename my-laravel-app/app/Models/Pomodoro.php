<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pomodoro extends Model
{
    protected $fillable = ['user_id', 'work_minutes', 'break_minutes', 'long_break_minutes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
