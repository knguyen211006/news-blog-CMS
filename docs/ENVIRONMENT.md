# Môi trường phát triển và kiểm thử

Ứng dụng dùng PHP 8.1 trở lên, MySQL 8 hoặc MariaDB 10.4 trở lên.
Các phiên bản từng được ghi nhận: PHP 8.2.12, MariaDB 10.4.32, Apache 2.4.58.
Kết quả chạy lại và phạm vi xác minh được ghi trong `TEST-RESULTS.md`.

## Cấu hình cục bộ

Dùng tài khoản MySQL cục bộ theo hướng dẫn trong README. Host, port, tài khoản
và mật khẩu do người cài đặt tự cấu hình; không ghi thông tin máy thật vào tài liệu.
`config/local.php` và `config/test.php` là cấu hình riêng, bị loại khỏi Git và ZIP.

- Database demo: `news_blog_cms`; không dùng để chạy kiểm thử.
- Database kiểm thử: `news_blog_cms_test`, qua `config/test.php`.
- Acceptance và mỗi lượt Playwright tự dọn rồi nạp lại dữ liệu database test.
- Máy chủ PHP tích hợp chỉ phục vụ kiểm thử cục bộ; chọn cổng trống theo README.
- Session được phân tách theo database và tên session trong `storage/sessions/`.
- GD không bắt buộc; Composer và Node không cần để vận hành website.

## Sự cố hệ thống và giới hạn xác minh

Nếu MySQL/MariaDB báo lỗi Aria hoặc bảng quyền, sao lưu và nhờ người quản trị
xử lý. Không tự xóa thư mục dữ liệu, sửa hay dựng lại bảng quyền hệ thống để chạy CMS.

Kiểm thử bằng PHP router không xác minh quy tắc `.htaccess` của Apache.
Kiểm tra ZIP trên database trống trong cùng bộ XAMPP không tương đương cài đặt
trên một bộ XAMPP mới. Các giới hạn và số liệu cụ thể xem `TEST-RESULTS.md`.
