<section class="home-intro">
  <div>
    <span class="eyebrow">TẠP CHÍ CỦA NHỮNG ĐIỀU ĐÁNG KỂ</span>
    <h1>Đọc một câu chuyện.<br>Mở thêm một <em>góc nhìn.</em></h1>
  </div>
  <p>Từ công nghệ đến đời sống, từ những điều nhỏ bé đến ý tưởng lớn. Tìm cảm hứng trong những câu chuyện của cộng đồng.</p>
</section>

<nav class="category-strip" aria-label="Chuyên mục" data-testid="category-strip">
  <a class="selected" href="<?= e(url('articles')) ?>">Tất cả câu chuyện <span>↗</span></a>
  <?php foreach ($categories as $c): ?>
    <a href="<?= e(url('articles', ['category' => $c['slug']])) ?>">
      <?= e($c['name']) ?><span><?= (int) $c['post_count'] ?></span>
    </a>
  <?php endforeach ?>
</nav>
<?php if (empty($categories)): ?>
  <div class="empty-state empty-state--compact" data-testid="home-categories-empty">
    <svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 9v12"/></svg>
    <h2 class="empty-state__title">Chưa có chuyên mục</h2>
    <p class="empty-state__desc">Bạn vẫn có thể khám phá các câu chuyện mới nhất.</p>
    <a class="btn btn-outline-dark" href="<?= e(url('articles')) ?>" data-testid="home-categories-browse-btn">Xem bài viết</a>
  </div>
<?php endif ?>

<?php if ($featured): $lead = $featured[0]; ?>
<section class="featured-grid" aria-label="Bài viết nổi bật">
  <article class="lead-story" data-testid="home-featured-article">
    <a class="lead-image" href="<?= e(url('article', ['slug' => $lead['slug']])) ?>">
      <img src="<?= e(asset($lead['cover_image'] ?: 'assets/images/cover-1.svg')) ?>" alt="<?= e($lead['title']) ?>">
      <span class="feature-label">LỰA CHỌN CỦA BIÊN TẬP VIÊN</span>
    </a>
    <div class="lead-content">
      <span class="eyebrow"><?= e($lead['category_name']) ?> · <?= date('d.m.Y', strtotime($lead['published_at'])) ?></span>
      <h2 class="line-clamp-2"><a href="<?= e(url('article', ['slug' => $lead['slug']])) ?>"><?= e($lead['title']) ?></a></h2>
      <p class="line-clamp-3"><?= e($lead['excerpt']) ?></p>
      <div class="story-meta">
        <span>Bởi <?= e($lead['author_name']) ?></span>
        <a href="<?= e(url('article', ['slug' => $lead['slug']])) ?>">Đọc câu chuyện ↗</a>
      </div>
    </div>
  </article>

  <aside class="featured-aside">
    <span class="eyebrow">ĐÁNG CHÚ Ý</span>
    <?php foreach (array_slice($featured, 1) as $i => $story): ?>
      <article class="side-story" data-testid="home-side-article">
        <span class="story-number">0<?= $i + 1 ?></span>
        <div>
          <span class="eyebrow"><?= e($story['category_name']) ?></span>
          <h3 class="line-clamp-2"><a href="<?= e(url('article', ['slug' => $story['slug']])) ?>"><?= e($story['title']) ?></a></h3>
          <p class="line-clamp-3"><?= e(excerpt($story['excerpt'], 105)) ?></p>
          <a class="text-link" href="<?= e(url('article', ['slug' => $story['slug']])) ?>">Đọc tiếp ↗</a>
        </div>
      </article>
    <?php endforeach ?>
    <div class="join-note">
      <span class="eyebrow">CÂU CHUYỆN TIẾP THEO</span>
      <h3>Có thể bắt đầu<br>từ chính bạn.</h3>
      <p>Đăng ký để cùng trao đổi và chia sẻ góc nhìn của mình.</p>
      <a href="<?= e(url('register')) ?>">Tham gia cộng đồng →</a>
    </div>
  </aside>
</section>
<?php endif ?>

<section class="latest-section">
  <div class="section-heading">
    <div>
      <span class="eyebrow">MỚI TRÊN GÓC NHÌN</span>
      <h2>Những câu chuyện vừa lên trang</h2>
    </div>
    <a class="text-link" href="<?= e(url('articles')) ?>">Xem tất cả ↗</a>
  </div>
  <?php if (!$latest): ?>
    <div class="empty-state" data-testid="home-empty-state">
      <div class="empty-state__icon" aria-hidden="true">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
      </div>
      <h2 class="empty-state__title">Chưa có bài viết nào</h2>
      <p class="empty-state__desc">Hẹn gặp bạn sớm! Những câu chuyện đang trên đường tới.</p>
      <a class="btn btn-dark" href="<?= e(url('register')) ?>" data-testid="home-empty-join-btn">Tham gia cộng đồng</a>
    </div>
  <?php else: ?>
    <div class="story-grid" data-testid="home-article-grid">
      <?php foreach ($latest as $story) require ROOT . '/app/Views/partials/card.php'; ?>
    </div>
  <?php endif ?>
</section>
