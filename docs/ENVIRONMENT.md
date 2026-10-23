# Biên bản môi trường máy Nguyễn Khôi Nguyên

Kiểm tra thực tế ngày 23/10/2026, múi giờ Asia/Bangkok (cùng UTC+7 với Việt Nam).

| Hạng mục | Kết quả quan sát |
|---|---|
| XAMPP | C:\xampp |
| DocumentRoot | C:/xampp/htdocs |
| Apache | 2.4.58, đang chạy trên cổng 80; localhost trả HTTP 302 tới /dashboard/ |
| PHP | 8.2.12; CLI C:\xampp\php\php.exe |
| MariaDB | 10.4.32, cổng 3306, kết nối TCP 127.0.0.1 thành công |
| PDO / pdo_mysql | Có |
| mbstring / fileinfo / DOM / iconv / session / OpenSSL | Có |
| GD | Chưa bật; ứng dụng không cần GD |
| rewrite_module / AllowOverride | Rewrite đã bật, htdocs AllowOverride All |
| phpMyAdmin | auth_type=config; host=127.0.0.1; root; mật khẩu trống được đọc từ cấu hình và xác minh bằng kết nối |
| Git | 2.53.0.windows.2 |
| Node / Python | Đã có; không cần để chạy website |
| Composer | Không có trong PATH; không cần vì thư viện được đóng gói |
| Database main | news_blog_cms, 8 bảng; 5 users, 4 categories, 6 tags, 12 posts, 6 comments |
| Database nghiệm thu | news_blog_cms_test, độc lập với demo chính |
| Cấu hình thực | config/local.php; config/test.php; đều bị Git bỏ qua |

## Giới hạn quyền công cụ

Công cụ đọc được các file XAMPP cụ thể và kết nối localhost/MariaDB, nhưng không tạo được thư mục dưới C:\Users\Nguyen hoặc C:\xampp\htdocs. Mã nguồn được tạo tại:

C:\Users\Nguyen\AppData\Local\Temp\news-blog-cms-work\news-blog-cms

Máy chủ PHP thử nghiệm đã phục vụ ứng dụng ở http://127.0.0.1:8088/news-blog-cms/ và bản nghiệm thu ở http://127.0.0.1:8089/news-blog-cms/. Đây là các server tạm, không phải URL Apache bàn giao.

Không sửa httpd.conf, php.ini, my.ini hoặc dữ liệu dự án khác. Session được lưu trong storage/sessions của dự án để không phụ thuộc quyền ghi C:\xampp\tmp.

## Lỗi có sẵn của MariaDB

Khi tạo tài khoản database riêng cho ứng dụng, GRANT gặp lỗi Aria 176, Read page with wrong checksum. CHECK TABLE mysql.db trả Corrupt; mysql.global_priv trả OK.

Schema và dữ liệu CMS dùng InnoDB đã được import thành công trước lỗi cấp quyền. Cấu hình local dùng kết nối root đã xác minh; không đổi mật khẩu root. Một tài khoản nb_app_* đã được tạo trước lúc GRANT thất bại nhưng không được ứng dụng sử dụng. Tài khoản ngẫu nhiên nb_test_* cho database kiểm thử được tạo và dùng thành công. Không tự sửa/rebuild/REPAIR bảng quyền hệ thống.

Không nên coi lỗi mysql.db là lỗi mã nguồn CMS. Nếu sau này không đăng nhập hoặc cấp quyền được qua phpMyAdmin, cần sao lưu đầy đủ và nhờ người quản trị XAMPP xử lý riêng; đồ án không chạy thao tác sửa hệ thống.

## Kiểm thử và phần chưa xác nhận

- PHP syntax, HTML Purifier, kết nối DB và 88 ca HTTP + database đã chạy thật, PASS. Chi tiết TEST-RESULTS.md.
- Hai lần chạy kiểm thử sau sửa session/validation đều PASS.
- Chưa xác nhận trực tiếp trên một bộ XAMPP mới hoàn toàn; đã import SQL vào database trống riêng trên XAMPP hiện tại.
- Chrome headless được thử bằng profile riêng trong thư mục tạm nhưng thoát vì GPU process không khởi động được trong môi trường giới hạn. Chưa xác nhận tương tác editor hoặc bố cục bằng trình duyệt đồ họa.
- Apache thử nghiệm riêng không đọc được DocumentRoot trong thư mục tạm dưới quyền công cụ. Apache hiện có vẫn chạy bình thường. Cần chép bản local vào htdocs để kiểm tra URL chính và .htaccess trên Apache.
- File HANDOFF-VERIFICATION.md (nếu có) ghi kiểm tra bổ sung sau khi bản local được chép vào htdocs.
