<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove duplicate skills, keeping only the first occurrence
        DB::statement('
            DELETE FROM skills
            WHERE id NOT IN (
                SELECT MIN(id)
                FROM skills
                GROUP BY user_id, title, type
            )
        ');

        // Add unique constraint
        Schema::table('skills', function (Blueprint $table) {
            $table->unique(['user_id', 'title', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'title', 'type']);
        });
    }
};
