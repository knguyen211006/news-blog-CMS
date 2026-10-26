# Kiểm chứng Giai đoạn C — 26/10/2026

Kết nối đã được xác nhận trước mọi test dùng database bằng lệnh PDO chỉ-đọc:
`database=news_blog_cms_test; SELECT 1 = 1` (exit 0). Lỗi xác thực 1045 trước
đây không còn tái hiện trong lượt này. Các bộ test ứng dụng dưới đây chỉ chạy
trên `news_blog_cms_test`; không truy cập htdocs hoặc database demo.

| Kiểm tra | Bằng chứng thực tế | Kết quả |
|---|---|---|
| Nút chọn file, `author/edit`, Dark mode | Playwright đọc `::file-selector-button`: chữ `rgb(232, 236, 244)`, nền `rgb(30, 38, 66)`, tỷ lệ **12.568519752431673:1**; thường/hover, desktop/mobile đều giống nhau; axe không có violation trong các ca editor | Đạt >= 4.5:1, không sửa CSS/font |
| PHP lint | `php -l`: 288 file tracked (46 mã dự án, 242 vendor) + 2 config riêng = **290 file, 0 lỗi** | Exit 0 |
| Acceptance | `php tests/acceptance.php http://127.0.0.1:8089/news-blog-cms/` | **89 PASS / 0 FAIL**, exit 0; bảng chi tiết bên dưới |
| View-contract | `php tests/view-contract.php` → `Missing original contracts: 0` | Exit 0 |
| View-states | `php tests/view-states.php` → 10 ca render, **12 trạng thái rỗng**, không có PHP warning, không thay đổi database | Exit 0 |
| Check | `CMS_CONFIG=config/test.php`, `php scripts/check.php` | 7 extension OK; PHP 8.2.12; MariaDB 10.4.32; HTML Purifier: `<p>safe</p>`; exit 0 |
| Dữ liệu khi chạy check sau acceptance | users=6, categories=4, tags=6, posts=14, comments=8 | Số thực tế sau test, không phải seed ban đầu |
| E2E typecheck | `npm.cmd run typecheck` | Exit 0 |
| E2E lượt 1 | `npm.cmd test`, 1 setup + 21 desktop + 21 mobile | **43 passed / 0 failed**, 30.0s, exit 0 |
| E2E lượt 2 | Cùng lệnh, fixture tự dọn và nạp lại | **43 passed / 0 failed**, 30.4s, exit 0 |
| E2E lượt 3 | Cùng lệnh, fixture tự dọn và nạp lại | **43 passed / 0 failed**, 30.2s, exit 0 |
| HTTP smoke | `php tests/http-smoke.php http://127.0.0.1:8089/news-blog-cms/` | **39 passed / 0 failed**, exit 0; chi tiết ở `HANDOFF-VERIFICATION.md` |
| Quét E2E | `pages/`, `tests/`, `support/`, `playwright.config.ts`; mẫu `waitForTimeout`, `xpath=`, `/html/`, `/html[` | **0 kết quả** (rg exit 1 = không khớp) |
| Gói thử trước khi chốt ZIP | 484 file; CRC OK; khớp chính xác `docs/FILE-LIST.txt`; đủ README, SQL, example config, HTML Purifier, tests/support | Không có cấu hình riêng, .env, node_modules, .browsers, .git, tests/results, session/log, upload thử, GEMINI/ux-ui-automation |
| Cài từ ZIP vào database tạm | Tạo `news_blog_cms_zipcheck` bằng tài khoản test bị từ chối với **1044** (không đủ quyền), khác lỗi xác thực 1045 đã hết | Chưa xác minh cài sạch, trang chủ và reader2 từ ZIP; thư mục giải nén thử đã dọn |

Ba lượt E2E chạy liên tiếp, không sửa mã hoặc reset thủ công giữa các lượt.
Toàn bộ 5 ca `auth.spec.ts` trên Mobile Chrome đều đạt ở cả ba lượt; không tái
hiện lỗi auth nên không suy đoán nguyên nhân lỗi lịch sử. Không sửa Controllers/Models.

Lượt đo tương phản riêng có 3 ca đạt (gồm setup), nhưng tiến trình server không
tự kết thúc khi chạy trong sandbox Windows. Sau khi xác định đúng cây tiến trình
do lượt này tạo và dừng nó, Playwright báo `3 passed (3.2m)`, exit 0. Ba lượt đầy
đủ chạy ngoài sandbox, tự kết thúc bình thường trong thời gian ghi ở bảng trên.
Không thêm fixed sleep, không chụp lại hàng loạt ảnh.

Phạm vi chưa xác minh: bảo vệ Apache `.htaccess` và cài trên XAMPP mới. HTTP smoke
ở đây chỉ kiểm tra PHP router. 12 trạng thái rỗng được xác minh bằng render View
và kiểm tra DOM (icon/nội dung/CTA), không phải 12 ảnh chụp trình duyệt.

## Acceptance — kết quả chạy thực tế của lượt này

Thời điểm: 2026-10-26T15:41:26+07:00

URL: http://127.0.0.1:8089/news-blog-cms/

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
