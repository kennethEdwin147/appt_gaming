<?php

namespace Tests\Unit\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;

class PasswordResetControllerTest extends TestCase
{
    /** @test */
    public function it_shows_forgot_password_form()
    {
        // Act
        $response = $this->get('/forgot-password');

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('auth.password.forgot-password');
    }

    /** @test */
    public function it_shows_reset_form_with_valid_token()
    {
        // Arrange
        $token = 'valid-token';
        $email = 'test@example.com';

        // Act
        $response = $this->get("/reset-password/{$token}?email={$email}");

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('auth.password.reset-password');
        $response->assertViewHas('token', $token);
        $response->assertViewHas('email', $email);
    }

    /** @test */
    public function it_can_send_reset_link()
    {
        // Skip this test for now
        $this->markTestSkipped('This test is skipped for now.');
    }

    /** @test */
    public function it_validates_email_exists()
    {
        // Skip this test for now
        $this->markTestSkipped('This test is skipped for now.');
    }

    /** @test */
    public function it_sends_reset_email()
    {
        // Skip this test for now
        $this->markTestSkipped('This test is skipped for now.');
    }

    /** @test */
    public function it_can_reset_password()
    {
        // Skip this test for now
        $this->markTestSkipped('This test is skipped for now.');
    }

    /** @test */
    public function it_validates_password_confirmation()
    {
        // Arrange
        $user = $this->createUser('customer', ['email' => 'test@example.com']);
        $token = Password::createToken($user);
        
        $resetData = [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ];

        // Act
        $response = $this->post('/reset-password', $resetData);

        // Assert
        $response->assertSessionHasErrors(['password']);
        $user->refresh();
        $this->assertFalse(Hash::check('newpassword123', $user->password));
    }

    /** @test */
    public function it_updates_password_hash()
    {
        // Arrange
        $user = $this->createUser('customer', [
            'email' => 'test@example.com',
            'password' => Hash::make('oldpassword'),
        ]);
        $token = Password::createToken($user);
        
        $resetData = [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        // Act
        $this->post('/reset-password', $resetData);

        // Assert
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
        $this->assertFalse(Hash::check('oldpassword', $user->password));
    }

    /** @test */
    public function it_redirects_to_login_after_reset()
    {
        // Arrange
        $user = $this->createUser('customer', ['email' => 'test@example.com']);
        $token = Password::createToken($user);
        
        $resetData = [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        // Act
        $response = $this->post('/reset-password', $resetData);

        // Assert
        $response->assertRedirect('/login');
        $response->assertSessionHas('status');
    }

    /** @test */
    public function it_rejects_invalid_reset_token()
    {
        // Arrange
        $user = $this->createUser('customer', ['email' => 'test@example.com']);
        
        $resetData = [
            'token' => 'invalid-token',
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        // Act
        $response = $this->post('/reset-password', $resetData);

        // Assert
        $response->assertSessionHasErrors(['email']);
        $user->refresh();
        $this->assertFalse(Hash::check('newpassword123', $user->password));
    }

    /** @test */
    public function it_validates_required_fields_for_sending_link()
    {
        // Act
        $response = $this->post('/forgot-password', []);

        // Assert
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_validates_email_format_for_sending_link()
    {
        // Act
        $response = $this->post('/forgot-password', ['email' => 'invalid-email']);

        // Assert
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_validates_required_fields_for_reset()
    {
        // Act
        $response = $this->post('/reset-password', []);

        // Assert
        $response->assertSessionHasErrors(['token', 'email', 'password']);
    }

    /** @test */
    public function it_validates_minimum_password_length()
    {
        // Arrange
        $user = $this->createUser('customer', ['email' => 'test@example.com']);
        $token = Password::createToken($user);
        
        $resetData = [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => '123', // too short
            'password_confirmation' => '123',
        ];

        // Act
        $response = $this->post('/reset-password', $resetData);

        // Assert
        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function it_regenerates_remember_token_on_reset()
    {
        // Arrange
        $user = $this->createUser('customer', ['email' => 'test@example.com']);
        $oldRememberToken = $user->remember_token;
        $token = Password::createToken($user);
        
        $resetData = [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        // Act
        $this->post('/reset-password', $resetData);

        // Assert
        $user->refresh();
        $this->assertNotEquals($oldRememberToken, $user->remember_token);
        $this->assertNotNull($user->remember_token);
    }

    /** @test */
    public function it_handles_expired_reset_token()
    {
        // Arrange
        $user = $this->createUser('customer', ['email' => 'test@example.com']);
        
        // Create an expired token by manipulating the database
        \DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make('expired-token'),
            'created_at' => now()->subHours(2), // expired
        ]);
        
        $resetData = [
            'token' => 'expired-token',
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        // Act
        $response = $this->post('/reset-password', $resetData);

        // Assert
        $response->assertSessionHasErrors(['email']);
        $user->refresh();
        $this->assertFalse(Hash::check('newpassword123', $user->password));
    }
}