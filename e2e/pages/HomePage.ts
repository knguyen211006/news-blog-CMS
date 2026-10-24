import { expect, type Page } from '@playwright/test';
import { route } from '../support/env';

export class HomePage {
  constructor(readonly page: Page) {}
  get categoryStrip() { return this.page.getByRole('navigation', { name: 'Chuyên mục', exact: true }); }
  get articleCards() { return this.page.getByTestId('article-card-item'); }
  get emptyState() { return this.page.getByTestId('article-empty-state'); }
  get resetButton() { return this.page.getByTestId('article-empty-reset-btn'); }
  get searchInput() { return this.page.getByRole('textbox', { name: 'Từ khoá tìm kiếm' }); }
  get searchButton() { return this.page.getByRole('button', { name: 'Thực hiện tìm kiếm' }); }
  get categoryFilter() { return this.page.getByLabel('Chuyên mục', { exact: true }); }
  get tagFilter() { return this.page.getByLabel('Thẻ', { exact: true }); }
  get results() { return this.page.getByTestId('article-results-heading'); }
  get firstHeading() { return this.articleCards.first().getByRole('heading'); }
  get firstExcerpt() { return this.articleCards.first().getByTestId('article-card-excerpt'); }
  get firstCover() { return this.articleCards.first().getByTestId('article-card-cover-image'); }
  async goto() { await this.page.goto('./'); }
  async gotoArticles() { await this.page.goto(route('articles')); }
  async search(query: string) {
    await this.searchInput.fill(query);
    await this.searchButton.click();
    await expect(this.page).toHaveURL(url => url.searchParams.get('r') === 'articles' && url.searchParams.get('q') === query);
  }
  async filterCategory(value: string) {
    await this.categoryFilter.selectOption(value);
    await this.searchButton.click();
    await expect(this.page).toHaveURL(url => url.searchParams.get('category') === value);
  }
  async filterTag(value: string) {
    await this.tagFilter.selectOption(value);
    await this.searchButton.click();
    await expect(this.page).toHaveURL(url => url.searchParams.get('tag') === value);
  }
  async openFirstArticle() {
    const title = await this.firstTitle();
    await this.articleCards.first().getByTestId('article-card-read-btn').click();
    await expect(this.page).toHaveURL(url => url.searchParams.get('r') === 'article');
    return title;
  }
  async firstTitle() { return this.articleCards.first().getByRole('heading').innerText(); }
  async expectCategoryCards(category: string) {
    for (const card of await this.articleCards.all()) {
      await expect(card.getByRole('link', { name: category, exact: true })).toBeVisible();
    }
  }
}
