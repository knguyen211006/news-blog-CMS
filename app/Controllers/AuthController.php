<?php
namespace App\Controllers;
use App\Core\{Auth,DB,Security,View,HttpException};
use App\Models\User;
final class AuthController {
    public function login(): void {
        $error=null;
        if($_SERVER['REQUEST_METHOD']==='POST') {
            try {
                $email=mb_strtolower(posted('email'));
                Security::length($email,1,190,'Email');
                Security::rate('login-ip:'.($_SERVER['REMOTE_ADDR']??'local'),25,900);
                Security::rate('login-email:'.$email,5,900);
                $u=DB::one('SELECT * FROM users WHERE email=?',[$email]);
                $hash=$u['password_hash']??'$2y$10$abcdefghijklmnopqrstuuLf.FVWcwtkdIHgVPrbLnzjPFkzHtWX.';
                if(!password_verify(posted('password'),$hash) || !$u || $u['is_locked']) throw new HttpException(422,'Email hoặc mật khẩu không đúng, hoặc tài khoản đã bị khóa.');
                session_regenerate_id(true);
                $_SESSION['user_id']=(int)$u['id']; unset($_SESSION['csrf']);
                DB::query('DELETE FROM rate_limits WHERE bucket=?',[hash('sha256','login-email:'.$email)]);
                flash('Chào mừng '.$u['name'].' quay trở lại!');
                redirect($u['role']==='ADMIN'?'admin':($u['role']==='AUTHOR'?'author/posts':'profile'));
            }catch(HttpException $e){$error=$e->getMessage();$status=$e->status;}
        }
        View::render('auth/login',['title'=>'Đăng nhập','error'=>$error],$status??200);
    }
    public function register(): void {
        $error=null;
        if($_SERVER['REQUEST_METHOD']==='POST'){
            try{
                Security::rate('register-ip:'.($_SERVER['REMOTE_ADDR']??'local'),12,3600);
                if(posted('password')!==posted('password_confirmation')) throw new HttpException(422,'Mật khẩu nhập lại chưa khớp.');
                User::register(posted('name'),posted('email'),posted('password'));
                flash('Đăng ký thành công. Bạn có thể đăng nhập bằng tài khoản độc giả.');
                redirect('login');
            }catch(HttpException $e){$error=$e->getMessage();$status=$e->status;}
        }
        View::render('auth/register',['title'=>'Tạo tài khoản','error'=>$error],$status??200);
    }
    public function logout(): void {
        $_SESSION=[];
        setcookie(session_name(),'', ['expires'=>time()-3600,'path'=>config('base_path').'/','httponly'=>true,'samesite'=>'Lax']);
        session_destroy(); redirect();
    }
    public function profile(): void {
        $u=Auth::requireUser();$error=null;
        if($_SERVER['REQUEST_METHOD']==='POST'){
            try{
                $name=posted('name');$email=mb_strtolower(posted('email'));$bio=posted('bio');
                Security::length($name,2,100,'Họ tên');Security::length($bio,0,500,'Giới thiệu');
                if(!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($email)>190) throw new HttpException(422,'Email không hợp lệ.');
                DB::transaction(function() use($u,$name,$email,$bio){
                    $hash=null;
                    if(posted('new_password')!==''){
                        Security::password(posted('new_password'));
                        $existing=DB::one('SELECT password_hash FROM users WHERE id=? FOR UPDATE',[$u['id']]);
                        if(!password_verify(posted('current_password'),$existing['password_hash'])) throw new HttpException(422,'Mật khẩu hiện tại không đúng.');
                        $hash=password_hash(posted('new_password'),PASSWORD_DEFAULT);
                    }
                    try{DB::query('UPDATE users SET name=?,email=?,bio=? WHERE id=?',[$name,$email,$bio,$u['id']]);}
                    catch(\PDOException $e){if($e->getCode()==='23000')throw new HttpException(422,'Email đã được sử dụng.');throw $e;}
                    if($hash)DB::query('UPDATE users SET password_hash=? WHERE id=?',[$hash,$u['id']]);
                });
                flash('Đã cập nhật hồ sơ.');redirect('profile');
            }catch(HttpException $e){$error=$e->getMessage();$status=$e->status;}
        }
        $comments=DB::all('SELECT c.*,p.title,p.slug,p.status post_status,p.deleted_at FROM comments c JOIN posts p ON p.id=c.post_id WHERE c.user_id=? ORDER BY c.id DESC LIMIT 50',[$u['id']]);
        View::render('auth/profile',['title'=>'Hồ sơ của bạn','profile'=>$u,'comments'=>$comments,'error'=>$error],$status??200);
    }
}
