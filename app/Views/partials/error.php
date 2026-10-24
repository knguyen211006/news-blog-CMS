<?php if (!empty($error)): ?>
<div class="alert-semantic alert-semantic--danger" role="alert" aria-live="assertive" data-testid="flash-error-msg">
  <span class="alert-semantic__icon" aria-hidden="true">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  </span>
  <span class="alert-semantic__text"><?= e($error) ?></span>
</div>
<?php endif ?>
