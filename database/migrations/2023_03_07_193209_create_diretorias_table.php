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
        Schema::create('diretorias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->string('nome')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->unsignedBigInteger('user_created')->nullable();
            $table->timestamps();
            $table->foreign('group_id')->references('id')->on('group_users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diretorias');
    }
};
