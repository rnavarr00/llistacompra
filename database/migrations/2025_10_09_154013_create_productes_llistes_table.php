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
        Schema::create('productes_llistes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producte_id')->constrained('productes')->onDelete('cascade');
            $table->foreignId('llista_id')->constrained('llistes')->onDelete('cascade');
            $table->boolean('comprat')->default(false);
            $table->integer('quantitat')->default(1);
            $table->timestamps();

            // Evitar duplicats (un mateix producte no pot aparèixer dues vegades a la mateixa llista)
            $table->unique(['producte_id', 'llista_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productes_llistes');
    }
};
