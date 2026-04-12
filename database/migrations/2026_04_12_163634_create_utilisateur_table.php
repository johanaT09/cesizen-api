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
        Schema::create('utilisateur', function (Blueprint $table) {
            $table->increments('id_utilisateur');
            $table->string('prenom', 50)->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('email', 50);
            $table->string('mot_de_passe')->nullable();
            $table->timestamp('consentement_rgpd')->nullable();
            $table->boolean('est_actif')->nullable();
            $table->timestamp('date_anonymisation')->nullable();
            $table->unsignedInteger('id_genre');
            $table->unsignedInteger('id_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateur');
    }
};
