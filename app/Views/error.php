<section class="empty-state error-state" data-testid="error-page" role="alert">
  <div class="empty-state__icon" aria-hidden="true">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
  </div>
  <span class="eyebrow">GÓC NHÌN · <?= (int) $code ?></span>
  <h1><?= e($title) ?></h1>
  <p><?= e($message) ?></p>
  <div class="d-flex justify-content-center gap-3">
    <a class="btn btn-dark" href="<?= e(url()) ?>">Về trang chủ</a>
    <?php if ($code === 401): ?>
      <a class="btn btn-outline-dark" href="<?= e(url('login')) ?>">Đăng nhập</a>
    <?php else: ?>
      <button class="btn btn-outline-dark" type="button" data-testid="error-back-btn" data-go-back>Quay lại</button>
    <?php endif ?>
  </div>
</section>
