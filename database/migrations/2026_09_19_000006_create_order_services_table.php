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
        Schema::create('order_services', function (Blueprint $table) {
            $table->bigIncrements('order_service_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('service_id');
            $table->integer('quantity');
            $table->decimal('service_fee', 10, 2);

            $table->foreign('order_id')
                ->references('order_id')
                ->on('laundry_orders')
                ->cascadeOnDelete();

            $table->foreign('service_id')
                ->references('service_id')
                ->on('services')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_services');
    }
};
