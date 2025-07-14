import { defineConfig } from '@playwright/test';

export default defineConfig({
  use: {
    baseURL: process.env.BASE_URL || 'http://localhost',
    headless: true,
  },
  testDir: process.env.TESTS_PATH || './tests',
  retries: 0,
  workers: 1,
});
