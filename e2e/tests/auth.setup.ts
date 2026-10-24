import { test as setup, expect } from '@playwright/test';
import { LoginPage } from '../pages/LoginPage';
import { admin, adminState } from '../support/env';

setup('authenticate admin once and save the PHP session', async ({ page }) => {
  const login = new LoginPage(page);
  await login.goto();
  await login.login(admin.email, admin.password);
  await login.expectAdminRedirect();
  await expect(login.alertMessage).toContainText('Chào mừng');
  await page.context().storageState({ path: adminState });
});
