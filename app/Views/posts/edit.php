<section class="page-heading">
  <span class="eyebrow">BÀN VIẾT CỦA BẠN</span>
  <h1><?= e($title) ?></h1>
  <p>Lưu bản nháp để xem trước. Khi sẵn sàng, gửi bài cho Admin duyệt.</p>
</section>

<?php require ROOT . '/app/Views/partials/error.php'; ?>

<?php if ($post['rejection_reason']): ?>
  <div class="alert-semantic alert-semantic--warning" role="alert">
    <span class="alert-semantic__icon" aria-hidden="true">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    </span>
    <span class="alert-semantic__text">Góp ý biên tập: <?= e($post['rejection_reason']) ?></span>
  </div>
<?php endif ?>

<form method="post" action="<?= e(url('author/edit', $post['id'] ? ['id' => $post['id']] : [])) ?>"
      enctype="multipart/form-data" id="post-form"
      data-testid="cms-post-form" data-submit-lock>
  <?= csrf_field() ?>
  <input type="hidden" name="version" value="<?= (int) $post['version'] ?>">
  <input type="hidden" name="MAX_FILE_SIZE" value="2097152">

  <div class="row g-4">
    <section class="col-lg-8">
      <div class="panel">
        <div class="mb-4">
          <label class="form-label" for="title">
            Tiêu đề <span style="color:var(--danger)" aria-hidden="true">*</span>
          </label>
          <input class="form-control title-input" id="title" name="title"
                 required minlength="5" maxlength="200"
                 value="<?= e(posted('title', $post['title'])) ?>"
                 placeholder="Câu chuyện của bạn có tên là…"
                 data-testid="post-title-input">
        </div>
        <div class="mb-4">
          <label class="form-label" for="excerpt">Tóm tắt</label>
          <textarea class="form-control" id="excerpt" name="excerpt" rows="3" maxlength="500"
                    placeholder="Một vài dòng gợi mở nội dung câu chuyện"
                    data-testid="post-excerpt-input"><?= e(posted('excerpt', $post['excerpt'])) ?></textarea>
        </div>
        <div class="mb-2">
          <label class="form-label" for="content">Nội dung bài viết</label>
          <p class="form-text">Hỗ trợ tiêu đề, in đậm, danh sách, trích dẫn và liên kết. Nội dung được lọc an toàn khi lưu.</p>
        </div>
        <textarea id="content" name="content" class="form-control" rows="15" maxlength="100000"
                  data-testid="post-content-input"><?= e($_SERVER['REQUEST_METHOD'] === 'POST' ? App\Core\Security::html(posted('content')) : $post['content']) ?></textarea>
        <div id="rich-editor" class="rich-editor" hidden></div>
      </div>
    </section>

    <aside class="col-lg-4">
      <div class="panel">
        <h2>Thông tin bài viết</h2>
        <div class="mb-4">
          <label class="form-label" for="category_id">Chuyên mục</label>
          <select class="form-select" id="category_id" name="category_id" data-testid="post-category-select">
            <option value="">Chọn chuyên mục</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= (int) $c['id'] ?>" <?= (int) posted('category_id', (string) $post['category_id']) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach ?>
          </select>
          <?php if (empty($categories)): ?>
            <div class="empty-state empty-state--compact" data-testid="post-categories-empty">
              <svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 9v12"/></svg>
              <h3 class="empty-state__title">Chưa có chuyên mục</h3>
              <p class="empty-state__desc">Bạn có thể lưu nháp. Liên hệ quản trị viên để thêm chuyên mục trước khi gửi duyệt.</p>
              <a class="btn btn-outline-dark" href="<?= e(url('author/posts')) ?>" data-testid="post-categories-back-btn">Về danh sách bài</a>
            </div>
          <?php endif ?>
        </div>

        <fieldset class="mb-4">
          <legend class="form-label">Thẻ bài viết</legend>
          <div class="tag-options">
            <?php if (empty($tags)): ?>
              <div class="empty-state empty-state--compact" data-testid="post-tags-empty">
                <svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3h8l10 10-8 8L3 11z"/><circle cx="7" cy="7" r="1"/></svg>
                <h3 class="empty-state__title">Chưa có thẻ bài viết</h3>
                <p class="empty-state__desc">Bạn vẫn có thể lưu bài và bổ sung thẻ sau.</p>
                <a class="btn btn-outline-dark" href="#title" data-testid="post-tags-write-btn">Tiếp tục viết bài</a>
              </div>
            <?php endif ?>
            <?php $picked = $_SERVER['REQUEST_METHOD'] === 'POST' && is_array($_POST['tags'] ?? null) ? array_filter($_POST['tags'], 'is_scalar') : $selected;
            foreach ($tags as $t): ?>
              <label class="tag-checkbox">
                <input type="checkbox" name="tags[]" value="<?= (int) $t['id'] ?>" <?= in_array($t['id'], $picked) ? 'checked' : '' ?>>
                <?= e($t['name']) ?>
              </label>
            <?php endforeach ?>
          </div>
        </fieldset>

        <div class="mb-3">
          <label class="form-label" for="cover">Ảnh đại diện</label>
          <input class="form-control" id="cover" name="cover" type="file"
                 accept="image/jpeg,image/png,image/webp"
                 data-testid="post-cover-input">
          <p class="form-text">JPEG, PNG hoặc WebP · tối đa 2 MB. Bỏ trống để giữ ảnh hiện tại.</p>
          <img id="cover-preview" class="cover-preview"
               src="<?= e(asset($post['cover_image'] ?: 'assets/images/cover-1.svg')) ?>"
               alt="Xem trước ảnh đại diện">
        </div>

        <?php if (current_user()['role'] === 'ADMIN'): ?>
          <label class="form-check mt-3">
            <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                   <?= posted('is_featured', $_SERVER['REQUEST_METHOD'] === 'POST' ? '0' : (string) $post['is_featured']) === '1' ? 'checked' : '' ?>>
            Đánh dấu bài nổi bật
          </label>
        <?php endif ?>

        <hr>
        <p class="form-text">Trước khi gửi duyệt: tiêu đề 5–200 ký tự, tóm tắt tối thiểu 10 ký tự, nội dung tối thiểu 30 ký tự và một chuyên mục.</p>
        <button class="btn btn-dark w-100" type="submit" data-testid="post-save-btn">Lưu &amp; xem trước ↗</button>
        <a class="btn btn-link w-100 mt-2" href="<?= e(url('author/posts')) ?>">Quay về danh sách</a>
      </div>
    </aside>
  </div>
</form>
