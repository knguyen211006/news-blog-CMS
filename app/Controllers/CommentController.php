<?php
namespace App\Controllers;
use App\Core\{Auth,DB,Security};
use App\Models\Comment;
final class CommentController {
    public function create(): void {
        $u=Auth::requireUser();
        Security::rate('comment-user:'.$u['id'],1,20);
        Comment::create($u,number_input('post_id'),number_input('parent_id'),posted('content'));
        flash('Bình luận đã được gửi và đang chờ Admin duyệt.');
        $p=DB::one('SELECT slug FROM posts WHERE id=?',[number_input('post_id')]);
        redirect('article',['slug'=>$p['slug']]);
    }
    public function hide(): void {
        $u=Auth::requireUser();
        Comment::moderate($u,number_input('id'),number_input('version'),'hidden');
        flash('Đã ẩn bình luận. Các phản hồi đã duyệt vẫn được giữ lại.');redirect('profile');
    }
}
