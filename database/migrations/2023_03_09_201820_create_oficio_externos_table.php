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
        Schema::create('oficio_externos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('oficio_id')->nullable();
            $table->string('descricao')->nullable();
            $table->timestamps();
            $table->foreign('oficio_id')->references('id')->on('oficios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oficio_externos');
    }
};
