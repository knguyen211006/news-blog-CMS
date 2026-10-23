<?php
namespace App\Core;
final class Auth {
    public static function user(): ?array {
        if (empty($_SESSION['user_id'])) return null;
        $u=DB::one('SELECT id,name,email,role,is_locked,bio,created_at FROM users WHERE id=?',[$_SESSION['user_id']]);
        if (!$u || $u['is_locked']) { unset($_SESSION['user_id']); return null; }
        return $u;
    }
    public static function requireUser(array $roles=[]): array {
        $u=self::user();
        if (!$u) throw new HttpException(401,'Bạn cần đăng nhập bằng tài khoản đang hoạt động.');
        if ($roles && !in_array($u['role'],$roles,true)) throw new HttpException(403,'Bạn không có quyền thực hiện thao tác này.');
        return $u;
    }
}
