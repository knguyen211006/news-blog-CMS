-- news-blog-cms: no DROP statements. Import only into a NEW database.
CREATE DATABASE IF NOT EXISTS news_blog_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE news_blog_cms;
SET NAMES utf8mb4;
CREATE TABLE users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('USER','AUTHOR','ADMIN') NOT NULL DEFAULT 'USER',
 is_locked TINYINT(1) NOT NULL DEFAULT 0,
 bio VARCHAR(500) NOT NULL DEFAULT '',
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_users_role_locked(role,is_locked)
) ENGINE=InnoDB;
CREATE TABLE categories (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 slug VARCHAR(180) NOT NULL UNIQUE,
 description VARCHAR(500) NOT NULL DEFAULT '',
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE tags (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 slug VARCHAR(180) NOT NULL UNIQUE,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE posts (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 author_id BIGINT UNSIGNED NOT NULL,
 category_id BIGINT UNSIGNED NULL,
 title VARCHAR(200) NOT NULL,
 slug VARCHAR(200) NOT NULL UNIQUE,
 excerpt VARCHAR(500) NOT NULL DEFAULT '',
 content MEDIUMTEXT NOT NULL,
 cover_image VARCHAR(255) NULL,
 status ENUM('draft','pending','published','rejected') NOT NULL DEFAULT 'draft',
 rejection_reason VARCHAR(2000) NULL,
 is_featured TINYINT(1) NOT NULL DEFAULT 0,
 views BIGINT UNSIGNED NOT NULL DEFAULT 0,
 version INT UNSIGNED NOT NULL DEFAULT 1,
 published_at DATETIME NULL,
 deleted_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_posts_author FOREIGN KEY(author_id) REFERENCES users(id) ON DELETE RESTRICT,
 CONSTRAINT fk_posts_category FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE RESTRICT,
 INDEX idx_posts_public(status,deleted_at,published_at),
 INDEX idx_posts_author(author_id,status,deleted_at),
 INDEX idx_posts_category(category_id,status,deleted_at),
 INDEX idx_posts_views(status,deleted_at,views)
) ENGINE=InnoDB;
CREATE TABLE post_tags (
 post_id BIGINT UNSIGNED NOT NULL,
 tag_id BIGINT UNSIGNED NOT NULL,
 PRIMARY KEY(post_id,tag_id),
 CONSTRAINT fk_pt_post FOREIGN KEY(post_id) REFERENCES posts(id) ON DELETE CASCADE,
 CONSTRAINT fk_pt_tag FOREIGN KEY(tag_id) REFERENCES tags(id) ON DELETE CASCADE,
 INDEX idx_pt_tag(tag_id,post_id)
) ENGINE=InnoDB;
CREATE TABLE comments (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 post_id BIGINT UNSIGNED NOT NULL,
 user_id BIGINT UNSIGNED NOT NULL,
 parent_id BIGINT UNSIGNED NULL,
 content VARCHAR(2000) NOT NULL,
 status ENUM('pending','approved','hidden') NOT NULL DEFAULT 'pending',
 version INT UNSIGNED NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_comments_post FOREIGN KEY(post_id) REFERENCES posts(id) ON DELETE RESTRICT,
 CONSTRAINT fk_comments_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE RESTRICT,
 CONSTRAINT fk_comments_parent FOREIGN KEY(parent_id) REFERENCES comments(id) ON DELETE RESTRICT,
 INDEX idx_comments_thread(post_id,parent_id,status,created_at),
 INDEX idx_comments_status(status,created_at)
) ENGINE=InnoDB;
CREATE TABLE post_history (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 post_id BIGINT UNSIGNED NOT NULL,
 actor_id BIGINT UNSIGNED NOT NULL,
 action VARCHAR(30) NOT NULL,
 from_status VARCHAR(20) NULL,
 to_status VARCHAR(20) NOT NULL,
 note VARCHAR(2000) NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_history_post FOREIGN KEY(post_id) REFERENCES posts(id) ON DELETE RESTRICT,
 CONSTRAINT fk_history_actor FOREIGN KEY(actor_id) REFERENCES users(id) ON DELETE RESTRICT,
 INDEX idx_history_post(post_id,created_at)
) ENGINE=InnoDB;
CREATE TABLE rate_limits (
 bucket CHAR(64) PRIMARY KEY,
 attempts INT UNSIGNED NOT NULL DEFAULT 0,
 reset_at DATETIME NOT NULL,
 INDEX idx_rate_reset(reset_at)
) ENGINE=InnoDB;


START TRANSACTION;
INSERT INTO users(name,email,password_hash,role) VALUES ('Nguyễn Khôi Nguyên','admin@newsblog.local','$2y$10$I1k/FG.OgD4N5g9A9FQHBuysHlxoQdqzUggSl1Ho6Ipi0HAOXlQ.S','ADMIN');
INSERT INTO users(name,email,password_hash,role) VALUES ('Nguyễn Lê Duy Ân','author1@newsblog.local','$2y$10$I1k/FG.OgD4N5g9A9FQHBuysHlxoQdqzUggSl1Ho6Ipi0HAOXlQ.S','AUTHOR');
INSERT INTO users(name,email,password_hash,role) VALUES ('Nguyễn Trí Phong','author2@newsblog.local','$2y$10$I1k/FG.OgD4N5g9A9FQHBuysHlxoQdqzUggSl1Ho6Ipi0HAOXlQ.S','AUTHOR');
INSERT INTO users(name,email,password_hash,role) VALUES ('Lê Tuấn Quy','reader1@newsblog.local','$2y$10$I1k/FG.OgD4N5g9A9FQHBuysHlxoQdqzUggSl1Ho6Ipi0HAOXlQ.S','USER');
INSERT INTO users(name,email,password_hash,role) VALUES ('Nguyễn Tấn Tài','reader2@newsblog.local','$2y$10$I1k/FG.OgD4N5g9A9FQHBuysHlxoQdqzUggSl1Ho6Ipi0HAOXlQ.S','USER');
INSERT INTO categories(name,slug,description) VALUES ('Công nghệ','cong-nghe','Những thay đổi số quanh chúng ta.');
INSERT INTO categories(name,slug,description) VALUES ('Đời sống','doi-song','Nhìn cuộc sống từ những điều nhỏ.');
INSERT INTO categories(name,slug,description) VALUES ('Góc học tập','goc-hoc-tap','Học tốt hơn, mỗi ngày một chút.');
INSERT INTO categories(name,slug,description) VALUES ('Cộng đồng','cong-dong','Kết nối những câu chuyện đáng chia sẻ.');
INSERT INTO tags(name,slug) VALUES ('PHP','php');
INSERT INTO tags(name,slug) VALUES ('Thiết kế','thiet-ke');
INSERT INTO tags(name,slug) VALUES ('Kỹ năng','ky-nang');
INSERT INTO tags(name,slug) VALUES ('Sinh viên','sinh-vien');
INSERT INTO tags(name,slug) VALUES ('Sáng tạo','sang-tao');
INSERT INTO tags(name,slug) VALUES ('Chuyện nhỏ','chuyen-nho');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (2,2,'Xây dựng thói quen đọc trong thời đại số','xay-dung-thoi-quen-doc','Mười lăm phút đọc mỗi ngày có thể mở ra những góc nhìn mới. Bắt đầu bằng một cuốn sách khiến bạn tò mò.','<h2>Đọc không phải một cuộc đua</h2><p>Hãy chọn thời điểm bạn thấy thoải mái nhất, cất điện thoại và dành sự chú ý cho từng trang. Một nhịp đọc đều đặn có giá trị hơn mục tiêu quá lớn.</p><h2>Ghi lại điều bạn muốn nhớ</h2><p>Sau mỗi chương, viết một câu bằng ngôn ngữ của chính mình. Bạn có thể chia sẻ với bạn bè để biến kiến thức thành một cuộc trò chuyện.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-2.svg','published',1,17,'2026-10-01 08:00:00',NULL,'2026-10-01 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (1,1),(1,3);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (1,1,'seed','published','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (3,1,'Từ ý tưởng đến một website đầu tiên bằng PHP','website-dau-tien-bang-php','Một hành trình nhỏ từ form HTML đến dữ liệu được lưu trong database, dành cho người vừa bắt đầu.','<h2>Bắt đầu từ một chức năng cụ thể</h2><p>Một trang ghi chú đơn giản giúp bạn hiểu cách trình duyệt gửi yêu cầu, PHP xử lý và MySQL lưu dữ liệu. Hãy làm cho từng bước hoạt động rồi mới mở rộng.</p><h2>Tách trách nhiệm để dễ sửa</h2><p>Model làm việc với dữ liệu, View trình bày và Controller điều phối. Cấu trúc này giúp cả nhóm tìm đúng nơi cần thay đổi.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-1.svg','published',1,30,'2026-10-02 08:00:00',NULL,'2026-10-02 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (2,2),(2,4);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (2,1,'seed','published','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (2,2,'Một buổi sáng chậm giữa nhịp sống thành phố','buoi-sang-cham','Thức dậy sớm hơn một chút, đi bộ một đoạn đường quen và quan sát những điều thường bị bỏ lỡ.','<h2>Tìm khoảng trống cho mình</h2><p>Không cần một kế hoạch cầu kỳ. Một bữa sáng tử tế và vài phút nhìn ra cửa sổ cũng đủ để bắt đầu ngày mới nhẹ nhàng.</p><h2>Giữ một thói quen nhỏ</h2><p>Chọn điều bạn có thể làm ngay cả trong ngày bận rộn. Sự đều đặn giúp khoảng nghỉ ấy trở thành một phần tự nhiên của cuộc sống.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-2.svg','published',0,43,'2026-10-03 08:00:00',NULL,'2026-10-03 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (3,3),(3,5);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (3,1,'seed','published','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (3,3,'Ghi chép có hệ thống để học sâu hơn','ghi-chep-co-he-thong','Ghi chép không phải chép lại tất cả. Đó là chọn điều quan trọng và kết nối nó với những gì mình đã biết.','<h2>Đặt câu hỏi trước khi ghi</h2><p>Bạn đang tìm lời giải cho vấn đề nào? Viết câu hỏi ở đầu trang giúp bạn nhận ra phần kiến thức thực sự cần giữ lại.</p><h2>Ôn bằng cách tự giải thích</h2><p>Đóng tài liệu rồi thử trình bày lại cho một người bạn. Những điểm chưa giải thích được chính là nơi cần học thêm.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-3.svg','published',0,56,'2026-10-04 08:00:00',NULL,'2026-10-04 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (4,4),(4,6);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (4,1,'seed','published','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (2,4,'Những góc xanh được vun trồng cùng nhau','nhung-goc-xanh','Một nhóm sinh viên biến khoảng sân nhỏ thành nơi gặp gỡ, trao đổi cây và chăm sóc không gian chung.','<h2>Mỗi người góp một việc</h2><p>Người mang đất, người chuẩn bị chậu, người ghi lịch tưới. Khi công việc được chia nhỏ, ai cũng tìm được cách tham gia phù hợp.</p><h2>Chăm sóc cả sự kết nối</h2><p>Khu vườn mang lại nhiều hơn màu xanh. Những buổi gặp định kỳ tạo cơ hội lắng nghe và giúp đỡ nhau trong cuộc sống.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-4.svg','published',1,69,'2026-10-05 08:00:00',NULL,'2026-10-05 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (5,5),(5,1);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (5,1,'seed','published','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (3,1,'Thiết kế giao diện bắt đầu từ khả năng đọc','thiet-ke-kha-nang-doc','Cỡ chữ, khoảng cách và độ tương phản là nền tảng của một trang web dễ sử dụng trên cả điện thoại lẫn máy tính.','<h2>Cho nội dung một thứ tự rõ ràng</h2><p>Tiêu đề nói lên chủ đề, phần tóm tắt mở đường và các đoạn ngắn giúp người đọc theo dõi mạch ý. Hãy thử đọc trang ở kích thước màn hình nhỏ.</p><h2>Kiểm tra bằng những thao tác thật</h2><p>Dùng bàn phím để đi qua biểu mẫu, thử một tiêu đề dài và quan sát thông báo lỗi. Thiết kế tốt cần hoạt động với nội dung thực tế.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-1.svg','published',0,82,'2026-10-06 08:00:00',NULL,'2026-10-06 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (6,6),(6,2);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (6,1,'seed','published','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (2,3,'Làm việc nhóm: bắt đầu bằng một thỏa thuận nhỏ','lam-viec-nhom','Thống nhất cách đặt tên, cách chia việc và thời gian trao đổi giúp nhóm dành nhiều công sức hơn cho sản phẩm.','<h2>Chia việc theo kết quả</h2><p>Mỗi đầu việc nên có người phụ trách, dữ liệu đầu vào và dấu hiệu hoàn thành. Cả nhóm sẽ dễ nhận ra vấn đề trước khi nó thành trở ngại lớn.</p><h2>Tích hợp sớm và thường xuyên</h2><p>Đừng đợi từng phần hoàn hảo mới ghép lại. Một phiên bản nhỏ chạy được giúp mọi người kiểm tra giả định và trao đổi bằng kết quả cụ thể.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-3.svg','published',0,95,'2026-10-07 08:00:00',NULL,'2026-10-07 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (7,1),(7,3);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (7,1,'seed','published','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (3,1,'Nhật ký học SQL: hiểu khóa ngoại qua ví dụ','nhat-ky-hoc-sql','Bản nháp về quan hệ dữ liệu trong một hệ thống nhiều tác giả.','<h2>Dữ liệu cần có mối liên hệ</h2><p>Khóa ngoại giúp một bài viết luôn tham chiếu tới tác giả có thật. Đây là nền tảng để giữ dữ liệu nhất quán.</p><h2>Thử trước khi kết luận</h2><p>Hãy tạo một database thử nghiệm và quan sát điều gì xảy ra khi xóa dữ liệu đang được tham chiếu.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-1.svg','draft',0,108,NULL,NULL,'2026-10-08 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (8,2),(8,4);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (8,1,'seed','draft','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (2,4,'Kế hoạch đọc sách của câu lạc bộ','ke-hoach-doc-sach','Dự kiến hoạt động đọc và chia sẻ sách trong tháng tới.','<h2>Một chủ đề chung</h2><p>Câu lạc bộ lựa chọn một chủ đề để mỗi thành viên mang đến một cuốn sách khác nhau.</p><h2>Buổi gặp đầu tiên</h2><p>Mỗi người có năm phút chia sẻ ý tưởng mình yêu thích, sau đó cả nhóm cùng thảo luận.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-4.svg','draft',0,121,NULL,NULL,'2026-10-09 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (9,3),(9,5);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (9,1,'seed','draft','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (3,3,'Một tuần thử học bằng sơ đồ tư duy','hoc-bang-so-do-tu-duy','Bài đang chờ biên tập viên duyệt trước khi xuất bản.','<h2>Bắt đầu từ khái niệm trung tâm</h2><p>Viết chủ đề chính rồi mở rộng những nhánh liên quan bằng các từ khóa ngắn.</p><h2>Nhìn lại các kết nối</h2><p>Sau một tuần, thêm ví dụ thực tế và điều chỉnh những nhánh chưa rõ để sơ đồ phản ánh cách hiểu của mình.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-3.svg','pending',0,134,NULL,NULL,'2026-10-10 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (10,4),(10,6);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (10,1,'seed','pending','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (2,4,'Tủ sách trao đổi ở khu sinh hoạt chung','tu-sach-trao-doi','Một đề xuất nhỏ giúp những cuốn sách có thêm hành trình mới.','<h2>Cho sách cơ hội được đọc tiếp</h2><p>Người tham gia để lại một cuốn sách đã đọc và chọn một cuốn mình muốn khám phá.</p><h2>Cùng giữ tủ sách ngăn nắp</h2><p>Sổ theo dõi đơn giản và lịch chăm sóc định kỳ giúp hoạt động được duy trì lâu dài.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-4.svg','pending',0,147,NULL,NULL,'2026-10-11 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (11,5),(11,1);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (11,1,'seed','pending','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (3,3,'Đi tìm nhịp học phù hợp với chính mình','nhip-hoc-cua-minh','Bài được trả lại để bổ sung ví dụ và làm rõ các bước thực hiện.','<h2>Quan sát trước khi thay đổi</h2><p>Ghi lại thời gian mình tập trung tốt nhất thay vì áp dụng lịch học của người khác.</p><h2>Điều chỉnh từng bước</h2><p>Thử một thay đổi nhỏ trong vài ngày, quan sát kết quả rồi quyết định có tiếp tục hay không.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-3.svg','rejected',0,160,NULL,'Bổ sung ví dụ cụ thể và nguồn tham khảo cho phần hướng dẫn.','2026-10-12 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (12,6),(12,2);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (12,1,'seed','rejected','Dữ liệu demo của đồ án.');
INSERT INTO comments(post_id,user_id,parent_id,content,status) VALUES (1,4,NULL,'Mình bắt đầu từ mười trang mỗi tối, cảm ơn bài viết!','approved');
INSERT INTO comments(post_id,user_id,parent_id,content,status) VALUES (1,2,1,'Cảm ơn bạn đã chia sẻ. Giữ nhịp đọc phù hợp với mình là điều quan trọng.','approved');
INSERT INTO comments(post_id,user_id,parent_id,content,status) VALUES (1,5,NULL,'Mình muốn gợi ý thêm một vài cuốn sách.','pending');
INSERT INTO comments(post_id,user_id,parent_id,content,status) VALUES (2,4,NULL,'Bình luận gốc này đã được ẩn.','hidden');
INSERT INTO comments(post_id,user_id,parent_id,content,status) VALUES (2,3,4,'Phản hồi đã duyệt vẫn được giữ trong cuộc trò chuyện.','approved');
INSERT INTO comments(post_id,user_id,parent_id,content,status) VALUES (2,5,NULL,'<script>alert("demo")</script> Nội dung này được lưu và hiển thị như văn bản.','pending');
COMMIT;
