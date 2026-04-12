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
        Schema::table('favori', function (Blueprint $table) {
            $table->foreign(['id_activite'], 'favori_id_activite_fkey')->references(['id_activite'])->on('activite_detente')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_utilisateur'], 'favori_id_utilisateur_fkey')->references(['id_utilisateur'])->on('utilisateur')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favori', function (Blueprint $table) {
            $table->dropForeign('favori_id_activite_fkey');
            $table->dropForeign('favori_id_utilisateur_fkey');
        });
    }
};
