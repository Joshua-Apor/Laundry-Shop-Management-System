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
        Schema::table('laundry_orders', function (Blueprint $table): void {
            $table->index(['order_date', 'order_time', 'order_id'], 'laundry_orders_date_time_id_index');
            $table->index(['order_status', 'order_date', 'order_time', 'order_id'], 'laundry_orders_status_date_time_id_index');
            $table->index(['customer_id', 'order_date', 'order_time', 'order_id'], 'laundry_orders_customer_date_time_id_index');
        });

        Schema::table('payments', function (Blueprint $table): void {
            $table->index(['order_id', 'payment_date', 'payment_id'], 'payments_order_date_id_index');
        });

        Schema::table('order_services', function (Blueprint $table): void {
            $table->index(['order_id', 'service_id'], 'order_services_order_service_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_services', function (Blueprint $table): void {
            $table->dropIndex('order_services_order_service_index');
        });

        Schema::table('payments', function (Blueprint $table): void {
            $table->dropIndex('payments_order_date_id_index');
        });

        Schema::table('laundry_orders', function (Blueprint $table): void {
            $table->dropIndex('laundry_orders_date_time_id_index');
            $table->dropIndex('laundry_orders_status_date_time_id_index');
            $table->dropIndex('laundry_orders_customer_date_time_id_index');
        });
    }
};
