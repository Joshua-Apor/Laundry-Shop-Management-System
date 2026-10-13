<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laundry_orders', function (Blueprint $table): void {
            $table->timestamp('ready_for_pickup_at')->nullable();
            $table->decimal('storage_fee', 10, 2)->default(0);
        });

        DB::table('laundry_orders')
            ->where('order_status', 'Ready for Pickup')
            ->update(['ready_for_pickup_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laundry_orders', function (Blueprint $table): void {
            $table->dropColumn(['ready_for_pickup_at', 'storage_fee']);
        });
    }
};
