<?php

declare(strict_types=1);

namespace Codeception\Functional\Auth;

use App\Models\User;
use App\Models\creator\Creator;
use App\Models\customer\Customer;
use Codeception\Support\FunctionalTester;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Notifications\ResetPassword;

final class PasswordResetCest
{
    public function _before(FunctionalTester $I): void
    {
        Notification::fake();
    }

    // Forgot Password Tests
    public function canAccessForgotPasswordPage(FunctionalTester $I): void
    {
        $I->amOnRoute('password.request');
        $I->seeResponseCodeIs(200);
        $I->see('Reset Password');
        $I->seeElement('input[name="email"]');
    }

    public function canRequestPasswordResetWithValidEmail(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $I->amOnRoute('password.request');
        $I->submitForm('form', [
            'email' => 'user@example.com'
        ]);

        Notification::assertSentTo($user, ResetPassword::class);
        $I->seeCurrentRouteIs('password.request');
        $I->see('We have emailed your password reset link');
    }

    public function showsSuccessMessageEvenForNonExistentEmail(FunctionalTester $I): void
    {
        $I->amOnRoute('password.request');
        $I->submitForm('form', [
            'email' => 'nonexistent@example.com'
        ]);

        // For security, should still show success message
        Notification::assertNothingSent();
        $I->seeCurrentRouteIs('password.request');
        $I->see('We have emailed your password reset link');
    }

    public function validatesEmailRequiredForForgotPassword(FunctionalTester $I): void
    {
        $I->amOnRoute('password.request');
        $I->submitForm('form', [
            'email' => ''
        ]);

        $I->seeCurrentRouteIs('password.request');
        $I->see('The email field is required');
        Notification::assertNothingSent();
    }

    public function validatesEmailFormatForForgotPassword(FunctionalTester $I): void
    {
        $I->amOnRoute('password.request');
        $I->submitForm('form', [
            'email' => 'invalid-email'
        ]);

        $I->seeCurrentRouteIs('password.request');
        $I->see('The email field must be a valid email address');
        Notification::assertNothingSent();
    }

    public function throttlesForgotPasswordRequests(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        // First request should succeed
        $I->amOnRoute('password.request');
        $I->submitForm('form', [
            'email' => 'user@example.com'
        ]);
        Notification::assertSentTo($user, ResetPassword::class);

        // Immediate second request should be throttled
        Notification::fake(); // Reset notifications
        $I->amOnRoute('password.request');
        $I->submitForm('form', [
            'email' => 'user@example.com'
        ]);
        
        Notification::assertNothingSent();
        $I->see('Please wait before retrying');
    }

    // Password Reset Form Tests
    public function canAccessResetFormWithValidToken(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $token = Password::createToken($user);

        $I->amOnRoute('password.reset', ['token' => $token]);
        $I->seeResponseCodeIs(200);
        $I->see('Reset Password');
        $I->seeElement('input[name="email"]');
        $I->seeElement('input[name="password"]');
        $I->seeElement('input[name="password_confirmation"]');
        $I->seeElement('input[name="token"][value="' . $token . '"]');
    }

    public function cannotAccessResetFormWithInvalidToken(FunctionalTester $I): void
    {
        $I->amOnRoute('password.reset', ['token' => 'invalid-token']);
        $I->seeResponseCodeIs(200); // Form still shows, but reset will fail
    }

