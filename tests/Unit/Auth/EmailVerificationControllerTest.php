<?php

namespace Tests\Unit\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;

class EmailVerificationControllerTest extends TestCase
{
    /** @test */
    public function it_shows_verification_notice()
    {
        // Arrange
        $user = $this->createUser('customer', ['email_verified_at' => null]);
        $this->actingAs($user);

        // Act
        $response = $this->get('/email/verify');

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');
    }

    /** @test */
    public function it_redirects_verified_users_from_notice()
    {
        // Arrange
        $user = $this->createUser('customer', ['email_verified_at' => now()]);
        $this->actingAs($user);

        // Act
        $response = $this->get('/email/verify');

        // Assert
        $response->assertRedirect('/customer/dashboard');
    }

    /** @test */
    public function it_can_verify_email()
    {
        // Arrange
        $user = $this->createUser('customer', ['email_verified_at' => null]);
        $this->actingAs($user);
        
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Act
        $response = $this->get($verificationUrl);

        // Assert
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        Event::assertDispatched(Verified::class);
        $response->assertRedirect('/customer/dashboard');
    }

    /** @test */
    public function it_rejects_invalid_verification_token()
    {
        // Arrange
        $user = $this->createUser('customer', ['email_verified_at' => null]);
        $this->actingAs($user);
        
        $invalidUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'invalid-hash']
        );

        // Act
        $response = $this->get($invalidUrl);

        // Assert
        $response->assertStatus(403);
        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    /** @test */
    public function it_marks_email_as_verified()
    {
        // Arrange
        $user = $this->createUser('customer', ['email_verified_at' => null]);
        $this->actingAs($user);
        
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Act
        $this->get($verificationUrl);

        // Assert
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email_verified_at' => $user->fresh()->email_verified_at,
        ]);
    }

    /** @test */
    public function it_can_resend_verification_email()
    {
        // Arrange
        $user = $this->createUser('customer', ['email_verified_at' => null]);
        $this->actingAs($user);

        // Act
        $response = $this->post('/email/verification-notification');

        // Assert
        Mail::assertSent(VerifyEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
        $response->assertRedirect();
        $response->assertSessionHas('message', 'Le lien de vérification a été renvoyé !');
    }

    /** @test */
    public function it_redirects_creator_to_setup_after_verification()
    {
        // Arrange
        $creator = $this->actingAsCreator(['timezone' => null, 'bio' => null]);
        $creator->user->update(['email_verified_at' => null]);
        
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $creator->user->id, 'hash' => sha1($creator->user->email)]
        );

        // Act
        $response = $this->get($verificationUrl);

        // Assert
        $response->assertRedirect('/creator/setup/timezone');
        $response->assertSessionHas('success', 'Email vérifié ! Terminons la configuration.');
    }

    /** @test */
    public function it_redirects_creator_to_dashboard_if_setup_complete()
    {
        // Arrange
        $creator = $this->actingAsCreator([
            'timezone' => 'America/Toronto',
            'bio' => 'Complete setup',
        ]);
        $creator->user->update(['email_verified_at' => null]);
        
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $creator->user->id, 'hash' => sha1($creator->user->email)]
        );

        // Act
        $response = $this->get($verificationUrl);

        // Assert
        $response->assertRedirect('/creator/dashboard');
    }

    /** @test */
    public function it_redirects_customer_to_dashboard_after_verification()
    {
        // Arrange
        $customer = $this->actingAsCustomer();
        $customer->user->update(['email_verified_at' => null]);
        
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $customer->user->id, 'hash' => sha1($customer->user->email)]
        );

        // Act
        $response = $this->get($verificationUrl);

        // Assert
        $response->assertRedirect('/customer/dashboard');
        $response->assertSessionHas('success', 'Email vérifié avec succès !');
    }

    /** @test */
    public function it_does_not_verify_already_verified_email()
    {
        // Arrange
        $user = $this->createUser('customer', ['email_verified_at' => now()]);
        $this->actingAs($user);
        
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Act
        $response = $this->get($verificationUrl);

        // Assert
        $response->assertRedirect('/customer/dashboard');
        Event::assertNotDispatched(Verified::class);
    }

    /** @test */
    public function it_requires_authentication_for_verification()
    {
        // Arrange
        $user = $this->createUser('customer', ['email_verified_at' => null]);
        
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Act
        $response = $this->get($verificationUrl);

        // Assert
        $response->assertRedirect('/login');
    }

    /** @test */
    public function it_requires_authentication_for_resend()
    {
        // Act
        $response = $this->post('/email/verification-notification');

        // Assert
        $response->assertRedirect('/login');
    }

    /** @test */
    public function it_redirects_verified_users_from_resend()
    {
        // Arrange
        $user = $this->createUser('customer', ['email_verified_at' => now()]);
        $this->actingAs($user);

        // Act
        $response = $this->post('/email/verification-notification');

        // Assert
        $response->assertRedirect('/customer/dashboard');
        Mail::assertNotSent(VerifyEmail::class);
    }

    /** @test */
    public function it_handles_expired_verification_links()
    {
        // Arrange
        $user = $this->createUser('customer', ['email_verified_at' => null]);
        $this->actingAs($user);
        
        $expiredUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinutes(60), // expired
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Act
        $response = $this->get($expiredUrl);

        // Assert
        $response->assertStatus(403);
        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }
}