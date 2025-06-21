# Tests Codeception pour Gaming Platform

Ce dossier contient les tests Codeception pour le projet Gaming Platform. Codeception est un framework de test PHP qui permet de tester les interfaces utilisateur et l'intégration Laravel.

## Structure des dossiers

```
codeception/
├── codeception.yml           # Configuration principale
├── README.md                # Cette documentation
└── tests/
    ├── Acceptance/          # Tests interface utilisateur (PhpBrowser)
    │   ├── auth/           # Tests formulaires d'authentification
    │   ├── creator/        # Tests pages créateur
    │   ├── customer/       # Tests pages client
    │   ├── navigation/     # Tests navigation générale
    │   └── public/         # Tests pages publiques
    ├── Functional/         # Tests intégration Laravel
    │   ├── auth/          # Tests auth avec modèles Laravel
    │   ├── creator/       # Tests logique créateur
    │   ├── customer/      # Tests logique client
    │   ├── reservation/   # Tests système de réservation
    │   └── availability/  # Tests disponibilités
    └── Support/           # Classes de support et helpers
        ├── AcceptanceTester.php
        ├── FunctionalTester.php
        └── _generated/    # Classes générées automatiquement
```

## Types de tests

### Acceptance Tests (PhpBrowser)
- **Objectif** : Tester l'interface utilisateur via HTTP
- **Capacités** : Formulaires, navigation, contenu HTML
- **Limitations** : Pas de JavaScript, pas d'interactions complexes
- **Idéal pour** : Tests de formulaires, navigation, validation

### Functional Tests (Laravel)
- **Objectif** : Tester l'intégration avec Laravel
- **Capacités** : Accès direct aux modèles, base de données, sessions
- **Avantages** : Plus rapide, accès complet à l'application Laravel
- **Idéal pour** : Tests d'authentification, logique métier, API

## Comment lancer les tests

### Depuis le dossier codeception

```bash
cd codeception

# Tous les tests
../vendor/bin/codecept run

# Tests Acceptance uniquement
../vendor/bin/codecept run Acceptance

# Tests Functional uniquement  
../vendor/bin/codecept run Functional

# Test spécifique
../vendor/bin/codecept run Acceptance navigation/NavigationCest

# Avec détails
../vendor/bin/codecept run --steps

# Avec débobage
../vendor/bin/codecept run --debug
```

### Depuis la racine du projet

```bash
# Tous les tests
./vendor/bin/codecept -c codeception run

# Tests Acceptance uniquement
./vendor/bin/codecept -c codeception run Acceptance

# Tests Functional uniquement
./vendor/bin/codecept -c codeception run Functional
```

## Commandes utiles

### Génération de tests

```bash
cd codeception

# Générer un test Acceptance
../vendor/bin/codecept generate:cest Acceptance auth/LoginFormCest

# Générer un test Functional
../vendor/bin/codecept generate:cest Functional auth/AuthenticationCest
```

### Reconstruction des actors

```bash
cd codeception
../vendor/bin/codecept build
```

### Nettoyage des outputs

```bash
cd codeception
rm -rf tests/_output/*
```

## Configuration

### Acceptance Tests (PhpBrowser)
- **URL de base** : `http://localhost:8000`
- **Module** : PhpBrowser (pas de JavaScript)
- **Configuration** : `tests/Acceptance.suite.yml`

### Functional Tests (Laravel)
- **Modules** : Laravel + Asserts
- **Environnement** : `.env.testing`
- **Configuration** : `tests/Functional.suite.yml`

## Exemples de tests

### Test Acceptance basique

```php
public function canAccessLoginPage(AcceptanceTester $I): void
{
    $I->amOnPage('/login');
    $I->seeResponseCodeIs(200);
    $I->see('Sign in');
    $I->seeElement('input[name="email"]');
    $I->seeElement('input[name="password"]');
}
```

### Test Functional avec Laravel

```php
public function canLoginUser(FunctionalTester $I): void
{
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password')
    ]);
    
    $I->amLoggedAs($user);
    $I->seeAuthentication();
    $I->amOnRoute('creator.dashboard');
    $I->see('Dashboard');
}
```

## Bonnes pratiques

1. **Organisation** : Utilisez les dossiers modulaires (auth, creator, etc.)
2. **Nommage** : Noms de méthodes descriptifs (`canAccessLoginPage`)
3. **Namespaces** : Respectez la structure `Codeception\\Acceptance\\Module\\`
4. **Tests atomiques** : Un test = une fonctionnalité
5. **Données** : Utilisez les factories Laravel pour les tests Functional

## Complémentarité avec autres tests

- **Codeception** : Tests UI et intégration Laravel
- **PHPUnit** (dossier `/tests`) : Tests unitaires Laravel
- **Playwright** (dossier `/playwright-tests`) : Tests E2E avec JavaScript

## Dépannage

### Erreur "Actor not found"
```bash
cd codeception && ../vendor/bin/codecept build
```

### Erreur de namespace
Vérifiez que les namespaces dans vos tests correspondent à la structure des dossiers.

### Erreur de connexion
Assurez-vous que le serveur Laravel fonctionne sur `http://localhost:8000`

## URLs testées

- `/` - Page d'accueil
- `/choose-role` - Sélection de rôle
- `/login` - Connexion
- `/register/creator` - Inscription créateur
- `/register/client` - Inscription client
- `/creator/dashboard` - Tableau de bord créateur
- `/customer/dashboard` - Tableau de bord client