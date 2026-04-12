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
        Schema::create('favori', function (Blueprint $table) {
            $table->unsignedInteger('id_utilisateur');
            $table->unsignedInteger('id_activite');

            $table->primary(['id_utilisateur', 'id_activite']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favori');
    }
};
