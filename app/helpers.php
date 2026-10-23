<?php
function config(string $key, mixed $default = null): mixed { return $GLOBALS['config'][$key] ?? $default; }
function e(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $route = '', array $params = []): string {
    return config('base_path') . '/' . ($route === '' && !$params ? '' : 'index.php?' . http_build_query(['r' => $route] + $params));
}
function asset(string $path): string { return config('base_path') . '/' . ltrim($path, '/'); }
function redirect(string $route = '', array $params = []): never { header('Location: ' . url($route, $params), true, 303); exit; }
function flash(string $text, string $kind = 'success'): void { $_SESSION['flash'] = [$text, $kind]; }
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . e(App\Core\Security::token()) . '">'; }
function current_user(): ?array { return App\Core\Auth::user(); }
function label_status(string $s): string { return ['draft'=>'Bản nháp','pending'=>'Chờ duyệt','published'=>'Đã xuất bản','rejected'=>'Bị trả','approved'=>'Đã duyệt','hidden'=>'Đã ẩn'][$s] ?? $s; }
function badge(string $s): string { return '<span class="status status-' . e($s) . '">' . e(label_status($s)) . '</span>'; }
function posted(string $key, string $default = ''): string {
    $v=$_POST[$key] ?? $default;
    if(!is_scalar($v)) return $default;
    return str_contains($key,'password') ? (string)$v : trim((string)$v);
}
function number_input(string $key, array $source = []): int {
    $v = ($source ?: $_POST)[$key] ?? 0;
    return is_scalar($v) && ctype_digit((string)$v) ? (int)$v : 0;
}
function excerpt(string $s, int $limit = 130): string { return mb_strimwidth($s,0,$limit,'…','UTF-8'); }
