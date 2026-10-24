<section class="section-heading page-heading">
  <div>
    <span class="eyebrow">TÒA SOẠN GÓC NHÌN</span>
    <h1>Tổng quan hôm nay.</h1>
    <p>Theo dõi nội dung, thành viên và những cuộc trò chuyện.</p>
  </div>
  <a class="btn btn-dark" href="<?= e(url('admin/posts', ['status' => 'pending'])) ?>" data-testid="admin-pending-queue-btn">
    Đến hàng chờ duyệt ↗
  </a>
</section>

<div class="stats-grid" data-testid="admin-stats-grid">
  <?php $byStatus = array_column($counts, 'count', 'status');
  foreach (App\Models\Post::STATUSES as $s): ?>
    <a class="stat-card" href="<?= e(url('admin/posts', ['status' => $s])) ?>" data-testid="admin-stat-card-<?= e($s) ?>">
      <span><?= e(label_status($s)) ?></span>
      <strong><?= (int) ($byStatus[$s] ?? 0) ?></strong>
      <small>Xem bài viết ↗</small>
    </a>
  <?php endforeach ?>
</div>

<div class="stats-strip" data-testid="admin-stats-strip">
  <a href="<?= e(url('admin/users')) ?>"><strong><?= $users ?></strong> thành viên</a>
  <a href="<?= e(url('admin/comments')) ?>"><strong><?= $pendingComments ?></strong> bình luận chờ duyệt</a>
  <span><strong><?= number_format($views) ?></strong> lượt xem bài công khai</span>
</div>

<div class="row g-4">
  <section class="col-lg-6">
    <div class="panel" data-testid="admin-popular-panel">
      <div class="section-heading">
        <h2>Được đọc nhiều nhất</h2>
        <span class="eyebrow">TOP 05</span>
      </div>
      <?php if (!$popular): ?>
        <div class="empty-state empty-state--compact" data-testid="admin-popular-empty">
          <div class="empty-state__icon" aria-hidden="true">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 20h.01M7 20v-4"/><path d="M12 20V10"/><path d="M17 20V4"/><path d="M22 20h.01"/></svg>
          </div>
          <p class="empty-state__desc">Chưa có bài công khai.</p>
          <a class="btn btn-dark" href="<?= e(url('admin/posts', ['status' => 'pending'])) ?>" data-testid="admin-popular-review-btn">Xem hàng chờ duyệt</a>
        </div>
      <?php endif ?>
      <?php foreach ($popular as $i => $p): ?>
        <a class="popular-row" href="<?= e(url('article', ['slug' => $p['slug']])) ?>">
          <span class="story-number">0<?= $i + 1 ?></span>
          <strong class="line-clamp-2"><?= e($p['title']) ?></strong>
          <span><?= (int) $p['views'] ?><small>lượt xem</small></span>
        </a>
      <?php endforeach ?>
    </div>
  </section>

  <section class="col-lg-6">
    <div class="panel" data-testid="admin-activity-panel">
      <h2>Nhật ký biên tập</h2>
      <?php if (!$recent): ?>
        <div class="empty-state empty-state--compact">
          <div class="empty-state__icon" aria-hidden="true">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
          </div>
          <p class="empty-state__desc">Chưa có hoạt động.</p>
          <a class="btn btn-outline-dark" href="<?= e(url('author/edit')) ?>" data-testid="admin-activity-create-btn">Viết bài mới</a>
        </div>
      <?php endif ?>
      <div class="activity-list" tabindex="0" role="region" aria-label="Nhật ký biên tập" data-testid="admin-activity-list">
        <?php foreach ($recent as $h): ?>
          <div>
            <strong><?= e($h['actor_name']) ?></strong>
            <span><?= e($h['title']) ?></span>
            <small><?= e(label_status($h['to_status'])) ?> · <?= e($h['created_at']) ?></small>
          </div>
        <?php endforeach ?>
      </div>
    </div>
  </section>
</div>
