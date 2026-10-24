import { expect, type Page } from '@playwright/test';
import { route } from '../support/env';

export class LoginPage {
  constructor(readonly page: Page) {}
  get form() { return this.page.getByTestId('auth-login-form'); }
  get emailInput() { return this.page.getByRole('textbox', { name: 'Địa chỉ email' }); }
  get passwordInput() { return this.page.getByLabel('Mật khẩu đăng nhập', { exact: true }); }
  get loginBtn() { return this.page.getByTestId('auth-login-submit-btn'); }
  get alertMessage() { return this.page.getByRole('alert').or(this.page.getByRole('status')); }
  async goto() { await this.page.goto(route('login')); }
  async fill(email: string, password: string) {
    await this.emailInput.fill(email);
    await this.passwordInput.fill(password);
  }
  async login(email: string, password: string) {
    await this.fill(email, password);
    await this.loginBtn.click();
  }
  async expectAdminRedirect() {
    await expect(this.page).toHaveURL(url => url.searchParams.get('r') === 'admin');
    await expect(this.page.getByRole('heading', { name: 'Tổng quan hôm nay.' })).toBeVisible();
  }
}
