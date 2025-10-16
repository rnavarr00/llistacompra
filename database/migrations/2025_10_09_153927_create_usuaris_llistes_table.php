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
        Schema::create('usuaris_llistes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuari_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('llista_id')->constrained('llistes')->onDelete('cascade');
            $table->timestamps();

            // Evitem duplicats (un mateix usuari no pot tenir la mateixa llista dos cops)
            $table->unique(['usuari_id', 'llista_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuaris_llistes');
    }
};
