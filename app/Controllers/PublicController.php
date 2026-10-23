<?php
namespace App\Controllers;
use App\Core\{DB,Auth,View,HttpException};
use App\Models\Post;
final class PublicController {
    public function home(): void {
        View::render('public/home',[
            'title'=>'Những câu chuyện mở ra góc nhìn mới',
            'latest'=>Post::publicList([],1,6)['items'],
            'featured'=>Post::publicList(['featured'=>true],1,3)['items'],
            'categories'=>DB::all('SELECT c.*,COUNT(p.id) post_count FROM categories c LEFT JOIN posts p ON p.category_id=c.id AND p.status=\'published\' AND p.deleted_at IS NULL GROUP BY c.id ORDER BY c.id')
        ]);
    }
    public function articles(): void {
        $filters=[];
        foreach(['q','category','tag'] as $key) $filters[$key]=is_string($_GET[$key]??null)?mb_substr(trim($_GET[$key]),0,200):'';
        $heading='Khám phá bài viết';$description='Tìm một câu chuyện, một ý tưởng, một góc nhìn dành cho bạn.';
        foreach(['category'=>'categories','tag'=>'tags'] as $key=>$table){
            if($filters[$key]!==''){
                $item=DB::one("SELECT * FROM $table WHERE slug=?",[$filters[$key]]);
                if(!$item)throw new HttpException(404,'Chuyên mục hoặc thẻ không tồn tại.');
                $heading=$key==='tag'?'#'.$item['name']:$item['name'];
                $description=$item['description']??'Các câu chuyện cùng chủ đề.';
            }
        }
        $data=Post::publicList($filters,max(1,number_input('page',$_GET)));
        View::render('public/articles',$data+['title'=>$heading,'description'=>$description,'filters'=>$filters,'categories'=>DB::all('SELECT * FROM categories ORDER BY name'),'tags'=>DB::all('SELECT * FROM tags ORDER BY name')]);
    }
    public function article(): void {
        $slug=is_string($_GET['slug']??null)?$_GET['slug']:'';
        $p=DB::one("SELECT p.*,u.name author_name,c.name category_name,c.slug category_slug FROM posts p JOIN users u ON u.id=p.author_id LEFT JOIN categories c ON c.id=p.category_id WHERE p.slug=? AND p.status='published' AND p.deleted_at IS NULL",[$slug]);
        if(!$p)throw new HttpException(404,'Không tìm thấy bài viết công khai.');
        $last=$_SESSION['views'][$p['id']]??0;
        if(time()-(int)$last>=1800){
            $changed=DB::query("UPDATE posts SET views=views+1 WHERE id=? AND status='published' AND deleted_at IS NULL",[$p['id']])->rowCount();
            if(!$changed)throw new HttpException(404,'Bài viết đã được gỡ.');
            $_SESSION['views'][$p['id']]=time();$p['views']++;
        }
        $u=Auth::user();
        $comments=DB::all("SELECT c.*,u.name user_name FROM comments c JOIN users u ON u.id=c.user_id WHERE c.post_id=? AND (c.status IN ('approved','hidden') OR c.user_id=?) ORDER BY c.created_at,c.id",[$p['id'],$u['id']??0]);
        View::render('public/article',['title'=>$p['title'],'post'=>$p,'preview'=>false,'comments'=>$comments,'tags'=>DB::all('SELECT t.* FROM tags t JOIN post_tags pt ON pt.tag_id=t.id WHERE pt.post_id=?',[$p['id']])]);
    }
}
