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
        Schema::table('utilisateur', function (Blueprint $table) {
            $table->foreign(['id_genre'], 'utilisateur_id_genre_fkey')->references(['id_genre'])->on('genre_utilisateur')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_role'], 'utilisateur_id_role_fkey')->references(['id_role'])->on('role')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utilisateur', function (Blueprint $table) {
            $table->dropForeign('utilisateur_id_genre_fkey');
            $table->dropForeign('utilisateur_id_role_fkey');
        });
    }
};
