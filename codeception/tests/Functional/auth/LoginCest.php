<?php

declare(strict_types=1);

namespace Codeception\Functional\Auth;

use App\Models\User;
use App\Models\creator\Creator;
use App\Models\customer\Customer;
use Codeception\Support\FunctionalTester;
use Illuminate\Support\Facades\Hash;

final class LoginCest
{
    public function _before(FunctionalTester $I): void
    {
        // Code here will be executed before each test.
    }

    public function canLoginWithValidCredentials(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $I->amOnRoute('login');
        $I->submitForm('form', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);
        $I->seeAuthentication();
        $I->seeCurrentRouteIs('home');
    }

    public function cannotLoginWithInvalidCredentials(FunctionalTester $I): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $I->amOnRoute('login');
        $I->submitForm('form', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);
        $I->dontSeeAuthentication();
        $I->seeCurrentRouteIs('login');
        $I->see('These credentials do not match our records');
    }

    public function cannotLoginWithNonExistentEmail(FunctionalTester $I): void
    {
        $I->amOnRoute('login');
        $I->submitForm('form', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ]);
        $I->dontSeeAuthentication();
        $I->seeCurrentRouteIs('login');
        $I->see('These credentials do not match our records');
    }

    public function redirectsUnverifiedUserToEmailVerification(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
        ]);

        $I->amOnRoute('login');
        $I->submitForm('form', [
            'email' => 'unverified@example.com',
            'password' => 'password123'
        ]);
        $I->seeCurrentRouteIs('verification.notice');
    }

    public function redirectsCreatorWithIncompleteSetupToSetupPage(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'creator@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'role' => 'creator',
        ]);

        Creator::factory()->create([
            'user_id' => $user->id,
            'setup_completed_at' => null, // Incomplete setup
        ]);

        $I->amOnRoute('login');
        $I->submitForm('form', [
            'email' => 'creator@example.com',
            'password' => 'password123'
        ]);
        $I->seeAuthentication();
        $I->seeCurrentRouteIs('creator.setup');
    }

    public function redirectsCreatorWithCompleteSetupToDashboard(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'creator@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'role' => 'creator',
        ]);

        Creator::factory()->create([
            'user_id' => $user->id,
            'setup_completed_at' => now(), // Complete setup
            'timezone' => 'America/New_York',
            'bio' => 'Gaming professional with 5+ years experience.',
        ]);

        $I->amOnRoute('login');
        $I->submitForm('form', [
            'email' => 'creator@example.com',
            'password' => 'password123'
        ]);
        $I->seeAuthentication();
        $I->seeCurrentRouteIs('creator.dashboard');
    }

    public function redirectsCustomerToDashboard(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'role' => 'customer',
        ]);

        Customer::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $I->amOnRoute('login');
        $I->submitForm('form', [
            'email' => 'customer@example.com',
            'password' => 'password123'
        ]);
        $I->seeAuthentication();
        $I->seeCurrentRouteIs('customer.dashboard');
    }

    public function validatesRequiredFields(FunctionalTester $I): void
    {
        $I->amOnRoute('login');
        $I->submitForm('form', [
            'email' => '',
            'password' => ''
        ]);
        $I->dontSeeAuthentication();
        $I->seeCurrentRouteIs('login');
        $I->see('The email field is required');
        $I->see('The password field is required');
    }

    public function validatesEmailFormat(FunctionalTester $I): void
    {
        $I->amOnRoute('login');
        $I->submitForm('form', [
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);
        $I->dontSeeAuthentication();
        $I->seeCurrentRouteIs('login');
        $I->see('The email field must be a valid email address');
    }

    public function canLogout(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $I->amLoggedAs($user);
        $I->seeAuthentication();
        
        $I->amOnRoute('logout');
        $I->dontSeeAuthentication();
        $I->seeCurrentRouteIs('home');
    }

    public function preventsAccessToLoginWhenAuthenticated(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'customer',
        ]);

        Customer::factory()->create(['user_id' => $user->id]);

        $I->amLoggedAs($user);
        $I->amOnRoute('login');
        $I->seeCurrentRouteIs('customer.dashboard');
    }
}