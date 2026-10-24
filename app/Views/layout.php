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
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;0,6..72,700;1,6..72,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('assets/vendor/bootstrap/bootstrap.min.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/vendor/pell/pell.min.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
<script src="<?= e(asset('assets/vendor/pell/pell.min.js')) ?>" defer></script>
<script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
</head><body>
<a class="skip-link" href="#main">Đi đến nội dung</a>
<div class="topline"><div class="container d-flex justify-content-between"><span>MỖI CÂU CHUYỆN, MỘT GÓC NHÌN</span><span><?= date('d.m.Y') ?> · Blog cộng đồng</span></div></div>
<header class="site-header" data-testid="site-header"><div class="container header-row">
<a class="brand" href="<?= e(url()) ?>" aria-label="Góc Nhìn - Trang chủ"><span class="brand-mark">g.</span><span>góc nhìn<span class="brand-dot">.</span></span></a>
<nav class="main-nav" aria-label="Điều hướng chính"><a class="<?= $route===''?'active':'' ?>" href="<?= e(url()) ?>">Trang chủ</a><a class="<?= $route==='articles'?'active':'' ?>" href="<?= e(url('articles')) ?>">Khám phá</a><a href="<?= e(url('articles',['category'=>'goc-hoc-tap'])) ?>">Góc học tập</a></nav>
<div class="account-nav">
<?php if($viewer): ?>
<a class="profile-link" href="<?= e(url('profile')) ?>"><?= e(excerpt($viewer['name'],25)) ?></a>
<?php if(in_array($viewer['role'],['ADMIN','AUTHOR'],true)): ?><a class="btn btn-dark btn-sm" href="<?= e(url($viewer['role']==='ADMIN'?'admin':'author/posts')) ?>">Tòa soạn ↗</a><?php endif ?>
<form method="post" action="<?= e(url('logout')) ?>"><?= csrf_field() ?><button class="btn btn-link btn-sm" type="submit">Đăng xuất</button></form>
<?php else: ?><a href="<?= e(url('login')) ?>">Đăng nhập</a><a class="btn btn-dark btn-sm" href="<?= e(url('register')) ?>">Tham gia viết tiếp ↗</a><?php endif ?>
</div></div></header>
<?php if($isWorkspace && $viewer): ?><div class="workspace-nav"><nav class="container" aria-label="Tòa soạn">
<?php if($viewer['role']==='ADMIN'): ?>
<a href="<?= e(url('admin')) ?>">Tổng quan</a><a href="<?= e(url('admin/posts')) ?>">Bài viết & duyệt bài</a><a href="<?= e(url('admin/taxonomy',['kind'=>'categories'])) ?>">Chuyên mục</a><a href="<?= e(url('admin/taxonomy',['kind'=>'tags'])) ?>">Thẻ</a><a href="<?= e(url('admin/users')) ?>">Tài khoản</a><a href="<?= e(url('admin/comments')) ?>">Bình luận</a>
<?php endif ?><a href="<?= e(url('author/posts')) ?>">Bài của tôi</a><a href="<?= e(url('author/edit')) ?>">+ Viết bài</a>
</nav></div><?php endif ?>
<main id="main" class="container page-main">
<?php if($notice): ?><div class="alert alert-<?= e($notice[1]) ?>" role="status" data-testid="app-flash-alert"><?= e($notice[0]) ?></div><?php endif ?>
<?= $content ?>
</main>
<footer class="site-footer"><div class="container footer-grid"><div><a class="brand" href="<?= e(url()) ?>">góc nhìn<span class="brand-dot">.</span></a><p>Nơi những câu chuyện được lắng nghe.<br>Và những góc nhìn được nối dài.</p></div><div><strong>Khám phá cùng chúng tôi</strong><a href="<?= e(url('articles')) ?>">Tất cả bài viết</a><a href="<?= e(url('register')) ?>">Trở thành thành viên</a></div><div><strong>Đồ án CMS đa tác giả</strong><p>Nhóm Nguyễn Khôi Nguyên · 5 thành viên<br>PHP MVC · MySQL · 2026</p></div></div><div class="container footer-bottom">Được xây dựng để học hỏi, chia sẻ và kết nối.</div></footer>
</body></html>
