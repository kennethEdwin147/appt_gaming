<?php

declare(strict_types=1);

namespace Codeception\Functional\Auth;

use App\Models\User;
use App\Models\creator\Creator;
use App\Models\customer\Customer;
use Codeception\Support\FunctionalTester;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;

final class RegistrationCest
{
    public function _before(FunctionalTester $I): void
    {
        Event::fake();
    }

    // Creator Registration Tests
    public function canRegisterAsCreator(FunctionalTester $I): void
    {
        $I->amOnRoute('creator.register');
        $I->submitForm('form', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'creator@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $I->seeInDatabase('users', [
            'email' => 'creator@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'role' => 'creator',
            'email_verified_at' => null
        ]);

        $user = User::where('email', 'creator@example.com')->first();
        $I->seeInDatabase('creators', [
            'user_id' => $user->id,
            'setup_completed_at' => null
        ]);

        $I->seeAuthentication();
        $I->seeCurrentRouteIs('verification.notice');
        Event::assertDispatched(Registered::class);
    }

    public function canRegisterAsCustomer(FunctionalTester $I): void
    {
        $I->amOnRoute('customer.register');
        $I->submitForm('form', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'customer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $I->seeInDatabase('users', [
            'email' => 'customer@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'role' => 'customer',
            'email_verified_at' => null
        ]);

        $user = User::where('email', 'customer@example.com')->first();
        $I->seeInDatabase('customers', [
            'user_id' => $user->id,
            'status' => 'active'
        ]);

        $I->seeAuthentication();
        $I->seeCurrentRouteIs('verification.notice');
        Event::assertDispatched(Registered::class);
    }

    // Validation Tests
    public function validatesRequiredFieldsForCreator(FunctionalTester $I): void
    {
        $I->amOnRoute('creator.register');
        $I->submitForm('form', [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => ''
        ]);

        $I->seeCurrentRouteIs('creator.register');
        $I->see('The first name field is required');
        $I->see('The last name field is required');
        $I->see('The email field is required');
        $I->see('The password field is required');
        $I->dontSeeAuthentication();
    }

    public function validatesRequiredFieldsForCustomer(FunctionalTester $I): void
    {
        $I->amOnRoute('customer.register');
        $I->submitForm('form', [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => ''
        ]);

        $I->seeCurrentRouteIs('customer.register');
        $I->see('The first name field is required');
        $I->see('The last name field is required');
        $I->see('The email field is required');
        $I->see('The password field is required');
        $I->dontSeeAuthentication();
    }

    public function validatesEmailFormat(FunctionalTester $I): void
    {
        $I->amOnRoute('creator.register');
        $I->submitForm('form', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $I->seeCurrentRouteIs('creator.register');
        $I->see('The email field must be a valid email address');
        $I->dontSeeAuthentication();
    }

    public function validatesEmailUniqueness(FunctionalTester $I): void
    {
        // Create existing user
        User::factory()->create(['email' => 'existing@example.com']);

        $I->amOnRoute('creator.register');
        $I->submitForm('form', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $I->seeCurrentRouteIs('creator.register');
        $I->see('The email has already been taken');
        $I->dontSeeAuthentication();
    }

    public function validatesPasswordLength(FunctionalTester $I): void
    {
        $I->amOnRoute('creator.register');
        $I->submitForm('form', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'password' => '123',
            'password_confirmation' => '123'
        ]);

        $I->seeCurrentRouteIs('creator.register');
        $I->see('The password field must be at least 8 characters');
        $I->dontSeeAuthentication();
    }

    public function validatesPasswordConfirmation(FunctionalTester $I): void
    {
        $I->amOnRoute('creator.register');
        $I->submitForm('form', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123'
        ]);

        $I->seeCurrentRouteIs('creator.register');
        $I->see('The password field confirmation does not match');
        $I->dontSeeAuthentication();
    }

    // Role Selection Tests
    public function canAccessChooseRolePage(FunctionalTester $I): void
    {
        $I->amOnRoute('choose-role');
        $I->seeResponseCodeIs(200);
        $I->see('Choisissez votre rôle');
        $I->seeElement('input[name="role"][value="creator"]');
        $I->seeElement('input[name="role"][value="client"]');
    }

    public function canChooseCreatorRole(FunctionalTester $I): void
    {
        $I->amOnRoute('choose-role');
        $I->submitForm('form', [
            'role' => 'creator'
        ]);
        $I->seeCurrentRouteIs('creator.register');
    }

    public function canChooseCustomerRole(FunctionalTester $I): void
    {
        $I->amOnRoute('choose-role');
        $I->submitForm('form', [
            'role' => 'client'
        ]);
        $I->seeCurrentRouteIs('customer.register');
    }

    public function validatesRoleSelectionRequired(FunctionalTester $I): void
    {
        $I->amOnRoute('choose-role');
        $I->submitForm('form', [
            'role' => ''
        ]);
        $I->seeCurrentRouteIs('choose-role');
        $I->see('The role field is required');
    }

    public function validatesRoleSelectionValue(FunctionalTester $I): void
    {
        $I->amOnRoute('choose-role');
        $I->submitForm('form', [
            'role' => 'invalid_role'
        ]);
        $I->seeCurrentRouteIs('choose-role');
        $I->see('The selected role is invalid');
    }

    // Creator-specific Tests
    public function creatorRegistrationCreatesConfirmationToken(FunctionalTester $I): void
    {
        $I->amOnRoute('creator.register');
        $I->submitForm('form', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'creator@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $user = User::where('email', 'creator@example.com')->first();
        $creator = Creator::where('user_id', $user->id)->first();
        
        $I->assertNotNull($creator->confirmation_token);
        $I->assertNull($creator->confirmed_at);
        $I->assertNull($creator->setup_completed_at);
    }

    // Guest Protection Tests
    public function preventsAccessToRegistrationWhenAuthenticated(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);
        Customer::factory()->create(['user_id' => $user->id]);

        $I->amLoggedAs($user);
        $I->amOnRoute('creator.register');
        $I->seeCurrentRouteIs('customer.dashboard');
    }

    public function preventsAccessToChooseRoleWhenAuthenticated(FunctionalTester $I): void
    {
        $user = User::factory()->create([
            'role' => 'creator',
            'email_verified_at' => now(),
        ]);
        Creator::factory()->create([
            'user_id' => $user->id,
            'setup_completed_at' => now(),
        ]);

        $I->amLoggedAs($user);
        $I->amOnRoute('choose-role');
        $I->seeCurrentRouteIs('creator.dashboard');
    }

    // Registration Alias Tests
    public function registerRouteRedirectsToChooseRole(FunctionalTester $I): void
    {
        $I->amOnRoute('register');
        $I->seeCurrentRouteIs('choose-role');
    }

    // Newsletter Subscription Tests (Optional)
    public function canOptInToNewsletter(FunctionalTester $I): void
    {
        $I->amOnRoute('creator.register');
        $I->submitForm('form', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'creator@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'newsletter' => '1'
        ]);

        // This would test newsletter subscription if implemented
        // For now, just ensure registration still works
        $I->seeInDatabase('users', [
            'email' => 'creator@example.com',
            'role' => 'creator'
        ]);
        $I->seeAuthentication();
    }
}