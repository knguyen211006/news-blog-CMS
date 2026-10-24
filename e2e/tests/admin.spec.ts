import { test, expect } from '@playwright/test';
import { randomUUID } from 'node:crypto';
import { AdminPage } from '../pages/AdminPage';
import { PostPage } from '../pages/PostPage';
import { UiPage } from '../pages/UiPage';
import { adminState, route } from '../support/env';
import { observePending, holdSubmit } from '../support/submit';

test.use({ storageState: adminState });

test('admin list and status filtering use the saved PHP session', async ({ page }) => {
  const admin = new AdminPage(page);
  await admin.gotoPosts();
  await expect(admin.postList).toBeVisible();
  await expect(admin.rows.first()).toBeVisible();
  await expect(admin.createPostBtn).toBeVisible();
  await admin.filterStatus('published');
  await expect(admin.rows.first()).toContainText('Đã xuất bản');
  await new UiPage(page).expectDarkResponsive();
  await new UiPage(page).expectAccessible();
});

test('save editor content then delete only the created draft, preserving named submitter', async ({ page }) => {
  const admin = new AdminPage(page);
  const article = new PostPage(page);
  const title = 'E2E UI ' + randomUUID();
  const content = 'Nội dung kiểm thử thực tế của bộ soạn thảo, được giữ nguyên khi gửi form.';
  await admin.gotoEditor();
  await admin.fillPost(title, content);
  const saveGate = await holdSubmit(page, 'author/edit');
  const savePending = await observePending(admin.savePostBtn, admin.form);
  try {
    await admin.savePostBtn.click({ noWaitAfter: true });
    await saveGate.arrived;
    await savePending.expectLocked();
    saveGate.release();
    await admin.expectPreview(title);
    await expect(article.body).toContainText(content);
    const history = page.getByRole('region', { name: 'Lịch sử biên tập' });
    await history.focus();
    await expect(history).toBeFocused();
    await page.keyboard.press('Tab');
    await page.keyboard.press('Shift+Tab');
    await expect(history).toBeFocused();
    await new UiPage(page).expectAccessible();
  } finally { await saveGate.dispose(); await savePending.dispose(); }

  const deleteGate = await holdSubmit(page, 'author/action');
  const deletePending = await observePending(admin.deleteButton, admin.deleteForm);
  try {
    page.once('dialog', dialog => dialog.accept());
    await admin.deleteButton.click({ noWaitAfter: true });
    const request = await deleteGate.arrived;
    expect(new URLSearchParams(request.postData()!).get('action')).toBe('delete');
    await deletePending.expectLocked();
    expect(deleteGate.requests).toHaveLength(1);
    deleteGate.release();
    await expect(page).toHaveURL(url => url.searchParams.get('r') === 'admin/posts');
    await expect(admin.flash).toBeVisible();
    await expect(admin.rows.filter({ hasText: title })).toHaveCount(0);
  } finally { await deleteGate.dispose(); await deletePending.dispose(); }
});

test('cancelling confirmation leaves the action available and sends no POST', async ({ page }) => {
  const admin = new AdminPage(page);
  await admin.gotoTaxonomy();
  let posts = 0;
  page.on('request', request => { if (request.method() === 'POST') posts++; });
  page.once('dialog', dialog => dialog.dismiss());
  await admin.taxonomyDeleteButton.click();
  await expect(admin.taxonomyDeleteButton).toBeEnabled();
  expect(posts).toBe(0);
});

for (const [name, target, params] of [
  ['dashboard', 'admin', {}],
  ['editor', 'author/edit', {}],
  ['categories', 'admin/taxonomy', { kind: 'categories' }],
  ['tags', 'admin/taxonomy', { kind: 'tags' }],
  ['users', 'admin/users', {}],
  ['comments', 'admin/comments', {}],
  ['profile', 'profile', {}],
] as const) {
  test(name + ' is responsive and passes accessibility/contrast checks', async ({ page }) => {
    await page.goto(route(target, params));
    await new UiPage(page).expectDarkResponsive();
    await new UiPage(page).expectAccessible();
    await new UiPage(page).expectButtonTargets();
    await new UiPage(page).expectReadableLinks(page.getByTestId('profile-link'));
    if (name === 'editor') await new UiPage(page).expectFileButtonContrast();
  });
}
