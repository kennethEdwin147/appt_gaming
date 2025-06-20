import { test, expect } from '@playwright/test';
import { refreshDatabase, createUser } from '../utils/database-helpers';
import { loginAs } from '../utils/auth-helpers';

test.describe('Session Management', () => {
  test.beforeEach(async () => {
    await refreshDatabase();
  });

  test('should handle "Remember Me" functionality', async ({ page }) => {
    // Créer un utilisateur vérifié
    await createUser({
      first_name: 'Test',
      last_name: 'User',
      email: 'test@example.com',
      password: 'password123',
      email_verified_at: new Date().toISOString()
    });

    await page.goto('/login');
    
    // Se connecter avec "Remember Me" coché
    await page.fill('[data-testid="email-input"]', 'test@example.com');
    await page.fill('[data-testid="password-input"]', 'password123');
    await page.check('[data-testid="remember-me-checkbox"]');
    await page.click('[data-testid="submit-button"]');
    
    // Vérifier la connexion réussie
    await expect(page).toHaveURL(/.*\/dashboard/);
    
    // Fermer et rouvrir le navigateur (simulation)
    await page.context().close();
    const newContext = await page.context().browser().newContext();
    const newPage = await newContext.newPage();
    
    // Vérifier que l'utilisateur est toujours connecté
    await newPage.goto('/dashboard');
    await expect(newPage).toHaveURL(/.*\/dashboard/);
    await expect(newPage.locator('[data-testid="user-menu"]')).toBeVisible();
    
    await newContext.close();
  });

  test('should maintain session persistence during navigation', async ({ page }) => {
    // Créer un utilisateur vérifié
    await createUser({
      first_name: 'Session',
      last_name: 'User',
      email: 'session@example.com',
      password: 'password123',
      email_verified_at: new Date().toISOString()
    });

    // Se connecter
    await loginAs(page, 'session@example.com', 'password123');
    
    // Naviguer entre différentes pages
    await page.goto('/dashboard');
    await expect(page.locator('[data-testid="user-menu"]')).toBeVisible();
    
    await page.goto('/profile');
    await expect(page.locator('[data-testid="profile-page"]')).toBeVisible();
    
    await page.goto('/settings');
    await expect(page.locator('[data-testid="settings-page"]')).toBeVisible();
    
    // Vérifier que l'utilisateur reste connecté
    await expect(page.locator('[data-testid="user-menu"]')).toBeVisible();
  });

  test('should handle logout functionality correctly', async ({ page }) => {
    // Créer un utilisateur vérifié
    await createUser({
      first_name: 'Logout',
      last_name: 'User',
      email: 'logout@example.com',
      password: 'password123',
      email_verified_at: new Date().toISOString()
    });

    // Se connecter
    await loginAs(page, 'logout@example.com', 'password123');
    await expect(page).toHaveURL(/.*\/dashboard/);
    
    // Se déconnecter
    await page.click('[data-testid="user-menu"]');
    await page.click('[data-testid="logout-button"]');
    
    // Vérifier la redirection vers la page d'accueil ou de connexion
    await expect(page).toHaveURL(/.*\/(home|login|\/)$/);
    
    // Tenter d'accéder à une page protégée
    await page.goto('/dashboard');
    
    // Vérifier la redirection vers la page de connexion
    await expect(page).toHaveURL(/.*\/login/);
    await expect(page.locator('[data-testid="login-form"]')).toBeVisible();
  });

  test('should handle multiple sessions appropriately', async ({ browser }) => {
    // Créer un utilisateur vérifié
    await createUser({
      first_name: 'Multi',
      last_name: 'Session',
      email: 'multi@example.com',
      password: 'password123',
      email_verified_at: new Date().toISOString()
    });

    // Créer deux contextes de navigateur (simule deux navigateurs)
    const context1 = await browser.newContext();
    const context2 = await browser.newContext();
    
    const page1 = await context1.newPage();
    const page2 = await context2.newPage();
    
    // Se connecter dans les deux sessions
    await loginAs(page1, 'multi@example.com', 'password123');
    await loginAs(page2, 'multi@example.com', 'password123');
    
    // Vérifier que les deux sessions sont actives
    await expect(page1).toHaveURL(/.*\/dashboard/);
    await expect(page2).toHaveURL(/.*\/dashboard/);
    
    // Se déconnecter de la première session
    await page1.click('[data-testid="user-menu"]');
    await page1.click('[data-testid="logout-button"]');
    
    // Vérifier que la première session est fermée
    await expect(page1).toHaveURL(/.*\/(home|login|\/)$/);
    
    // Vérifier que la deuxième session reste active
    await page2.reload();
    await expect(page2).toHaveURL(/.*\/dashboard/);
    await expect(page2.locator('[data-testid="user-menu"]')).toBeVisible();
    
    await context1.close();
    await context2.close();
  });
});