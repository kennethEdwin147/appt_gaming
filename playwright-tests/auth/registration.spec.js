import { test, expect } from '@playwright/test';
import { refreshDatabase, createUser } from '../utils/database-helpers';

test.describe('Registration Page', () => {
  test.beforeEach(async () => {
    // Réinitialiser la base de données avant chaque test
    await refreshDatabase();
  });

  test('should display registration form correctly', async ({ page }) => {
    await page.goto('/register');
    
    // Vérifier que tous les éléments du formulaire sont présents
    await expect(page.locator('[data-testid="role-selector"]')).toBeVisible();
    await expect(page.locator('[data-testid="role-customer"]')).toBeVisible();
    await expect(page.locator('[data-testid="role-creator"]')).toBeVisible();
  });

  test('should register customer successfully', async ({ page }) => {
    await page.goto('/register');
    
    // Sélectionner le rôle client
    await page.click('[data-testid="role-customer"]');
    
    // Vérifier que le formulaire d'inscription client est affiché
    await expect(page.locator('[data-testid="register-form"]')).toBeVisible();
    
    // Remplir le formulaire
    await page.fill('[data-testid="first-name-input"]', 'John');
    await page.fill('[data-testid="last-name-input"]', 'Doe');
    await page.fill('[data-testid="email-input"]', 'john@example.com');
    await page.fill('[data-testid="password-input"]', 'password123');
    await page.fill('[data-testid="password-confirmation-input"]', 'password123');
    
    await page.click('[data-testid="submit-button"]');
    
    // Vérifier la redirection vers la page de vérification d'email
    await expect(page).toHaveURL(/.*\/email\/verify/);
    await expect(page.locator('[data-testid="verification-message"]')).toBeVisible();
  });

  test('should register creator and redirect to email verification', async ({ page }) => {
    await page.goto('/register');
    
    // Sélectionner le rôle créateur
    await page.click('[data-testid="role-creator"]');
    
    // Vérifier que le formulaire d'inscription créateur est affiché
    await expect(page.locator('[data-testid="register-form"]')).toBeVisible();
    
    // Remplir le formulaire
    await page.fill('[data-testid="first-name-input"]', 'Jane');
    await page.fill('[data-testid="last-name-input"]', 'Creator');
    await page.fill('[data-testid="email-input"]', 'jane@example.com');
    await page.fill('[data-testid="password-input"]', 'password123');
    await page.fill('[data-testid="password-confirmation-input"]', 'password123');
    
    await page.click('[data-testid="submit-button"]');
    
    // Vérifier la redirection vers la page de vérification d'email
    await expect(page).toHaveURL(/.*\/email\/verify/);
    await expect(page.locator('[data-testid="verification-message"]')).toBeVisible();
  });

  test('should show validation errors with empty form submission', async ({ page }) => {
    await page.goto('/register');
    
    // Sélectionner le rôle client
    await page.click('[data-testid="role-customer"]');
    
    // Soumettre le formulaire vide
    await page.click('[data-testid="submit-button"]');
    
    // Vérifier que les messages d'erreur s'affichent
    await expect(page.locator('[data-testid="validation-error"]')).toBeVisible();
  });

  test('should show error when email is already taken', async ({ page }) => {
    // Créer un utilisateur avec un email spécifique
    await createUser({
      first_name: 'Existing',
      last_name: 'User',
      email: 'existing@example.com',
      password: 'password123'
    });
    
    await page.goto('/register');
    
    // Sélectionner le rôle client
    await page.click('[data-testid="role-customer"]');
    
    // Remplir le formulaire avec l'email existant
    await page.fill('[data-testid="first-name-input"]', 'Test');
    await page.fill('[data-testid="last-name-input"]', 'User');
    await page.fill('[data-testid="email-input"]', 'existing@example.com');
    await page.fill('[data-testid="password-input"]', 'password123');
    await page.fill('[data-testid="password-confirmation-input"]', 'password123');
    
    await page.click('[data-testid="submit-button"]');
    
    // Vérifier que le message d'erreur s'affiche
    await expect(page.locator('[data-testid="validation-error"]')).toBeVisible();
  });
});