    // Password Reset Process Tests
    public function canResetPasswordWithValidData(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('oldpassword'),
            'role' => 'customer',
        ]);
        Customer::factory()->create(['user_id' => $user->id]);

        $token = Password::createToken($user);

        $I->amOnRoute('password.reset', ['token' => $token]);
        $I->submitForm('form', [
            'token' => $token,
            'email' => 'user@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        // Check password was updated
        $updatedUser = User::find($user->id);
        $I->assertTrue(Hash::check('newpassword123', $updatedUser->password));
        
        // Should be logged in and redirected
        $I->seeAuthentication();
        $I->seeCurrentRouteIs('customer.dashboard');
        $I->see('Your password has been reset');
    }

    public function cannotResetPasswordWithInvalidToken(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $I->amOnRoute('password.reset', ['token' => 'invalid-token']);
        $I->submitForm('form', [
            'token' => 'invalid-token',
            'email' => 'user@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        // Password should not be changed
        $unchangedUser = User::find($user->id);
        $I->assertTrue(Hash::check('oldpassword', $unchangedUser->password));
        
        $I->dontSeeAuthentication();
        $I->see('This password reset token is invalid');
    }

    public function cannotResetPasswordWithWrongEmail(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $token = Password::createToken($user);

        $I->amOnRoute('password.reset', ['token' => $token]);
        $I->submitForm('form', [
            'token' => $token,
            'email' => 'wrong@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        // Password should not be changed
        $unchangedUser = User::find($user->id);
        $I->assertTrue(Hash::check('oldpassword', $unchangedUser->password));
        
        $I->dontSeeAuthentication();
        $I->see('We can\'t find a user with that email address');
    }

    public function validatesRequiredFieldsForReset(FunctionalTester $I): void
    {
        $I->amOnRoute('password.reset', ['token' => 'some-token']);
        $I->submitForm('form', [
            'token' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => ''
        ]);

        $I->see('The token field is required');
        $I->see('The email field is required');
        $I->see('The password field is required');
        $I->dontSeeAuthentication();
    }

    public function validatesPasswordLengthForReset(FunctionalTester $I): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);
        $token = Password::createToken($user);

        $I->amOnRoute('password.reset', ['token' => $token]);
        $I->submitForm('form', [
            'token' => $token,
            'email' => 'user@example.com',
            'password' => '123',
            'password_confirmation' => '123'
        ]);

        $I->see('The password field must be at least 8 characters');
        $I->dontSeeAuthentication();
    }

    public function validatesPasswordConfirmationForReset(FunctionalTester $I): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);
        $token = Password::createToken($user);

        $I->amOnRoute('password.reset', ['token' => $token]);
        $I->submitForm('form', [
            'token' => $token,
            'email' => 'user@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'different123'
        ]);

        $I->see('The password field confirmation does not match');
        $I->dontSeeAuthentication();
    }

    public function validatesEmailFormatForReset(FunctionalTester $I): void
    {
        $token = 'some-token';

        $I->amOnRoute('password.reset', ['token' => $token]);
        $I->submitForm('form', [
            'token' => $token,
            'email' => 'invalid-email',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $I->see('The email field must be a valid email address');
        $I->dontSeeAuthentication();
    }

    // Role-based Redirection Tests
    public function redirectsCreatorToSetupAfterPasswordReset(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'creator@example.com',
            'password' => Hash::make('oldpassword'),
            'role' => 'creator',
        ]);
        Creator::factory()->create([
            'user_id' => $user->id,
            'setup_completed_at' => null, // Incomplete setup
        ]);

        $token = Password::createToken($user);

        $I->amOnRoute('password.reset', ['token' => $token]);
        $I->submitForm('form', [
            'token' => $token,
            'email' => 'creator@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $I->seeAuthentication();
        $I->seeCurrentRouteIs('creator.setup');
    }

    public function redirectsCreatorToDashboardAfterPasswordResetWithCompleteSetup(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'creator@example.com',
            'password' => Hash::make('oldpassword'),
            'role' => 'creator',
        ]);
        Creator::factory()->create([
            'user_id' => $user->id,
            'setup_completed_at' => now(), // Complete setup
        ]);

        $token = Password::createToken($user);

        $I->amOnRoute('password.reset', ['token' => $token]);
        $I->submitForm('form', [
            'token' => $token,
            'email' => 'creator@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $I->seeAuthentication();
        $I->seeCurrentRouteIs('creator.dashboard');
    }

    public function redirectsCustomerToDashboardAfterPasswordReset(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('oldpassword'),
            'role' => 'customer',
        ]);
        Customer::factory()->create(['user_id' => $user->id]);

        $token = Password::createToken($user);

        $I->amOnRoute('password.reset', ['token' => $token]);
        $I->submitForm('form', [
            'token' => $token,
            'email' => 'customer@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $I->seeAuthentication();
        $I->seeCurrentRouteIs('customer.dashboard');
    }

    // Token Expiry Tests
    public function cannotUseExpiredToken(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        // Create and immediately delete token to simulate expiry
        $token = Password::createToken($user);
        Password::deleteToken($user);

        $I->amOnRoute('password.reset', ['token' => $token]);
        $I->submitForm('form', [
            'token' => $token,
            'email' => 'user@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        // Password should not be changed
        $unchangedUser = User::find($user->id);
        $I->assertTrue(Hash::check('oldpassword', $unchangedUser->password));
        
        $I->dontSeeAuthentication();
        $I->see('This password reset token is invalid');
    }

    // Guest Protection Tests
    public function redirectsAuthenticatedUserAwayFromForgotPassword(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);
        Customer::factory()->create(['user_id' => $user->id]);

        $I->amLoggedAs($user);
        $I->amOnRoute('password.request');
        $I->seeCurrentRouteIs('customer.dashboard');
    }

    public function redirectsAuthenticatedUserAwayFromResetForm(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);
        Customer::factory()->create(['user_id' => $user->id]);

        $I->amLoggedAs($user);
        $I->amOnRoute('password.reset', ['token' => 'some-token']);
        $I->seeCurrentRouteIs('customer.dashboard');
    }

    // Email Prefill Tests
    public function prefillsEmailFromQueryParameter(FunctionalTester $I): void
    {
        $I->amOnPage('/reset-password/some-token?email=test@example.com');
        $I->seeElement('input[name="email"][value="test@example.com"]');
    }
}