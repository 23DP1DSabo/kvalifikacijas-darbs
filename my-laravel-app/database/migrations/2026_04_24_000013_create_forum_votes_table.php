<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('comment_id')->constrained('forum_comments')->onDelete('cascade');
            $table->tinyInteger('value'); // 1 = up, -1 = down
            $table->timestamps();
            $table->unique(['user_id', 'comment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_votes');
    }
};
