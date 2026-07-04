<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_returns_200_and_sends_notification()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'usuario@exemplo.com',
        ]);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'usuario@exemplo.com'
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Se o e-mail existir em nossa base de dados, um link de recuperação foi enviado.');

        // Assert notification was sent
        Notification::assertSentTo(
            $user,
            \App\Notifications\CustomResetPassword::class
        );
    }

    public function test_forgot_password_returns_200_even_if_email_does_not_exist_preventing_enumeration()
    {
        Notification::fake();

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'naoexiste@exemplo.com'
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Se o e-mail existir em nossa base de dados, um link de recuperação foi enviado.');

        Notification::assertNothingSent();
    }

    public function test_forgot_password_is_rate_limited()
    {
        // Execute the request multiple times to trigger rate limiting
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/auth/forgot-password', ['email' => 'test@exemplo.com']);
        }

        // The 4th request should be rate limited
        $response = $this->postJson('/api/auth/forgot-password', ['email' => 'test@exemplo.com']);

        $response->assertStatus(429); // Too Many Requests
    }

    public function test_reset_password_resets_password_and_invalidates_token()
    {
        $user = User::factory()->create([
            'email' => 'reset@exemplo.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $token = 'valid-token';
        
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Senha redefinida com sucesso.');

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_reset_password_fails_if_token_is_invalid()
    {
        $user = User::factory()->create([
            'email' => 'reset@exemplo.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'token' => 'invalid-token',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertStatus(400); // Bad Request (or 422 depending on implementation)
    }

    public function test_reset_password_fails_if_password_is_too_weak()
    {
        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'reset@exemplo.com',
            'token' => 'some-token',
            'password' => 'weak',
            'password_confirmation' => 'weak'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }
}
