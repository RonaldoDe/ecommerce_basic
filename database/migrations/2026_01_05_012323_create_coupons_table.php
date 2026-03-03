<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed'])->default('fixed');
            $table->decimal('value', 10, 2); // Porcentaje o monto fijo
            $table->decimal('min_purchase', 10, 2)->nullable(); // Compra mínima requerida
            $table->decimal('max_discount', 10, 2)->nullable(); // Descuento máximo (para porcentajes)
            $table->integer('usage_limit')->nullable(); // Límite de usos totales
            $table->integer('usage_count')->default(0); // Contador de usos
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('is_active');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};