import { test, expect } from '@playwright/test';
import { refreshDatabase, createUser } from '../utils/database-helpers';
import { loginAs } from '../utils/auth-helpers';

test.describe('Login Page', () => {
  test.beforeEach(async () => {
    // Réinitialiser la base de données avant chaque test
    await refreshDatabase();
  });

  test('should display login form correctly', async ({ page }) => {
    await page.goto('/login');
    
    // Vérifier que tous les éléments du formulaire sont présents
    await expect(page.locator('[data-testid="login-form"]')).toBeVisible();
    await expect(page.locator('[data-testid="email-input"]')).toBeVisible();
    await expect(page.locator('[data-testid="password-input"]')).toBeVisible();
    await expect(page.locator('[data-testid="submit-button"]')).toBeVisible();
  });

  test('should show validation errors with empty form submission', async ({ page }) => {
    await page.goto('/login');
    
    // Soumettre le formulaire vide
    await page.click('[data-testid="submit-button"]');
    
    // Vérifier que les messages d'erreur s'affichent
    await expect(page.locator('[data-testid="validation-error"]').first()).toBeVisible();
  });

  test('should show error with invalid credentials', async ({ page }) => {
    await page.goto('/login');
    
    // Remplir le formulaire avec des identifiants incorrects
    await page.fill('[data-testid="email-input"]', 'invalid@example.com');
    await page.fill('[data-testid="password-input"]', 'wrongpassword');
    await page.click('[data-testid="submit-button"]');
    
    // Vérifier que le message d'erreur s'affiche
    await expect(page.locator('[data-testid="validation-error"]').first()).toBeVisible();
  });

  test('should show login form and accept user input', async ({ page }) => {
    await page.goto('/login');
    
    // Vérifier que le formulaire est fonctionnel
    await page.fill('[data-testid="email-input"]', 'test@example.com');
    await page.fill('[data-testid="password-input"]', 'password');
    
    // Vérifier que les valeurs sont bien saisies
    await expect(page.locator('[data-testid="email-input"]')).toHaveValue('test@example.com');
    await expect(page.locator('[data-testid="password-input"]')).toHaveValue('password');
    
    // Vérifier que le bouton de soumission est cliquable
    await expect(page.locator('[data-testid="submit-button"]')).toBeEnabled();
    
    console.log('✅ Login form is functional and ready for authentication');
  });
});
