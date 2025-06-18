import { test, expect } from '@playwright/test';
import { refreshDatabase, createUser } from '../utils/database-helpers';
import { loginAs } from '../utils/auth-helpers';

test.describe('Creator Setup Wizard', () => {
  test.beforeEach(async () => {
    // Réinitialiser la base de données avant chaque test
    await refreshDatabase();
  });

  test('should complete timezone setup successfully', async ({ page }) => {
    // Créer un utilisateur créateur
    const user = await createUser({
      first_name: 'Creator',
      last_name: 'Test',
      email: 'creator@example.com',
      password: 'password123',
      role: 'creator'
    });
    
    // Se connecter en tant que créateur
    await loginAs(page, 'creator@example.com', 'password123');
    
    // Vérifier qu'on est sur la page de configuration du fuseau horaire
    await expect(page).toHaveURL(/.*\/creator\/setup\/timezone/);
    
    // Sélectionner un fuseau horaire
    await page.selectOption('select[name="timezone"]', 'Europe/Paris');
    
    // Soumettre le formulaire
    await page.click('button[type="submit"]');
    
    // Vérifier la redirection vers la page de configuration du profil
    await expect(page).toHaveURL(/.*\/creator\/setup\/profile/);
  });

  test('should complete profile setup successfully', async ({ page }) => {
    // Créer un utilisateur créateur
    const user = await createUser({
      first_name: 'Creator',
      last_name: 'Test',
      email: 'creator@example.com',
      password: 'password123',
      role: 'creator'
    });
    
    // Mettre à jour le fuseau horaire du créateur
    await page.evaluate(() => {
      return fetch('/api/creator/setup/timezone', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ timezone: 'Europe/Paris' })
      });
    });
    
    // Se connecter en tant que créateur
    await loginAs(page, 'creator@example.com', 'password123');
    
    // Vérifier qu'on est sur la page de configuration du profil
    await expect(page).toHaveURL(/.*\/creator\/setup\/profile/);
    
    // Remplir le formulaire de profil
    await page.fill('[data-testid="gaming-pseudo-input"]', 'ProGamer');
    await page.fill('[data-testid="bio-input"]', 'Expert gaming coach with 10+ years of experience');
    await page.fill('[data-testid="main-game-input"]', 'Fortnite');
    await page.fill('[data-testid="rank-info-input"]', 'Champion League');
    await page.fill('[data-testid="default-hourly-rate-input"]', '50');
    
    // Soumettre le formulaire
    await page.click('[data-testid="complete-setup-button"]');
    
    // Vérifier la redirection vers le tableau de bord du créateur
    await expect(page).toHaveURL(/.*\/creator\/dashboard/);
    await expect(page.locator('[data-testid="success-message"]')).toBeVisible();
  });

  test('should validate timezone form fields', async ({ page }) => {
    // Créer un utilisateur créateur
    const user = await createUser({
      first_name: 'Creator',
      last_name: 'Test',
      email: 'creator@example.com',
      password: 'password123',
      role: 'creator'
    });
    
    // Se connecter en tant que créateur
    await loginAs(page, 'creator@example.com', 'password123');
    
    // Vérifier qu'on est sur la page de configuration du fuseau horaire
    await expect(page).toHaveURL(/.*\/creator\/setup\/timezone/);
    
    // Soumettre le formulaire sans sélectionner de fuseau horaire
    await page.click('button[type="submit"]');
    
    // Vérifier que les messages d'erreur s'affichent
    await expect(page.locator('[data-testid="validation-error"]')).toBeVisible();
  });

  test('should validate profile form fields', async ({ page }) => {
    // Créer un utilisateur créateur avec fuseau horaire
    const user = await createUser({
      first_name: 'Creator',
      last_name: 'Test',
      email: 'creator@example.com',
      password: 'password123',
      role: 'creator'
    });
    
    // Mettre à jour le fuseau horaire du créateur
    await page.evaluate(() => {
      return fetch('/api/creator/setup/timezone', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ timezone: 'Europe/Paris' })
      });
    });
    
    // Se connecter en tant que créateur
    await loginAs(page, 'creator@example.com', 'password123');
    
    // Vérifier qu'on est sur la page de configuration du profil
    await expect(page).toHaveURL(/.*\/creator\/setup\/profile/);
    
    // Soumettre le formulaire vide
    await page.click('[data-testid="complete-setup-button"]');
    
    // Vérifier que les messages d'erreur s'affichent
    await expect(page.locator('[data-testid="validation-error"]')).toBeVisible();
  });
});
