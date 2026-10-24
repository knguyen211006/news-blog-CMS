import { expect, type Page } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

export class UiPage {
  constructor(readonly page: Page) {}
  get header() { return this.page.getByTestId('site-header').or(this.page.getByRole('banner')); }
  get main() { return this.page.getByRole('main'); }
  async expectButtonTargets() {
    const buttons = this.page.getByRole('button');
    await expect(buttons.first()).toBeVisible();
    for (const button of await buttons.all()) {
      const size = await button.boundingBox();
      expect(size?.width).toBeGreaterThanOrEqual(44);
      expect(size?.height).toBeGreaterThanOrEqual(44);
      await expect(button).toHaveAccessibleName(/.+/);
    }
  }
  async expectDarkResponsive() {
    await expect(this.header).toBeVisible();
    await expect(this.main).toBeVisible();
    await expect.poll(() => this.page.evaluate(() => getComputedStyle(document.body).backgroundColor)).toBe('rgb(11, 15, 25)');
    await expect.poll(() => this.page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth)).toBeLessThanOrEqual(1);
    await expect.poll(() => this.page.evaluate(() => getComputedStyle(document.documentElement).colorScheme)).toBe('dark');
  }
  async expectAccessible() {
    const results = await new AxeBuilder({ page: this.page }).withTags(['wcag2a', 'wcag2aa', 'wcag21aa']).analyze();
    expect(results.violations.map(v => ({ id: v.id, impact: v.impact, nodes: v.nodes.map(n => ({ target: n.target, summary: n.failureSummary })) }))).toEqual([]);
    // Explicitly require a completed contrast audit, not only a theme color.
    expect(results.passes.some(rule => rule.id === 'color-contrast')).toBe(true);
  }
}
