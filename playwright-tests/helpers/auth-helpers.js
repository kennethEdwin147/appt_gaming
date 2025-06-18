// Auth helpers for Playwright tests
import { execSync } from 'child_process';

class AuthHelpers {
  constructor(page) {
    this.page = page;
  }

  // Database operations
  async resetDatabase() {
    try {
      execSync('cd /home/kenneth/dev/gaming-platform && php artisan migrate:fresh --seed --env=testing', { encoding: 'utf-8' });
      return true;
    } catch (error) {
      console.error('Database reset failed:', error.message);
      return false;
    }
  }

  async createUser(userData = {}) {
    const defaults = {
      first_name: 'Test',
      last_name: 'User',
      email: 'test@example.com',
      password: 'password123',
      role: 'customer',
      email_verified_at: 'now()'
    };

    const finalData = { ...defaults, ...userData };
    
    try {
      const command = `cd /home/kenneth/dev/gaming-platform && php artisan tinker --execute="
        \\$user = \\App\\Models\\User::create([
          'first_name' => '${finalData.first_name}',
          'last_name' => '${finalData.last_name}',
          'email' => '${finalData.email}',
          'password' => bcrypt('${finalData.password}'),
          'role' => '${finalData.role}',
          'email_verified_at' => ${finalData.email_verified_at === 'now()' ? 'now()' : 'null'}
        ]);
        echo json_encode(\\$user->toArray());
      "`;
      
      const output = execSync(command, { encoding: 'utf-8' });
      return JSON.parse(output.trim());
    } catch (error) {
      console.error('User creation failed:', error.message);
      return null;
    }
  }

  async createCustomer(overrides = {}) {
    const userData = {
      first_name: 'John',
      last_name: 'Customer',
      email: 'customer@test.com',
      role: 'customer',
      email_verified_at: 'now()',
      ...overrides
    };
    return await this.createUser(userData);
  }

  async createCreatorUser(overrides = {}) {
    const userData = {
      first_name: 'Jane',
      last_name: 'Creator',
      email: 'creator@test.com',
      role: 'creator',
      email_verified_at: 'now()',
      ...overrides
    };
    return await this.createUser(userData);
  }

  async createCompleteCreator(userOverrides = {}, creatorOverrides = {}) {
    const user = await this.createCreatorUser(userOverrides);
    if (!user) return null;

    const creatorData = {
      user_id: user.id,
      bio: 'Expert gaming coach',
      gaming_pseudo: 'ProCoachValo',
      timezone: 'America/Toronto',
      setup_completed: true,
      ...creatorOverrides
    };

    try {
      const command = `cd /home/kenneth/dev/gaming-platform && php artisan tinker --execute="
        \\$creator = \\App\\Models\\creator\\Creator::create([
          'user_id' => ${creatorData.user_id},
          'bio' => '${creatorData.bio}',
          'gaming_pseudo' => '${creatorData.gaming_pseudo}',
          'timezone' => '${creatorData.timezone}',
          'setup_completed' => ${creatorData.setup_completed ? 'true' : 'false'}
        ]);
        echo json_encode(\\$creator->toArray());
      "`;
      
      const output = execSync(command, { encoding: 'utf-8' });
      const creator = JSON.parse(output.trim());
      return { user, creator };
    } catch (error) {
      console.error('Creator creation failed:', error.message);
      return { user, creator: null };
    }
  }

  // Login helpers
  async loginAsCustomer(email = 'customer@test.com', password = 'password123') {
    await this.page.goto('/login');
    await this.page.fill('[data-testid="email-input"]', email);
    await this.page.fill('[data-testid="password-input"]', password);
    await this.page.click('[data-testid="submit-button"]');
    
    // Wait for redirect to dashboard
    await this.page.waitForURL('**/dashboard**');
  }

  async loginAsCreator(email = 'creator@test.com', password = 'password123', expectSetup = false) {
    await this.page.goto('/login');
    await this.page.fill('[data-testid="email-input"]', email);
    await this.page.fill('[data-testid="password-input"]', password);
    await this.page.click('[data-testid="submit-button"]');
    
    if (expectSetup) {
      await this.page.waitForURL('**/creator/setup**');
    } else {
      await this.page.waitForURL('**/creator/dashboard**');
    }
  }

  // Assertion helpers
  async shouldBeOnCustomerDashboard() {
    await this.page.waitForURL('**/dashboard**');
    await this.page.waitForSelector('[data-testid="customer-dashboard"]');
    await this.page.waitForSelector('[data-testid="booking-section"]');
  }

  async shouldBeOnCreatorDashboard() {
    await this.page.waitForURL('**/creator/dashboard**');
    await this.page.waitForSelector('[data-testid="creator-dashboard"]');
    await this.page.waitForSelector('[data-testid="sessions-overview"]');
  }

  async shouldBeOnCreatorSetup() {
    await this.page.waitForURL('**/creator/setup**');
    await this.page.waitForSelector('[data-testid="setup-wizard"]');
  }

  async shouldBeOnLogin() {
    await this.page.waitForURL('**/login**');
    await this.page.waitForSelector('[data-testid="login-form"]');
  }
}

export { AuthHelpers };