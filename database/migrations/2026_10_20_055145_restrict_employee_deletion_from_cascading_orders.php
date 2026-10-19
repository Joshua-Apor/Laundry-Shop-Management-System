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
        $employeeForeignKey = collect(Schema::getForeignKeys('laundry_orders'))
            ->first(fn (array $foreignKey): bool => in_array('user_id', $foreignKey['columns'], true));

        if ($employeeForeignKey !== null && (
            $employeeForeignKey['columns'] !== ['user_id']
            || $employeeForeignKey['foreign_table'] !== 'users'
            || $employeeForeignKey['foreign_columns'] !== ['user_id']
        )) {
            throw new RuntimeException('The laundry_orders.user_id foreign key has an unexpected definition.');
        }

        if (DB::table('laundry_orders')
            ->leftJoin('users', 'users.user_id', '=', 'laundry_orders.user_id')
            ->whereNull('users.user_id')
            ->exists()) {
            throw new RuntimeException('Cannot add the employee foreign key while orders reference missing users. Existing order data was left unchanged.');
        }

        Schema::table('laundry_orders', function (Blueprint $table) use ($employeeForeignKey): void {
            if ($employeeForeignKey !== null) {
                $table->dropForeign($employeeForeignKey['name'] ?? ['user_id']);
            }

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
        $employeeForeignKey = collect(Schema::getForeignKeys('laundry_orders'))
            ->first(fn (array $foreignKey): bool => $foreignKey['columns'] === ['user_id']);

        if ($employeeForeignKey === null) {
            return;
        }

        Schema::table('laundry_orders', function (Blueprint $table) use ($employeeForeignKey): void {
            $table->dropForeign($employeeForeignKey['name'] ?? ['user_id']);
        });
    }
};
