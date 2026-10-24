<div class="auth-layout">
  <aside class="auth-intro">
    <span class="eyebrow">GIA NHẬP CỘNG ĐỒNG</span>
    <h1>Góc nhìn hay<br>bắt đầu từ<br><em>sự tò mò.</em></h1>
    <p>Tạo tài khoản độc giả để bình luận và trả lời. Nếu muốn trở thành tác giả, hãy liên hệ quản trị viên của nhóm.</p>
    <div class="join-note">
      <strong>Mỗi thành viên, một câu chuyện.</strong>
      <p>Chúng tôi tin rằng những trải nghiệm bình thường cũng có thể mang lại điều mới mẻ.</p>
    </div>
  </aside>

  <section class="auth-card" data-testid="auth-register-card">
    <h2>Tạo tài khoản</h2>
    <p>Chỉ mất một phút để bắt đầu.</p>
    <?php require ROOT . '/app/Views/partials/error.php'; ?>

    <form method="post" action="<?= e(url('register')) ?>" data-testid="auth-register-form" data-submit-lock>
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label" for="name">Họ và tên</label>
        <input id="name" name="name" class="form-control" autocomplete="name"
               required minlength="2" maxlength="100"
               value="<?= e(posted('name')) ?>"
               data-testid="auth-register-name-input">
      </div>
      <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input id="email" name="email" type="email" autocomplete="email"
               class="form-control" required maxlength="190"
               value="<?= e(posted('email')) ?>"
               data-testid="auth-register-email-input">
      </div>
      <div class="mb-3">
        <label class="form-label" for="password">Mật khẩu <small>(8–72 ký tự)</small></label>
        <input id="password" name="password" type="password" autocomplete="new-password"
               class="form-control" required minlength="8" maxlength="72"
               data-testid="auth-register-password-input">
      </div>
      <div class="mb-4">
        <label class="form-label" for="password_confirmation">Nhập lại mật khẩu</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
               class="form-control" required minlength="8" maxlength="72"
               data-testid="auth-register-confirm-input">
      </div>
      <button class="btn btn-dark w-100" type="submit" data-testid="auth-register-submit-btn">
        Tạo tài khoản độc giả ↗
      </button>
    </form>
    <p class="auth-switch">Đã có tài khoản? <a href="<?= e(url('login')) ?>">Đăng nhập</a></p>
  </section>
</div>
