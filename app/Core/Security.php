<?php
namespace App\Core;
final class Security {
    public static function token(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
    public static function checkCsrf(): void {
        if (!is_string($_POST['_csrf'] ?? null) || !hash_equals(self::token(), $_POST['_csrf'])) {
            throw new HttpException(419,'Phiên biểu mẫu đã hết hạn hoặc thiếu mã CSRF. Vui lòng tải lại trang rồi thử lại.');
        }
    }
    public static function length(string $text, int $min, int $max, string $label): void {
        $len=mb_strlen($text);
        if ($len<$min || $len>$max) throw new HttpException(422,"$label phải có từ $min đến $max ký tự.");
    }
    public static function html(string $input): string {
        self::length($input,0,100000,'Nội dung HTML');
        require_once ROOT . '/vendor/htmlpurifier/library/HTMLPurifier.auto.php';
        static $purifier;
        if (!$purifier) {
            $c=\HTMLPurifier_Config::createDefault();
            $c->set('HTML.Allowed','p,br,div,strong,b,em,i,u,s,h2,h3,h4,ul,ol,li,blockquote,pre,code,a[href|title],hr');
            $c->set('URI.AllowedSchemes',['http'=>true,'https'=>true,'mailto'=>true]);
            $c->set('URI.DisableExternalResources',true);
            $c->set('Cache.SerializerPath',ROOT . '/storage');
            $purifier=new \HTMLPurifier($c);
        }
        return $purifier->purify($input);
    }
    public static function password(string $password): void {
        self::length($password,8,72,'Mật khẩu');
        if(strlen($password)>72 || str_contains($password,"\0")) throw new HttpException(422,'Mật khẩu không được quá 72 byte hoặc chứa ký tự null. Với chữ có dấu, hãy dùng mật khẩu ngắn hơn.');
    }
    public static function slug(string $text): string {
        $text=str_replace(['đ','Đ'],['d','D'],$text);
        $ascii=iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$text);
        $slug=strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/','-',$ascii ?: $text),'-'));
        return substr($slug ?: 'bai-viet',0,160);
    }
    public static function rate(string $key, int $limit, int $seconds): void {
        $hash=hash('sha256',$key);
        DB::transaction(function () use ($hash,$limit,$seconds) {
            DB::query('INSERT IGNORE INTO rate_limits (bucket, attempts, reset_at) VALUES (?,0,?)',[$hash,date('Y-m-d H:i:s',time()+$seconds)]);
            $row=DB::one('SELECT * FROM rate_limits WHERE bucket=? FOR UPDATE',[$hash]);
            if (strtotime($row['reset_at'])<=time()) {
                DB::query('UPDATE rate_limits SET attempts=0,reset_at=? WHERE bucket=?',[date('Y-m-d H:i:s',time()+$seconds),$hash]);
                $row['attempts']=0;
            }
            if ((int)$row['attempts'] >= $limit) throw new HttpException(429,'Bạn thao tác quá nhanh hoặc đăng nhập sai quá nhiều lần. Vui lòng thử lại sau.');
            DB::query('UPDATE rate_limits SET attempts=attempts+1 WHERE bucket=?',[$hash]);
        });
    }
    public static function upload(?array $file): ?string {
        if($file && (!is_int($file['error']??null) || !is_int($file['size']??null) || !is_string($file['tmp_name']??null))) throw new HttpException(422,'Dữ liệu tệp tải lên không hợp lệ.');
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE) return null;
        if ($file['error']!==UPLOAD_ERR_OK || $file['size']>2*1024*1024) throw new HttpException(422,'Ảnh phải tải lên thành công và không vượt quá 2 MB.');
        $mime=(new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        $info=@getimagesize($file['tmp_name']);
        if (!isset($types[$mime]) || !$info || $info['mime']!==$mime || $info[0]>8000 || $info[1]>8000) {
            throw new HttpException(422,'Chỉ nhận ảnh JPEG, PNG hoặc WebP hợp lệ, kích thước tối đa 8000 × 8000.');
        }
        $name=bin2hex(random_bytes(16)).'.'.$types[$mime];
        if (!move_uploaded_file($file['tmp_name'],ROOT.'/uploads/'.$name)) throw new HttpException(500,'Không thể lưu ảnh. Hãy kiểm tra quyền ghi uploads.');
        return 'uploads/'.$name;
    }
}
