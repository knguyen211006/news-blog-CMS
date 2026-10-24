<section class="page-heading">
  <span class="eyebrow">THƯ VIỆN CÂU CHUYỆN</span>
  <h1><?= e($title) ?></h1>
  <p><?= e($description) ?></p>
</section>

<form method="get" action="<?= e(config('base_path') . '/index.php') ?>" class="search-panel" data-testid="article-search-form">
  <input type="hidden" name="r" value="articles">
  <div class="search-input">
    <label class="visually-hidden" for="q">Tìm theo tiêu đề hoặc tóm tắt</label>
    <input class="form-control" id="q" name="q"
           value="<?= e($filters['q']) ?>"
           placeholder="Bạn muốn khám phá điều gì?"
           maxlength="200"
           data-testid="article-search-input"
           aria-label="Từ khoá tìm kiếm">
  </div>
  <div>
    <label class="visually-hidden" for="category">Chuyên mục</label>
    <select class="form-select" id="category" name="category" data-testid="article-filter-category">
      <option value="">Tất cả chuyên mục</option>
      <?php foreach ($categories as $c): ?>
        <option value="<?= e($c['slug']) ?>" <?= $filters['category'] === $c['slug'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
      <?php endforeach ?>
    </select>
  </div>
  <div>
    <label class="visually-hidden" for="tag">Thẻ</label>
    <select class="form-select" id="tag" name="tag" data-testid="article-filter-tag">
      <option value="">Tất cả thẻ</option>
      <?php foreach ($tags as $t): ?>
        <option value="<?= e($t['slug']) ?>" <?= $filters['tag'] === $t['slug'] ? 'selected' : '' ?>><?= e($t['name']) ?></option>
      <?php endforeach ?>
    </select>
  </div>
  <button class="btn btn-dark" data-testid="article-search-btn" aria-label="Thực hiện tìm kiếm">Tìm kiếm ↗</button>
</form>

<div class="results-heading" data-testid="article-results-heading">
  <span><?= (int) $total ?> bài viết<?= $filters['q'] !== '' ? ' cho “' . e($filters['q']) . '”' : '' ?></span>
  <a href="<?= e(url('articles')) ?>">Xóa bộ lọc</a>
</div>

<?php if (!$items): ?>
  <div class="empty-state" data-testid="article-empty-state">
    <div class="empty-state__icon" aria-hidden="true">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
    </div>
    <h2 class="empty-state__title">Chưa tìm thấy câu chuyện phù hợp</h2>
    <p class="empty-state__desc">Thử một từ khoá khác hoặc chọn tất cả chuyên mục.</p>
    <a class="btn btn-dark" href="<?= e(url('articles')) ?>" data-testid="article-empty-reset-btn">Xem tất cả bài viết</a>
  </div>
<?php else: ?>
  <div class="story-grid" data-testid="article-list-grid">
    <?php foreach ($items as $story) require ROOT . '/app/Views/partials/card.php'; ?>
  </div>
<?php endif ?>

<?php $pagerRoute = 'articles'; $pagerParams = $filters; require ROOT . '/app/Views/partials/pagination.php'; ?>
