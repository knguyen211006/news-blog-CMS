# Kết quả kiểm thử HTTP + MariaDB

Thời điểm: 2026-10-24T10:54:03+07:00

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
