<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->string('image')->nullable();
            $table->ForeignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pomodoro_id')->constrained('pomodoros')->onDelete('cascade');
            $table->foreignId('board_id')->constrained('board')->onDelete('cascade');
            $table->foreignId('calendar_id')->constrained('calendar')->onDelete('cascade');
            $table->foreignId('matrix_id')->constrained('matrix')->onDelete('cascade');
            $table->ForeignId('post_id')->constrained('posts')->onDelete('cascade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
