# Thư viện bên thứ ba

Các bản sao dưới đây được giữ nguyên mã nguồn và thông báo bản quyền, lưu tại máy và không gọi CDN khi ứng dụng chạy.

| Thư viện | Phiên bản / tham chiếu cố định | Giấy phép | Vị trí |
|---|---|---|---|
| Bootstrap | 5.3.8 | MIT | assets/vendor/bootstrap/bootstrap.min.css; LICENSE |
| Pell | 1.0.6; commit d5556baa2e0bfc1411621ee4d780752200b367fd | MIT | assets/vendor/pell/ |
| HTML Purifier | 4.19.0; tag v4.19.0 | LGPL-2.1-or-later | vendor/htmlpurifier/library; LICENSE |

Nguồn chính thức:
- https://github.com/twbs/bootstrap/tree/v5.3.8
- https://getbootstrap.com/docs/5.3/about/license/
- https://github.com/jaredreich/pell/tree/d5556baa2e0bfc1411621ee4d780752200b367fd
- https://github.com/ezyang/htmlpurifier/tree/v4.19.0

Pell và Bootstrap cho phép sử dụng theo MIT, giữ thông báo giấy phép. HTML Purifier được phân phối nguyên mã nguồn cùng LGPL; ứng dụng gọi thư viện mà không sửa mã của thư viện. Không yêu cầu khóa API hay tài khoản trả phí.

Pell dùng contenteditable/execCommand của trình duyệt. Đây là lựa chọn gọn cho phạm vi đồ án; không có cộng tác realtime, bảng phức tạp hoặc kiểm tra chính tả từ dịch vụ ngoài. Có textarea dự phòng khi JavaScript không tải.

Ảnh SVG trong assets/images do dự án tạo bằng mã vector, không lấy ảnh bên ngoài. Font dùng font hệ thống.
