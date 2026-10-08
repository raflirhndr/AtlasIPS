<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_is_rate_limited_after_repeated_attempts_and_refresh_does_not_reset_limit(): void
    {
        User::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.test',
            'role' => 'admin',
            'password' => 'correct-password',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'admin@example.test',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->get('/login')->assertOk();

        $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'wrong-password',
        ])->assertStatus(429);
    }

    public function test_successful_admin_login_clears_rate_limit(): void
    {
        User::create([
            'name' => 'Admin Test',
            'email' => 'admin-success@example.test',
            'role' => 'admin',
            'password' => 'correct-password',
        ]);

        for ($i = 0; $i < 4; $i++) {
            $this->post('/login', [
                'email' => 'admin-success@example.test',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post('/login', [
            'email' => 'admin-success@example.test',
            'password' => 'correct-password',
        ])->assertRedirect(route('admin.index'));

        $this->assertAuthenticated();
    }
}
