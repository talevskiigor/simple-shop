<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_admin_registration_is_unavailable(): void
    {
        $this->get('/admin/register')->assertNotFound();
        $this->post('/admin/register', [
            'name' => 'Uninvited user', 'email' => 'uninvited@example.test',
            'password' => 'password', 'password_confirmation' => 'password',
        ])->assertNotFound();
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }
}
