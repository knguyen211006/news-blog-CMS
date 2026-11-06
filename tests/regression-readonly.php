<?php
declare(strict_types=1);
// Isolated doubles: no application bootstrap, connection, session or file writes.
namespace App\Core {
    final class DB {
        public static array $calls=[];
        public static function scalar(string $sql,array $params=[]): mixed {
            self::$calls[]=[$sql,$params];
            return str_contains($sql,'FROM users') ? 'Demo author' : 0;
        }
        public static function all(string $sql,array $params=[]): array { self::$calls[]=[$sql,$params]; return []; }
        public static function one(string $sql,array $params=[]): ?array {
            self::$calls[]=[$sql,$params];
            if(str_contains($sql,'FROM posts')) return ['id'=>7,'author_id'=>2,'category_id'=>3,'title'=>'Draft'];
            if(str_contains($sql,'FROM categories')) return $params[0]===3 ? ['name'=>'Renamed category','slug'=>'new-category-slug'] : null;
            return null;
        }
    }
    final class Auth { public static function requireUser(array $roles=[]): array {return ['id'=>2,'role'=>'AUTHOR'];} }
    final class View { public static array $data=[]; public static function render(string $name,array $data=[],int $status=200): void {self::$data=$data;} }
}
namespace {
    $root=dirname(__DIR__);
    require $root.'/app/helpers.php';
    require $root.'/app/Models/Post.php';
    require $root.'/app/Controllers/PostController.php';
    $passed=0;$failed=0;
    function check(string $name,bool $ok): void {
        global $passed,$failed;
        if($ok) ++$passed; else ++$failed;
        echo ($ok?'PASS':'FAIL')." | $name\n";
    }
    foreach(['q'=>'LIKE','category'=>'c.slug=?','tag'=>'EXISTS('] as $key=>$fragment){
        \App\Core\DB::$calls=[];
        \App\Models\Post::publicList([$key=>'0']);
        [$sql,$params]=\App\Core\DB::$calls[0];
        check("Zero-valued $key is filtered",str_contains($sql,$fragment)&&in_array($key==='q'?'%0%':'0',$params,true));
        \App\Core\DB::$calls=[];
        \App\Models\Post::publicList([$key=>'']);
        check("Empty $key is not filtered",!str_contains(\App\Core\DB::$calls[0][0],$fragment));
    }
    \App\Core\DB::$calls=[];
    \App\Models\Post::publicList(['q'=>'%_!']);
    check('Search wildcard characters remain literal',\App\Core\DB::$calls[0][1]===['%!%!_!!%','%!%!_!!%']);
    \App\Core\DB::$calls=[];
    \App\Models\Post::publicList(['q'=>"' OR 1=1 --"]);
    check('Search stays parameterized',!str_contains(\App\Core\DB::$calls[0][0],"' OR 1=1"));
    $_GET=['id'=>'7'];
    (new \App\Controllers\PostController)->preview();
    $post=\App\Core\View::$data['post'];
    check('Preview uses current category slug',$post['category_slug']==='new-category-slug');
    check('Preview uses current category name',$post['category_name']==='Renamed category');
    check('Preview never updates view counter',!array_filter(\App\Core\DB::$calls,fn($call)=>preg_match('/^\s*(UPDATE|INSERT|DELETE)/i',$call[0])));
    echo "$passed PASS / $failed FAIL (no database or file writes)\n";
    exit($failed?1:0);
}
