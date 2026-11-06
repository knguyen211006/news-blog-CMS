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

---

# Kết quả kiểm thử HTTP + MariaDB

Thời điểm: 2026-11-07T02:03:20+07:00

URL: http://127.0.0.1:18093/news-blog-cms/

Database riêng: news_blog_cms_test. Không chạy thao tác nghiệm thu trên database demo chính.

**89 PASS / 0 FAIL**

| # | Ca kiểm thử | Kết quả | Ghi chú |
|---|---|---|---|
| 1 | Trang chủ lấy bài từ database | PASS |  |
| 2 | Tìm kiếm SQL injection được coi là dữ liệu | PASS |  |
| 3 | Tìm theo tiêu đề | PASS |  |
| 4 | Tìm theo tóm tắt | PASS |  |
| 5 | Lọc chuyên mục | PASS |  |
| 6 | Lọc thẻ | PASS |  |
| 7 | Phân trang công khai | PASS |  |
| 8 | Ẩn URL chưa xuất bản: nhat-ky-hoc-sql | PASS |  |
| 9 | Ẩn URL chưa xuất bản: hoc-bang-so-do-tu-duy | PASS |  |
| 10 | Ẩn URL chưa xuất bản: nhip-hoc-cua-minh | PASS |  |
| 11 | Đăng ký email trùng bị chặn | PASS |  |
| 12 | Đăng ký thành công, chống tự cấp ADMIN | PASS |  |
| 13 | Đăng nhập sai bị chặn | PASS |  |
| 14 | Admin đăng nhập | PASS |  |
| 15 | Tác giả 1 đăng nhập | PASS |  |
| 16 | Tác giả 2 đăng nhập | PASS |  |
| 17 | Độc giả đăng nhập | PASS |  |
| 18 | USER không vào được Admin | PASS |  |
| 19 | Khách được chuyển tới đăng nhập khi mở Admin | PASS |  |
| 20 | USER không vào được form tác giả | PASS |  |
| 21 | AUTHOR không vào được Admin | PASS |  |
| 22 | AUTHOR không vào được danh sách toàn bộ bài | PASS |  |
| 23 | Thiếu CSRF bị từ chối | PASS |  |
| 24 | GET không được xóa dữ liệu | PASS |  |
| 25 | Không hạ quyền Admin cuối cùng | PASS |  |
| 26 | Không khóa Admin cuối cùng | PASS |  |
| 27 | Tài khoản bị khóa không đăng nhập được | PASS |  |
| 28 | Admin cấp tác giả và mở khóa | PASS |  |
| 29 | Tài khoản được mở khóa đăng nhập được | PASS |  |
| 30 | Phiên đã đăng nhập bị vô hiệu khi bị khóa | PASS |  |
| 31 | Lưu nháp chưa hoàn chỉnh | PASS |  |
| 32 | Không gửi duyệt bài thiếu dữ liệu | PASS |  |
| 33 | Tạo bài và liên kết thẻ trong database | PASS |  |
| 34 | HTML Purifier loại script/event/javascript URI | PASS |  |
| 35 | AUTHOR không tự đặt nổi bật | PASS |  |
| 36 | Tác giả khác không sửa bằng thay ID | PASS |  |
| 37 | Tác giả khác không xem trước bài | PASS |  |
| 38 | Tác giả khác không xóa bằng thay ID | PASS |  |
| 39 | Khách không đọc nháp vừa tạo | PASS |  |
| 40 | Xem trước không tăng lượt xem | PASS |  |
| 41 | Nháp -> chờ duyệt | PASS |  |
| 42 | Bài pending không được sửa | PASS |  |
| 43 | AUTHOR không tự xuất bản | PASS |  |
| 44 | Trả bài phải có lý do | PASS |  |
| 45 | Admin trả bài kèm lý do | PASS |  |
| 46 | Sửa bài rejected | PASS |  |
| 47 | Không ghi đè phiên bản cũ | PASS |  |
| 48 | Gửi duyệt lần hai | PASS |  |
| 49 | Admin xuất bản | PASS |  |
| 50 | Chống duyệt trùng bằng version | PASS |  |
| 51 | Chống duyệt lại bằng trạng thái | PASS |  |
| 52 | Published không được sửa trực tiếp | PASS |  |
| 53 | Khách đọc bài đã duyệt | PASS |  |
| 54 | Cùng phiên chỉ tăng view một lần | PASS |  |
| 55 | Phiên khác được tăng view | PASS |  |
| 56 | Khách không được bình luận | PASS |  |
| 57 | Bình luận rỗng bị từ chối | PASS |  |
| 58 | Bình luận quá 2000 ký tự bị từ chối | PASS |  |
| 59 | Bình luận cha khác bài bị chặn | PASS |  |
| 60 | Không cho trả lời cấp thứ ba | PASS |  |
| 61 | Không trả lời cha pending | PASS |  |
| 62 | Bình luận mới chờ duyệt | PASS |  |
| 63 | Khách không thấy bình luận pending | PASS |  |
| 64 | Giới hạn gửi bình luận liên tục | PASS |  |
| 65 | Admin duyệt bình luận | PASS |  |
| 66 | Bình luận HTML được escape | PASS |  |
| 67 | Tạo phản hồi một cấp | PASS |  |
| 68 | Không ẩn bình luận của người khác | PASS |  |
| 69 | Độc giả ẩn bình luận của mình | PASS |  |
| 70 | Ẩn cha giữ phản hồi đã duyệt | PASS |  |
| 71 | Không xóa chuyên mục có bài | PASS |  |
| 72 | Slug chuyên mục không trùng | PASS |  |
| 73 | Thêm chuyên mục | PASS |  |
| 74 | Sửa chuyên mục | PASS |  |
| 75 | Xóa chuyên mục rỗng | PASS |  |
| 76 | Xóa thẻ chỉ gỡ liên kết | PASS |  |
| 77 | Admin gỡ bài về nháp | PASS |  |
| 78 | Không bình luận bài đã gỡ | PASS |  |
| 79 | Ảnh MIME giả bị từ chối | PASS |  |
| 80 | Upload PNG hợp lệ và tên ngẫu nhiên | PASS |  |
| 81 | Upload vượt 2 MB bị chặn | PASS |  |
| 82 | Xóa mềm giữ lịch sử và bình luận | PASS |  |
| 83 | URL bài đã xóa trả 404 | PASS |  |
| 84 | Xem trước bài đã xóa trả 404 | PASS |  |
| 85 | Cập nhật hồ sơ không tự cấp quyền | PASS |  |
| 86 | Hồ sơ escape HTML | PASS |  |
| 87 | Dashboard dùng thống kê thực | PASS |  |
| 88 | Giới hạn đăng nhập sai | PASS |  |
| 89 | Đăng xuất kết thúc phiên | PASS |  |
