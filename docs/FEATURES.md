# Chức năng bàn giao

| Nhóm | Hiện thực |
|---|---|
| Tài khoản | Đăng ký USER, đăng nhập/đăng xuất, email duy nhất, băm mật khẩu, đổi hồ sơ/mật khẩu, khóa/mở khóa, cấp vai trò, bảo vệ Admin cuối cùng |
| Công khai | Bài mới, bài nổi bật, chuyên mục/thẻ, tìm tiêu đề/tóm tắt, phân trang, chi tiết bài, ảnh, tác giả, ngày, lượt xem |
| Tác giả | Tạo/sửa nháp và bài bị trả của mình, editor Pell, ảnh, thẻ/chuyên mục, xem trước, gửi duyệt, xem trạng thái/lý do |
| Quản trị | Toàn bộ bài, duyệt/trả/gỡ, đánh dấu nổi bật khi sửa nháp, chuyên mục/thẻ, tài khoản, bình luận, dashboard |
| Bình luận | Văn bản thuần, duyệt trước, một cấp phản hồi, ẩn của mình, ẩn cha giữ con đã duyệt |
| Dữ liệu | 8 bảng InnoDB utf8mb4, FK/index/unique, transaction, version, xóa mềm, lịch sử duyệt |
| Bảo mật | CSRF, prepared statements, escape, HTML Purifier, giới hạn thao tác, upload MIME, chặn tệp nội bộ, không lộ lỗi SQL |
| Bàn giao | SQL có demo, cấu hình mẫu, README, phân công, Use Case/ERD/Sequence, bộ kiểm thử HTTP, kết quả kiểm thử |

## Phạm vi không nằm trong đồ án

Không có gửi email/reset mật khẩu bằng email, đăng nhập mạng xã hội, thông báo realtime, cộng tác soạn thảo, khôi phục từng phiên bản nội dung hoặc triển khai production. Phần nhiều lượt xem đếm theo session, không nhận diện người thật xuyên thiết bị.

## Hạn chế môi trường hiện tại

Xem ENVIRONMENT.md và TEST-RESULTS.md để phân biệt các bước đã chạy thật với các bước cần thao tác người dùng. Không thay đổi hoặc sửa bảng hệ thống MySQL khi có lỗi sẵn trên máy.
