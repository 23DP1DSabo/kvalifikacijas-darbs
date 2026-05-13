<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->decimal('x_pos', 5, 2)->nullable()->after('quadrant');
            $table->decimal('y_pos', 5, 2)->nullable()->after('x_pos');
        });

        // Seed coordinates from existing quadrant values so placed tasks stay placed
        DB::table('tasks')->where('quadrant', 'do')       ->update(['x_pos' => 25, 'y_pos' => 25]);
        DB::table('tasks')->where('quadrant', 'schedule') ->update(['x_pos' => 75, 'y_pos' => 25]);
        DB::table('tasks')->where('quadrant', 'delegate') ->update(['x_pos' => 25, 'y_pos' => 75]);
        DB::table('tasks')->where('quadrant', 'eliminate')->update(['x_pos' => 75, 'y_pos' => 75]);
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['x_pos', 'y_pos']);
        });
    }
};
