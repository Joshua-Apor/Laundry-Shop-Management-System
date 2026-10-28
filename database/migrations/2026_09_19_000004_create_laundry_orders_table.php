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
        Schema::create('laundry_orders', function (Blueprint $table) {
            $table->bigIncrements('order_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('user_id');
            $table->date('order_date');
            $table->time('order_time')->nullable();
            $table->decimal('laundry_weight', 8, 2)->nullable();
            $table->string('order_type')->default('Drop Off');
            $table->unsignedInteger('self_service_loads')->nullable();
            $table->string('employee_name')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('order_status');
            $table->date('pickup_date')->nullable();
            $table->timestamp('ready_for_pickup_at')->nullable();
            $table->decimal('storage_fee', 10, 2)->default(0);

            $table->index(['order_date', 'order_time', 'order_id'], 'laundry_orders_date_time_id_index');
            $table->index(['order_status', 'order_date', 'order_time', 'order_id'], 'laundry_orders_status_date_time_id_index');
            $table->index(['customer_id', 'order_date', 'order_time', 'order_id'], 'laundry_orders_customer_date_time_id_index');

            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->restrictOnDelete();

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laundry_orders');
    }
};
