<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('available_for', 20)->default('Both');
        });

        DB::table('services')
            ->where('service_name', 'Drop Off')
            ->update(['available_for' => 'Drop Off']);

        DB::table('services')
            ->whereIn('service_name', ['Self Service', 'Dry', 'Sabon'])
            ->update(['available_for' => 'Self Service']);
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('available_for');
        });
    }
};
