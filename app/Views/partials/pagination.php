<?php $pages=(int)ceil($total/$size); if($pages>1): ?>
<nav aria-label="Phân trang" class="pagination-bar">
<span><?= (int)$total ?> kết quả · Trang <?= (int)$page ?>/<?= $pages ?></span>
<div class="d-flex gap-2">
<?php if($page>1): ?><a class="btn btn-outline-dark btn-sm" href="<?= e(url($pagerRoute,($pagerParams??[])+['page'=>$page-1])) ?>">← Trước</a><?php endif ?>
<?php for($n=max(1,$page-2);$n<=min($pages,$page+2);$n++): ?><a aria-label="Trang <?= $n ?>" <?= $n===$page?'aria-current="page"':'' ?> class="btn btn-sm <?= $n===$page?'btn-dark':'btn-outline-dark' ?>" href="<?= e(url($pagerRoute,($pagerParams??[])+['page'=>$n])) ?>"><?= $n ?></a><?php endfor ?>
<?php if($page<$pages): ?><a class="btn btn-outline-dark btn-sm" href="<?= e(url($pagerRoute,($pagerParams??[])+['page'=>$page+1])) ?>">Sau →</a><?php endif ?>
</div></nav><?php endif ?>
