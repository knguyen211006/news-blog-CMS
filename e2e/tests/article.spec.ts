import { test, expect } from '@playwright/test';
import { HomePage } from '../pages/HomePage';
import { PostPage } from '../pages/PostPage';
import { UiPage } from '../pages/UiPage';

test('article detail renders content, metadata, cover and login prompt', async ({ page }) => {
  const home = new HomePage(page);
  const article = new PostPage(page);
  await home.gotoArticles();
  const title = await home.openFirstArticle();
  await expect(article.articleTitle).toHaveText(title);
  await expect(article.body).toBeVisible();
  await expect(article.body).not.toBeEmpty();
  await expect(article.metadata).toContainText('lượt xem');
  await expect(article.metadata).toHaveCSS('font-variant-numeric', 'tabular-nums');
  await expect(article.cover).toBeVisible();
  await expect(article.cover).toHaveCSS('aspect-ratio', '16 / 9');
  await expect(article.cover).toHaveCSS('object-fit', 'cover');
  await expect(article.loginLink).toBeVisible();
  await expect(article.commentForm).toHaveCount(0);
  await new UiPage(page).expectDarkResponsive();
  await new UiPage(page).expectAccessible();
  await article.loginLink.click();
  await expect(page).toHaveURL(url => url.searchParams.get('r') === 'login');
});
