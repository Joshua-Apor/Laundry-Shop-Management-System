<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('services')
            ->where('service_name', 'Wash & Dry')
            ->update(['price_unit' => '/kilo']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The column is defined by the services table creation migration.
    }
};
