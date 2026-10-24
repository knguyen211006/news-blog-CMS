<div class="auth-layout">
  <aside class="auth-intro">
    <span class="eyebrow">CHÀO MỪNG TRỞ LẠI</span>
    <h1>Một nơi để đọc.<br>Một nơi để <em>chia sẻ.</em></h1>
    <p>Đăng nhập để tiếp tục cuộc trò chuyện, lưu dấu góc nhìn và kết nối cùng cộng đồng.</p>
    <img src="<?= e(asset('assets/images/cover-4.svg')) ?>" alt="" class="auth-art">
  </aside>

  <section class="auth-card" data-testid="auth-login-card">
    <h2>Đăng nhập</h2>
    <p>Rất vui được gặp lại bạn.</p>
    <?php require ROOT . '/app/Views/partials/error.php'; ?>

    <form method="post" action="<?= e(url('login')) ?>" data-testid="auth-login-form" data-submit-lock>
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input id="email" name="email" type="email" autocomplete="username"
               class="form-control" required maxlength="190"
               value="<?= e(posted('email')) ?>"
               data-testid="auth-login-email-input"
               aria-label="Địa chỉ email">
      </div>
      <div class="mb-4">
        <label class="form-label" for="password">Mật khẩu</label>
        <input id="password" name="password" type="password" autocomplete="current-password"
               class="form-control" required maxlength="72"
               data-testid="auth-login-password-input"
               aria-label="Mật khẩu đăng nhập">
      </div>
      <button class="btn btn-dark w-100" type="submit" data-testid="auth-login-submit-btn">
        Đăng nhập ↗
      </button>
    </form>
    <p class="auth-switch">Chưa có tài khoản? <a href="<?= e(url('register')) ?>">Đăng ký tại đây</a></p>
  </section>
</div>
