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
        Schema::table('information', function (Blueprint $table) {
            $table->foreign(['id_categorie'], 'information_id_categorie_fkey')->references(['id_categorie'])->on('categorie_activite')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_utilisateur'], 'information_id_utilisateur_fkey')->references(['id_utilisateur'])->on('utilisateur')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('information', function (Blueprint $table) {
            $table->dropForeign('information_id_categorie_fkey');
            $table->dropForeign('information_id_utilisateur_fkey');
        });
    }
};
