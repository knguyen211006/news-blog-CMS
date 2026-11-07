<?php
try { $viewer=current_user(); } catch (Throwable $ignored) { $viewer=null; }
$notice=$_SESSION['flash']??null;unset($_SESSION['flash']);
$route=is_string($_GET['r']??null)?$_GET['r']:'';
$isWorkspace=str_starts_with($route,'admin')||str_starts_with($route,'author');
?>
<!doctype html><html lang="vi"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title??'Trang chủ') ?> · Góc Nhìn</title>
<meta name="description" content="Góc Nhìn — Không gian tin tức và blog của những người thích quan sát, học hỏi và chia sẻ.">
<meta name="color-scheme" content="dark">
<link rel="stylesheet" href="<?= e(asset('assets/vendor/bootstrap/bootstrap.min.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/vendor/pell/pell.min.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
<script src="<?= e(asset('assets/vendor/pell/pell.min.js')) ?>" defer></script>
<script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
</head><body>
<a class="skip-link" href="#main">Đi đến nội dung</a>
<header class="site-header" data-testid="site-header"><div class="topline"><div class="container d-flex justify-content-between"><span>NHẬT KÝ HỌC TẬP &amp; LÀM SẢN PHẨM</span><span><?= date('d.m.Y') ?> · Nhóm 5 thành viên</span></div></div><div class="container header-row">
<a class="brand" href="<?= e(url()) ?>" aria-label="Góc Nhìn - Trang chủ"><span class="brand-mark" aria-hidden="true">g.</span><span>góc nhìn<span class="brand-dot">.</span></span></a>
<nav class="main-nav" aria-label="Điều hướng chính"><a class="<?= $route===''?'active':'' ?>" href="<?= e(url()) ?>">Trang chủ</a><a class="<?= $route==='articles'?'active':'' ?>" href="<?= e(url('articles')) ?>">Khám phá</a></nav>
<div class="account-nav">
<button class="theme-toggle" id="theme-toggle" type="button" data-testid="theme-toggle-btn" aria-label="Sáng — Đổi giao diện" title="Đổi giao diện Sáng / Tối"><span class="theme-toggle__icon" aria-hidden="true">☀</span><span class="theme-toggle__text">Sáng</span></button>
<?php if($viewer): ?>
<a class="profile-link" data-testid="profile-link" href="<?= e(url('profile')) ?>"><?= e(excerpt($viewer['name'],25)) ?></a>
<?php if(in_array($viewer['role'],['ADMIN','AUTHOR'],true)): ?><a class="btn btn-dark btn-sm<?= $isWorkspace ? ' workspace-entry-active' : '' ?>" href="<?= e(url($viewer['role']==='ADMIN'?'admin':'author/posts')) ?>">Tòa soạn ↗</a><?php endif ?>
<form method="post" action="<?= e(url('logout')) ?>"><?= csrf_field() ?><button class="btn btn-link btn-sm" type="submit">Đăng xuất</button></form>
<?php else: ?><a href="<?= e(url('login')) ?>">Đăng nhập</a><a class="btn btn-dark btn-sm" href="<?= e(url('register')) ?>">Tham gia viết tiếp ↗</a><?php endif ?>
</div></div></header>
<?php if($isWorkspace && $viewer): ?><div class="workspace-nav"><nav class="container workspace-bar" aria-label="Tòa soạn" id="ws-nav">
<details class="workspace-menu" id="workspace-menu" open>
<summary class="workspace-menu__toggle">☰ Menu Tòa soạn</summary>
<div class="workspace-menu__list">
<?php if($viewer['role']==='ADMIN'): ?>
<a class="<?= $route==='admin'?'active':'' ?>" href="<?= e(url('admin')) ?>">Tổng quan</a><a class="<?= $route==='admin/posts'?'active':'' ?>" href="<?= e(url('admin/posts')) ?>">Bài viết &amp; duyệt bài</a><a class="<?= ($route==='admin/taxonomy' && ($_GET['kind']??'categories')==='categories')?'active':'' ?>" href="<?= e(url('admin/taxonomy',['kind'=>'categories'])) ?>">Chuyên mục</a><a class="<?= ($route==='admin/taxonomy' && ($_GET['kind']??'')==='tags')?'active':'' ?>" href="<?= e(url('admin/taxonomy',['kind'=>'tags'])) ?>">Thẻ</a><a class="<?= $route==='admin/users'?'active':'' ?>" href="<?= e(url('admin/users')) ?>">Tài khoản</a><a class="<?= $route==='admin/comments'?'active':'' ?>" href="<?= e(url('admin/comments')) ?>">Bình luận</a>
<?php endif ?><a class="<?= $route==='author/posts'?'active':'' ?>" href="<?= e(url('author/posts')) ?>">Bài của tôi</a>
</div>
</details>
<a class="btn btn-dark btn-sm workspace-write-btn" href="<?= e(url('author/edit')) ?>">+ Viết bài</a>
</nav></div><?php endif ?>
<main id="main" class="container page-main">
<?php if($notice): ?><div class="alert alert-<?= e($notice[1]) ?>" role="status" data-testid="app-flash-alert"><?= e($notice[0]) ?></div><?php endif ?>
<?= $content ?>
</main>
<footer class="site-footer"><div class="container footer-grid"><div><a class="brand" href="<?= e(url()) ?>">góc nhìn<span class="brand-dot">.</span></a><p>Ghi chép thực hành lập trình web, cơ sở dữ liệu và chuyện học tập của nhóm.</p></div><div><strong>Chuyên mục &amp; Khám phá</strong><a href="<?= e(url('articles')) ?>">Tất cả bài viết</a><a href="<?= e(url('register')) ?>">Trở thành thành viên</a></div><div><strong>Đồ án CMS đa tác giả</strong><p>Nhóm Nguyễn Khôi Nguyên · 5 thành viên<br>PHP MVC · MySQL · 2026</p></div></div><div class="container footer-bottom">Xây dựng từ những bài học thực tế trên giảng đường.</div></footer>
</body></html>
