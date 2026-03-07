<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('best_seller')
                  ->nullable()
                  ->unsigned()
                  ->unique() // Solo un producto por posición
                  ->after('featured')
                  ->comment('1 = featured card, 2 = mini card izquierda, 3 = mini card derecha');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('best_seller');
        });
    }
};