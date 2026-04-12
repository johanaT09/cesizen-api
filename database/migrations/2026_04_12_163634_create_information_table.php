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
        Schema::create('information', function (Blueprint $table) {
            $table->increments('id_information');
            $table->string('titre_information', 50)->nullable();
            $table->text('contenu_information')->nullable();
            $table->date('date_publication_information')->nullable();
            $table->boolean('est_actif')->nullable();
            $table->unsignedInteger('id_categorie');
            $table->unsignedInteger('id_utilisateur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('information');
    }
};
