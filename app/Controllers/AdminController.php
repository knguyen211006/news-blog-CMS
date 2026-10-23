<?php
namespace App\Controllers;
use App\Core\{Auth,DB,View,HttpException};
use App\Models\{User,Taxonomy,Comment};
final class AdminController {
    public function dashboard(): void {
        Auth::requireAdmin();
        View::render('admin/dashboard',[
            'title'=>'Tổng quan tòa soạn',
            'counts'=>DB::all('SELECT status,COUNT(*) count FROM posts WHERE deleted_at IS NULL GROUP BY status'),
            'users'=>(int)DB::scalar('SELECT COUNT(*) FROM users'),
            'pendingComments'=>(int)DB::scalar("SELECT COUNT(*) FROM comments WHERE status='pending'"),
            'views'=>(int)DB::scalar("SELECT COALESCE(SUM(views),0) FROM posts WHERE status='published' AND deleted_at IS NULL"),
            'popular'=>DB::all("SELECT id,title,slug,views FROM posts WHERE status='published' AND deleted_at IS NULL ORDER BY views DESC LIMIT 5"),
            'recent'=>DB::all('SELECT h.*,p.title,u.name actor_name FROM post_history h JOIN posts p ON p.id=h.post_id JOIN users u ON u.id=h.actor_id ORDER BY h.id DESC LIMIT 10'),
        ]);
    }
    public function users(): void {
        Auth::requireAdmin();$error=null;
        if($_SERVER['REQUEST_METHOD']==='POST'){
            try{
                $locked=posted('is_locked');
                if(!in_array($locked,['0','1'],true))throw new HttpException(422,'Trạng thái tài khoản không hợp lệ.');
                User::updateRole(number_input('id'),posted('role'),(int)$locked);
                flash('Đã cập nhật vai trò và trạng thái tài khoản.');redirect('admin/users');
            }catch(HttpException $e){$error=$e->getMessage();$status=$e->status;}
        }
        $q=is_string($_GET['q']??null)?mb_substr(trim($_GET['q']),0,190):'';
        $params=['%'.$q.'%','%'.$q.'%'];
        $total=(int)DB::scalar('SELECT COUNT(*) FROM users WHERE name LIKE ? OR email LIKE ?',$params);
        $size=20;$page=max(1,min(number_input('page',$_GET),max(1,(int)ceil($total/$size))));$offset=($page-1)*$size;
        $items=DB::all("SELECT id,name,email,role,is_locked,created_at FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY id LIMIT $size OFFSET $offset",$params);
        View::render('admin/users',compact('items','q','total','size','page','error')+['title'=>'Thành viên & phân quyền'],$status??200);
    }
    public function taxonomy(): void {
        Auth::requireAdmin();
        $kind=Taxonomy::table(is_string($_GET['kind']??null)?$_GET['kind']:'categories');$error=null;
        if($_SERVER['REQUEST_METHOD']==='POST'){
            try{
                if(posted('action')==='delete')Taxonomy::delete($kind,number_input('id'));
                elseif(posted('action')==='save')Taxonomy::save($kind,number_input('id'),posted('name'),posted('slug'),posted('description'));
                else throw new HttpException(422,'Thao tác không hợp lệ.');
                flash('Đã cập nhật dữ liệu.');redirect('admin/taxonomy',['kind'=>$kind]);
            }catch(HttpException $e){$error=$e->getMessage();$status=$e->status;}
        }
        $edit=number_input('edit',$_GET)?DB::one("SELECT * FROM $kind WHERE id=?",[number_input('edit',$_GET)]):null;
        if($_SERVER['REQUEST_METHOD']==='POST' && posted('action')==='save')$edit=['id'=>number_input('id'),'name'=>posted('name'),'slug'=>posted('slug'),'description'=>posted('description')];
        View::render('admin/taxonomy',['title'=>$kind==='categories'?'Quản lý chuyên mục':'Quản lý thẻ','kind'=>$kind,'items'=>DB::all("SELECT * FROM $kind ORDER BY name"),'edit'=>$edit,'error'=>$error],$status??200);
    }
    public function comments(): void {
        $u=Auth::requireAdmin();
        if($_SERVER['REQUEST_METHOD']==='POST'){
            Comment::moderate($u,number_input('id'),number_input('version'),posted('status'));
            flash('Đã cập nhật trạng thái bình luận.');redirect('admin/comments');
        }
        $status=is_string($_GET['status']??null)?$_GET['status']:'pending';
        if(!in_array($status,['pending','approved','hidden'],true))$status='pending';
        $total=(int)DB::scalar('SELECT COUNT(*) FROM comments WHERE status=?',[$status]);
        $size=20;$page=max(1,min(number_input('page',$_GET),max(1,(int)ceil($total/$size))));$offset=($page-1)*$size;
        $items=DB::all("SELECT c.*,u.name user_name,p.title,p.slug FROM comments c JOIN users u ON u.id=c.user_id JOIN posts p ON p.id=c.post_id WHERE c.status=? ORDER BY c.id DESC LIMIT $size OFFSET $offset",[$status]);
        View::render('admin/comments',compact('items','status','total','size','page')+['title'=>'Kiểm duyệt bình luận']);
    }
}
