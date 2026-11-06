# Kiểm chứng bản nộp — 07/11/2026

## Kết quả thực tế

| Hạng mục | Kết quả |
|---|---|
| Cài từ ZIP vào thư mục sạch | Thành công, tài khoản database riêng được tạo bởi installer |
| MariaDB riêng | 10.4.32, data directory mới, chỉ lắng nghe 127.0.0.1:13369 |
| Dữ liệu seed sau cài sạch | 5 users, 4 categories, 6 tags, 12 posts, 6 comments |
| scripts/check.php | 7 extension OK; HTML Purifier trả `<p>safe</p>` |
| PHP acceptance | 89 PASS / 0 FAIL, chỉ trên database test của MariaDB riêng |
| Apache 2.4, quy tắc .htaccess thật | 39 PASS / 0 FAIL; cấu hình, SQL, log, mã nguồn nội bộ và PHP trong uploads đều bị chặn |
| Playwright Chromium desktop/mobile | 43 passed / 0 failed, exit 0; máy chủ thử nghiệm được quản lý riêng |
| TypeScript | tsc --noEmit, exit 0 |
| Lỗi tìm kiếm zero, escape wildcard, preview chuyên mục | 11 PASS / 0 FAIL qua tests/regression-readonly.php; không cần database |
| View contract | Missing original contracts: 0; 18 View, chạy được từ ZIP không có Git |
| Trạng thái rỗng | 12 trạng thái qua 10 ca render, không có PHP warning |
| Sơ đồ bàn giao | Use Case, ERD 8 bảng, Sequence xuất bản, Sequence bình luận |

## Phần đã sửa

- Từ khóa `0`, slug chuyên mục `0` và slug thẻ `0` được lọc đúng.
- Menu Chuyên mục dẫn tới bộ lọc hiện hành; không còn phụ thuộc slug gắn cứng.
- Xem trước lấy cả tên và slug hiện tại của chuyên mục; chuyên mục chưa chọn vẫn có đường dẫn an toàn.
- Bài kiểm tra hợp đồng View dùng baseline kèm gói; không gọi Git.
- HTTP smoke hỗ trợ `--apache` để ghi đúng môi trường đã kiểm tra.
- Có chế độ `CMS_E2E_EXTERNAL_SERVER=1` cho môi trường Windows không tự kết thúc tiến trình con.
- Hướng dẫn liệt kê extension curl cần cho acceptance/HTTP smoke.

## Bảo toàn dữ liệu và giới hạn

Database đang dùng `news_blog_cms` trên máy không được dùng chạy acceptance/E2E.
Mọi reset fixture, tạo bài và upload thử đều diễn ra trên bản giải nén trong thư mục
kiểm tra và MariaDB riêng. Cấu hình thật và ảnh upload của bản gốc được đối chiếu SHA-256.
Không import SQL seed lên database đang có dữ liệu. Khi chuyển thư mục, giữ cấu hình
`config/local.php` và ảnh upload hiện có; kết nối vẫn trỏ tới database chính.

Kiểm tra dùng các chương trình PHP/Apache/MariaDB của XAMPP hiện có với cấu hình và
data directory riêng. Đây là cài sạch ứng dụng và database trên cùng máy, chưa phải
kiểm chứng một máy Windows mới hay tất cả các phiên bản MySQL/PHP khác.

Lần thử acceptance đầu gặp lỗi lưu cookie vào thư mục tạm ngoài vùng kiểm thử;
chạy lại với `php -d sys_temp_dir=<thư-mục-tạm-có-quyền-ghi>` đạt 89/89.
Lượt E2E đầu hoàn thành các ca nhưng vướng đóng máy chủ con; lượt xác minh cuối
dùng máy chủ riêng và kết thúc bình thường với 43 passed, exit 0.

## Lệnh kiểm tra không ghi dữ liệu

```powershell
php tests/regression-readonly.php
php tests/view-contract.php
```

Acceptance/E2E có reset dữ liệu test. Chỉ chạy sau khi tạo cấu hình và database test
riêng theo README; không trỏ máy chủ kiểm thử tới database cần giữ.
