<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit;
$base=rtrim($argv[1]??'http://localhost/news-blog-cms/','/').'/';
if(!preg_match('~^http://(localhost|127\.0\.0\.1)(:\d+)?/news-blog-cms/$~D',$base))exit("Only local CMS URLs are allowed.\n");
$tests=[
 ''=>200,
 'index.php?r=articles'=>200,
 'index.php?r=articles&category=cong-nghe'=>200,
 'index.php?r=articles&tag=php'=>200,
 'index.php?r=articles&page=2'=>200,
 'index.php?r=article&slug=xay-dung-thoi-quen-doc'=>200,
 'index.php?r=article&slug=nhat-ky-hoc-sql'=>404,
 'index.php?r=login'=>200,
 'index.php?r=register'=>200,
 'index.php?r=admin'=>303,
 'assets/css/app.css'=>200,
 'assets/js/app.js'=>200,
 'assets/vendor/bootstrap/bootstrap.min.css'=>200,
 'assets/vendor/pell/pell.min.js'=>200,
 'assets/vendor/pell/pell.min.css'=>200,
 'assets/images/cover-1.svg'=>200,
 'assets/images/cover-2.svg'=>200,
 'assets/images/cover-3.svg'=>200,
 'assets/images/cover-4.svg'=>200,
 'config/local.php'=>403,
 'config/example.php'=>403,
 'config/routes.php'=>403,
 'database/news_blog_cms.sql'=>403,
 'storage/app.log'=>403,
 'app/bootstrap.php'=>403,
 'vendor/htmlpurifier/library/HTMLPurifier.php'=>403,
 'scripts/install.php'=>403,
 'tests/acceptance.php'=>403,
 '.git/config'=>403,
 '.env'=>403,
 'README.md'=>403,
 'uploads/fake.php'=>403,
 'config/test.php'=>403,
 'database/schema.sql'=>403,
 'storage/sessions/'=>403,
 'app/Models/Post.php'=>403,
 'e2e/.env'=>403,
 'e2e/.auth/admin.json'=>403,
 '.htaccess'=>403,
];
$rows=[];$failed=0;$root=dirname(__DIR__);
foreach($tests as $path=>$expect){
 $ch=curl_init($base.$path);
 $location='';
 curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_TIMEOUT=>10,
  CURLOPT_HEADERFUNCTION=>static function($curl,string $header)use(&$location):int{
   if(stripos($header,'Location:')===0)$location=trim(substr($header,9));
   return strlen($header);
  }]);
 $body=curl_exec($ch);$status=(int)curl_getinfo($ch,CURLINFO_RESPONSE_CODE);$error=curl_error($ch);curl_close($ch);
 $ok=$status===$expect&&$error==='';
 if($path==='index.php?r=admin')$ok=$ok&&$location==='/news-blog-cms/index.php?r=login';
 if(!$ok)$failed++;
 $rows[]=[$path?:'/',$expect,$status,$ok?'PASS':'FAIL'];
 echo ($ok?'PASS':'FAIL')." $path expected=$expect actual=$status".($location?' location='.$location:'').($error?' '.$error:'').PHP_EOL;
}
$md="# Kiểm tra URL và tài nguyên sau bàn giao\n\nThời điểm: ".date(DATE_ATOM)."\n\nĐích kiểm tra: máy chủ PHP cục bộ tạm, dưới đường dẫn `/news-blog-cms/`.\n\n".(count($rows)-$failed)." PASS / $failed FAIL. Kiểm thử HTTP này không xác nhận bố cục hoặc tương tác JavaScript bằng trình duyệt; không xác minh Apache .htaccess.\n\n| Đường dẫn | Mong đợi | HTTP thực tế | Kết quả |\n|---|---|---|---|\n";
foreach($rows as $r)$md.='| '.implode(' | ',$r)." |\n";
file_put_contents($root.'/docs/HANDOFF-VERIFICATION.md',$md);
echo (count($rows)-$failed)." passed, $failed failed\n";
exit($failed?1:0);
