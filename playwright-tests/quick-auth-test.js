#!/usr/bin/env node

import { execSync } from 'child_process';

console.log('🔥 Quick Authentication Test Suite');
console.log('Testing critical authentication flows...\n');

// Test rapide - uniquement les tests essentiels
const quickTests = [
  {
    name: 'Login Basic',
    file: 'auth/login.spec.js',
    test: 'should display login form correctly'
  },
  {
    name: 'Registration Flow', 
    file: 'auth/registration.spec.js',
    test: 'should register customer successfully'
  },
  {
    name: 'Email Verification',
    file: 'auth/email-verification.spec.js', 
    test: 'should redirect unverified user to verification page'
  }
];

for (const { name, file, test } of quickTests) {
  console.log(`\n🧪 Testing: ${name}`);
  
  try {
    const command = `npx playwright test "${file}" -g "${test}" --reporter=line`;
    console.log(`   Running: ${command}`);
    
    const output = execSync(command, {
      encoding: 'utf8',
      cwd: '/home/kenneth/dev/gaming-platform',
      timeout: 30000 // 30 secondes max par test
    });
    
    console.log(`   ✅ ${name} passed`);
    
  } catch (error) {
    console.log(`   ❌ ${name} failed`);
    console.log(`   Error: ${error.message.split('\n')[0]}`);
  }
}

console.log('\n✨ Quick test completed!');
console.log('\nTo run full auth test suite:');
console.log('node playwright-tests/run-auth-tests.js');