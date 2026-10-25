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
        $foreignKey = collect(Schema::getForeignKeys('laundry_orders'))
            ->first(fn (array $key): bool => in_array('customer_id', $key['columns'], true));

        if ($foreignKey !== null && (
            $foreignKey['columns'] !== ['customer_id']
            || $foreignKey['foreign_table'] !== 'customers'
            || $foreignKey['foreign_columns'] !== ['customer_id']
        )) {
            throw new RuntimeException('The laundry_orders.customer_id foreign key has an unexpected definition.');
        }

        if (DB::table('laundry_orders')->leftJoin('customers', 'customers.customer_id', '=', 'laundry_orders.customer_id')->whereNull('customers.customer_id')->exists()) {
            throw new RuntimeException('Cannot restrict customer deletion while orders reference missing customers. Existing order data was left unchanged.');
        }

        Schema::table('laundry_orders', function (Blueprint $table) use ($foreignKey): void {
            if ($foreignKey !== null) {
                $table->dropForeign($foreignKey['name'] ?? ['customer_id']);
            }

            $table->foreign('customer_id')->references('customer_id')->on('customers')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $foreignKey = collect(Schema::getForeignKeys('laundry_orders'))
            ->first(fn (array $key): bool => $key['columns'] === ['customer_id']);

        if ($foreignKey === null) {
            return;
        }

        Schema::table('laundry_orders', function (Blueprint $table) use ($foreignKey): void {
            $table->dropForeign($foreignKey['name'] ?? ['customer_id']);
            $table->foreign('customer_id')->references('customer_id')->on('customers')->cascadeOnDelete();
        });
    }
};
