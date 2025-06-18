# Tests Playwright pour Gaming Platform

Ce dossier contient les tests end-to-end (E2E) utilisant Playwright pour le projet Gaming Platform.

## Structure des dossiers

```
tests/playwright/
├── auth/                  # Tests d'authentification (login, register, email verification)
│   ├── login.spec.js      # Tests de connexion
│   └── registration.spec.js # Tests d'inscription
├── creator/               # Tests pour les fonctionnalités des créateurs
│   └── setup-wizard.spec.js # Tests du wizard de configuration
├── customer/              # Tests pour les fonctionnalités des clients
├── booking/               # Tests pour les fonctionnalités de réservation
├── fixtures/              # Données de test statiques
└── utils/                 # Utilitaires pour les tests
    ├── auth-helpers.js    # Helpers pour l'authentification
    └── database-helpers.js # Helpers pour la base de données
```

## Commandes disponibles

```bash
# Exécuter tous les tests
npm run test:e2e

# Exécuter les tests avec l'interface utilisateur de Playwright
npm run test:e2e:ui

# Exécuter les tests en mode debug
npm run test:e2e:debug

# Exécuter les tests avec navigateur visible
npm run test:e2e:headed

# Exécuter uniquement les tests d'authentification
npm run test:e2e:auth

# Exécuter uniquement les tests des créateurs
npm run test:e2e:creator

# Exécuter uniquement les tests des clients
npm run test:e2e:customer

# Exécuter uniquement les tests de réservation
npm run test:e2e:booking
```

## Helpers disponibles

### Database Helpers

```javascript
import { refreshDatabase, createUser, createCreator } from '../utils/database-helpers';

// Réinitialiser la base de données
await refreshDatabase();

// Créer un utilisateur
const user = await createUser({
  first_name: 'Test',
  last_name: 'User',
  email: 'test@example.com',
  password: 'password123',
  role: 'customer'
});

// Créer un créateur avec profil
const { user, creator } = await createCreator({
  first_name: 'Creator',
  last_name: 'Test',
  email: 'creator@example.com'
});
```

### Auth Helpers

```javascript
import { loginAs, registerUser } from '../utils/auth-helpers';

// Se connecter en tant qu'utilisateur
await loginAs(page, 'test@example.com', 'password123');

// Inscrire un nouvel utilisateur
await registerUser(page, {
  first_name: 'New',
  last_name: 'User',
  email: 'new@example.com',
  password: 'password123',
  role: 'customer'
});
```

## Bonnes pratiques

1. **Isolation des tests** : Chaque test doit être indépendant et ne pas dépendre de l'état d'autres tests.
2. **Réinitialisation de la base de données** : Utilisez `refreshDatabase()` avant chaque test pour garantir un état propre.
3. **Sélecteurs robustes** : Utilisez des attributs `data-testid` pour sélectionner les éléments dans les tests.
4. **Attentes explicites** : Utilisez `await expect(page).toHaveURL()` et `await expect(page.locator()).toBeVisible()` pour des attentes explicites.
5. **Helpers réutilisables** : Créez des helpers pour les actions répétitives comme la connexion ou la création d'utilisateurs.

## Configuration

La configuration de Playwright se trouve dans le fichier `playwright.config.js` à la racine du projet. Elle inclut :

- Configuration des navigateurs (Chrome, Safari)
- Configuration du serveur web Laravel
- Configuration des captures d'écran et vidéos
- Configuration des rapports de test
