<?php
declare(strict_types=1);

/** Reset only the disposable local test database; never run schema SQL or touch files. */
function resetTestDatabase(bool $e2e = false): void {
    if (PHP_SAPI !== 'cli') throw new RuntimeException('Test fixtures require CLI.');
    $root = dirname(__DIR__, 2);
    $config = require $root.'/config/test.php';
    if (($config['db_name'] ?? '') !== 'news_blog_cms_test'
        || !in_array($config['db_host'] ?? '', ['127.0.0.1', 'localhost'], true)) {
        throw new RuntimeException('Refusing to reset anything except local news_blog_cms_test.');
    }
    $db = new PDO('mysql:host='.$config['db_host'].';port='.($config['db_port'] ?? 3306)
        .';dbname=news_blog_cms_test;charset=utf8mb4', $config['db_user'], $config['db_password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    if ($db->query('SELECT DATABASE()')->fetchColumn() !== 'news_blog_cms_test') {
        throw new RuntimeException('Unexpected fixture database.');
    }
    $tables = ['users','categories','tags','posts','post_tags','post_history','comments'];
    $statements = [];
    $ids = [];
    foreach (file($root.'/database/news_blog_cms.sql', FILE_IGNORE_NEW_LINES) as $line) {
        if (!str_starts_with($line, 'INSERT INTO ')) continue;
        if (!preg_match('/^INSERT INTO ([a-z_]+)\(([^)]+)\) VALUES (.+);$/D', $line, $m)
            || !in_array($m[1], $tables, true)) {
            throw new RuntimeException('Unsupported seed INSERT; no records have been changed.');
        }
        $table = $m[1];
        if ($table === 'post_tags') {
            $statements[] = $line;
        } else {
            // Stable seed IDs without ALTER/TRUNCATE or resetting AUTO_INCREMENT.
            $id = $ids[$table] = ($ids[$table] ?? 0) + 1;
            $statements[] = 'INSERT INTO '.$table.'(id,'.$m[2].') VALUES ('
                .$id.','.substr($m[3], 1).';';
        }
    }
    foreach (['users'=>5,'categories'=>4,'tags'=>6,'posts'=>12,'comments'=>6] as $table=>$count) {
        if (($ids[$table] ?? 0) !== $count) throw new RuntimeException('Unexpected seed: '.$table);
    }
    $db->beginTransaction();
    try {
        $db->exec('DELETE FROM comments WHERE parent_id IS NOT NULL');
        foreach (['comments','post_history','post_tags','posts','categories','tags','users','rate_limits'] as $table) {
            $db->exec('DELETE FROM '.$table);
        }
        foreach ($statements as $sql) $db->exec($sql);
        if ($e2e) {
            foreach (['ADMIN'=>1,'AUTHOR'=>2,'USER'=>4] as $role=>$id) {
                $email = getenv($role.'_EMAIL');
                $password = getenv($role.'_PASSWORD');
                if ($email === false && $password === false) continue;
                if (!is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)
                    || !is_string($password) || $password === '') {
                    throw new RuntimeException('Configure both '.$role.'_EMAIL and '.$role.'_PASSWORD.');
                }
                $q = $db->prepare('UPDATE users SET email=?,password_hash=? WHERE id=?');
                $q->execute([$email, password_hash($password, PASSWORD_DEFAULT), $id]);
            }
        }
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        throw $e;
    }
    echo "Fixture ready: news_blog_cms_test; users=5 posts=12 comments=6 rate_limits=0\n";
}

if (PHP_SAPI === 'cli' && realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    resetTestDatabase(in_array('--e2e', $argv, true));
}
