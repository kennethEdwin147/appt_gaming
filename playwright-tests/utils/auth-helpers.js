export async function loginAs(page, email, password = 'password', rememberMe = false) {
  await page.goto('/login');
  await page.fill('[data-testid="email-input"]', email);
  await page.fill('[data-testid="password-input"]', password);
  
  if (rememberMe) {
    await page.check('[data-testid="remember-me-checkbox"]');
  }
  
  await page.click('[data-testid="submit-button"]');
  await page.waitForURL(/\/dashboard|\/creator|\/email\/verify/);
}

export async function registerUser(page, userData) {
  await page.goto('/register');
  
  // Sélectionner le rôle
  await page.click(`[data-testid="role-${userData.role}"]`);
  
  // Remplir le formulaire
  await page.fill('[data-testid="first-name-input"]', userData.first_name);
  await page.fill('[data-testid="last-name-input"]', userData.last_name);
  await page.fill('[data-testid="email-input"]', userData.email);
  await page.fill('[data-testid="password-input"]', userData.password);
  await page.fill('[data-testid="password-confirmation-input"]', userData.password);
  
  await page.click('[data-testid="submit-button"]');
}
