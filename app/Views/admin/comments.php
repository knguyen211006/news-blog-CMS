<section class="page-heading">
  <span class="eyebrow">CHĂM SÓC CUỘC TRÒ CHUYỆN</span>
  <h1><?= e($title) ?></h1>
  <p>Bình luận được hiển thị dưới dạng văn bản thuần. Ẩn bình luận gốc vẫn giữ các phản hồi đã duyệt.</p>
</section>

<nav class="filter-tabs" aria-label="Trạng thái bình luận" data-testid="comment-filter-tabs">
  <?php foreach (['pending', 'approved', 'hidden'] as $s): ?>
    <a class="<?= $status === $s ? 'selected' : '' ?>"
       href="<?= e(url('admin/comments', ['status' => $s])) ?>"
       data-testid="comment-tab-<?= e($s) ?>"><?= e(label_status($s)) ?></a>
  <?php endforeach ?>
</nav>

<div class="panel" data-testid="admin-comments-panel">
  <?php if (!$items): ?>
    <div class="empty-state" data-testid="admin-comments-empty">
      <div class="empty-state__icon" aria-hidden="true">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="9" y1="10" x2="15" y2="10"/><line x1="9" y1="14" x2="13" y2="14"/></svg>
      </div>
      <h2 class="empty-state__title">Không có bình luận</h2>
      <p class="empty-state__desc">Không có bình luận ở trạng thái này.</p>
      <a class="btn btn-outline-dark" href="<?= e(url('admin')) ?>" data-testid="admin-comments-dashboard-btn">Về tổng quan</a>
    </div>
  <?php endif ?>

  <?php foreach ($items as $c): ?>
    <article class="moderation-comment" data-testid="admin-comment-item">
      <div class="comment-heading">
        <strong><?= e($c['user_name']) ?> <?= $c['parent_id'] ? '· Trả lời #' . (int) $c['parent_id'] : '' ?></strong>
        <?= badge($c['status']) ?>
      </div>
      <p class="plain-comment"><?= e($c['content']) ?></p>
      <small>Bài: <?= e($c['title']) ?> · <?= e($c['created_at']) ?></small>
      <div class="mt-3">
        <form class="d-flex gap-2" method="post" action="<?= e(url('admin/comments')) ?>" data-testid="admin-comment-action-form">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
          <input type="hidden" name="version" value="<?= (int) $c['version'] ?>">
          <?php if ($c['status'] !== 'approved'): ?>
            <button class="btn btn-success btn-sm" name="status" value="approved" data-testid="admin-comment-approve-btn">Duyệt hiển thị</button>
          <?php endif ?>
          <?php if ($c['status'] !== 'hidden'): ?>
            <button class="btn btn-outline-danger btn-sm" name="status" value="hidden" data-testid="admin-comment-hide-btn">Ẩn bình luận</button>
          <?php endif ?>
        </form>
      </div>
    </article>
  <?php endforeach ?>
</div>

<?php $pagerRoute = 'admin/comments'; $pagerParams = ['status' => $status]; require ROOT . '/app/Views/partials/pagination.php'; ?>
