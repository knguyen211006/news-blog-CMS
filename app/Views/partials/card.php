<article class="story-card" data-testid="article-card-item">
  <a class="card-cover" href="<?= e(url('article', ['slug' => $story['slug']])) ?>" tabindex="-1" aria-hidden="true">
    <div class="card-cover__frame">
      <img src="<?= e(asset($story['cover_image'] ?: 'assets/images/cover-1.svg')) ?>" alt="" loading="lazy" data-testid="article-card-cover-image">
    </div>
  </a>
  <div class="story-body">
    <div class="story-kicker">
      <a href="<?= e(url('articles', ['category' => $story['category_slug'] ?? ''])) ?>"><?= e($story['category_name'] ?? 'Câu chuyện') ?></a>
      <span><?= date('d.m.Y', strtotime($story['published_at'])) ?></span>
    </div>
    <h3 class="story-body__title line-clamp-2">
      <a href="<?= e(url('article', ['slug' => $story['slug']])) ?>"><?= e($story['title']) ?></a>
    </h3>
    <p class="story-body__excerpt line-clamp-3" data-testid="article-card-excerpt"><?= e(excerpt($story['excerpt'], 155)) ?></p>
    <div class="story-meta">
      <span><?= e($story['author_name']) ?></span>
      <a href="<?= e(url('article', ['slug' => $story['slug']])) ?>"
         aria-label="Đọc bài: <?= e($story['title']) ?>"
         class="story-meta__read-link" data-testid="article-card-read-btn">Đọc tiếp ↗</a>
    </div>
  </div>
</article>
