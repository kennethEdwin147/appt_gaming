import { test, expect } from '@playwright/test';
import { refreshDatabase, createUser } from '../utils/database-helpers';

test.describe('Email Verification', () => {
  test.beforeEach(async () => {
    await refreshDatabase();
  });

  test('should redirect unverified user to verification page', async ({ page }) => {
    // Créer un utilisateur non vérifié
    await createUser({
      first_name: 'John',
      last_name: 'Doe',
      email: 'unverified@example.com',
      password: 'password123',
      email_verified_at: null // Utilisateur non vérifié
    });

    // Tenter d'accéder à une page protégée
    await page.goto('/dashboard');
    
    // Vérifier la redirection vers la page de vérification
    await expect(page).toHaveURL(/.*\/email\/verify/);
    await expect(page.locator('[data-testid="verification-message"]')).toBeVisible();
    await expect(page.locator('[data-testid="resend-verification-button"]')).toBeVisible();
  });

  test('should show resend verification functionality', async ({ page }) => {
    // Créer un utilisateur non vérifié
    await createUser({
      first_name: 'Jane',
      last_name: 'Doe',
      email: 'unverified2@example.com',
      password: 'password123',
      email_verified_at: null
    });

    await page.goto('/email/verify');
    
    // Cliquer sur le bouton de renvoi
    await page.click('[data-testid="resend-verification-button"]');
    
    // Vérifier que le message de confirmation s'affiche
    await expect(page.locator('[data-testid="resend-success-message"]')).toBeVisible();
    
    // Vérifier que le bouton est temporairement désactivé
    await expect(page.locator('[data-testid="resend-verification-button"]')).toBeDisabled();
  });

  test('should allow verified users to access protected pages', async ({ page }) => {
    // Créer un utilisateur vérifié
    await createUser({
      first_name: 'Verified',
      last_name: 'User',
      email: 'verified@example.com',
      password: 'password123',
      email_verified_at: new Date().toISOString() // Utilisateur vérifié
    });

    // Se connecter
    await page.goto('/login');
    await page.fill('[data-testid="email-input"]', 'verified@example.com');
    await page.fill('[data-testid="password-input"]', 'password123');
    await page.click('[data-testid="submit-button"]');
    
    // Vérifier l'accès direct au dashboard sans redirection vers verification
    await expect(page).toHaveURL(/.*\/dashboard/);
    await expect(page.locator('[data-testid="dashboard-content"]')).toBeVisible();
  });
});