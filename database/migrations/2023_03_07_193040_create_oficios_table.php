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
        Schema::create('oficios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('destinatario_id')->nullable();
            $table->string('numero_oficio')->nullable();
            $table->longText('assunto_oficio')->nullable();
            $table->date('data_emissao')->nullable();
            $table->date('data_recebimento')->nullable();
            $table->date('data_prazo')->nullable();
            $table->string('prazo')->nullable();
            $table->boolean('dias_corridos')->nullable();
            $table->string('numero_notificacao')->nullable();
            $table->longText('observacao')->nullable();
            $table->string('tipo_oficio')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->longText('status_inicial')->nullable();
            $table->longText('status_final')->nullable();
            $table->string('etapa')->nullable();
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->unsignedBigInteger('user_created')->nullable();
            $table->timestamps();
            $table->foreign('destinatario_id')->references('id')->on('destinatarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oficios');
    }
};
