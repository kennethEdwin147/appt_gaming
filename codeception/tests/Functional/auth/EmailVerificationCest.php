<?php

declare(strict_types=1);

namespace Codeception\Functional\Auth;

use App\Models\User;
use App\Models\creator\Creator;
use App\Models\customer\Customer;
use Codeception\Support\FunctionalTester;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;

final class EmailVerificationCest
{
    public function _before(FunctionalTester $I): void
    {
        Notification::fake();
        Event::fake();
    }

    // Email Verification Notice Tests
    public function showsVerificationNoticeForUnverifiedUser(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $I->amLoggedAs($user);
        $I->amOnRoute('verification.notice');
        $I->seeResponseCodeIs(200);
        $I->see('Verify Your Email Address');
        $I->see($user->email);
    }

    public function redirectsVerifiedUserFromNotice(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'customer',
        ]);
        Customer::factory()->create(['user_id' => $user->id]);

        $I->amLoggedAs($user);
        $I->amOnRoute('verification.notice');
        $I->seeCurrentRouteIs('customer.dashboard');
    }

    public function redirectsVerifiedCreatorWithIncompleteSetup(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'creator',
        ]);
        Creator::factory()->create([
            'user_id' => $user->id,
            'setup_completed_at' => null,
        ]);

        $I->amLoggedAs($user);
        $I->amOnRoute('verification.notice');
        $I->seeCurrentRouteIs('creator.setup');
    }

    public function redirectsVerifiedCreatorWithCompleteSetup(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'creator',
        ]);
        Creator::factory()->create([
            'user_id' => $user->id,
            'setup_completed_at' => now(),
        ]);

        $I->amLoggedAs($user);
        $I->amOnRoute('verification.notice');
        $I->seeCurrentRouteIs('creator.dashboard');
    }

    public function requiresAuthenticationForVerificationNotice(FunctionalTester $I): void
    {
        $I->amOnRoute('verification.notice');
        $I->seeCurrentRouteIs('login');
    }

    // Email Verification Process Tests
    public function canVerifyEmailWithValidLink(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'role' => 'customer',
        ]);
        Customer::factory()->create(['user_id' => $user->id]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $I->amLoggedAs($user);
        $I->amOnPage($verificationUrl);
        
        // Check user is verified
        $I->seeInDatabase('users', [
            'id' => $user->id,
            'email_verified_at' => now()->format('Y-m-d H:i:s')
        ]);

        Event::assertDispatched(Verified::class);
        $I->seeCurrentRouteIs('customer.dashboard');
    }

    public function cannotVerifyEmailWithInvalidHash(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'invalid-hash']
        );

        $I->amLoggedAs($user);
        $I->amOnPage($verificationUrl);
        
        // Should abort with 403
        $I->seeResponseCodeIs(403);
        
        // User should still be unverified
        $I->seeInDatabase('users', [
            'id' => $user->id,
            'email_verified_at' => null
        ]);
    }

    public function cannotVerifyEmailWithExpiredLink(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // Create expired URL (past time)
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $I->amLoggedAs($user);
        $I->amOnPage($verificationUrl);
        
        // Should abort with 403
        $I->seeResponseCodeIs(403);
        
        // User should still be unverified
        $I->seeInDatabase('users', [
            'id' => $user->id,
            'email_verified_at' => null
        ]);
    }

    public function cannotVerifyEmailForDifferentUser(FunctionalTester $I): void
    {
        $user1 = User::factory()->create(['email_verified_at' => null]);
        $user2 = User::factory()->create(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user2->id, 'hash' => sha1($user2->email)]
        );

        $I->amLoggedAs($user1);
        $I->amOnPage($verificationUrl);
        
        // Should abort with 403
        $I->seeResponseCodeIs(403);
        
        // Both users should still be unverified
        $I->seeInDatabase('users', [
            'id' => $user1->id,
            'email_verified_at' => null
        ]);
        $I->seeInDatabase('users', [
            'id' => $user2->id,
            'email_verified_at' => null
        ]);
    }

    public function redirectsAlreadyVerifiedUser(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'customer',
        ]);
        Customer::factory()->create(['user_id' => $user->id]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $I->amLoggedAs($user);
        $I->amOnPage($verificationUrl);
        $I->seeCurrentRouteIs('customer.dashboard');
    }

    // Resend Verification Email Tests
    public function canResendVerificationEmail(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $I->amLoggedAs($user);
        $I->amOnRoute('verification.send');
        
        Notification::assertSentTo($user, VerifyEmail::class);
        $I->seeCurrentRouteIs('verification.notice');
        $I->see('A fresh verification link has been sent');
    }

    public function cannotResendToVerifiedUser(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'customer',
        ]);
        Customer::factory()->create(['user_id' => $user->id]);

        $I->amLoggedAs($user);
        $I->amOnRoute('verification.send');
        
        Notification::assertNothingSent();
        $I->seeCurrentRouteIs('customer.dashboard');
    }

    public function requiresAuthenticationForResend(FunctionalTester $I): void
    {
        $I->amOnRoute('verification.send');
        $I->seeCurrentRouteIs('login');
    }

    public function throttlesResendRequests(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $I->amLoggedAs($user);
        
        // First request should succeed
        $I->amOnRoute('verification.send');
        Notification::assertSentTo($user, VerifyEmail::class);
        
        // Immediate second request should be throttled
        Notification::fake(); // Reset notifications
        $I->amOnRoute('verification.send');
        Notification::assertNothingSent();
        $I->see('Please wait before requesting another');
    }

    // Creator-specific Verification Tests
    public function redirectsCreatorToSetupAfterVerification(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'role' => 'creator',
        ]);
        Creator::factory()->create([
            'user_id' => $user->id,
            'setup_completed_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $I->amLoggedAs($user);
        $I->amOnPage($verificationUrl);
        $I->seeCurrentRouteIs('creator.setup');
    }

    public function redirectsCreatorToDashboardAfterVerificationWithCompleteSetup(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'role' => 'creator',
        ]);
        Creator::factory()->create([
            'user_id' => $user->id,
            'setup_completed_at' => now(),
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $I->amLoggedAs($user);
        $I->amOnPage($verificationUrl);
        $I->seeCurrentRouteIs('creator.dashboard');
    }

    // Edge Cases
    public function requiresAuthenticationForVerification(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $I->amOnPage($verificationUrl);
        $I->seeCurrentRouteIs('login');
    }

    public function handlesNonExistentUserGracefully(FunctionalTester $I): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => 999999, 'hash' => 'some-hash']
        );

        $user = User::factory()->create();
        $I->amLoggedAs($user);
        $I->amOnPage($verificationUrl);
        $I->seeResponseCodeIs(403);
    }
}