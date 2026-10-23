<?php
namespace App\Models;
use App\Core\{DB,Security,HttpException};
final class Post {
    public const STATUSES=['draft','pending','published','rejected'];
    public static function get(int $id,bool $lock=false): array {
        $p=DB::one('SELECT * FROM posts WHERE id=? AND deleted_at IS NULL'.($lock?' FOR UPDATE':''),[$id]);
        if(!$p) throw new HttpException(404,'Không tìm thấy bài viết.');
        return $p;
    }
    public static function own(array $post,array $user): void {
        if($user['role']!=='ADMIN' && (int)$post['author_id']!==(int)$user['id']) throw new HttpException(403,'Bạn chỉ được thao tác với bài viết của mình.');
    }
    public static function editable(array $p,array $u): void {
        self::own($p,$u);
        if(!in_array($p['status'],['draft','rejected'],true)) throw new HttpException(409,'Chỉ có thể sửa bản nháp hoặc bài bị trả. Bài đã xuất bản cần được Admin gỡ về nháp.');
    }
    public static function version(array $p,int $version): void {
        if((int)$p['version']!==$version) throw new HttpException(409,'Bản ghi đã thay đổi. Hãy tải lại trang trước khi thao tác.');
    }
    public static function validatePublication(array $p): void {
        Security::length($p['title'],5,200,'Tiêu đề');
        Security::length(trim($p['excerpt']),10,500,'Tóm tắt');
        Security::length(trim(html_entity_decode(strip_tags($p['content']),ENT_QUOTES,'UTF-8')),30,100000,'Nội dung');
        if(!$p['category_id'] || !DB::one('SELECT id FROM categories WHERE id=?',[$p['category_id']])) throw new HttpException(422,'Hãy chọn chuyên mục hợp lệ trước khi gửi duyệt.');
    }
    private static function history(int $id,int $actor,string $action,?string $from,string $to,?string $note=null): void {
        DB::query('INSERT INTO post_history(post_id,actor_id,action,from_status,to_status,note) VALUES (?,?,?,?,?,?)',[$id,$actor,$action,$from,$to,$note]);
    }
    public static function save(array $user,int $id): int {
        $title=posted('title'); $excerpt=posted('excerpt'); $content=Security::html(posted('content'));
        Security::length($title,5,200,'Tiêu đề'); Security::length($excerpt,0,500,'Tóm tắt');
        $category=number_input('category_id')?:null;
        if($category && !DB::one('SELECT id FROM categories WHERE id=?',[$category])) throw new HttpException(422,'Chuyên mục không tồn tại.');
        $rawTags=$_POST['tags']??[];
        if(!is_array($rawTags) || count($rawTags)>20) throw new HttpException(422,'Danh sách thẻ không hợp lệ.');
        foreach($rawTags as $tag) if(!is_scalar($tag) || !ctype_digit((string)$tag)) throw new HttpException(422,'Thẻ không hợp lệ.');
        $tags=array_values(array_unique(array_map('intval',$rawTags)));
        if($tags && (int)DB::scalar('SELECT COUNT(*) FROM tags WHERE id IN ('.implode(',',array_fill(0,count($tags),'?')).')',$tags)!==count($tags)) throw new HttpException(422,'Có thẻ không tồn tại.');
        $image=null;
        try {
            return DB::transaction(function() use($user,$id,$title,$excerpt,$content,$category,$tags,&$image) {
                $p=$id?self::get($id,true):null;
                if($p) { self::editable($p,$user); self::version($p,number_input('version')); }
                $image=Security::upload($_FILES['cover']??null);
                $cover=$image??($p['cover_image']??null);
                $featured=$user['role']==='ADMIN' ? (posted('is_featured')==='1'?1:0) : ($p['is_featured']??0);
                if($id) {
                    DB::query('UPDATE posts SET title=?,excerpt=?,content=?,category_id=?,cover_image=?,is_featured=?,version=version+1 WHERE id=?',[$title,$excerpt,$content,$category,$cover,$featured,$id]);
                    self::history($id,$user['id'],'edit',$p['status'],$p['status']);
                } else {
                    $slug=Security::slug($title).'-'.bin2hex(random_bytes(5));
                    DB::query("INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,is_featured,status) VALUES (?,?,?,?,?,?,?,?,'draft')",[$user['id'],$category,$title,$slug,$excerpt,$content,$cover,$featured]);
                    $id=(int)DB::connection()->lastInsertId();
                    self::history($id,$user['id'],'create',null,'draft');
                }
                DB::query('DELETE FROM post_tags WHERE post_id=?',[$id]);
                foreach($tags as $tag) DB::query('INSERT INTO post_tags(post_id,tag_id) VALUES (?,?)',[$id,$tag]);
                return $id;
            });
        }catch(\Throwable $e){ if($image && is_file(ROOT.'/'.$image)) unlink(ROOT.'/'.$image); throw $e; }
    }
    public static function transition(int $id,int $version,string $action,string $reason,array $user): void {
        DB::transaction(function() use($id,$version,$action,$reason,$user) {
            $p=self::get($id,true); self::own($p,$user); self::version($p,$version);
            $from=$p['status']; $to=$from;
            if($action==='submit'){
                self::editable($p,$user); self::validatePublication($p); $to='pending';
            } elseif($action==='delete') {
                if($user['role']!=='ADMIN') self::editable($p,$user);
                DB::query('UPDATE posts SET deleted_at=NOW(),version=version+1 WHERE id=?',[$id]);
                self::history($id,$user['id'],'delete',$from,$to);
                return;
            } else {
                if($user['role']!=='ADMIN') throw new HttpException(403,'Chỉ Admin được duyệt hoặc gỡ bài.');
                if($action==='publish' && $from==='pending') { self::validatePublication($p); $to='published'; }
                elseif($action==='reject' && $from==='pending') { Security::length($reason,5,2000,'Lý do trả bài'); $to='rejected'; }
                elseif($action==='unpublish' && $from==='published') $to='draft';
                else throw new HttpException(409,'Thao tác không phù hợp với trạng thái hiện tại của bài.');
            }
            DB::query('UPDATE posts SET status=?,rejection_reason=?,published_at=?,version=version+1 WHERE id=?',
                [$to,$to==='rejected'?$reason:null,$to==='published'?date('Y-m-d H:i:s'):null,$id]);
            self::history($id,$user['id'],$action,$from,$to,$action==='reject'?$reason:null);
        });
    }
    public static function publicList(array $filters=[],int $page=1,int $size=6): array {
        $where=["p.status='published'","p.deleted_at IS NULL"]; $params=[];
        if(!empty($filters['q'])) {
            $term='%'.str_replace(['!','%','_'],['!!','!%','!_'],$filters['q']).'%';
            $where[]="(p.title LIKE ? ESCAPE '!' OR p.excerpt LIKE ? ESCAPE '!')"; array_push($params,$term,$term);
        }
        if(!empty($filters['category'])) { $where[]='c.slug=?'; $params[]=$filters['category']; }
        if(!empty($filters['tag'])) { $where[]='EXISTS(SELECT 1 FROM post_tags pt JOIN tags t ON t.id=pt.tag_id WHERE pt.post_id=p.id AND t.slug=?)'; $params[]=$filters['tag']; }
        if(!empty($filters['featured'])) $where[]='p.is_featured=1';
        $condition=implode(' AND ',$where);
        $total=(int)DB::scalar("SELECT COUNT(*) FROM posts p JOIN users u ON u.id=p.author_id LEFT JOIN categories c ON c.id=p.category_id WHERE $condition",$params);
        $page=max(1,min($page,max(1,(int)ceil($total/$size)))); $offset=($page-1)*$size;
        $items=DB::all("SELECT p.*,u.name author_name,c.name category_name,c.slug category_slug FROM posts p JOIN users u ON u.id=p.author_id LEFT JOIN categories c ON c.id=p.category_id WHERE $condition ORDER BY p.published_at DESC,p.id DESC LIMIT $size OFFSET $offset",$params);
        return compact('items','page','total','size');
    }
}
