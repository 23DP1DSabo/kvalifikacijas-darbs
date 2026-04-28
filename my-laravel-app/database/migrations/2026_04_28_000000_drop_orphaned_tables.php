<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('calendar');
        Schema::dropIfExists('matrix');
        Schema::dropIfExists('forum');
    }

    public function down(): void
    {
        // These tables are intentionally gone; no rollback needed.
    }
};
