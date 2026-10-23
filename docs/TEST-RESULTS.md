# Kết quả kiểm thử HTTP + MariaDB

Thời điểm: 2026-10-23T22:11:30+07:00

URL: http://127.0.0.1:8089/news-blog-cms/

Database riêng: news_blog_cms_test. Không chạy thao tác nghiệm thu trên database demo chính.

**88 PASS / 0 FAIL**

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
| 19 | USER không vào được form tác giả | PASS |  |
| 20 | AUTHOR không vào được Admin | PASS |  |
| 21 | AUTHOR không vào được danh sách toàn bộ bài | PASS |  |
| 22 | Thiếu CSRF bị từ chối | PASS |  |
| 23 | GET không được xóa dữ liệu | PASS |  |
| 24 | Không hạ quyền Admin cuối cùng | PASS |  |
| 25 | Không khóa Admin cuối cùng | PASS |  |
| 26 | Tài khoản bị khóa không đăng nhập được | PASS |  |
| 27 | Admin cấp tác giả và mở khóa | PASS |  |
| 28 | Tài khoản được mở khóa đăng nhập được | PASS |  |
| 29 | Phiên đã đăng nhập bị vô hiệu khi bị khóa | PASS |  |
| 30 | Lưu nháp chưa hoàn chỉnh | PASS |  |
| 31 | Không gửi duyệt bài thiếu dữ liệu | PASS |  |
| 32 | Tạo bài và liên kết thẻ trong database | PASS |  |
| 33 | HTML Purifier loại script/event/javascript URI | PASS |  |
| 34 | AUTHOR không tự đặt nổi bật | PASS |  |
| 35 | Tác giả khác không sửa bằng thay ID | PASS |  |
| 36 | Tác giả khác không xem trước bài | PASS |  |
| 37 | Tác giả khác không xóa bằng thay ID | PASS |  |
| 38 | Khách không đọc nháp vừa tạo | PASS |  |
| 39 | Xem trước không tăng lượt xem | PASS |  |
| 40 | Nháp -> chờ duyệt | PASS |  |
| 41 | Bài pending không được sửa | PASS |  |
| 42 | AUTHOR không tự xuất bản | PASS |  |
| 43 | Trả bài phải có lý do | PASS |  |
| 44 | Admin trả bài kèm lý do | PASS |  |
| 45 | Sửa bài rejected | PASS |  |
| 46 | Không ghi đè phiên bản cũ | PASS |  |
| 47 | Gửi duyệt lần hai | PASS |  |
| 48 | Admin xuất bản | PASS |  |
| 49 | Chống duyệt trùng bằng version | PASS |  |
| 50 | Chống duyệt lại bằng trạng thái | PASS |  |
| 51 | Published không được sửa trực tiếp | PASS |  |
| 52 | Khách đọc bài đã duyệt | PASS |  |
| 53 | Cùng phiên chỉ tăng view một lần | PASS |  |
| 54 | Phiên khác được tăng view | PASS |  |
| 55 | Khách không được bình luận | PASS |  |
| 56 | Bình luận rỗng bị từ chối | PASS |  |
| 57 | Bình luận quá 2000 ký tự bị từ chối | PASS |  |
| 58 | Bình luận cha khác bài bị chặn | PASS |  |
| 59 | Không cho trả lời cấp thứ ba | PASS |  |
| 60 | Không trả lời cha pending | PASS |  |
| 61 | Bình luận mới chờ duyệt | PASS |  |
| 62 | Khách không thấy bình luận pending | PASS |  |
| 63 | Giới hạn gửi bình luận liên tục | PASS |  |
| 64 | Admin duyệt bình luận | PASS |  |
| 65 | Bình luận HTML được escape | PASS |  |
| 66 | Tạo phản hồi một cấp | PASS |  |
| 67 | Không ẩn bình luận của người khác | PASS |  |
| 68 | Độc giả ẩn bình luận của mình | PASS |  |
| 69 | Ẩn cha giữ phản hồi đã duyệt | PASS |  |
| 70 | Không xóa chuyên mục có bài | PASS |  |
| 71 | Slug chuyên mục không trùng | PASS |  |
| 72 | Thêm chuyên mục | PASS |  |
| 73 | Sửa chuyên mục | PASS |  |
| 74 | Xóa chuyên mục rỗng | PASS |  |
| 75 | Xóa thẻ chỉ gỡ liên kết | PASS |  |
| 76 | Admin gỡ bài về nháp | PASS |  |
| 77 | Không bình luận bài đã gỡ | PASS |  |
| 78 | Ảnh MIME giả bị từ chối | PASS |  |
| 79 | Upload PNG hợp lệ và tên ngẫu nhiên | PASS |  |
| 80 | Upload vượt 2 MB bị chặn | PASS |  |
| 81 | Xóa mềm giữ lịch sử và bình luận | PASS |  |
| 82 | URL bài đã xóa trả 404 | PASS |  |
| 83 | Xem trước bài đã xóa trả 404 | PASS |  |
| 84 | Cập nhật hồ sơ không tự cấp quyền | PASS |  |
| 85 | Hồ sơ escape HTML | PASS |  |
| 86 | Dashboard dùng thống kê thực | PASS |  |
| 87 | Giới hạn đăng nhập sai | PASS |  |
| 88 | Đăng xuất kết thúc phiên | PASS |  |
