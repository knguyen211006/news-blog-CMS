import { expect, type Page } from '@playwright/test';
import { route } from '../support/env';

export class AdminPage {
  constructor(readonly page: Page) {}
  get createPostBtn() { return this.page.getByTestId('cms-new-post-btn'); }
  get postTitleInput() { return this.page.getByRole('textbox', { name: 'Tiêu đề', exact: true }); }
  get excerptInput() { return this.page.getByLabel('Tóm tắt', { exact: true }); }
  get editor() { return this.page.getByRole('textbox', { name: 'Nội dung bài viết', exact: true }); }
  get savePostBtn() { return this.page.getByTestId('post-save-btn'); }
  get form() { return this.page.getByTestId('cms-post-form'); }
  get postList() { return this.page.getByTestId('cms-post-list-panel'); }
  get rows() { return this.page.getByTestId('post-list-row'); }
  get preview() { return this.page.getByTestId('article-preview-banner'); }
  get deleteButton() { return this.page.getByTestId('cms-post-delete-btn'); }
  get deleteForm() { return this.page.getByTestId('cms-post-action-form').filter({ has: this.deleteButton }); }
  get flash() { return this.page.getByTestId('app-flash-alert'); }
  get emptyState() { return this.page.getByTestId('post-list-empty'); }
  get taxonomyRows() { return this.page.getByTestId('taxonomy-row'); }
  get taxonomyDeleteButton() { return this.taxonomyRows.first().getByTestId('taxonomy-delete-btn'); }
  async gotoDashboard() { await this.page.goto(route('admin')); }
  async gotoPosts() { await this.page.goto(route('admin/posts')); }
  async gotoEditor() { await this.page.goto(route('author/edit')); }
  async gotoTaxonomy() { await this.page.goto(route('admin/taxonomy', { kind: 'categories' })); }
  async filterStatus(status: string) {
    await this.page.getByTestId('post-tab-' + status).click();
    await expect(this.page).toHaveURL(url => url.searchParams.get('status') === status);
  }
  async fillPost(title: string, content: string) {
    await this.postTitleInput.fill(title);
    await this.excerptInput.fill('Tóm tắt kiểm thử biểu mẫu và bộ soạn thảo.');
    await this.editor.fill(content);
  }
  async expectPreview(title: string) {
    await expect(this.page).toHaveURL(url => url.searchParams.get('r') === 'author/preview' && !!url.searchParams.get('id'));
    await expect(this.preview).toBeVisible();
    await expect(this.page.getByRole('heading', { level: 1 })).toHaveText(title);
  }
}
