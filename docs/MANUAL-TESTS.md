# Checklist demo và kiểm tra bằng trình duyệt

Các mục dưới đây là hướng dẫn nghiệm thu trực quan, không được hiểu là đã thực hiện nếu không có bằng chứng trong TEST-RESULTS.md hoặc HANDOFF-VERIFICATION.md.

1. Mở Chrome/Edge, vào http://localhost/news-blog-cms/. Kiểm tra bài nổi bật, bài mới, ảnh và menu. Bấm Khám phá; tìm "website", lọc Công nghệ, chọn thẻ PHP; xóa bộ lọc; qua trang 2.
2. Bấm Đăng ký, thử email đã có để thấy lỗi. Tạo email mới; tài khoản có vai trò Độc giả.
3. Đăng nhập author1@newsblog.local / Demo@12345. Bấm Tòa soạn → Viết bài. Nhập tiêu đề/tóm tắt, gõ nội dung bằng editor; bôi đen rồi chọn B, danh sách, tiêu đề mục và liên kết.
4. Chọn chuyên mục, hai thẻ; tải ảnh JPEG/PNG/WebP <=2 MB; bấm Lưu & xem trước. Kiểm tra ảnh, chữ định dạng, lịch sử; bấm Gửi duyệt, xác nhận.
5. Đăng xuất; đăng nhập Admin. Vào Bài viết & duyệt bài → Chờ duyệt → Xem/xử lý. Chọn Trả bài, nhập lý do, xác nhận.
6. Đăng nhập tác giả; thấy lý do, sửa bài rồi gửi lại. Admin mở bài và bấm Duyệt & xuất bản.
7. Mở cửa sổ Ẩn danh, tìm và đọc bài mới; tải lại hai lần, lượt xem không tăng tiếp trong cùng phiên. Xem trước trong tài khoản tác giả cũng không tăng.
8. Đăng nhập reader1. Bình luận có HTML/script để kiểm tra văn bản được hiển thị an toàn; đợi Admin duyệt. Tạo một phản hồi, duyệt; ẩn bình luận gốc và kiểm tra phản hồi vẫn còn.
9. Admin thử xóa chuyên mục Công nghệ: báo không thể xóa. Thêm một chuyên mục mới, sửa và xóa. Thêm/sửa thẻ, gắn vào bài nháp; xóa thẻ và kiểm tra bài vẫn tồn tại.
10. Admin vào Tài khoản, cấp AUTHOR cho tài khoản mới; khóa/mở khóa. Thử tự khóa Admin duy nhất phải báo lỗi.
11. Mở DevTools (F12) → chế độ thiết bị (Ctrl+Shift+M); chọn rộng 390 px, rồi 768 px và 1440 px. Kiểm tra không có cuộn ngang toàn trang, form còn dùng được, nội dung không đè nhau. Bảng quản trị có vùng cuộn riêng.
12. Nhấn Tab để di chuyển qua menu/form/editor, kiểm tra nhãn, focus và nút xác nhận. Tắt mạng Internet, tải lại; các chức năng vẫn hoạt động khi Apache/MySQL còn chạy.
13. Thử các URL nội bộ liệt kê trong README; phải trả 403. Không được tải config, SQL hoặc log.
14. Admin gỡ bài published về draft trước khi sửa; kiểm tra URL công khai trả 404. Sau xóa mềm, lịch sử và bình luận còn trong DB nhưng không lộ ra công khai.

## Dữ liệu demo phù hợp để bắt đầu

- Bài published: 1–7.
- Bài draft: 8–9.
- Bài pending: 10–11.
- Bài rejected: 12.
- Bình luận gốc đã duyệt #1, phản hồi đã duyệt #2, pending #3.
- Bình luận gốc ẩn #4 và phản hồi đã duyệt #5 trên bài #2.
