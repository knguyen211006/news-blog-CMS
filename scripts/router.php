<?php
// Safe router for local CLI testing. Apache uses .htaccess instead.
$base='/news-blog-cms';
$path=rawurldecode(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH));
$relative=substr($path,strlen($base));
$root=dirname(__DIR__);
if (!str_starts_with($path,$base.'/') || str_contains($relative,'..') || str_contains($relative,'\\')) { http_response_code(404); exit; }
if ($relative==='/' || $relative==='/index.php') { require $root.'/index.php'; return; }
if (preg_match('~^/(assets/[a-zA-Z0-9/_-]+(?:\.min)?\.(css|js|svg|png|jpg|webp)|uploads/[a-f0-9]{32}\.(jpg|png|webp))$~D',$relative) && is_file($root.$relative)) {
    $ext=pathinfo($relative,PATHINFO_EXTENSION);
    header('X-Content-Type-Options: nosniff');
    header('Content-Type: '.(['css'=>'text/css','js'=>'text/javascript','svg'=>'image/svg+xml','png'=>'image/png','jpg'=>'image/jpeg','webp'=>'image/webp'][$ext]));
    readfile($root.$relative); return;
}
http_response_code(403);
echo 'Forbidden';
