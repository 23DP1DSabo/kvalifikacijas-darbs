<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pomodoros', function (Blueprint $table) {
            $table->unsignedSmallInteger('long_break_minutes')->default(15)->after('break_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('pomodoros', function (Blueprint $table) {
            $table->dropColumn('long_break_minutes');
        });
    }
};
