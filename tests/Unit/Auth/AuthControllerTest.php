<?php

namespace Tests\Unit\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\creator\Creator;
use App\Models\customer\Customer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

class AuthControllerTest extends TestCase
{
    /** @test */
    public function it_can_register_a_client()
    {
        // Arrange
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Act
        $response = $this->post('/register/client', $data);

        // Assert
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role' => 'customer',
        ]);
        $this->assertDatabaseHas('customers', [
            'user_id' => User::where('email', 'john@example.com')->first()->id,
        ]);
        $user = User::where('email', 'john@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);
        $response->assertRedirect('/email/verify');
    }

    /** @test */
    public function it_can_register_a_creator()
    {
        // Arrange
        $data = $this->getGamingCreatorData();

        // Act
        $response = $this->post('/register/creator', $data);

        // Assert
        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
            'role' => 'creator',
        ]);
        $this->assertDatabaseHas('creators', [
            'gaming_pseudo' => $data['gaming_pseudo'],
        ]);
        $user = User::where('email', $data['email'])->first();
        Notification::assertSentTo($user, VerifyEmail::class);
        $response->assertRedirect('/email/verify');
    }

    /** @test */
    public function it_validates_required_fields_on_registration()
    {
        // Act
        $response = $this->post('/register/client', []);

        // Assert
        $response->assertSessionHasErrors(['first_name', 'last_name', 'email', 'password']);
    }

    /** @test */
    public function it_prevents_duplicate_emails()
    {
        // Arrange
        $this->createUser('customer', ['email' => 'test@example.com']);
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Act
        $response = $this->post('/register/client', $data);

        // Assert
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_prevents_duplicate_gaming_pseudo()
    {
        // Arrange
        $this->createCreator(['gaming_pseudo' => 'ExistingGamer']);
        $data = $this->getGamingCreatorData();
        $data['gaming_pseudo'] = 'ExistingGamer';

        // Act
        $response = $this->post('/register/creator', $data);

        // Assert
        $response->assertSessionHasErrors(['gaming_pseudo']);
    }

    /** @test */
    public function it_sends_verification_email_on_registration()
    {
        // Arrange
        $data = $this->getGamingCreatorData();

        // Act
        $this->post('/register/creator', $data);

        // Assert
        $user = User::where('email', $data['email'])->first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /** @test */
    public function it_can_login_with_valid_credentials()
    {
        // Arrange
        $user = $this->createUser('customer', [
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
        $user->markEmailAsVerified(); // Explicit verification

        // Act
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $this->assertAuthenticated();
        $this->assertEquals($user->id, auth()->id());
        $response->assertRedirect('/customer/dashboard');
    }

    /** @test */
    public function it_rejects_invalid_credentials()
    {
        // Arrange
        $this->createUser('customer', [
            'email' => 'test@example.com',
            'password' => Hash::make('correctpassword'),
        ]);

        // Act
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        // Assert
        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_redirects_creator_to_setup_after_login()
    {
        // Arrange
        $creator = $this->createCreator([
            'timezone' => null, 
            'bio' => null
        ]);
        $creator->user->markEmailAsVerified();

        // Act
        $response = $this->post('/login', [
            'email' => $creator->user->email,
            'password' => 'password123',
        ]);

        // Assert
        $this->assertAuthenticated();
        $response->assertRedirect('/creator/setup/timezone');
    }

    /** @test */
    public function it_redirects_creator_to_dashboard_when_setup_complete()
    {
        // Arrange
        $creator = $this->createCreator([
            'timezone' => 'America/Toronto',
            'bio' => 'Complete setup'
        ]);
        $creator->user->markEmailAsVerified();

        // Act
        $response = $this->post('/login', [
            'email' => $creator->user->email,
            'password' => 'password123',
        ]);

        // Assert
        $this->assertAuthenticated();
        $response->assertRedirect('/creator/dashboard');
    }

    /** @test */
    public function it_redirects_customer_to_dashboard_after_login()
    {
        // Arrange
        $customer = $this->createCustomer();
        $customer->user->markEmailAsVerified();

        // Act
        $response = $this->post('/login', [
            'email' => $customer->user->email,
            'password' => 'password123',
        ]);

        // Assert
        $this->assertAuthenticated();
        $response->assertRedirect('/customer/dashboard');
    }

    /** @test */
    public function it_redirects_to_verification_if_email_not_verified()
    {
        // Arrange
        $user = $this->createUser('customer', [
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
        ]);

        // Act
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $this->assertAuthenticated();
        $response->assertRedirect('/email/verify');
    }

    /** @test */
    public function it_can_logout()
    {
        // Arrange
        $user = $this->createUser();
        $this->actingAs($user);

        // Act
        $response = $this->post('/logout');

        // Assert
        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /** @test */
    public function it_clears_session_on_logout()
    {
        // Arrange
        $user = $this->createUser();
        $this->actingAs($user);
        session(['test_key' => 'test_value']);

        // Act
        $response = $this->post('/logout');

        // Assert
        $this->assertGuest();
        $this->assertNull(session('test_key'));
        $response->assertRedirect('/');
    }
}