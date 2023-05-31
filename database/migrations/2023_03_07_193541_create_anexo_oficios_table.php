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
        Schema::create('anexo_oficios', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->nullable();
            $table->string('tipo')->nullable();
            $table->string('tamanho')->nullable();
            $table->string('caminho')->nullable();
            $table->unsignedBigInteger('oficio_id')->nullable();
            $table->timestamps();
            $table->foreign('oficio_id')->references('id')->on('oficios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anexo_oficios');
    }
};
