<?php
namespace App\Controllers;
use App\Core\{DB,Auth,View,HttpException};
use App\Models\Post;
final class PublicController {
    public function home(): void {
        $featured=Post::publicList(['featured'=>true],1,3)['items'];
        $latestPool=Post::publicList([],1,7)['items'];
        if($featured && count($latestPool)>6){
            $leadId=(int)($featured[0]['id']??0);
            $filtered=array_values(array_filter($latestPool,static fn(array $item):bool=>(int)($item['id']??0)!==$leadId));
            $latest=array_slice($filtered,0,6);
        }else{
            $latest=array_slice($latestPool,0,6);
        }
        View::render('public/home',[
            'title'=>'Những câu chuyện mở ra góc nhìn mới',
            'latest'=>$latest,
            'featured'=>$featured,
            'categories'=>DB::all("SELECT c.*,COUNT(p.id) post_count FROM categories c INNER JOIN posts p ON p.category_id=c.id AND p.status='published' AND p.deleted_at IS NULL GROUP BY c.id HAVING COUNT(p.id)>0 ORDER BY c.id")
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
        $categories=DB::all("SELECT c.*,COUNT(p.id) post_count FROM categories c INNER JOIN posts p ON p.category_id=c.id AND p.status='published' AND p.deleted_at IS NULL GROUP BY c.id HAVING COUNT(p.id)>0 ORDER BY c.id");
        if($filters['category']!=='' && !in_array($filters['category'],array_column($categories,'slug'),true)){
            $selectedCat=DB::one("SELECT c.*,0 post_count FROM categories c WHERE c.slug=?",[$filters['category']]);
            if($selectedCat)$categories[]=$selectedCat;
        }
        $tags=$filters['category']!==''
            ? DB::all("SELECT DISTINCT t.* FROM tags t JOIN post_tags pt ON pt.tag_id=t.id JOIN posts p ON p.id=pt.post_id JOIN categories c ON c.id=p.category_id WHERE p.status='published' AND p.deleted_at IS NULL AND c.slug=? ORDER BY t.name",[$filters['category']])
            : DB::all("SELECT DISTINCT t.* FROM tags t JOIN post_tags pt ON pt.tag_id=t.id JOIN posts p ON p.id=pt.post_id WHERE p.status='published' AND p.deleted_at IS NULL ORDER BY t.name");
        if($filters['tag']!=='' && !in_array($filters['tag'],array_column($tags,'slug'),true)){
            $selectedTag=DB::one("SELECT * FROM tags WHERE slug=?",[$filters['tag']]);
            if($selectedTag)$tags[]=$selectedTag;
        }
        View::render('public/articles',$data+['title'=>$heading,'description'=>$description,'filters'=>$filters,'categories'=>$categories,'tags'=>$tags]);
    }
    public function article(): void {
        $slug=is_string($_GET['slug']??null)?$_GET['slug']:'';
        $p=DB::one("SELECT p.*,u.name author_name,c.name category_name,c.slug category_slug FROM posts p JOIN users u ON u.id=p.author_id LEFT JOIN categories c ON c.id=p.category_id WHERE p.slug=? AND p.status='published' AND p.deleted_at IS NULL",[$slug]);
        if(!$p)throw new HttpException(404,'Không tìm thấy bài viết công khai.');
        $p=\topic_cover($p);
        $last=$_SESSION['views'][$p['id']]??0;
        if(time()-(int)$last>=1800){
            $changed=DB::query("UPDATE posts SET views=views+1 WHERE id=? AND status='published' AND deleted_at IS NULL",[$p['id']])->rowCount();
            if(!$changed)throw new HttpException(404,'Bài viết đã được gỡ.');
            $_SESSION['views'][$p['id']]=time();$p['views']++;
        }
        $u=Auth::user();
        $comments=DB::all("SELECT c.*,u.name user_name FROM comments c JOIN users u ON u.id=c.user_id WHERE c.post_id=? AND (c.status IN ('approved','hidden') OR c.user_id=?) ORDER BY c.created_at,c.id",[$p['id'],$u['id']??0]);
        $related=$p['category_id']?array_map('topic_cover',DB::all("SELECT p.*,u.name author_name,c.name category_name,c.slug category_slug FROM posts p JOIN users u ON u.id=p.author_id LEFT JOIN categories c ON c.id=p.category_id WHERE p.category_id=? AND p.id<>? AND p.status='published' AND p.deleted_at IS NULL ORDER BY p.published_at DESC,p.id DESC LIMIT 3",[$p['category_id'],$p['id']])):[];
        View::render('public/article',['title'=>$p['title'],'post'=>$p,'preview'=>false,'comments'=>$comments,'related'=>$related,'tags'=>DB::all('SELECT t.* FROM tags t JOIN post_tags pt ON pt.tag_id=t.id WHERE pt.post_id=?',[$p['id']])]);
    }
}
