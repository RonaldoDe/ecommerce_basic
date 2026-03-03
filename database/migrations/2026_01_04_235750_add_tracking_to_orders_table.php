<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Para tracking de envíos
            $table->string('tracking_number')->nullable()->after('transaction_id');
            $table->string('shipping_company')->nullable()->after('tracking_number');
            
            // Para descuentos y cupones
            $table->string('coupon_code')->nullable()->after('total');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('coupon_code');
            $table->decimal('subtotal', 10, 2)->after('discount_amount');
            
            // Para notas internas
            $table->text('admin_notes')->nullable()->after('address');
            
            // Para notas del cliente
            $table->text('customer_notes')->nullable()->after('admin_notes');

            // Índices para mejorar rendimiento
            $table->index('user_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'tracking_number',
                'shipping_company',
                'coupon_code',
                'discount_amount',
                'subtotal',
                'admin_notes',
                'customer_notes'
            ]);
            $table->dropIndex('orders_user_id_index');
            $table->dropIndex('orders_status_index');
            $table->dropIndex('orders_payment_status_index');
            $table->dropIndex('orders_created_at_index');
        });
    }
};