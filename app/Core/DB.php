<?php
namespace App\Core;
use PDO;
final class DB {
    private static ?PDO $pdo = null;
    public static function connection(): PDO {
        return self::$pdo ??= new PDO('mysql:host=' . config('db_host') . ';port=' . config('db_port',3306) . ';dbname=' . config('db_name') . ';charset=utf8mb4',
            config('db_user'),config('db_password'),[
                PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES=>false,
            ]);
    }
    public static function query(string $sql, array $params = []): \PDOStatement {
        $q=self::connection()->prepare($sql); $q->execute($params); return $q;
    }
    public static function one(string $sql, array $params = []): ?array { return self::query($sql,$params)->fetch() ?: null; }
    public static function all(string $sql, array $params = []): array { return self::query($sql,$params)->fetchAll(); }
    public static function scalar(string $sql, array $params = []): mixed { return self::query($sql,$params)->fetchColumn(); }
    public static function transaction(callable $callback): mixed {
        $db=self::connection(); $db->beginTransaction();
        try { $result=$callback(); $db->commit(); return $result; }
        catch (\Throwable $e) { if ($db->inTransaction()) $db->rollBack(); throw $e; }
    }
}
