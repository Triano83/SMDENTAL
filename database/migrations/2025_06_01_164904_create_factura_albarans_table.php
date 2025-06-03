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
        Schema::create('factura_albarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')
                ->constrained('facturas')
                ->onDelete('cascade');
            $table->foreignId('albaran_id')
                ->constrained('albarans')
                ->onDelete('cascade');
            $table->decimal('importe', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura_albarans');
    }
};
