<section class="page-heading">
  <span class="eyebrow">QUẢN TRỊ CỘNG ĐỒNG</span>
  <h1><?= e($title) ?></h1>
  <p>Tài khoản đăng ký mới luôn là độc giả. Cấp quyền tác giả khi thành viên sẵn sàng viết bài.</p>
</section>

<?php require ROOT . '/app/Views/partials/error.php'; ?>

<form method="get" action="<?= e(config('base_path') . '/index.php') ?>" class="search-panel" data-testid="user-search-form">
  <input type="hidden" name="r" value="admin/users">
  <label class="visually-hidden" for="q">Tên hoặc email</label>
  <input class="form-control" id="q" name="q" maxlength="190"
         value="<?= e($q) ?>" placeholder="Tìm theo tên hoặc email"
         data-testid="user-search-input" aria-label="Tìm kiếm thành viên">
  <button class="btn btn-dark" data-testid="user-search-btn">Tìm kiếm</button>
</form>

<div class="panel table-responsive" data-testid="user-list-panel">
  <table class="table align-middle">
    <thead>
      <tr><th>Thành viên</th><th>Ngày tạo</th><th>Vai trò &amp; trạng thái</th></tr>
    </thead>
    <tbody>
      <?php if (!$items): ?>
        <tr><td colspan="3">
          <div class="empty-state empty-state--compact" data-testid="user-list-empty">
            <div class="empty-state__icon" aria-hidden="true">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <p class="empty-state__desc">Không tìm thấy tài khoản.</p>
            <a class="btn btn-outline-dark" href="<?= e(url('admin/users')) ?>" data-testid="user-empty-reset-btn">Xóa bộ lọc</a>
          </div>
        </td></tr>
      <?php endif ?>
      <?php foreach ($items as $u): ?>
        <tr data-testid="user-row">
          <td>
            <strong><?= e($u['name']) ?></strong>
            <small class="d-block"><?= e($u['email']) ?></small>
          </td>
          <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
          <td>
            <form class="inline-admin-form" method="post" action="<?= e(url('admin/users')) ?>"
                  data-confirm="Cập nhật quyền và trạng thái tài khoản này?"
                  data-testid="user-role-form">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
              <select class="form-select form-select-sm" name="role" aria-label="Vai trò của <?= e($u['name']) ?>">
                <?php foreach (['USER' => 'Độc giả', 'AUTHOR' => 'Tác giả', 'ADMIN' => 'Admin'] as $role => $name): ?>
                  <option value="<?= e($role) ?>" <?= $u['role'] === $role ? 'selected' : '' ?>><?= e($name) ?></option>
                <?php endforeach ?>
              </select>
              <select class="form-select form-select-sm" name="is_locked" aria-label="Trạng thái của <?= e($u['name']) ?>">
                <option value="0" <?= !$u['is_locked'] ? 'selected' : '' ?>>Hoạt động</option>
                <option value="1" <?= $u['is_locked'] ? 'selected' : '' ?>>Đã khóa</option>
              </select>
              <button class="btn btn-sm btn-outline-dark" data-testid="user-role-save-btn">Lưu</button>
            </form>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>

<?php $pagerRoute = 'admin/users'; $pagerParams = ['q' => $q]; require ROOT . '/app/Views/partials/pagination.php'; ?>
