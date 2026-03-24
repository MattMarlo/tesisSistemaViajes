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
        Schema::create('grupos_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos')->restrictOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOndelete();
            $table->decimal('monto_asignado',10,2)->default(0.00);
            $table->boolean('es_lider')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos_clientes');
    }
};
