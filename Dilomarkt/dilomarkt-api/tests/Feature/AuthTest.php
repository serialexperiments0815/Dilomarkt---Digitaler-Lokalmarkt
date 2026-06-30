<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_verify_account(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ada@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'buyer',
        ]);

        $response->assertOk();
        $response->assertJsonPath('status', 'verification_sent');

        $user = User::where('email', 'ada@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->verification_code);

        $verifyResponse = $this->postJson('/api/auth/verify', [
            'email' => 'ada@example.com',
            'code' => $user->verification_code,
        ]);

        $verifyResponse->assertOk();
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
