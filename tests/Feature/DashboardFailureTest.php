<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

it('returns service unavailable when dashboard data queries fail', function () {
    DB::shouldReceive('table')->once()->andThrow(new RuntimeException('Database unavailable.'));
    Log::shouldReceive('error')->once();

    $user = User::factory()->make(['role' => 'employee', 'account_status' => true]);

    $this->actingAs($user)
        ->get(route('dashboard.data'))
        ->assertStatus(503);
});
