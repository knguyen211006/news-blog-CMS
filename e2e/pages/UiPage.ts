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
  async expectFileButtonContrast() {
    const input = this.page.getByTestId('post-cover-input');
    for (const hover of [false, true]) {
      if (hover) await input.hover();
      const ratio = await input.evaluate(element => {
        const style = getComputedStyle(element, '::file-selector-button');
        const luminance = (color: string) => {
          const channels = color.match(/[\d.]+/g)!.slice(0, 3).map(Number).map(v => v / 255)
            .map(v => v <= 0.04045 ? v / 12.92 : ((v + 0.055) / 1.055) ** 2.4);
          return channels.reduce((sum, v, i) => sum + v * [0.2126, 0.7152, 0.0722][i], 0);
        };
        const a = luminance(style.color), b = luminance(style.backgroundColor);
        return (Math.max(a, b) + 0.05) / (Math.min(a, b) + 0.05);
      });
      expect(ratio).toBeGreaterThanOrEqual(4.5);
    }
  }
  async expectReadableLinks(links: import('@playwright/test').Locator) {
    await expect(links).not.toHaveCount(0);
    for (const link of await links.all()) {
      await expect(link).toBeVisible();
      const fits = await link.evaluate(element => {
        const range = document.createRange();
        range.selectNodeContents(element);
        const text = range.getBoundingClientRect(), box = element.getBoundingClientRect();
        return text.left >= box.left - 1 && text.right <= box.right + 1;
      });
      expect(fits).toBe(true);
    }
  }
  async expectAccessible() {
    const results = await new AxeBuilder({ page: this.page }).withTags(['wcag2a', 'wcag2aa', 'wcag21aa']).analyze();
    expect(results.violations.map(v => ({ id: v.id, impact: v.impact, nodes: v.nodes.map(n => ({ target: n.target, summary: n.failureSummary })) }))).toEqual([]);
    // Explicitly require a completed contrast audit, not only a theme color.
    expect(results.passes.some(rule => rule.id === 'color-contrast')).toBe(true);
  }
}
