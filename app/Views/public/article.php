<?php $reader = current_user(); ?>

<?php if ($preview): ?>
<section class="preview-panel" data-testid="article-preview-banner">
  <div>
    <span class="eyebrow">BẢN XEM TRƯỚC · KHÔNG TĂNG LƯỢT XEM</span>
    <p><?= badge($post['status']) ?> · Phiên bản <?= (int) $post['version'] ?></p>
    <?php if ($post['rejection_reason']): ?>
      <div class="alert-semantic alert-semantic--warning" role="alert">
        <span class="alert-semantic__icon" aria-hidden="true">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </span>
        <span class="alert-semantic__text">Lý do trả bài: <?= e($post['rejection_reason']) ?></span>
      </div>
    <?php endif ?>
  </div>
  <?php require ROOT . '/app/Views/partials/post-actions.php'; ?>
</section>
<?php endif ?>

<article class="article-page" data-testid="article-detail-page">
  <header class="article-header">
    <a class="eyebrow" href="<?= e(url('articles', ['category' => $post['category_slug'] ?? ''])) ?>"><?= e($post['category_name']) ?></a>
    <h1><?= e($post['title']) ?></h1>
    <p class="article-excerpt"><?= e($post['excerpt']) ?></p>
    <div class="article-meta" data-testid="article-metadata">
      <span class="avatar" aria-hidden="true"><?= e(mb_substr($post['author_name'], 0, 1)) ?></span>
      <span>
        <strong><?= e($post['author_name']) ?></strong>
        <small><?= $post['published_at'] ? date('d/m/Y H:i', strtotime($post['published_at'])) : 'Chưa xuất bản' ?></small>
      </span>
      <span class="view-count" aria-label="Lượt xem bài viết">
        <?= number_format((int) $post['views']) ?> lượt xem
      </span>
    </div>
  </header>

  <?php if ($post['cover_image']): ?>
    <img class="article-cover" data-testid="article-cover-image" src="<?= e(asset($post['cover_image'])) ?>" alt="<?= e($post['title']) ?>">
  <?php endif ?>

  <div class="article-content" data-testid="article-content-body"><?= $post['content'] ?></div>

    <div class="article-tags" data-testid="article-tags">
      <?php foreach ($tags as $tag): ?>
        <a href="<?= e(url('articles', ['tag' => $tag['slug']])) ?>"># <?= e($tag['name']) ?></a>
      <?php endforeach ?>
    </div>
</article>

<?php if ($preview): ?>
<section class="article-discussion">
  <h2 id="edit-history-heading">Lịch sử biên tập</h2>
  <div class="table-responsive" tabindex="0" role="region" aria-labelledby="edit-history-heading">
    <table class="table" data-testid="article-edit-history-table">
      <thead><tr><th>Thời điểm</th><th>Người thao tác</th><th>Thao tác</th><th>Trạng thái</th></tr></thead>
      <tbody>
        <?php foreach ($history as $h): ?>
          <tr>
            <td><?= e($h['created_at']) ?></td>
            <td><?= e($h['actor_name']) ?></td>
            <td>
              <?= e(['create' => 'Tạo nháp', 'edit' => 'Sửa bài', 'submit' => 'Gửi duyệt', 'reject' => 'Trả bài', 'publish' => 'Xuất bản', 'unpublish' => 'Gỡ về nháp', 'delete' => 'Xóa mềm', 'seed' => 'Dữ liệu demo'][$h['action']] ?? $h['action']) ?>
              <?php if ($h['note']): ?><small class="d-block"><?= e($h['note']) ?></small><?php endif ?>
            </td>
            <td><?= badge($h['to_status']) ?></td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</section>

<?php else: ?>

