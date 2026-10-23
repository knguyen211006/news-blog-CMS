<?php
namespace App\Models;
use App\Core\{DB,Security,HttpException};
final class Taxonomy {
    public static function table(string $kind): string {
        if(!in_array($kind,['categories','tags'],true)) throw new HttpException(404,'Không tìm thấy danh mục.');
        return $kind;
    }
    public static function save(string $kind,int $id,string $name,string $slug,string $description): void {
        $table=self::table($kind);
        Security::length($name,2,100,'Tên'); Security::length($description,0,500,'Mô tả');
        $slug=$slug?:Security::slug($name);
        if(!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D',$slug) || strlen($slug)>180) throw new HttpException(422,'Slug chỉ gồm chữ thường không dấu, số và dấu gạch ngang (tối đa 180 ký tự).');
        if($id && !DB::one("SELECT id FROM $table WHERE id=?",[$id])) throw new HttpException(404,'Không tìm thấy dữ liệu.');
        try {
            $fields=$table==='categories'?'name=?,slug=?,description=?':'name=?,slug=?';
            $params=$table==='categories'?[$name,$slug,$description]:[$name,$slug];
            if($id) DB::query("UPDATE $table SET $fields WHERE id=?",[...$params,$id]);
            else DB::query("INSERT INTO $table SET $fields",$params);
        } catch(\PDOException $e) {
            if($e->getCode()==='23000') throw new HttpException(422,'Slug đã tồn tại. Hãy chọn slug khác.');
            throw $e;
        }
    }
    public static function delete(string $kind,int $id): void {
        $table=self::table($kind);
        try { DB::query("DELETE FROM $table WHERE id=?",[$id]); }
        catch(\PDOException $e) {
            if($e->getCode()==='23000') throw new HttpException(422,'Không thể xóa chuyên mục đang được bài viết sử dụng, kể cả bài đã xóa mềm.');
            throw $e;
        }
    }
}
