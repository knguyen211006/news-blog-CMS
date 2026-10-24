import { test, expect } from '@playwright/test';
import { HomePage } from '../pages/HomePage';
import { UiPage } from '../pages/UiPage';
import { required } from '../support/env';

test('home has usable article cards and a dark, accessible responsive layout', async ({ page }) => {
  const home = new HomePage(page);
  await home.goto();
  await expect(home.articleCards.first()).toBeVisible();
  await expect(home.categoryStrip).toBeVisible();
  await new UiPage(page).expectDarkResponsive();
  await new UiPage(page).expectAccessible();
});

test('search returns real SSR results, empty state, and resets filters', async ({ page }) => {
  const home = new HomePage(page);
  await home.gotoArticles();
  const title = await home.firstTitle();
  await home.search(title);
  await expect(home.firstHeading).toHaveText(title);
  await expect(home.emptyState).toHaveCount(0);
  await home.search(required('EMPTY_QUERY'));
  await expect(home.emptyState).toBeVisible();
  await expect(home.results).toContainText('0 bài viết');
  await expect(home.articleCards).toHaveCount(0);
  await new UiPage(page).expectDarkResponsive();
  await new UiPage(page).expectAccessible();
  await home.resetButton.click();
  await expect(page).toHaveURL(url => url.searchParams.get('r') === 'articles' && !url.searchParams.has('q'));
  await expect(home.articleCards.first()).toBeVisible();
});

test('category and tag filters preserve their values through SSR navigation', async ({ page }) => {
  const home = new HomePage(page);
  await home.gotoArticles();
  await home.filterCategory(required('CATEGORY_SLUG'));
  await expect(home.categoryFilter).toHaveValue(required('CATEGORY_SLUG'));
  await expect(home.articleCards.first()).toBeVisible();
  const category = await home.categoryFilter.evaluate((select: HTMLSelectElement) => select.selectedOptions[0].textContent!.trim());
  await home.expectCategoryCards(category);
  await home.gotoArticles();
  await home.filterTag(required('TAG_SLUG'));
  await expect(home.tagFilter).toHaveValue(required('TAG_SLUG'));
  await expect(home.articleCards.first()).toBeVisible();
  await new UiPage(page).expectDarkResponsive();
});

test('tablet 768px does not overflow', async ({ page }) => {
  await page.setViewportSize({ width: 768, height: 1024 });
  await new HomePage(page).goto();
  await new UiPage(page).expectDarkResponsive();
});

test('article cards clamp text and keep a 16:9 cover frame', async ({ page }) => {
  const home = new HomePage(page);
  await home.gotoArticles();
  await expect(home.firstHeading).toHaveCSS('-webkit-line-clamp', '2');
  await expect(home.firstExcerpt).toHaveCSS('-webkit-line-clamp', '3');
  await expect(home.firstCover).toHaveCSS('object-fit', 'cover');
  const ratio = await home.firstCover.evaluate(image => image.getBoundingClientRect().width / image.getBoundingClientRect().height);
  expect(ratio).toBeCloseTo(16 / 9, 2);
});
