import { test, expect } from '@playwright/test';
import { randomUUID } from 'node:crypto';
import { LoginPage } from '../pages/LoginPage';
import { UiPage } from '../pages/UiPage';
import { admin, invalid, route } from '../support/env';
import { observePending, holdSubmit } from '../support/submit';

test('invalid login locks one request, then renders the PHP error and unlocks', async ({ page }) => {
  const login = new LoginPage(page);
  await login.goto();
  const [local, domain] = invalid.email.split('@');
  await login.fill(local + '+' + randomUUID() + '@' + domain, invalid.password);
  const gate = await holdSubmit(page, 'login');
  const pending = await observePending(login.loginBtn, login.form);
  try {
    await login.loginBtn.click({ noWaitAfter: true });
    const request = await gate.arrived;
    expect(new URLSearchParams(request.postData()!).has('_csrf')).toBe(true);
    await pending.expectLocked();
    expect(gate.requests).toHaveLength(1);
    const response = page.waitForResponse(r => r.request().method() === 'POST' && new URL(r.url()).searchParams.get('r') === 'login');
    gate.release();
    expect((await response).status()).toBe(422);
    await expect(login.alertMessage).toContainText('Email hoặc mật khẩu không đúng');
    await expect(page).toHaveURL(url => url.searchParams.get('r') === 'login');
    await expect(login.loginBtn).toBeEnabled();
    await new UiPage(page).expectAccessible();
  } finally { await gate.dispose(); await pending.dispose(); }
});

test('valid login navigates to the actual admin URL with a success alert', async ({ page }) => {
  const login = new LoginPage(page);
  await login.goto();
  await login.login(admin.email, admin.password);
  await login.expectAdminRedirect();
  await expect(login.alertMessage).toContainText('Chào mừng');
});

test('native required-field validation leaves submit enabled and sends no request', async ({ page }) => {
  const login = new LoginPage(page);
  await login.goto();
  let posts = 0;
  page.on('request', request => { if (request.method() === 'POST') posts++; });
  await login.loginBtn.click();
  await expect(login.emailInput).toBeFocused();
  await expect(login.loginBtn).toBeEnabled();
  await expect(login.form).not.toHaveAttribute('aria-busy', 'true');
  expect(posts).toBe(0);
  await new UiPage(page).expectDarkResponsive();
  await new UiPage(page).expectAccessible();
  await new UiPage(page).expectButtonTargets();
});

test('registration form is accessible on both viewports', async ({ page }) => {
  await page.goto(route('register'));
  await new UiPage(page).expectDarkResponsive();
  await new UiPage(page).expectAccessible();
  await new UiPage(page).expectButtonTargets();
});

test('keyboard focus has a visible indicator', async ({ page }) => {
  const login = new LoginPage(page);
  await login.goto();
  await login.emailInput.focus();
  await page.keyboard.press('Tab');
  await expect(login.passwordInput).toBeFocused();
  await expect(login.passwordInput).toHaveCSS('outline-style', 'solid');
  await expect(login.passwordInput).toHaveCSS('outline-width', '2px');
});
