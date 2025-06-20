import { test, expect } from '@playwright/test';
import { refreshDatabase, createUser, createCreator } from '../utils/database-helpers';
import { loginAs } from '../utils/auth-helpers';

test.describe('Role-based Authentication & Redirections', () => {
  test.beforeEach(async () => {
    await refreshDatabase();
  });

  test('should redirect customer to dashboard after login', async ({ page }) => {
    // Créer un customer vérifié
    await createUser({
      first_name: 'Customer',
      last_name: 'User',
      email: 'customer@example.com',
      password: 'password123',
      email_verified_at: new Date().toISOString(),
      role: 'customer'
    });

    // Se connecter
    await loginAs(page, 'customer@example.com', 'password123');
    
    // Vérifier la redirection vers le dashboard customer
    await expect(page).toHaveURL(/.*\/dashboard/);
    await expect(page.locator('[data-testid="customer-dashboard"]')).toBeVisible();
  });

  test('should redirect complete creator to creator dashboard', async ({ page }) => {
    // Créer un créateur avec profil complet
    const creatorData = await createCreator({
      first_name: 'Creator',
      last_name: 'Complete',
      email: 'creator-complete@example.com',
      password: 'password123',
      email_verified_at: new Date().toISOString(),
      role: 'creator',
      // Profil créateur complet
      creator_profile: {
        gaming_pseudo: 'ProGamer123',
        bio: 'Professional gamer',
        timezone: 'Europe/Paris',
        setup_completed: true
      }
    });

    // Se connecter
    await loginAs(page, 'creator-complete@example.com', 'password123');
    
    // Vérifier la redirection vers le dashboard créateur
    await expect(page).toHaveURL(/.*\/creator\/dashboard/);
    await expect(page.locator('[data-testid="creator-dashboard"]')).toBeVisible();
  });

  test('should redirect incomplete creator to setup wizard', async ({ page }) => {
    // Créer un créateur avec profil incomplet
    await createUser({
      first_name: 'Creator',
      last_name: 'Incomplete',
      email: 'creator-incomplete@example.com',
      password: 'password123',
      email_verified_at: new Date().toISOString(),
      role: 'creator'
      // Pas de profil créateur = setup incomplet
    });

    // Se connecter
    await loginAs(page, 'creator-incomplete@example.com', 'password123');
    
    // Vérifier la redirection vers le setup wizard
    await expect(page).toHaveURL(/.*\/creator\/setup/);
    await expect(page.locator('[data-testid="setup-wizard"]')).toBeVisible();
    await expect(page.locator('[data-testid="timezone-step"]')).toBeVisible();
  });

  test('should handle role-based access control for protected routes', async ({ page }) => {
    // Créer un customer
    await createUser({
      first_name: 'Customer',
      last_name: 'User',
      email: 'customer2@example.com',
      password: 'password123',
      email_verified_at: new Date().toISOString(),
      role: 'customer'
    });

    // Se connecter en tant que customer
    await loginAs(page, 'customer2@example.com', 'password123');
    
    // Tenter d'accéder à une route réservée aux créateurs
    await page.goto('/creator/dashboard');
    
    // Vérifier la redirection ou le message d'erreur
    await expect(page).toHaveURL(/.*\/(dashboard|403|unauthorized)/);
    
    // Si redirection vers une page d'erreur, vérifier le message
    const currentUrl = page.url();
    if (currentUrl.includes('403') || currentUrl.includes('unauthorized')) {
      await expect(page.locator('[data-testid="access-denied-message"]')).toBeVisible();
    }
  });
});