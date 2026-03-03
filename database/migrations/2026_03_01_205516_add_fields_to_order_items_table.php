<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Variante seleccionada (nullable porque no todos los productos tienen variantes)
            $table->foreignId('variant_id')
                  ->nullable()
                  ->after('product_id')
                  ->constrained('product_variants')
                  ->nullOnDelete();

            // Snapshot del producto al momento de la compra
            $table->string('product_name')->after('variant_id');      // nombre en el momento
            $table->string('product_code')->nullable()->after('product_name');
            $table->string('product_sku')->nullable()->after('product_code');
            $table->json('variant_attributes')->nullable()->after('product_sku'); // {"Color":"Rojo","Talla":"L"}
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->dropColumn(['variant_id', 'product_name', 'product_code', 'product_sku', 'variant_attributes']);
        });
    }
};