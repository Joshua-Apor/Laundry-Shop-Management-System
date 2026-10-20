<?php

use App\Mail\EmployeeWelcome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('sends the new employee a welcome email with an alphanumeric temporary password', function () {
    Mail::fake();
    $manager = User::factory()->create(['role' => 'manager']);
    $email = 'new.employee@example.test';

    $this->actingAs($manager)
        ->post(route('manager.employees.store'), [
            'name' => 'New Employee',
            'username' => 'new.employee',
            'email' => $email,
        ])
        ->assertRedirect(route('manager.employees'));

    Mail::assertSent(EmployeeWelcome::class, function (EmployeeWelcome $mail) use ($email): bool {
        return $mail->hasTo($email) && preg_match('/\A[A-Za-z0-9]{8}\z/', $mail->password) === 1;
    });
});

it('keeps the employee account when sending the welcome email fails', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $email = 'mail.failure@example.test';
    $pendingMail = new class
    {
        public function send(Mailable $mailable): void
        {
            throw new RuntimeException('SMTP connection failed.');
        }
    };

    Mail::shouldReceive('to')
        ->once()
        ->with($email)
        ->andReturn($pendingMail);

    $this->actingAs($manager)
        ->from(route('manager.employees'))
        ->post(route('manager.employees.store'), [
            'name' => 'Email Failure Employee',
            'username' => 'email.failure.employee',
            'email' => $email,
        ])
        ->assertRedirect(route('manager.employees'))
        ->assertSessionHasErrors('email');

    $this->assertDatabaseHas('users', [
        'name' => 'Email Failure Employee',
        'username' => 'email.failure.employee',
        'email' => $email,
        'role' => 'employee',
        'deleted_at' => null,
    ]);
});
