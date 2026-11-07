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
INSERT INTO users(name,email,password_hash,role) VALUES ('Võ Tấn Tài','reader2@newsblog.local','$2y$10$I1k/FG.OgD4N5g9A9FQHBuysHlxoQdqzUggSl1Ho6Ipi0HAOXlQ.S','USER');
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
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (2,2,'Xây dựng thói quen đọc trong thời đại số','xay-dung-thoi-quen-doc','Mười lăm phút đọc mỗi ngày trước khi mở máy tính có thể mở ra những góc nhìn mới. Bắt đầu bằng một chương sách ngắn và đặt điện thoại úp xuống ở bàn bên cạnh.','<h2>Đặt điện thoại ra ngoài tầm tay trong mười lăm phút đầu tiên</h2><p>Trở ngại lớn nhất khi đọc một cuốn sách chuyên ngành hay tản văn dài không phải là thiếu thời gian, mà là thói quen cầm điện thoại kiểm tra tin nhắn cứ mỗi năm phút. Nhóm thử áp dụng một quy tắc nhỏ: pha một cốc trà ấm, úp màn hình điện thoại xuống bàn bên cạnh và chỉ đặt mục tiêu đọc trọn vẹn 10 đến 15 trang giấy trước khi bật máy tính làm việc.</p><h2>Tóm tắt lại bằng một câu của chính mình</h2><p>Thay vì gạch dưới kín cả trang sách, sau mỗi chương ngắn hãy gấp sách lại và viết đúng một câu tóm tắt ý cốt lõi vào sổ tay bằng ngôn ngữ của mình. Chính những câu ghi chú ngắn ấy lại trở thành nguồn chất liệu tự nhiên nhất khi cần viết bài chia sẻ trên Góc Nhìn.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-2.svg','published',1,17,'2026-10-01 08:00:00',NULL,'2026-10-01 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (1,1),(1,3);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (1,1,'seed','published','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (3,1,'Từ ý tưởng đến một website đầu tiên bằng PHP','website-dau-tien-bang-php','Một hành trình nhỏ từ form HTML đến dữ liệu được lưu trong database, dành cho người vừa bắt đầu làm quen với mô hình MVC và PDO trong PHP thuần.','<h2>Bài học từ tuần đầu viết gộp SQL chung với giao diện</h2><p>Ở những buổi đầu dựng thử trang tin tức trên XAMPP, phản xạ tự nhiên của người mới học là đặt câu lệnh SELECT và INSERT ngay trong cùng một tệp HTML cho nhanh thấy kết quả. Nhưng chỉ sau vài ngày, khi một bạn trong nhóm chỉnh lại khung thẻ bài viết thì vô tình làm mất biến kiểm tra CSRF của biểu mẫu lưu nháp. Từ sự cố đó, nhóm quyết định dừng lại một buổi tối để tách toàn bộ mã nguồn về đúng ba lớp: Router và Controller nhận yêu cầu ở cổng vào, Model xử lý giao dịch PDO có khóa dòng FOR UPDATE, còn View chỉ đảm nhiệm trình bày và thoát mã HTML bằng hàm e().</p><h2>Giữ luồng dữ liệu an toàn từ những chi tiết nhỏ</h2><p>Khi tự viết hệ thống không dựa vào framework lớn, từng bước kiểm tra đều giúp cả nhóm hiểu sâu cách trình duyệt và máy chủ giao tiếp: từ việc truyền mã phiên bản (version) ẩn trong biểu mẫu để hai biên tập viên không duyệt đè lên nhau, lọc thẻ HTML bằng HTML Purifier trước khi lưu vào MySQL, cho đến việc kiểm tra định dạng thực của ảnh tải lên bằng finfo thay vì chỉ tin vào đuôi tệp.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-1.svg','published',1,30,'2026-10-02 08:00:00',NULL,'2026-10-02 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (2,2),(2,4);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (2,1,'seed','published','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (2,2,'Một buổi sáng chậm giữa nhịp sống thành phố','buoi-sang-cham','Rời bàn máy tính sớm hơn hai mươi phút, đi bộ dưới hàng cây lúc nắng mới lên để đầu óc tỉnh táo trước khi bắt đầu một ngày học và sửa lỗi chương trình.','<h2>Hai mươi phút không màn hình trước giờ vào ca học sáng</h2><p>Sau những buổi tối thức khuya rà lỗi giao diện và cơ sở dữ liệu, việc mở mắt ra là nhìn ngay vào màn hình điện thoại thường khiến cả buổi sáng rơi vào trạng thái uể oải. Thử dậy sớm hơn 20 phút, đi bộ hoặc đạp xe chậm rãi qua đoạn đường có bóng cây quanh trường giúp mắt được điều tiết ở khoảng nhìn xa tự nhiên.</p><h2>Giải pháp cho những lỗi code khó thường đến lúc đi bộ</h2><p>Rất nhiều lần trong lúc đang bước đi thoải mái mà không nhìn vào dòng lệnh nào, các thành viên trong nhóm lại chợt nhận ra nguyên nhân thật sự của một lỗi khóa ngoại hay lỗi tràn khung CSS tối hôm trước. Một nhịp nghỉ ngắn đầu ngày chính là khoản đầu tư hiệu quả nhất cho sự tập trung.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-6.svg','published',0,43,'2026-10-03 08:00:00',NULL,'2026-10-03 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (3,3),(3,5);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (3,1,'seed','published','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (3,3,'Ghi chép có hệ thống để học sâu hơn','ghi-chep-co-he-thong','Chia trang sổ tay thành ba vùng theo cách ghi chép Cornell: cột ý chính bên trái, nội dung thực hành bên phải và ba dòng tổng kết ở cuối trang để ôn tập nhanh trước kỳ thi.','<h2>Đặt câu hỏi ở lề trái thay vì chép nguyên lời giảng</h2><p>Khi nghe giảng hoặc đọc tài liệu kỹ thuật dài, việc cố chép lại từng chữ khiến chúng ta không kịp suy nghĩ xem phần đó giải quyết vấn đề gì. Bằng cách kẻ một đường dọc chia trang giấy làm hai phần theo tỷ lệ 3:7, cột nhỏ bên trái chỉ dùng để ghi câu hỏi hoặc từ khóa (ví dụ: “Khi nào trả mã HTTP 409?”), còn cột bên phải ghi ví dụ minh họa ngắn gọn.</p><h2>Ba dòng đúc kết ở chân trang cho lần ôn lại</h2><p>Chừa lại 4 dòng trống dưới cùng mỗi trang sổ để viết phần kết luận sau buổi học 24 giờ. Khi cần ôn tập trước buổi vấn đáp đồ án, chỉ cần che cột phải và nhìn vào chuỗi câu hỏi ở cột trái để tự trả lời là biết ngay phần kiến thức nào mình đã thực sự nắm vững.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-3.svg','published',0,56,'2026-10-04 08:00:00',NULL,'2026-10-04 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (4,4),(4,6);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (4,1,'seed','published','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (2,4,'Những góc xanh được vun trồng cùng nhau','nhung-goc-xanh','Một nhóm sinh viên biến khoảng sân nhỏ sau giờ học thành nơi ươm những chậu hương thảo, bạc hà và chia nhau lịch trực tưới nước mỗi sáng trước khi vào lớp.','<h2>Bắt đầu từ năm chậu đất nung và bảng lịch trực viết tay</h2><p>Khoảng hiên nắng gắt phía sau khu tự học từng chỉ để vài chiếc ghế gỗ cũ. Đầu học kỳ, nhóm rủ nhau mỗi người góp một việc nhỏ: người xin giống cây hương thảo và bạc hà dễ sống, người mua đất trộn trấu, người kẻ bảng lịch tưới nước dán ngay cạnh vòi nước chung. Khi công việc được chia thành từng ca 10 phút đầu giờ sáng, việc chăm cây không còn là gánh nặng của riêng ai.</p><h2>Khoảng nghỉ mắt sau những giờ nhìn màn hình code</h2><p>Sau hai tháng, góc bàn gỗ giữa những chậu cây xanh trở thành điểm hẹn quen thuộc để cả nhóm ngồi rà lại bài tập lớn hoặc đơn giản là đứng dậy vươn vai sau hai tiếng liền tìm lỗi SQL. Một không gian chung được tự tay chăm chút luôn tạo ra sự gắn kết tự nhiên hơn bất kỳ khẩu hiệu nào.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-4.svg','published',1,69,'2026-10-05 08:00:00',NULL,'2026-10-05 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (5,5),(5,1);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (5,1,'seed','published','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (3,1,'Thiết kế giao diện bắt đầu từ khả năng đọc','thiet-ke-kha-nang-doc','Cỡ chữ, khoảng cách dòng, phông chữ hỗ trợ đầy đủ dấu tiếng Việt và độ tương phản đạt chuẩn WCAG AA là nền tảng để trang báo đọc êm mắt trên cả điện thoại 320px lẫn máy tính.','<h2>Phát hiện lỗi tách dấu tiếng Việt và độ tương phản khi chạy thật</h2><p>Một giao diện nhìn thoáng trên bản vẽ chưa chắc đã dễ đọc khi đổ dữ liệu tiếng Việt thực tế vào. Khi mở trang trên Chrome ở máy Windows, nhóm phát hiện các tiêu đề chứa nguyên âm ghép như “vun trồng” hay “bắt đầu” bị hở khoảng trắng ngay sau dấu mũ và dấu sắc do phông chữ Cambria và Georgia thiếu ký tự tiếng Việt dựng sẵn. Việc chuyển sang bộ phông hệ thống hỗ trợ trọn vẹn bảng mã Unicode tiếng Việt cùng độ rộng cột đọc 68 ký tự (68ch) giúp mắt người đọc theo dõi mạch bài dài mà không bị mỏi.</p><h2>Đo kiểm trên cả chế độ Sáng, Tối và màn hình 320px</h2><p>Thay vì đoán bằng mắt thường, nhóm dùng bộ kiểm thử tự động quét qua cả chế độ Tối (#0B0F19) và chế độ Sáng màu giấy ấm (#FAF7F2). Những chi tiết nhỏ như tăng độ đậm của nhãn trạng thái “Chờ duyệt” từ 4.1:1 lên 7.68:1, giữ kích thước vùng chạm tối thiểu 44×44px cho nút chuyển màu, hay gom gọn các mục quản trị vào menu Tòa soạn trên màn hình hẹp 320px đều xuất phát từ những lần bấm thử bằng bàn phím và điện thoại thật.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-5.svg','published',0,82,'2026-10-06 08:00:00',NULL,'2026-10-06 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (6,6),(6,2);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (6,1,'seed','published','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (2,3,'Làm việc nhóm: bắt đầu bằng một thỏa thuận nhỏ','lam-viec-nhom','Thống nhất cách tách cấu hình máy cá nhân, cô lập database kiểm thử và giữ hợp đồng tên trường giữa Backend với Frontend giúp nhóm 5 thành viên phối hợp nhịp nhàng.','<h2>Ba thỏa thuận nhỏ giải quyết xung đột khi ghép nhánh Git</h2><p>Khi 5 thành viên cùng làm việc trên một kho mã nguồn PHP và MySQL, rắc rối đầu tiên không nằm ở thuật toán khó mà ở môi trường chạy: mỗi máy đặt mật khẩu và cổng MariaDB khác nhau, nên mỗi lần đẩy tệp cấu hình lên là máy người khác báo lỗi kết nối. Nhóm lập tức thống nhất thỏa thuận đầu tiên: tách riêng config/local.php khỏi Git, dùng scripts/build-demo.php để sinh dữ liệu mẫu chuẩn và tách hẳn cơ sở dữ liệu news_blog_cms_test cho các kịch bản kiểm thử tự động.</p><h2>Kiểm tra tự động trước mỗi lần bàn giao giao diện</h2><p>Để người làm giao diện yên tâm chỉnh sửa CSS và bố cục mà không làm hỏng luồng xử lý của người viết Backend, nhóm xây dựng tệp kiểm tra hợp đồng View (tests/view-contract.php) và 11 bài kiểm tra hồi quy không chạm database (tests/regression-readonly.php). Chỉ cần chạy hai lệnh kiểm tra nhanh trong vài giây, cả nhóm biết chắc các biểu mẫu đăng nhập, duyệt bài và bình luận vẫn khớp hoàn toàn.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-7.svg','published',0,95,'2026-10-07 08:00:00',NULL,'2026-10-07 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (7,1),(7,3);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (7,1,'seed','published','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (3,1,'Nhật ký học SQL: hiểu khóa ngoại qua ví dụ','nhat-ky-hoc-sql','Ghi chép thực hành của nhóm khi thiết kế 8 bảng InnoDB cho Góc Nhìn: vì sao khóa ngoại chuyên mục dùng ON DELETE RESTRICT còn bảng thẻ bài viết dùng ON DELETE CASCADE.','<h2>Hiểu sự khác biệt giữa RESTRICT và CASCADE qua tình huống thật</h2><p>Lý thuyết về khóa ngoại (Foreign Key) trở nên rất rõ ràng khi nhóm đặt câu hỏi: chuyện gì xảy ra nếu quản trị viên bấm xóa một chuyên mục đang có 3 bài viết đã xuất bản? Nhờ khai báo CONSTRAINT fk_posts_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT, MySQL lập tức chặn thao tác xóa đó và hệ thống hiển thị thông báo rõ ràng thay vì để lại những bài viết mồ côi không có chuyên mục.</p><h2>Tự tham chiếu hai cấp cho cây bình luận</h2><p>Ngược lại, ở bảng trung gian post_tags nối giữa bài viết và thẻ (Tags), nhóm chọn ON DELETE CASCADE để khi xóa một thẻ, chỉ các dòng liên kết trong bảng trung gian được dọn sạch còn bài viết gốc vẫn an toàn. Với bảng comments, cột parent_id tự tham chiếu về chính comments(id) kết hợp kiểm tra ở tầng Model giúp giữ cấu trúc thảo luận đúng 2 cấp gọn gàng.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-8.svg','draft',0,108,NULL,NULL,'2026-10-08 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (8,2),(8,4);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (8,1,'seed','draft','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (2,4,'Kế hoạch đọc sách của câu lạc bộ','ke-hoach-doc-sach','Lịch sinh hoạt đọc sách tháng này của nhóm: mỗi thành viên chọn một chương ngắn về thiết kế web, cơ sở dữ liệu hoặc kỹ năng viết để trình bày trong 7 phút chiều thứ Bảy.','<h2>Mỗi người chỉ chọn một chương thực sự ứng dụng được ngay</h2><p>Thay vì yêu cầu cả nhóm cùng đọc hết một cuốn sách dày 400 trang trong một tháng, câu lạc bộ nhỏ của nhóm đổi sang hình thức mỗi thành viên tự chọn một chương sách gắn trực tiếp với phần việc mình đang làm trong đồ án để tóm tắt lại cho 4 người còn lại.</p><h2>Bảy phút chia sẻ và mười lăm phút hỏi đáp thực tế</h2><p>Trong buổi gặp chiều thứ Bảy, mỗi người có đúng 7 phút trình bày 3 ý tâm đắc nhất và cách áp dụng thẳng vào dự án Góc Nhìn (như cách đặt độ tương phản màu hay cách viết thông báo lỗi thân thiện). Cách đọc có mục tiêu cụ thể giúp buổi sinh hoạt luôn sôi nổi và không ai bị quá tải.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-11.svg','draft',0,121,NULL,NULL,'2026-10-09 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (9,3),(9,5);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (9,1,'seed','draft','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (3,3,'Một tuần thử học bằng sơ đồ tư duy','hoc-bang-so-do-tu-duy','Thử nghiệm vẽ sơ đồ tư duy cho môn Lập trình Web trong 7 ngày: từ bản nháp đầu tiên ôm đồm cả câu định nghĩa dài đến bản vẽ lại chỉ giữ từ khóa và tên bảng giúp cả nhóm nhớ chắc luồng duyệt bài.','<h2>Bản nháp ngày đầu: ôm đồm nên nhìn lại vẫn rối</h2><p>Khi bắt đầu ôn tập phần phân quyền và quy trình xuất bản của đồ án Góc Nhìn, cả nhóm thử vẽ chung một sơ đồ tư duy trên giấy khổ A4. Sai lầm của ba ngày đầu là ai cũng sợ thiếu ý nên chép nguyên cả câu dài vào nhánh con, lại gom chung trạng thái bài viết (draft, pending, published, rejected) với trạng thái bình luận (pending, approved, hidden) vào cùng một cụm. Đến khi gấp tài liệu lại để tự giải thích luồng dữ liệu, không ai nhớ chính xác bước nào tăng biến version và bước nào ghi vào bảng post_history.</p><h2>Phần được gạch sửa sau một tuần thực hành</h2><p>Cuối tuần, nhóm quyết định gạch bỏ toàn bộ nhánh văn bản dài ở góc trái sơ đồ và vẽ lại theo 3 nguyên tắc: mỗi nhánh chỉ gắn với tên bảng hoặc tên cột thực tế trong database, tách riêng nhánh Luồng duyệt bài (4 trạng thái kèm điều kiện bắt buộc nhập lý do khi trả bài) khỏi nhánh Phân quyền 3 vai trò (USER, AUTHOR, ADMIN), và dùng mũi tên ghi rõ ràng buộc khóa ngoại (ON DELETE RESTRICT cho chuyên mục, ON DELETE CASCADE cho bảng trung gian post_tags). Bản sơ đồ sau khi sửa trở thành tài liệu tra cứu nhanh mỗi khi nhóm viết kiểm thử nghiệm thu.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-10.svg','pending',0,134,NULL,NULL,'2026-10-10 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (10,4),(10,6);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (10,1,'seed','pending','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (2,4,'Tủ sách trao đổi ở khu sinh hoạt chung','tu-sach-trao-doi','Kệ gỗ hai tầng đặt ở góc phòng sinh hoạt chung với quy tắc đơn giản: ghi tên vào sổ tay khi mượn sách và kẹp lại một mảnh giấy nhỏ ghi chương tâm đắc nhất khi trả.','<h2>Để những cuốn sách giáo trình và kỹ năng không nằm yên trên giá</h2><p>Nhiều cuốn sách hay về thiết kế giao diện, cấu trúc dữ liệu hay kỹ năng viết chỉ được đọc một lần rồi cất kỹ. Nhóm đặt một kệ gỗ nhỏ hai tầng ở góc phòng sinh hoạt chung kèm cuốn sổ bìa cứng kẻ sẵn ba cột: Tên sách, Người mượn và Ngày hẹn trả. Ai mang đến một cuốn sách cũ đều có thể đổi lấy một cuốn khác về đọc trong hai tuần.</p><h2>Những tờ giấy ghi chú kẹp lại giữa trang sách</h2><p>Điều thú vị nhất của tủ sách không chỉ nằm ở số đầu sách được luân chuyển, mà ở những mảnh giấy nhỏ người đọc trước kẹp lại ở trang họ thấy tâm đắc nhất. Người đọc sau vừa tiết kiệm thời gian tìm ý chính, vừa có cảm giác như đang trò chuyện cùng một người bạn vừa đi qua môn học đó.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-9.svg','pending',0,147,NULL,NULL,'2026-10-11 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (11,5),(11,1);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (11,1,'seed','pending','Dữ liệu demo của đồ án.');
INSERT INTO posts(author_id,category_id,title,slug,excerpt,content,cover_image,status,is_featured,views,published_at,rejection_reason,created_at) VALUES (3,3,'Đi tìm nhịp học phù hợp với chính mình','nhip-hoc-cua-minh','Thử nghiệm chia khung giờ tự học thành các chặng 45 phút tập trung tuyệt đối kèm 10 phút nghỉ giải lao, ghi lại khung giờ bản thân viết code và đọc tài liệu hiệu quả nhất.','<h2>Quan sát khung giờ vàng của chính mình trong ba ngày liên tiếp</h2><p>Không phải ai cũng học tốt lúc nửa đêm hay lúc 5 giờ sáng. Trước khi ép mình theo lịch biểu mẫu trên mạng, hãy thử ghi chú lại mức độ tập trung của bản thân qua ba khung giờ Sáng, Chiều và Tối trong 3 ngày liên tiếp để nhận ra lúc nào mình giải quyết các bài toán logic nhanh nhất.</p><h2>Tách bạch giờ đọc hiểu sâu và giờ làm việc vặt</h2><p>Khi đã tìm ra nhịp học riêng, hãy dành trọn khung giờ tỉnh táo nhất cho việc thiết kế cấu trúc hoặc viết mã nguồn cốt lõi, và đẩy các việc nhẹ nhàng hơn như kiểm tra chính tả, căn chỉnh ảnh bìa hay trả lời bình luận sang cuối buổi chiều.</p><p>Hãy chia sẻ trải nghiệm của bạn trong phần bình luận để câu chuyện có thêm những góc nhìn.</p>','assets/images/cover-12.svg','rejected',0,160,NULL,'Bổ sung ví dụ cụ thể và nguồn tham khảo cho phần hướng dẫn.','2026-10-12 08:00:00');
INSERT INTO post_tags(post_id,tag_id) VALUES (12,6),(12,2);
INSERT INTO post_history(post_id,actor_id,action,to_status,note) VALUES (12,1,'seed','rejected','Dữ liệu demo của đồ án.');
INSERT INTO comments(post_id,user_id,parent_id,content,status) VALUES (1,4,NULL,'Mình bắt đầu từ mười trang mỗi tối, cảm ơn bài viết!','approved');
INSERT INTO comments(post_id,user_id,parent_id,content,status) VALUES (1,2,1,'Cảm ơn bạn đã chia sẻ. Giữ nhịp đọc phù hợp với mình là điều quan trọng.','approved');
INSERT INTO comments(post_id,user_id,parent_id,content,status) VALUES (1,5,NULL,'Mình muốn gợi ý thêm một vài cuốn sách.','pending');
INSERT INTO comments(post_id,user_id,parent_id,content,status) VALUES (2,4,NULL,'Bình luận gốc này đã được ẩn.','hidden');
INSERT INTO comments(post_id,user_id,parent_id,content,status) VALUES (2,3,4,'Phản hồi đã duyệt vẫn được giữ trong cuộc trò chuyện.','approved');
INSERT INTO comments(post_id,user_id,parent_id,content,status) VALUES (2,5,NULL,'<script>alert("demo")</script> Nội dung này được lưu và hiển thị như văn bản.','pending');
COMMIT;
