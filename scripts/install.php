<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli') exit;
$root=dirname(__DIR__);
$options=getopt('',['xampp:','test','configure-only']);
$test=isset($options['test']);
$dbName=$test?'news_blog_cms_test':'news_blog_cms';
$configPath=$root.'/config/'.($test?'test':'local').'.php';
try {
 if(is_file($configPath)) throw new RuntimeException('Config already exists; refusing to overwrite it.');
 if(isset($options['xampp'])){
   $xampp=rtrim($options['xampp'],'/\\');
   $cfg=[];
   require $xampp.'/phpMyAdmin/config.inc.php';
   $server=$cfg['Servers'][1]??[];
   if(($server['auth_type']??'')!=='config') throw new RuntimeException('phpMyAdmin uses cookie authentication. Set CMS_DB_ADMIN_USER and CMS_DB_ADMIN_PASSWORD explicitly.');
   $host=$server['host']?:'127.0.0.1'; $port=3306;
   $user=$server['user']; $pass=$server['password'];
 }else{
   $host=getenv('CMS_DB_HOST')?:'127.0.0.1'; $port=(int)(getenv('CMS_DB_PORT')?:3306);
   $user=getenv('CMS_DB_ADMIN_USER'); $pass=getenv('CMS_DB_ADMIN_PASSWORD');
   if($user===false || $pass===false) throw new RuntimeException('Set CMS_DB_ADMIN_USER and CMS_DB_ADMIN_PASSWORD (an explicitly empty password is allowed).');
 }
 $pdo=new PDO("mysql:host=$host;port=$port;charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_EMULATE_PREPARES=>false]);
 $q=$pdo->prepare('SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=?'); $q->execute([$dbName]);
 $tableCount=(int)$q->fetchColumn();
 if(isset($options['configure-only'])) {
   if($tableCount!==8) throw new RuntimeException('Expected the eight CMS tables before configuring an imported database.');
   $config=['db_host'=>$host,'db_port'=>$port,'db_name'=>$dbName,'db_user'=>$user,'db_password'=>$pass,'base_path'=>'/news-blog-cms','timezone'=>'Asia/Ho_Chi_Minh','session_name'=>$test?'news_blog_cms_test':'news_blog_cms'];
   file_put_contents($configPath,"<?php\nreturn ".var_export($config,true).";\n");
   echo "Configured the existing CMS database using explicitly supplied credentials.\n";
   exit;
 }
 if($tableCount>0) throw new RuntimeException("Database $dbName is not empty. Import stopped; no existing data changed.");
 $sql=file_get_contents($root.'/database/news_blog_cms.sql');
 if($test) $sql=str_replace('news_blog_cms','news_blog_cms_test',$sql);
 $pdo->exec($sql);
 $appUser=($test?'nb_test_':'nb_app_').bin2hex(random_bytes(4));
 $appPassword=bin2hex(random_bytes(24));
 $pdo->exec('CREATE USER '.$pdo->quote($appUser)."@'localhost' IDENTIFIED BY ".$pdo->quote($appPassword));
 $pdo->exec("GRANT SELECT, INSERT, UPDATE, DELETE ON `$dbName`.* TO ".$pdo->quote($appUser)."@'localhost'");
 $config=['db_host'=>$host,'db_port'=>$port,'db_name'=>$dbName,'db_user'=>$appUser,'db_password'=>$appPassword,'base_path'=>'/news-blog-cms','timezone'=>'Asia/Ho_Chi_Minh','session_name'=>$test?'news_blog_cms_test':'news_blog_cms'];
 file_put_contents($configPath,"<?php\nreturn ".var_export($config,true).";\n");
 echo "Installed $dbName with a dedicated database account. Credentials saved only in ignored config file.\n";
}catch(Throwable $e){fwrite(STDERR,"Installation stopped: ".$e->getMessage()."\n");exit(1);}
