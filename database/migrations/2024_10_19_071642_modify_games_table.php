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
        Schema::table('games', function (Blueprint $table) {
            // Drop existing columns
            $table->dropColumn(['name', 'creator_id', 'type', 'password']);

            // Modify the status column
            $table->enum('status', ['waiting', 'ongoing', 'finished'])->default('waiting')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Add back the removed columns
            $table->string('name');
            $table->foreignId('creator_id')->constrained('users');
            $table->enum('type', ['public', 'private'])->default('public');
            $table->string('password')->nullable();

            // Revert the status column to its previous state
            $table->enum('status', ['waiting', 'in_progress', 'ended'])->default('waiting')->change();
        });
    }
};