<section class="article-discussion" id="comments" data-testid="article-comments-section">
  <span class="eyebrow">CÙNG NỐI DÀI CÂU CHUYỆN</span>
  <h2>Góc nhìn của bạn</h2>
  <p class="text-muted">Bình luận được duyệt trước khi hiển thị. Hãy trao đổi lịch sự và tôn trọng nhau.</p>

  <?php if ($reader): ?>
    <form method="post" action="<?= e(url('comments/create')) ?>" class="comment-form" data-testid="comment-submit-form" data-submit-lock>
      <?= csrf_field() ?>
      <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
      <input type="hidden" name="parent_id" value="0">
      <label for="comment-content" class="form-label">Chia sẻ suy nghĩ của bạn</label>
      <textarea class="form-control" id="comment-content" name="content" rows="3" maxlength="2000" required placeholder="Một ý tưởng, một trải nghiệm, một câu hỏi…"></textarea>
      <button class="btn btn-dark mt-3" type="submit" data-testid="comment-submit-btn">Gửi bình luận ↗</button>
    </form>
  <?php else: ?>
    <div class="login-note" role="note">
      <a href="<?= e(url('login')) ?>">Đăng nhập</a> để bình luận và trò chuyện cùng cộng đồng.
    </div>
  <?php endif ?>

  <div class="comment-list" data-testid="comment-list">
    <?php $roots = array_filter($comments, fn($c) => $c['parent_id'] === null); if (!$roots): ?>
      <div class="empty-state empty-state--compact">
        <div class="empty-state__icon" aria-hidden="true">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <p class="empty-state__desc">Chưa có bình luận. Hãy mở đầu cuộc trò chuyện.</p>
        <a class="btn btn-outline-dark" href="#comments" data-testid="article-comment-start-btn">Tham gia thảo luận</a>
      </div>
    <?php endif ?>

    <?php foreach ($roots as $comment): ?>
      <div class="comment" data-testid="comment-item">
        <div class="comment-heading">
          <strong><?= e($comment['user_name']) ?></strong>
          <time><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></time>
        </div>
        <?php if ($comment['status'] === 'hidden'): ?>
          <p class="comment-hidden">Bình luận này đã được ẩn.</p>
        <?php else: ?>
          <p class="plain-comment"><?= e($comment['content']) ?></p>
          <?php if ($comment['status'] === 'pending'): ?><?= badge('pending') ?><?php endif ?>
        <?php endif ?>
        <?php if ($reader && (int) $reader['id'] === (int) $comment['user_id'] && $comment['status'] !== 'hidden'): ?>
          <form method="post" action="<?= e(url('comments/hide')) ?>" data-confirm="Ẩn bình luận của bạn?" data-testid="comment-hide-form">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $comment['id'] ?>">
            <input type="hidden" name="version" value="<?= (int) $comment['version'] ?>">
            <button class="text-button" data-testid="comment-hide-btn">Ẩn bình luận</button>
          </form>
        <?php endif ?>
        <?php if ($reader && $comment['status'] === 'approved'): ?>
          <details class="reply-form">
            <summary>Trả lời</summary>
            <form method="post" action="<?= e(url('comments/create')) ?>" data-submit-lock data-testid="comment-reply-form">
              <?= csrf_field() ?>
              <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
              <input type="hidden" name="parent_id" value="<?= (int) $comment['id'] ?>">
              <label class="form-label" for="reply-<?= (int) $comment['id'] ?>">Trả lời <?= e($comment['user_name']) ?></label>
              <textarea class="form-control" id="reply-<?= (int) $comment['id'] ?>" name="content" rows="2" maxlength="2000" required></textarea>
              <button class="btn btn-dark btn-sm mt-2" data-testid="comment-reply-submit-btn">Gửi trả lời</button>
            </form>
          </details>
        <?php endif ?>

        <?php foreach ($comments as $reply):
          if ((int) $reply['parent_id'] !== (int) $comment['id'] || ($reply['status'] !== 'approved' && !($comment['status'] === 'approved' && $reply['status'] === 'pending' && $reader && (int) $reply['user_id'] === (int) $reader['id']))) continue; ?>
          <div class="comment reply">
            <div class="comment-heading">
              <strong><?= e($reply['user_name']) ?></strong>
              <time><?= date('d/m/Y H:i', strtotime($reply['created_at'])) ?></time>
            </div>
            <p class="plain-comment"><?= e($reply['content']) ?></p>
            <?php if ($reply['status'] === 'pending') echo badge('pending'); ?>
            <?php if ($reader && (int) $reader['id'] === (int) $reply['user_id']): ?>
              <form method="post" action="<?= e(url('comments/hide')) ?>" data-confirm="Ẩn phản hồi của bạn?" data-testid="reply-hide-form">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $reply['id'] ?>">
                <input type="hidden" name="version" value="<?= (int) $reply['version'] ?>">
                <button class="text-button" data-testid="reply-hide-btn">Ẩn phản hồi</button>
              </form>
            <?php endif ?>
          </div>
        <?php endforeach ?>
      </div>
    <?php endforeach ?>
  </div>
</section>
<?php endif ?>
