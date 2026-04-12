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
        Schema::table('session_activite', function (Blueprint $table) {
            $table->foreign(['id_activite'], 'session_activite_id_activite_fkey')->references(['id_activite'])->on('activite_detente')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_utilisateur'], 'session_activite_id_utilisateur_fkey')->references(['id_utilisateur'])->on('utilisateur')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session_activite', function (Blueprint $table) {
            $table->dropForeign('session_activite_id_activite_fkey');
            $table->dropForeign('session_activite_id_utilisateur_fkey');
        });
    }
};
