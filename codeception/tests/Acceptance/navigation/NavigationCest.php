<?php

declare(strict_types=1);


namespace Codeception\Acceptance\Navigation;

use Codeception\Support\AcceptanceTester;

final class NavigationCest
{
    public function _before(AcceptanceTester $I): void
    {
        // Code here will be executed before each test.
    }

    public function canAccessHomePage(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
        $I->see('Gaming Platform');
    }

    public function canAccessChooseRolePage(AcceptanceTester $I): void
    {
        $I->amOnPage('/choose-role');
        $I->seeResponseCodeIs(200);
        $I->see('Choisissez votre rôle');
        $I->seeElement('input[name="role"][value="creator"]');
        $I->seeElement('input[name="role"][value="client"]');
    }

    public function canAccessLoginPage(AcceptanceTester $I): void
    {
        $I->amOnPage('/login');
        $I->seeResponseCodeIs(200);
        $I->see('Sign in');
        $I->seeElement('input[name="email"]');
        $I->seeElement('input[name="password"]');
    }

    public function canNavigateFromHomeToChooseRole(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->click('Sign in');
        $I->seeCurrentUrlEquals('/login');
    }

    public function canAccessCreatorRegistration(AcceptanceTester $I): void
    {
        $I->amOnPage('/register/creator');
        $I->seeResponseCodeIs(200);
        $I->see('Créer un compte Créateur');
    }

    public function canAccessCustomerRegistration(AcceptanceTester $I): void
    {
        $I->amOnPage('/register/client');
        $I->seeResponseCodeIs(200);
        $I->see('Créer un compte client');
    }
}
