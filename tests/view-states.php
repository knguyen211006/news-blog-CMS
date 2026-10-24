<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') exit;
putenv('CMS_CONFIG='.dirname(__DIR__).'/config/test.php');
require dirname(__DIR__).'/app/bootstrap.php';
if (config('db_name') !== 'news_blog_cms_test') throw new RuntimeException('Requires the isolated test database.');
set_error_handler(function(int $level, string $message, string $file, int $line): never {
    throw new ErrorException($message, 0, $level, $file, $line);
});
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SESSION = [];
$_POST = [];
$profile = App\Core\DB::one("SELECT * FROM users WHERE role='ADMIN' AND is_locked=0 LIMIT 1");
if (!$profile) throw new RuntimeException('Seed an admin in the test database first.');
$_SESSION['user_id'] = $profile['id'];
$post = ['id'=>0,'title'=>'','excerpt'=>'','content'=>'','rejection_reason'=>'',
    'category_id'=>null,'cover_image'=>'','version'=>1,'is_featured'=>0];
$common = ['title'=>'Kiểm tra trạng thái rỗng','items'=>[],'total'=>0,'size'=>10,
    'page'=>1,'error'=>null,'categories'=>[],'tags'=>[]];
$cases = [
    ['public/home', ['latest'=>[],'featured'=>[]], ['home-empty-state','home-categories-empty']],
    ['public/articles', ['description'=>'','filters'=>['q'=>'','category'=>'','tag'=>'']], ['article-empty-state']],
    ['posts/list', ['all'=>true,'status'=>''], ['post-list-empty']],
    ['posts/edit', ['post'=>$post,'selected'=>[]], ['post-categories-empty','post-tags-empty']],
    ['admin/taxonomy', ['kind'=>'categories','edit'=>null], ['taxonomy-empty']],
    ['admin/taxonomy', ['kind'=>'tags','edit'=>null], ['taxonomy-empty']],
    ['admin/users', ['q'=>''], ['user-list-empty']],
    ['admin/comments', ['status'=>'pending'], ['admin-comments-empty']],
    ['admin/dashboard', ['counts'=>[],'users'=>0,'pendingComments'=>0,'views'=>0,'popular'=>[],'recent'=>[]], ['admin-popular-empty']],
    ['auth/profile', ['profile'=>$profile,'comments'=>[]], ['profile-comments-empty']],
];
function renderEmpty(string $view, array $data): string {
    extract($data, EXTR_SKIP);
    ob_start();
    try { require ROOT.'/app/Views/'.$view.'.php'; return ob_get_contents(); }
    finally { ob_end_clean(); }
}
$count = 0;
foreach ($cases as [$view,$data,$states]) {
    $html = renderEmpty($view, $data + $common);
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8">'.$html);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);
    foreach ($states as $state) {
        $nodes = $xpath->query('//*[@data-testid="'.$state.'"]');
        if ($nodes->length !== 1) throw new RuntimeException("Missing empty state: $view/$state");
        $node = $nodes->item(0);
        foreach (['.//svg','.//a','.//p'] as $required) {
            if ($xpath->query($required, $node)->length === 0) throw new RuntimeException("Incomplete empty state: $view/$state ($required)");
        }
        ++$count;
    }
    echo "PASS | $view empty data renders without PHP warnings\n";
}
echo "Verified $count empty states. No database records were changed.\n";
