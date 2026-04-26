<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedSmallInteger('est_pomodoros')->default(1)->after('quadrant');
            $table->unsignedSmallInteger('done_pomodoros')->default(0)->after('est_pomodoros');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['est_pomodoros', 'done_pomodoros']);
        });
    }
};
