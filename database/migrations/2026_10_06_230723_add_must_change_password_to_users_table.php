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
        DB::table('users')
            ->where('role', 'employee')
            ->update(['must_change_password' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The column is defined by the users table creation migration.
    }
};
