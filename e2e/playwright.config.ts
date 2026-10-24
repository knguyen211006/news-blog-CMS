import { defineConfig, devices } from '@playwright/test';
import { baseURL } from './support/env';

export default defineConfig({
  testDir: './tests',
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: 1,
  reporter: [['list'], ['html', { open: 'never' }]],
  use: {
    baseURL, locale: 'vi-VN', colorScheme: 'dark',
    trace: 'on-first-retry', screenshot: 'only-on-failure', video: 'off',
  },
  webServer: {
    command: 'node support/server.cjs', url: baseURL,
    reuseExistingServer: false, timeout: 30_000, stderr: 'ignore',
  },
  projects: [
    { name: 'setup', testMatch: /auth\.setup\.ts/, use: { ...devices['Desktop Chrome'] } },
    {
      name: 'Desktop Chrome', dependencies: ['setup'],
      use: { ...devices['Desktop Chrome'], viewport: { width: 1280, height: 800 } },
    },
    {
      name: 'Mobile Chrome', dependencies: ['setup'],
      use: { ...devices['Pixel 5'], viewport: { width: 375, height: 812 } },
    },
  ],
});
