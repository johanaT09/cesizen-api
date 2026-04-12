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
        Schema::table('activite_detente', function (Blueprint $table) {
            $table->foreign(['id_categorie'], 'activite_detente_id_categorie_fkey')->references(['id_categorie'])->on('categorie_activite')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_type'], 'activite_detente_id_type_fkey')->references(['id_type'])->on('type')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activite_detente', function (Blueprint $table) {
            $table->dropForeign('activite_detente_id_categorie_fkey');
            $table->dropForeign('activite_detente_id_type_fkey');
        });
    }
};
