import { type Page } from '@playwright/test';
import { route } from '../support/env';

export class PostPage {
  constructor(readonly page: Page) {}
  get article() { return this.page.getByTestId('article-detail-page'); }
  get articleTitle() { return this.article.getByRole('heading', { level: 1 }); }
  get body() { return this.page.getByTestId('article-content-body'); }
  get cover() { return this.page.getByTestId('article-cover-image'); }
  get metadata() { return this.page.getByTestId('article-metadata'); }
  get discussion() { return this.page.getByTestId('article-comments-section'); }
  get commentForm() { return this.page.getByTestId('comment-submit-form'); }
  get commentInput() { return this.page.getByLabel('Chia sẻ suy nghĩ của bạn'); }
  get submitCommentBtn() { return this.page.getByTestId('comment-submit-btn'); }
  get loginLink() { return this.discussion.getByRole('link', { name: 'Đăng nhập', exact: true }); }
  async goto(slug: string) { await this.page.goto(route('article', { slug })); }
}
