<?php
if(PHP_SAPI!=='cli') exit;
require dirname(__DIR__).'/app/bootstrap.php';
foreach(['pdo_mysql','mbstring','fileinfo','dom','iconv','session','openssl'] as $e) {
 echo $e.': '.(extension_loaded($e)?'OK':'MISSING').PHP_EOL;
 if(!extension_loaded($e)) exit(1);
}
echo 'PHP '.PHP_VERSION.PHP_EOL;
echo 'Database '.App\Core\DB::scalar('SELECT VERSION()').PHP_EOL;
foreach(['users','categories','tags','posts','comments'] as $table) echo $table.': '.App\Core\DB::scalar("SELECT COUNT(*) FROM $table").PHP_EOL;
echo 'HTML Purifier: '.App\Core\Security::html('<p>safe</p><script>alert(1)</script>').PHP_EOL;
