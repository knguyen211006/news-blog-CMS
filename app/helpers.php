<?php
function config(string $key, mixed $default = null): mixed { return $GLOBALS['config'][$key] ?? $default; }
function e(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $route = '', array $params = []): string {
    return config('base_path') . '/' . ($route === '' && !$params ? '' : 'index.php?' . http_build_query(['r' => $route] + $params));
}
function asset(string $path): string {
    $relative = ltrim($path, '/');
    $url = config('base_path') . '/' . $relative;
    if (in_array(strtolower(pathinfo($relative, PATHINFO_EXTENSION)), ['css', 'js', 'svg', 'jpg', 'jpeg', 'png', 'webp'], true)) {
        $file = ROOT . '/' . $relative;
        if (is_file($file)) {
            static $versions = [];
            $versions[$relative] ??= substr(hash_file('sha256', $file), 0, 20);
            $url .= '?v=' . $versions[$relative];
        }
    }
    return $url;
}
function topic_cover(array $post): array {
    static $covers = [
        'hoc-bang-so-do-tu-duy' => 'assets/images/cover-10.svg',
        'website-dau-tien-bang-php' => 'assets/images/cover-1.svg',
        'thiet-ke-kha-nang-doc' => 'assets/images/cover-5.svg',
        'lam-viec-nhom' => 'assets/images/cover-7.svg',
        'nhat-ky-hoc-sql' => 'assets/images/cover-8.svg',
        'nhung-goc-xanh' => 'assets/images/cover-4.svg',
        'tu-sach-trao-doi' => 'assets/images/cover-9.svg',
        'xay-dung-thoi-quen-doc' => 'assets/images/cover-2.svg',
        'ghi-chep-co-he-thong' => 'assets/images/cover-3.svg',
        'buoi-sang-cham' => 'assets/images/cover-6.svg',
        'ke-hoach-doc-sach' => 'assets/images/cover-11.svg',
        'nhip-hoc-cua-minh' => 'assets/images/cover-12.svg',
    ];
    $slug = $post['slug'] ?? '';
    $cover = $post['cover_image'] ?? '';
    if (isset($covers[$slug]) && ($cover === '' || str_starts_with($cover, 'assets/images/cover-'))) {
        $post['cover_image'] = $covers[$slug];
    }
    return $post;
}
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
