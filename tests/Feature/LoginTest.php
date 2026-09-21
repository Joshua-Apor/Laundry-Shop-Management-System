<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the login page is available', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Welcome back')
        ->assertSee('Demo credentials');
});

test('a staff member can sign in with a username and role', function () {
    User::factory()->create([
        'username' => 'josh',
        'password' => 'employee123',
        'role' => 'employee',
    ]);

    $response = $this->post(route('login.store'), [
        'username' => 'josh',
        'password' => 'employee123',
        'role' => 'employee',
    ]);

    $response->assertRedirectToRoute('employee.dashboard');
    $this->assertAuthenticated();
});

test('the selected role must match the user role', function () {
    User::factory()->create([
        'username' => 'josh',
        'password' => 'employee123',
        'role' => 'employee',
    ]);

    $this->from(route('login'))
        ->post(route('login.store'), [
            'username' => 'josh',
            'password' => 'employee123',
            'role' => 'manager',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('username');

    $this->assertGuest();
});

test('an employee cannot open the dashboard as a guest', function () {
    $this->get(route('employee.dashboard'))
        ->assertRedirect(route('login'));
});
