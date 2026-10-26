# Kiểm chứng Giai đoạn C — đang bị chặn ở kết nối database

Lần kiểm tra ngày 26/10/2026 chỉ dùng cấu hình `config/test.php` cho
`news_blog_cms_test`. Kết nối bị từ chối với SQLSTATE/driver code **1045**,
kể cả khi chạy ngoài sandbox. Phép thử PDO chỉ-đọc `SELECT 1` chưa thực thi
được vì kết nối bị từ chối (exit 1 ở cả hai lần). Chưa chạy các bộ test dùng
database sau phép thử này. Chưa thể xác nhận bản dev đủ điều kiện nộp.
Không sửa thông tin xác thực, Controllers/Models hoặc font.

| Kiểm tra hiện tại | Lệnh / bằng chứng | Kết quả |
|---|---|---|
| PHP lint | `php -l` từng file PHP trong `git ls-files`, hai config riêng và script PDO tạm (chỉ kiểm tra cú pháp) | 288 file tracked + 2 config + 1 script tạm = 291 file, 0 lỗi; tracked gồm mã dự án 46 file, vendor 242 file; script tạm được xóa sau kiểm tra |
| TypeScript | `npm.cmd run typecheck` trong `e2e/` | Chạy lại 26/10: exit 0 |
| View contract | `php tests/view-contract.php` | `Missing original contracts: 0` sau khi sửa so sánh ký tự xuống dòng trong token mở PHP |
| Chốt fixture (lịch sử 25/10, không chạy lại) | Bản sao fixture trong thư mục tạm, cấu hình tên giả `cms_wrong_name_probe` | Từ chối trước PDO: `Refusing to reset anything except local news_blog_cms_test.`, exit 255 |
| scripts/check.php | Chưa chạy lại 26/10 do chưa qua cổng kiểm tra kết nối | Lịch sử 25/10: 7 extension `OK`, PHP 8.2.12; exit 1 trước phần database; chưa có output số bản ghi/Purifier |
| Acceptance | Chưa chạy do kết nối bị từ chối | Chưa xác minh |
| View states | Chưa chạy do kết nối bị từ chối | Chưa xác minh |
| Playwright ba lần liên tiếp | Chưa chạy; không reset thủ công | Chưa xác minh |
| HTTP 39 ca | Đã cập nhật script, `php -l` không lỗi; chưa chạy HTTP | Chưa xác minh |
| Nút chọn file trong Dark mode | Đã thêm output màu và tỷ lệ vào Playwright; chưa có phép đo trình duyệt | Chưa xác minh, không sửa CSS |
| Quét E2E | `pages/`, `tests/`, `support/`, `playwright.config.ts`; mẫu `waitForTimeout`, `xpath=`, `/html/`, `/html[` | Chạy lại 26/10: 0 kết quả (rg exit 1 = không khớp) |
| Danh sách file | So sánh `docs/FILE-LIST.txt` với `git ls-files` | Khớp đúng 484 đường dẫn |
| ZIP dev v1 | Chưa tạo khi cổng kiểm tra database chưa đạt | Chưa xác minh nội dung ZIP, cài trên `news_blog_cms_zipcheck`, trang chủ, reader2 hoặc SHA-256 |

Lỗi View contract ban đầu là 1 chênh lệch giả: `T_OPEN_TAG` giữ CRLF của
working tree, còn Git HEAD dùng LF. Bộ so sánh giờ bỏ khoảng trắng cuối token
mở PHP; không sửa View hay hợp đồng backend.

Chưa xác minh lại quy tắc Apache `.htaccess`, cài đặt trên bộ XAMPP mới,
kiểm thử ZIP, hay các kết quả accessibility/E2E lịch sử trong
`UX-UI-VERIFICATION.md`. PHP router không thay thế kiểm thử Apache.

## Biên bản acceptance lịch sử — không phải kết quả chạy lại Giai đoạn C

Thời điểm: 2026-10-24T10:54:03+07:00

Đích kiểm tra: máy chủ PHP cục bộ tạm, dưới đường dẫn `/news-blog-cms/`.

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
