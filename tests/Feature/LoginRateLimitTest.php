<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('limits repeated login attempts from the same client', function () {
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post(route('login.store'), [
            'identifier' => 'missing.user',
            'password' => 'invalid-password',
            'role' => 'employee',
        ])->assertRedirect();
    }

    $this->post(route('login.store'), [
        'identifier' => 'missing.user',
        'password' => 'invalid-password',
        'role' => 'employee',
    ])->assertStatus(429);
});
