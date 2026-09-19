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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('fullname');
            $table->string('phoneNumber', 11);
            $table->json('service');
            $table->decimal('weight', 8, 2);
            $table->text('specialRequest')->nullable();
            $table->string('paymentMethod')->default('Cash');
            $table->decimal('amount_paid', 8, 2)->default(0.00);
            $table->decimal('total_amount', 8, 2)->default(0.00);
            $table->string('status')->default('Received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
