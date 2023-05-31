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
        Schema::create('oficio_relacionados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('oficio_pai')->nullable();
            $table->unsignedBigInteger('oficio_filho')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oficio_relacionados');
    }
};
