<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::create([
            'name' => 'Siswa Test',
            'email' => 'siswa-test@example.test',
            'password' => 'TestPassword123!',
            'role' => 'siswa',
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-test@example.test',
            'password' => 'StrongTestPassword123!',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
    }
}
