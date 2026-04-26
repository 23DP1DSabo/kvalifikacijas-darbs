<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon', 60)->default('mdi-forum-outline');
            $table->string('color', 20)->default('#764ba2');
            $table->timestamps();
        });

        DB::table('forum_categories')->insert([
            ['name' => 'Laika pārvaldība',   'slug' => 'laika-parvaldiba',   'description' => 'Plāno laiku efektīvāk un tiec galā ar uzdevumiem laikā.',                    'icon' => 'mdi-clock-outline',   'color' => '#667eea', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Motivācija',           'slug' => 'motivacija',          'description' => 'Atrodi iedvesmu un spēku turpināt, pat kad ir grūti.',                        'icon' => 'mdi-lightning-bolt',  'color' => '#f39c12', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Uzmanības novēršana', 'slug' => 'uzmanibas-noversana', 'description' => 'Paliec fokusēts un novērš traucēkļus darba laikā.',                           'icon' => 'mdi-eye-off-outline', 'color' => '#e74c3c', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ģimene un darbs',     'slug' => 'gimene-un-darbs',     'description' => 'Sabalansē darba un personīgās dzīves prasības.',                              'icon' => 'mdi-home-heart',      'color' => '#27ae60', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paradumi un rutīna',  'slug' => 'paradumi-un-rutina',  'description' => 'Veido labas ieradumu sistēmas, kas darbojas ilgtermiņā.',                     'icon' => 'mdi-repeat',          'color' => '#9b59b6', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Stress un izdegšana', 'slug' => 'stress-un-izdgsana',  'description' => 'Apspried stresa pārvaldību un kā izvairīties no izdegšanas.',                 'icon' => 'mdi-fire-off',        'color' => '#e67e22', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vispārīgi',           'slug' => 'vispariigi',          'description' => 'Jebkas saistīts ar produktivitāti, kas neiekļaujas citās kategorijās.',       'icon' => 'mdi-forum-outline',   'color' => '#95a5a6', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_categories');
    }
};
