<?php
namespace App\Models;
use App\Core\{DB,Security,HttpException};
final class Comment {
    public static function create(array $user,int $postId,int $parentId,string $content): void {
        Security::length($content,1,2000,'Bình luận');
        DB::transaction(function() use($user,$postId,$parentId,$content) {
            $post=Post::get($postId,true);
            if($post['status']!=='published') throw new HttpException(404,'Bài viết chưa được công khai.');
            if($parentId) {
                $parent=DB::one('SELECT * FROM comments WHERE id=? FOR UPDATE',[$parentId]);
                if(!$parent || (int)$parent['post_id']!==$postId || $parent['parent_id']!==null || $parent['status']!=='approved') {
                    throw new HttpException(422,'Chỉ được trả lời một bình luận gốc đã duyệt thuộc cùng bài viết.');
                }
            }
            DB::query("INSERT INTO comments(post_id,user_id,parent_id,content,status) VALUES (?,?,?,?,'pending')",[$postId,$user['id'],$parentId?:null,$content]);
        });
    }
    public static function moderate(array $user,int $id,int $version,string $status): void {
        if(!in_array($status,['approved','hidden'],true)) throw new HttpException(422,'Trạng thái bình luận không hợp lệ.');
        DB::transaction(function() use($user,$id,$version,$status) {
            $c=DB::one('SELECT * FROM comments WHERE id=? FOR UPDATE',[$id]);
            if(!$c) throw new HttpException(404,'Không tìm thấy bình luận.');
            if($user['role']!=='ADMIN' && ((int)$c['user_id']!==(int)$user['id'] || $status!=='hidden')) throw new HttpException(403,'Bạn chỉ có thể ẩn bình luận của chính mình.');
            if((int)$c['version']!==$version) throw new HttpException(409,'Bình luận đã thay đổi. Hãy tải lại trang.');
            // Root rows remain to keep approved replies and the conversation history.
            DB::query('UPDATE comments SET status=?,version=version+1 WHERE id=?',[$status,$id]);
        });
    }
}
