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
        Schema::create('claim_receipts', function (Blueprint $table) {
            $table->bigIncrements('receipt_id');
            $table->unsignedBigInteger('order_id');
            $table->string('receipt_number');
            $table->date('issue_date');

            $table->foreign('order_id')
                ->references('order_id')
                ->on('laundry_orders')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_receipts');
    }
};
