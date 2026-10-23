<article class="story-card">
<a class="card-cover" href="<?= e(url('article',['slug'=>$story['slug']])) ?>"><img src="<?= e(asset($story['cover_image']?:'assets/images/cover-1.svg')) ?>" alt="" loading="lazy"></a>
<div class="story-body"><div class="story-kicker"><a href="<?= e(url('articles',['category'=>$story['category_slug']??''])) ?>"><?= e($story['category_name']??'Câu chuyện') ?></a><span><?= date('d.m.Y',strtotime($story['published_at'])) ?></span></div>
<h3><a href="<?= e(url('article',['slug'=>$story['slug']])) ?>"><?= e($story['title']) ?></a></h3><p><?= e(excerpt($story['excerpt'],155)) ?></p>
<div class="story-meta"><span><?= e($story['author_name']) ?></span><a href="<?= e(url('article',['slug'=>$story['slug']])) ?>" aria-label="Đọc <?= e($story['title']) ?>">↗</a></div></div>
</article>
