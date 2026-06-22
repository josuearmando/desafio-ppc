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
        Schema::create('propostas', function (Blueprint $table) {
            $table->id();

            // chaves estrangeiras
            $table->foreignId('unidade_academica_id')->constrained('unidades_academicas')->onDelete('cascade');
            $table->foreignId('avaliador_id')->nullable()->constrained('avaliadores')->onDelete('set null');
            
            // dados da proposta
            $table->string('nome');
            $table->integer('carga_horaria_total');
            $table->integer('quantidade_semestres');
            $table->text('justificativa');
            $table->text('impacto_social');

            // status da proposta
            $table->string('status')->default('em_analise');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propostas');
    }
};
