<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'column_id', 'title', 'description',
        'status', 'priority', 'due_date', 'quadrant',
        'est_pomodoros', 'done_pomodoros',
    ];

    protected $casts = [
        'due_date' => 'date:Y-m-d',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function column()
    {
        return $this->belongsTo(BoardColumn::class, 'column_id');
    }
}
