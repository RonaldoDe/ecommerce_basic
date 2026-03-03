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
        Schema::table('products', function (Blueprint $table) {
            // URLs y SEO
            $table->string('slug')->unique()->after('name');
            $table->string('meta_title')->nullable()->after('slug');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            
            // // Imágenes
            // $table->string('main_image')->nullable()->after('code');
            // $table->json('images')->nullable()->after('main_image');
            
            // SKU y gestión de inventario
            $table->string('sku')->unique()->nullable()->after('code');
            $table->integer('stock_alert')->default(10)->after('stock');
            $table->boolean('manage_stock')->default(true)->after('stock_alert');
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'on_backorder'])->default('in_stock')->after('manage_stock');
            
            // Precios y descuentos
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('selling_price');
            $table->decimal('discount_price', 10, 2)->nullable()->after('discount_percentage');
            $table->timestamp('discount_start_date')->nullable()->after('discount_price');
            $table->timestamp('discount_end_date')->nullable()->after('discount_start_date');
            
            // Dimensiones y peso
            $table->decimal('weight', 8, 2)->nullable()->after('discount_end_date')->comment('Peso en kg');
            $table->json('dimensions')->nullable()->after('weight')->comment('JSON: {length, width, height} en cm');
            
            // Estado y visibilidad
            $table->boolean('status')->default(true)->after('dimensions');
            $table->boolean('featured')->default(false)->after('status');
            $table->boolean('is_new')->default(false)->after('featured');
            $table->enum('visibility', ['public', 'private', 'catalog'])->default('public')->after('is_new');
            
            // Ratings y popularidad
            $table->decimal('rating', 3, 2)->default(0)->after('visibility')->comment('Promedio de 0 a 5');
            $table->unsignedInteger('reviews_count')->default(0)->after('rating');
            $table->unsignedInteger('views_count')->default(0)->after('reviews_count');
            $table->unsignedInteger('sales_count')->default(0)->after('views_count');
            $table->unsignedInteger('wishlist_count')->default(0)->after('sales_count');
            
            // Marca
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained('brands')->nullOnDelete();
            
            // Variantes
            $table->boolean('has_variants')->default(false)->after('brand_id');
            $table->foreignId('parent_id')->nullable()->after('has_variants')->constrained('products')->nullOnDelete();
            
            // Información adicional
            $table->text('warranty')->nullable()->after('long_description');
            $table->text('return_policy')->nullable()->after('warranty');
            $table->text('shipping_info')->nullable()->after('return_policy');
            $table->json('specifications')->nullable()->after('shipping_info')->comment('JSON: especificaciones técnicas');
            
            // Tags y búsqueda
            $table->text('tags')->nullable()->after('specifications')->comment('Tags separados por comas');
            $table->text('search_keywords')->nullable()->after('tags');
            
            // Fecha de publicación
            $table->timestamp('published_at')->nullable()->after('search_keywords');
            
            // Soft deletes
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Eliminar índices y foreign keys primero
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['parent_id']);
            
            // Eliminar columnas
            $table->dropColumn([
                'slug',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'sku',
                'stock_alert',
                'manage_stock',
                'stock_status',
                'discount_percentage',
                'discount_price',
                'discount_start_date',
                'discount_end_date',
                'weight',
                'dimensions',
                'status',
                'featured',
                'is_new',
                'visibility',
                'rating',
                'reviews_count',
                'views_count',
                'sales_count',
                'wishlist_count',
                'brand_id',
                'has_variants',
                'parent_id',
                'warranty',
                'return_policy',
                'shipping_info',
                'specifications',
                'tags',
                'search_keywords',
                'published_at',
                'deleted_at',
            ]);
        });
    }
};
