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
        Schema::create('diretoria_oficios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diretoria_id')->nullable();
            $table->unsignedBigInteger('oficio_id')->nullable();
            $table->timestamps();
            $table->foreign('diretoria_id')->references('id')->on('diretorias');
            $table->foreign('oficio_id')->references('id')->on('oficios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diretoria_oficios');
    }
};
