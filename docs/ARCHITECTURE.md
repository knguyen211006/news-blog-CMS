# Kiến trúc và quy tắc nghiệp vụ

## Luồng MVC

`index.php` nạp `app/bootstrap.php`, tra bảng `config/routes.php`, kiểm tra CSRF cho tất cả POST rồi gọi controller. Controller kiểm tra vai trò, nhận dữ liệu và gọi model. Model quản lý truy vấn PDO, transaction và các ràng buộc nghiệp vụ. View chỉ trình bày dữ liệu, escape với `e()`; duy nhất nội dung bài đã lọc bởi HTML Purifier được render dạng HTML.

Không cần Composer, Node hoặc API ngoài khi chạy. Thư viện đã nằm trong `assets/vendor` và `vendor/htmlpurifier`. Git dùng để quản lý thay đổi, không cần cho người chỉ chạy ứng dụng.

## Module và phân công

| Thành viên | Module giải thích / bảo vệ | File chính |
|---|---|---|
| Nguyễn Khôi Nguyên — trưởng nhóm | MVC, session, CSRF, đăng ký/đăng nhập, phân quyền, tài khoản, tích hợp | app/Core/*; AuthController.php; Models/User.php; config/routes.php; README.md |
| Nguyễn Trí Phong | Trang chủ, tìm kiếm, chuyên mục/thẻ công khai, chi tiết, view counter | PublicController.php; Post::publicList; Views/public/*; assets/css/app.css |
| Nguyễn Lê Duy Ân | CRUD bài, editor, ảnh, transaction bài/thẻ, gửi duyệt | PostController.php; Post::save/transition; Security::upload/html; Views/posts/*; assets/js/app.js |
| Lê Tuấn Quy | Duyệt/trả/gỡ bài, dashboard, chuyên mục/thẻ | AdminController.php; Models/Taxonomy.php; Views/admin/*; Post::transition |
| Võ Tấn Tài | Bình luận hai cấp, kiểm duyệt, demo, nghiệm thu | CommentController.php; Models/Comment.php; database/*; scripts/build-demo.php; tests/* |

## Trạng thái bài

- Tạo mới luôn `draft`, bỏ qua mọi tham số tự gửi để xuất bản hoặc đổi tác giả.
- AUTHOR sửa/xóa mềm bài của mình khi `draft` hoặc `rejected`.
- Gửi duyệt cần tiêu đề 5–200 ký tự, tóm tắt 10–500, nội dung sau bỏ thẻ tối thiểu 30 ký tự, chuyên mục hợp lệ.
- `draft/rejected -> pending`: tác giả sở hữu hoặc Admin.
- `pending -> rejected`: Admin, lý do 5–2000 ký tự.
- `pending -> published`: Admin; ghi published_at.
- `published -> draft`: Admin gỡ bài trước khi sửa; published_at được xóa.
- Không sửa `pending/published` trực tiếp, kể cả Admin.
- Mỗi lần lưu/chuyển trạng thái tăng `version`. Form gửi phiên bản đã đọc. Transaction khóa dòng với SELECT FOR UPDATE và đối chiếu version; lỗi 409 nếu đã có thay đổi.
- `post_history` giữ người thao tác, trạng thái, lý do. Đây là nhật ký hành động; không phải hệ thống lưu mọi phiên bản nội dung để khôi phục.
- Slug bài được sinh từ tiêu đề và mã ngẫu nhiên, ổn định khi sửa. Slug chuyên mục/thẻ duy nhất và có thể sửa bởi Admin.

## Quan hệ và xóa dữ liệu

8 bảng InnoDB, utf8mb4_unicode_ci. Sáu bảng yêu cầu và hai bảng hỗ trợ: `post_history`, `rate_limits`.

- Bài xóa mềm bằng deleted_at, không xóa post_tags, comments hoặc lịch sử.
- Không có chức năng xóa vật lý tài khoản; dùng khóa tài khoản, giữ quan hệ tác giả và bình luận.
- category_id có thể null ở nháp, phải hợp lệ trước khi gửi duyệt.
- Xóa chuyên mục bị khóa ngoại RESTRICT chặn nếu còn bất kỳ bài tham chiếu, kể cả bài xóa mềm.
- Xóa thẻ CASCADE chỉ trên post_tags, không xóa bài.
- Bình luận không xóa vật lý; dùng pending/approved/hidden. parent_id tự tham chiếu RESTRICT.
- Model kiểm tra cha cùng bài, là gốc đã duyệt. Chỉ một cấp trả lời. Ẩn cha thay nội dung bằng thông báo và vẫn hiện phản hồi đã duyệt.
- post_tags có khóa chính kép (post_id, tag_id).
- Các bộ đếm giới hạn chỉ lưu khóa SHA256, số lần và hạn cửa sổ. Có thể dọn dòng hết hạn bằng SQL sau nếu cần, không ảnh hưởng nội dung.

## Bảo mật và giới hạn

- PDO native prepared statements; tên bảng động chỉ lấy từ whitelist categories/tags.
- Băm mật khẩu PHP PASSWORD_DEFAULT, độ dài 8–72 ký tự và tối đa 72 byte cho bcrypt. Không dùng tài khoản demo ngoài local.
- Session ID đổi sau đăng nhập. Cookie HttpOnly, SameSite=Lax, Secure khi chạy HTTPS. Path đúng thư mục con.
- Mỗi yêu cầu đọc lại trạng thái tài khoản từ database, tài khoản khóa mất quyền ngay ở yêu cầu tiếp theo.
- Bảo vệ Admin cuối cùng bằng transaction và khóa các Admin hoạt động.
- Login: tối đa 5 lần theo email / 15 phút; 25 lần theo IP / 15 phút. Email được xóa bộ đếm khi đăng nhập đúng; bộ đếm IP tính mọi lần thử để giảm dò tài khoản.
- Đăng ký: 12 lần theo IP / giờ. Bình luận: một lần / 20 giây theo tài khoản, tính cả yêu cầu không hợp lệ.
- CSRF bắt buộc cho mọi POST; GET không thay nội dung, ngoại trừ lượt xem và session phục vụ đọc bài.
- Rich text: HTML Purifier whitelist thẻ văn bản, danh sách, code, link. Không cho script, iframe, style tùy ý hoặc ảnh nhúng bên ngoài. Ảnh đại diện có luồng upload riêng.
- MIME thực và getimagesize, JPEG/PNG/WebP <= 2 MB, tối đa 8000 × 8000; tên tệp ngẫu nhiên. Upload không được thực thi PHP. Không giải mã/chuyển đổi ảnh vì XAMPP hiện chưa bật GD.
- Public query luôn lọc published + deleted_at IS NULL.
- Session đếm mỗi bài một lần / 1800 giây. Đây không phải đo người dùng duy nhất: cookie mới/phiên mới được tính lượt mới.
- Log không hiện ra màn hình. Apache chặn toàn bộ đường dẫn nội bộ; router PHP thử nghiệm cũng có danh sách tài nguyên cho phép.
- Lưu bài/thẻ và thay đổi trạng thái dùng transaction. Ảnh mới được xóa nếu lưu bài thất bại; ảnh cũ được giữ lại để tránh xóa nhầm tài nguyên và để phục vụ lịch sử local. Dọn file không còn tham chiếu là công việc bảo trì, chưa có màn hình riêng.

## Sơ đồ chỉnh sửa

- `diagrams/use-case.puml`: PlantUML Use Case.
- `diagrams/erd.mmd`: Mermaid ERD.
- `diagrams/publishing-sequence.mmd`: Mermaid Sequence quy trình xuất bản.
- `diagrams/comment-sequence.mmd`: Mermaid Sequence bình luận.

Có thể sửa trực tiếp bằng VS Code/Notepad; dùng extension Mermaid/PlantUML để xuất SVG/PDF phục vụ báo cáo. Các công cụ sơ đồ không bắt buộc để chạy website.
