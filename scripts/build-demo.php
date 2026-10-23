<?php
// Generates the reproducible hand-in SQL. Run from CLI only.
if (PHP_SAPI!=='cli') exit;
$root=dirname(__DIR__);
$quote=fn($s)=>"'".str_replace(["\\","'"],["\\\\","''"],$s)."'";
$hash=password_hash('Demo@12345',PASSWORD_DEFAULT);
$users=[
 ['Nguyễn Khôi Nguyên','admin@newsblog.local','ADMIN'],
 ['Nguyễn Lê Duy Ân','author1@newsblog.local','AUTHOR'],
 ['Nguyễn Trí Phong','author2@newsblog.local','AUTHOR'],
 ['Lê Tuấn Quy','reader1@newsblog.local','USER'],
 ['Nguyễn Tấn Tài','reader2@newsblog.local','USER'],
];
$sql=file_get_contents($root.'/database/schema.sql')."\nSTART TRANSACTION;\n";
foreach ($users as $u) $sql.="INSERT INTO users(name,email,password_hash,role) VALUES (".implode(',',array_map($quote,[$u[0],$u[1],$hash,$u[2]])).");\n";
$categories=[['Công nghệ','cong-nghe','Những thay đổi số quanh chúng ta.'],['Đời sống','doi-song','Nhìn cuộc sống từ những điều nhỏ.'],['Góc học tập','goc-hoc-tap','Học tốt hơn, mỗi ngày một chút.'],['Cộng đồng','cong-dong','Kết nối những câu chuyện đáng chia sẻ.']];
foreach($categories as $c) $sql.="INSERT INTO categories(name,slug,description) VALUES (".implode(',',array_map($quote,$c)).");\n";
foreach([['PHP','php'],['Thiết kế','thiet-ke'],['Kỹ năng','ky-nang'],['Sinh viên','sinh-vien'],['Sáng tạo','sang-tao'],['Chuyện nhỏ','chuyen-nho']] as $t) $sql.="INSERT INTO tags(name,slug) VALUES (".implode(',',array_map($quote,$t)).");\n";
$posts=[
 ['Xây dựng thói quen đọc trong thời đại số','xay-dung-thoi-quen-doc',2,'published','Mười lăm phút đọc mỗi ngày có thể mở ra những góc nhìn mới. Bắt đầu bằng một cuốn sách khiến bạn tò mò.','Đọc không phải một cuộc đua','Hãy chọn thời điểm bạn thấy thoải mái nhất, cất điện thoại và dành sự chú ý cho từng trang. Một nhịp đọc đều đặn có giá trị hơn mục tiêu quá lớn.','Ghi lại điều bạn muốn nhớ','Sau mỗi chương, viết một câu bằng ngôn ngữ của chính mình. Bạn có thể chia sẻ với bạn bè để biến kiến thức thành một cuộc trò chuyện.',1],
 ['Từ ý tưởng đến một website đầu tiên bằng PHP','website-dau-tien-bang-php',1,'published','Một hành trình nhỏ từ form HTML đến dữ liệu được lưu trong database, dành cho người vừa bắt đầu.','Bắt đầu từ một chức năng cụ thể','Một trang ghi chú đơn giản giúp bạn hiểu cách trình duyệt gửi yêu cầu, PHP xử lý và MySQL lưu dữ liệu. Hãy làm cho từng bước hoạt động rồi mới mở rộng.','Tách trách nhiệm để dễ sửa','Model làm việc với dữ liệu, View trình bày và Controller điều phối. Cấu trúc này giúp cả nhóm tìm đúng nơi cần thay đổi.',1],
 ['Một buổi sáng chậm giữa nhịp sống thành phố','buoi-sang-cham',2,'published','Thức dậy sớm hơn một chút, đi bộ một đoạn đường quen và quan sát những điều thường bị bỏ lỡ.','Tìm khoảng trống cho mình','Không cần một kế hoạch cầu kỳ. Một bữa sáng tử tế và vài phút nhìn ra cửa sổ cũng đủ để bắt đầu ngày mới nhẹ nhàng.','Giữ một thói quen nhỏ','Chọn điều bạn có thể làm ngay cả trong ngày bận rộn. Sự đều đặn giúp khoảng nghỉ ấy trở thành một phần tự nhiên của cuộc sống.',0],
 ['Ghi chép có hệ thống để học sâu hơn','ghi-chep-co-he-thong',3,'published','Ghi chép không phải chép lại tất cả. Đó là chọn điều quan trọng và kết nối nó với những gì mình đã biết.','Đặt câu hỏi trước khi ghi','Bạn đang tìm lời giải cho vấn đề nào? Viết câu hỏi ở đầu trang giúp bạn nhận ra phần kiến thức thực sự cần giữ lại.','Ôn bằng cách tự giải thích','Đóng tài liệu rồi thử trình bày lại cho một người bạn. Những điểm chưa giải thích được chính là nơi cần học thêm.',0],
 ['Những góc xanh được vun trồng cùng nhau','nhung-goc-xanh',4,'published','Một nhóm sinh viên biến khoảng sân nhỏ thành nơi gặp gỡ, trao đổi cây và chăm sóc không gian chung.','Mỗi người góp một việc','Người mang đất, người chuẩn bị chậu, người ghi lịch tưới. Khi công việc được chia nhỏ, ai cũng tìm được cách tham gia phù hợp.','Chăm sóc cả sự kết nối','Khu vườn mang lại nhiều hơn màu xanh. Những buổi gặp định kỳ tạo cơ hội lắng nghe và giúp đỡ nhau trong cuộc sống.',1],
 ['Thiết kế giao diện bắt đầu từ khả năng đọc','thiet-ke-kha-nang-doc',1,'published','Cỡ chữ, khoảng cách và độ tương phản là nền tảng của một trang web dễ sử dụng trên cả điện thoại lẫn máy tính.','Cho nội dung một thứ tự rõ ràng','Tiêu đề nói lên chủ đề, phần tóm tắt mở đường và các đoạn ngắn giúp người đọc theo dõi mạch ý. Hãy thử đọc trang ở kích thước màn hình nhỏ.','Kiểm tra bằng những thao tác thật','Dùng bàn phím để đi qua biểu mẫu, thử một tiêu đề dài và quan sát thông báo lỗi. Thiết kế tốt cần hoạt động với nội dung thực tế.',0],
 ['Làm việc nhóm: bắt đầu bằng một thỏa thuận nhỏ','lam-viec-nhom',3,'published','Thống nhất cách đặt tên, cách chia việc và thời gian trao đổi giúp nhóm dành nhiều công sức hơn cho sản phẩm.','Chia việc theo kết quả','Mỗi đầu việc nên có người phụ trách, dữ liệu đầu vào và dấu hiệu hoàn thành. Cả nhóm sẽ dễ nhận ra vấn đề trước khi nó thành trở ngại lớn.','Tích hợp sớm và thường xuyên','Đừng đợi từng phần hoàn hảo mới ghép lại. Một phiên bản nhỏ chạy được giúp mọi người kiểm tra giả định và trao đổi bằng kết quả cụ thể.',0],
 ['Nhật ký học SQL: hiểu khóa ngoại qua ví dụ','nhat-ky-hoc-sql',1,'draft','Bản nháp về quan hệ dữ liệu trong một hệ thống nhiều tác giả.','Dữ liệu cần có mối liên hệ','Khóa ngoại giúp một bài viết luôn tham chiếu tới tác giả có thật. Đây là nền tảng để giữ dữ liệu nhất quán.','Thử trước khi kết luận','Hãy tạo một database thử nghiệm và quan sát điều gì xảy ra khi xóa dữ liệu đang được tham chiếu.',0],
 ['Kế hoạch đọc sách của câu lạc bộ','ke-hoach-doc-sach',4,'draft','Dự kiến hoạt động đọc và chia sẻ sách trong tháng tới.','Một chủ đề chung','Câu lạc bộ lựa chọn một chủ đề để mỗi thành viên mang đến một cuốn sách khác nhau.','Buổi gặp đầu tiên','Mỗi người có năm phút chia sẻ ý tưởng mình yêu thích, sau đó cả nhóm cùng thảo luận.',0],
 ['Một tuần thử học bằng sơ đồ tư duy','hoc-bang-so-do-tu-duy',3,'pending','Bài đang chờ biên tập viên duyệt trước khi xuất bản.','Bắt đầu từ khái niệm trung tâm','Viết chủ đề chính rồi mở rộng những nhánh liên quan bằng các từ khóa ngắn.','Nhìn lại các kết nối','Sau một tuần, thêm ví dụ thực tế và điều chỉnh những nhánh chưa rõ để sơ đồ phản ánh cách hiểu của mình.',0],
 ['Tủ sách trao đổi ở khu sinh hoạt chung','tu-sach-trao-doi',4,'pending','Một đề xuất nhỏ giúp những cuốn sách có thêm hành trình mới.','Cho sách cơ hội được đọc tiếp','Người tham gia để lại một cuốn sách đã đọc và chọn một cuốn mình muốn khám phá.','Cùng giữ tủ sách ngăn nắp','Sổ theo dõi đơn giản và lịch chăm sóc định kỳ giúp hoạt động được duy trì lâu dài.',0],
 ['Đi tìm nhịp học phù hợp với chính mình','nhip-hoc-cua-minh',3,'rejected','Bài được trả lại để bổ sung ví dụ và làm rõ các bước thực hiện.','Quan sát trước khi thay đổi','Ghi lại thời gian mình tập trung tốt nhất thay vì áp dụng lịch học của người khác.','Điều chỉnh từng bước','Thử một thay đổi nhỏ trong vài ngày, quan sát kết quả rồi quyết định có tiếp tục hay không.',0],
];
foreach($posts as $i=>$p){
 $id=$i+1; $author=$i%2+2;
 $html='<h2>'.$p[5].'</h2><p>'.$p[6].'</p><h2>'.$p[7].'</h2><p>'.$p[8].'</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>';
 $date=date('Y-m-d H:i:s',strtotime('2026-10-01 08:00:00')+$i*86400);
 $values=[$author,$p[2],$quote($p[0]),$quote($p[1]),$quote($p[4]),$quote($html),$quote('assets/images/cover-'.$p[2].'.svg'),$quote($p[3]),$p[9],17+$i*13,$p[3]==='published'?$quote($date):'NULL',$p[3]==='rejected'?$quote('Bổ sung ví dụ cụ thể và nguồn tham khảo cho phần hướng dẫn.'):'NULL',$quote($date)];
 $sql.="INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (".implode(',',$values).");\n";
 $sql.="INSERT INTO post_tags(post_id,tag_id) VALUES ($id,".($i%6+1)."),($id,".(($i+2)%6+1).");\n";
 $sql.="INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES ($id,1,'seed',".$quote($p[3]).",".$quote('Dữ liệu demo của đồ án.').");\n";
}
foreach([
 [1,4,null,'Mình bắt đầu từ mười trang mỗi tối, cảm ơn bài viết!','approved'],
 [1,2,1,'Cảm ơn bạn đã chia sẻ. Giữ nhịp đọc phù hợp với mình là điều quan trọng.','approved'],
 [1,5,null,'Mình muốn gợi ý thêm một vài cuốn sách.','pending'],
 [2,4,null,'Bình luận gốc này đã được ẩn.','hidden'],
 [2,3,4,'Phản hồi đã duyệt vẫn được giữ trong cuộc trò chuyện.','approved'],
 [2,5,null,'<script>alert("demo")</script> Nội dung này được lưu và hiển thị như văn bản.','pending'],
] as $c) $sql.="INSERT INTO comments(post_id,user_id,parent_id,content,status) VALUES (".$c[0].','.$c[1].','.($c[2]??'NULL').','.$quote($c[3]).','.$quote($c[4]).");\n";
$sql.="COMMIT;\n";
file_put_contents($root.'/database/news_blog_cms.sql',$sql);
echo "Generated database/news_blog_cms.sql\n";
