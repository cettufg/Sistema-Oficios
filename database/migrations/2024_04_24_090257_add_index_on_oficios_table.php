<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->index('destinatario_id');
            $table->index('numero_oficio');
            $table->index('prazo');
            $table->index('tipo_oficio');
            $table->index('tipo_documento');
            $table->index('etapa');
            $table->index('data_recebimento');
            $table->index('data_emissao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->dropIndex(['destinatario_id']);
            $table->dropIndex(['numero_oficio']);
            $table->dropIndex(['prazo']);
            $table->dropIndex(['tipo_oficio']);
            $table->dropIndex(['tipo_documento']);
            $table->dropIndex(['etapa']);
            $table->dropIndex(['data_recebimento']);
            $table->dropIndex(['data_emissao']);
        });
    }
};
