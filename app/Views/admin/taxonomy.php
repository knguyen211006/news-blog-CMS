<section class="page-heading">
  <span class="eyebrow">SẮP XẾP NỘI DUNG</span>
  <h1><?= e($title) ?></h1>
  <p><?= $kind === 'categories' ? 'Chuyên mục đang có bài, kể cả bài xóa mềm, sẽ được giữ lại.' : 'Xóa thẻ chỉ gỡ liên kết thẻ; các bài viết vẫn được giữ nguyên.' ?></p>
</section>

<?php require ROOT . '/app/Views/partials/error.php'; ?>

<div class="row g-4">
  <section class="col-lg-4">
    <div class="panel" data-testid="taxonomy-form-panel">
      <h2><?= $edit ? 'Sửa thông tin' : 'Thêm mới' ?></h2>
      <form method="post" action="<?= e(url('admin/taxonomy', ['kind' => $kind])) ?>" data-testid="taxonomy-save-form" data-submit-lock>
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <input type="hidden" name="action" value="save">
        <div class="mb-3">
          <label for="name" class="form-label">Tên</label>
          <input class="form-control" id="name" name="name" value="<?= e($edit['name'] ?? '') ?>" minlength="2" maxlength="100" required>
        </div>
        <div class="mb-3">
          <label for="slug" class="form-label">Slug</label>
          <input class="form-control" id="slug" name="slug" value="<?= e($edit['slug'] ?? '') ?>" maxlength="180" pattern="[a-z0-9]+(-[a-z0-9]+)*">
          <p class="form-text">Để trống để tạo từ tên. Ví dụ: cong-nghe.</p>
        </div>
        <?php if ($kind === 'categories'): ?>
          <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea class="form-control" id="description" name="description" maxlength="500" rows="3"><?= e($edit['description'] ?? '') ?></textarea>
          </div>
        <?php endif ?>
        <button class="btn btn-dark" type="submit" data-testid="taxonomy-save-btn">Lưu thông tin</button>
        <?php if ($edit): ?>
          <a class="btn btn-link" href="<?= e(url('admin/taxonomy', ['kind' => $kind])) ?>">Hủy sửa</a>
        <?php endif ?>
      </form>
    </div>
  </section>

  <section class="col-lg-8">
    <div class="panel table-responsive" data-testid="taxonomy-list-panel">
      <table class="table align-middle">
        <thead>
          <tr><th>Tên</th><th>Slug</th><th>Thao tác</th></tr>
        </thead>
        <tbody>
          <?php if (!$items): ?>
            <tr><td colspan="3">
              <div class="empty-state empty-state--compact" data-testid="taxonomy-empty">
                <div class="empty-state__icon" aria-hidden="true">
                  <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </div>
                <h2 class="empty-state__title">Chưa có dữ liệu</h2>
                <p class="empty-state__desc">Thêm mục đầu tiên để sắp xếp nội dung.</p>
                <a class="btn btn-dark" href="#name" data-testid="taxonomy-empty-create-btn">Thêm mục đầu tiên</a>
              </div>
            </td></tr>
          <?php endif ?>
          <?php foreach ($items as $item): ?>
            <tr data-testid="taxonomy-row">
              <td>
                <a href="<?= e(url('articles', [$kind === 'categories' ? 'category' : 'tag' => $item['slug']])) ?>"><?= e($item['name']) ?></a>
                <?php if ($kind === 'categories'): ?><small class="d-block"><?= e($item['description']) ?></small><?php endif ?>
              </td>
              <td><?= e($item['slug']) ?></td>
              <td>
                <div class="d-flex gap-2">
                  <a class="btn btn-outline-dark btn-sm" href="<?= e(url('admin/taxonomy', ['kind' => $kind, 'edit' => $item['id']])) ?>">Sửa</a>
                  <form method="post" action="<?= e(url('admin/taxonomy', ['kind' => $kind])) ?>" data-confirm="Xóa mục này?" data-testid="taxonomy-delete-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                    <button class="btn btn-outline-danger btn-sm" name="action" value="delete" data-testid="taxonomy-delete-btn">Xóa</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
