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
        Schema::create('session_activite', function (Blueprint $table) {
            $table->increments('id_session');
            $table->date('date_session')->nullable();
            $table->string('duree_realisee', 50)->nullable();
            $table->unsignedInteger('id_activite');
            $table->unsignedInteger('id_utilisateur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_activite');
    }
};
