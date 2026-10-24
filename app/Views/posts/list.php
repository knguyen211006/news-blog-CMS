<section class="section-heading page-heading">
  <div>
    <span class="eyebrow">KHÔNG GIAN BIÊN TẬP</span>
    <h1><?= e($title) ?></h1>
    <p>Từ ý tưởng đầu tiên đến câu chuyện được xuất bản.</p>
  </div>
  <a class="btn btn-dark" href="<?= e(url('author/edit')) ?>" data-testid="cms-new-post-btn">+ Viết bài mới</a>
</section>

<nav class="filter-tabs" aria-label="Trạng thái bài" data-testid="post-filter-tabs">
  <a class="<?= $status === '' ? 'selected' : '' ?>"
     href="<?= e(url($all ? 'admin/posts' : 'author/posts')) ?>">Tất cả</a>
  <?php foreach (App\Models\Post::STATUSES as $s): ?>
    <a class="<?= $status === $s ? 'selected' : '' ?>"
       href="<?= e(url($all ? 'admin/posts' : 'author/posts', ['status' => $s])) ?>"
       data-testid="post-tab-<?= e($s) ?>"><?= e(label_status($s)) ?></a>
  <?php endforeach ?>
</nav>

<div class="panel table-responsive" data-testid="cms-post-list-panel">
  <table class="table align-middle">
    <thead>
      <tr>
        <th>Bài viết</th><th>Tác giả</th><th>Trạng thái</th>
        <th>Lượt xem</th><th>Cập nhật</th><th>Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$items): ?>
        <tr><td colspan="6">
          <div class="empty-state empty-state--compact" data-testid="post-list-empty">
            <div class="empty-state__icon" aria-hidden="true">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14,2 14,8 20,8"/></svg>
            </div>
            <p class="empty-state__desc">Chưa có bài viết ở trạng thái này.</p>
            <a class="btn btn-dark" href="<?= e(url('author/edit')) ?>" data-testid="cms-empty-create-btn">Viết bài đầu tiên</a>
          </div>
        </td></tr>
      <?php endif ?>
      <?php foreach ($items as $p): ?>
        <tr data-testid="post-list-row">
          <td class="table-title">
            <strong class="line-clamp-2"><?= e($p['title']) ?></strong>
            <small><?= e($p['category_name'] ?? 'Chưa chọn chuyên mục') ?></small>
            <?php if ($p['rejection_reason']): ?>
              <small style="color:var(--danger)"><?= e(excerpt($p['rejection_reason'], 100)) ?></small>
            <?php endif ?>
          </td>
          <td><?= e($p['author_name']) ?></td>
          <td><?= badge($p['status']) ?></td>
          <td><?= (int) $p['views'] ?></td>
          <td><?= date('d/m/Y H:i', strtotime($p['updated_at'])) ?></td>
          <td>
            <a class="btn btn-sm btn-outline-dark"
               href="<?= e(url('author/preview', ['id' => $p['id']])) ?>"
               data-testid="post-action-preview-btn">Xem / xử lý ↗</a>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>

<?php $pagerRoute = $all ? 'admin/posts' : 'author/posts'; $pagerParams = ['status' => $status]; require ROOT . '/app/Views/partials/pagination.php'; ?>
