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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            
            // Contenido de la reseña
            $table->string('title')->nullable();
            $table->text('comment');
            $table->tinyInteger('rating')->unsigned()->comment('Calificación de 1 a 5');
            
            // Estado y moderación
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable()->comment('Nota interna del administrador');
            
            // Verificación
            $table->boolean('verified_purchase')->default(false)->comment('Si el usuario compró el producto');
            
            // Interacciones
            $table->integer('helpful_count')->default(0)->comment('Número de personas que encontraron útil esta reseña');
            $table->integer('not_helpful_count')->default(0)->comment('Número de personas que no encontraron útil');
            
            // Respuesta del vendedor/admin
            $table->text('seller_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['product_id', 'status']);
            $table->index(['user_id', 'product_id']);
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
