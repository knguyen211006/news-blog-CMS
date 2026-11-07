# Chạy Góc Nhìn bằng Docker

Mở thư mục `Goc-Nhin-Docker` trong VS Code. Khi Docker Desktop đang chạy ở chế độ Linux containers, chọn **Terminal → Run Task → Góc Nhìn: Khởi động Docker**. Bạn cũng có thể mở terminal PowerShell trong VS Code rồi chạy:

```powershell
.\Start-Docker.ps1
```

Trang mặc định là <http://127.0.0.1:8088/>. Nếu cổng đã được sử dụng, sửa `CMS_PORT` trong `.env` rồi chạy lại. Tài khoản quản trị demo là `admin@newsblog.local`, mật khẩu `Demo@12345`.

Compose chạy PHP/Apache và MariaDB trong hai container. Lần đầu MariaDB nạp dữ liệu mẫu từ `database/news_blog_cms.sql`. Ba volume của dự án lưu database, ảnh tải lên và dữ liệu chạy. File `.env` sinh khi khởi chạy, nên giữ file này cùng volume để lần chạy sau dùng lại đúng mật khẩu database.

```powershell
docker compose ps
docker compose exec web php /var/www/html/scripts/check.php
docker compose down
```

`docker compose down` dừng bộ container và giữ dữ liệu trong các volume. Lần tải image và build đầu tiên cần Internet. Gói chứa mã nguồn và cấu hình build, chưa chứa image xuất sẵn.
