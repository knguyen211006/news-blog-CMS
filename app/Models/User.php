<?php
namespace App\Models;
use App\Core\{DB,Security,HttpException};
final class User {
    public static function register(string $name,string $email,string $password): int {
        Security::length($name,2,100,'Họ tên');
        if(!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>190) throw new HttpException(422,'Email không hợp lệ.');
        Security::password($password);
        try {
            DB::query("INSERT INTO users(name,email,password_hash,role) VALUES (?,?,?,'USER')",[$name,mb_strtolower($email),password_hash($password,PASSWORD_DEFAULT)]);
        } catch(\PDOException $e) {
            if($e->getCode()==='23000') throw new HttpException(422,'Email đã được sử dụng.');
            throw $e;
        }
        return (int)DB::connection()->lastInsertId();
    }
    public static function updateRole(int $id,string $role,int $locked): void {
        if(!in_array($role,['USER','AUTHOR','ADMIN'],true) || !in_array($locked,[0,1],true)) throw new HttpException(422,'Vai trò hoặc trạng thái không hợp lệ.');
        DB::transaction(function() use($id,$role,$locked) {
            $admins=DB::all("SELECT id FROM users WHERE role='ADMIN' AND is_locked=0 ORDER BY id FOR UPDATE");
            $target=DB::one('SELECT * FROM users WHERE id=? FOR UPDATE',[$id]);
            if(!$target) throw new HttpException(404,'Không tìm thấy tài khoản.');
            if($target['role']==='ADMIN' && !$target['is_locked'] && count($admins)<=1 && ($role!=='ADMIN' || $locked===1)) {
                throw new HttpException(422,'Không thể khóa hoặc hạ quyền Admin đang hoạt động cuối cùng.');
            }
            DB::query('UPDATE users SET role=?,is_locked=? WHERE id=?',[$role,$locked,$id]);
        });
    }
}
