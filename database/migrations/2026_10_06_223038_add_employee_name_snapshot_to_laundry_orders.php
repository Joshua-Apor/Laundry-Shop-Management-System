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
        DB::table('laundry_orders')
            ->join('users', 'users.user_id', '=', 'laundry_orders.user_id')
            ->select(['laundry_orders.order_id', 'users.name as recorded_employee_name'])
            ->orderBy('laundry_orders.order_id')
            ->chunkById(500, function ($orders): void {
                foreach ($orders as $order) {
                    DB::table('laundry_orders')
                        ->where('order_id', $order->order_id)
                        ->update(['employee_name' => $order->recorded_employee_name]);
                }
            }, 'laundry_orders.order_id', 'order_id');

        Schema::table('laundry_orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('laundry_orders')
            ->leftJoin('users', 'users.user_id', '=', 'laundry_orders.user_id')
            ->whereNull('users.user_id')
            ->exists()) {
            throw new RuntimeException('Cannot restore the employee foreign key while deleted employees still have historical orders.');
        }

        Schema::table('laundry_orders', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }
};
