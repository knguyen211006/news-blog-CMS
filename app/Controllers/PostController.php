<?php
namespace App\Controllers;
use App\Core\{Auth,DB,View,HttpException};
use App\Models\Post;
final class PostController {
    public function listing(): void { $this->list(false); }
    public function adminListing(): void { Auth::requireAdmin(); $this->list(true); }
    private function list(bool $all): void {
        $u=Auth::requireUser(['AUTHOR','ADMIN']);
        $status=is_string($_GET['status']??null)?$_GET['status']:'';
        $params=[];$where=['p.deleted_at IS NULL'];
        if(!$all){$where[]='p.author_id=?';$params[]=$u['id'];}
        if(in_array($status,Post::STATUSES,true)){$where[]='p.status=?';$params[]=$status;}
        $condition=implode(' AND ',$where);
        $total=(int)DB::scalar("SELECT COUNT(*) FROM posts p WHERE $condition",$params);
        $size=12;$page=max(1,min(number_input('page',$_GET),max(1,(int)ceil($total/$size))));$offset=($page-1)*$size;
        $items=DB::all("SELECT p.*,u.name author_name,c.name category_name FROM posts p JOIN users u ON u.id=p.author_id LEFT JOIN categories c ON c.id=p.category_id WHERE $condition ORDER BY p.updated_at DESC,p.id DESC LIMIT $size OFFSET $offset",$params);
        View::render('posts/list',compact('items','status','all','total','size','page')+['title'=>$all?'Biên tập & duyệt bài':'Bài viết của tôi']);
    }
    public function edit(): void {
        $u=Auth::requireUser(['AUTHOR','ADMIN']);
        $id=number_input('id',$_GET);$error=null;
        $p=$id?Post::get($id):['id'=>0,'title'=>'','excerpt'=>'','content'=>'','category_id'=>null,'cover_image'=>null,'status'=>'draft','version'=>0,'is_featured'=>0,'rejection_reason'=>null];
        if($id)Post::editable($p,$u);
        if($_SERVER['REQUEST_METHOD']==='POST'){
            try{$saved=Post::save($u,$id);flash('Đã lưu bản nháp. Hãy xem trước rồi gửi bài để duyệt.');redirect('author/preview',['id'=>$saved]);}
            catch(HttpException $e){$error=$e->getMessage();$status=$e->status;}
        }
        $selected=$id?array_column(DB::all('SELECT tag_id FROM post_tags WHERE post_id=?',[$id]),'tag_id'):[];
        View::render('posts/edit',['title'=>$id?'Chỉnh sửa bài viết':'Viết một câu chuyện mới','post'=>$p,'selected'=>$selected,'categories'=>DB::all('SELECT * FROM categories ORDER BY name'),'tags'=>DB::all('SELECT * FROM tags ORDER BY name'),'error'=>$error],$status??200);
    }
    public function preview(): void {
        $u=Auth::requireUser(['AUTHOR','ADMIN']);$p=Post::get(number_input('id',$_GET));Post::own($p,$u);
        $p['author_name']=DB::scalar('SELECT name FROM users WHERE id=?',[$p['author_id']]);
        $p['category_name']=DB::scalar('SELECT name FROM categories WHERE id=?',[$p['category_id']])?:'Chưa chọn chuyên mục';
        View::render('public/article',['title'=>'Xem trước · '.$p['title'],'post'=>$p,'preview'=>true,'comments'=>[],'tags'=>DB::all('SELECT t.* FROM tags t JOIN post_tags pt ON pt.tag_id=t.id WHERE pt.post_id=?',[$p['id']]),'history'=>DB::all('SELECT h.*,u.name actor_name FROM post_history h JOIN users u ON u.id=h.actor_id WHERE h.post_id=? ORDER BY h.id DESC',[$p['id']])]);
    }
    public function action(): void {
        $u=Auth::requireUser(['AUTHOR','ADMIN']);
        $id=number_input('id');$action=posted('action');
        Post::transition($id,number_input('version'),$action,posted('reason'),$u);
        flash(['submit'=>'Đã gửi bài chờ duyệt.','publish'=>'Đã xuất bản bài viết.','reject'=>'Đã trả bài kèm lý do.','unpublish'=>'Đã gỡ bài về bản nháp.','delete'=>'Đã xóa mềm bài viết.'][$action]??'Đã cập nhật.');
        if($action==='delete')redirect($u['role']==='ADMIN'?'admin/posts':'author/posts');
        redirect('author/preview',['id'=>$id]);
    }
}
