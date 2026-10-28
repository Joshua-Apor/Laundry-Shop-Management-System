<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $manager = DB::table('users')
            ->where('username', 'manager')
            ->where('email', 'demo.manager@example.com')
            ->where('role', 'manager')
            ->first(['user_id', 'password']);

        if ($manager !== null && Hash::check('manager123', $manager->password)) {
            DB::table('users')
                ->where('user_id', $manager->user_id)
                ->update(['must_change_password' => true]);
        }
    }

    public function down(): void
    {
        $manager = DB::table('users')
            ->where('username', 'manager')
            ->where('email', 'demo.manager@example.com')
            ->where('role', 'manager')
            ->first(['user_id', 'password']);

        if ($manager !== null && Hash::check('manager123', $manager->password)) {
            DB::table('users')
                ->where('user_id', $manager->user_id)
                ->update(['must_change_password' => false]);
        }
    }
};
