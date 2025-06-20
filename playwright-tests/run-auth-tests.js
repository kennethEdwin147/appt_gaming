#!/usr/bin/env node

import { execSync } from 'child_process';

const tests = [
  'auth/login.spec.js',
  'auth/registration.spec.js', 
  'auth/email-verification.spec.js',
  'auth/role-based-auth.spec.js',
  'auth/session-management.spec.js'
];

console.log('🚀 Running comprehensive authentication tests...\n');

let totalTests = 0;
let passedTests = 0;
let failedTests = 0;

for (const test of tests) {
  console.log(`\n📝 Running ${test}...`);
  
  try {
    const output = execSync(`npx playwright test ${test} --reporter=json`, {
      encoding: 'utf8',
      cwd: '/home/kenneth/dev/gaming-platform'
    });
    
    const result = JSON.parse(output);
    const testCount = result.stats.total;
    const passed = result.stats.passed;
    const failed = result.stats.failed;
    
    totalTests += testCount;
    passedTests += passed;
    failedTests += failed;
    
    console.log(`✅ ${test}: ${passed}/${testCount} tests passed`);
    
    if (failed > 0) {
      console.log(`❌ ${failed} tests failed in ${test}`);
      result.tests.forEach(test => {
        if (test.status === 'failed') {
          console.log(`   - ${test.title}: ${test.error?.message || 'Unknown error'}`);
        }
      });
    }
    
  } catch (error) {
    console.log(`❌ ${test}: Failed to run tests`);
    console.log(`   Error: ${error.message}`);
    failedTests++;
  }
}

console.log('\n📊 Authentication Tests Summary:');
console.log(`   Total: ${totalTests} tests`);
console.log(`   Passed: ${passedTests} ✅`);
console.log(`   Failed: ${failedTests} ❌`);
console.log(`   Success Rate: ${totalTests > 0 ? Math.round((passedTests/totalTests) * 100) : 0}%`);

if (failedTests === 0) {
  console.log('\n🎉 All authentication tests passed!');
  process.exit(0);
} else {
  console.log('\n⚠️  Some tests failed. Please check the output above.');
  process.exit(1);
}