<?php

declare(strict_types=1);

$password = getenv('CMS_DB_PASSWORD');
if ($password === false || $password === '') {
    throw new RuntimeException('The Docker database password is missing.');
}

return [
    'db_host' => 'db',
    'db_port' => 3306,
    'db_name' => 'news_blog_cms',
    'db_user' => 'cms_app',
    'db_password' => $password,
    'base_path' => '',
    'timezone' => 'Asia/Ho_Chi_Minh',
    'session_name' => 'goc_nhin_docker',
];
