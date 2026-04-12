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
        Schema::create('activite_detente', function (Blueprint $table) {
            $table->increments('id_activite');
            $table->string('titre_activite', 50)->nullable();
            $table->text('contenu_activite')->nullable();
            $table->integer('duree_estimee')->nullable();
            $table->boolean('est_actif')->nullable();
            $table->unsignedInteger('id_type');
            $table->unsignedInteger('id_categorie');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activite_detente');
    }
};
