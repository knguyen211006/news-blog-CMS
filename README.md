# Góc Nhìn — CMS tin tức và blog đa tác giả

Đồ án môn BackEnd1 — Nhóm 6 — Đề tài 4: Hệ thống quản lý tin tức và blog multi-author (CMS thu nhỏ).

Ứng dụng viết bằng **PHP MVC thuần** (không framework), kết nối MySQL/MariaDB qua PDO, giao diện tiếng Việt. Khi vận hành không cần Composer, npm, CDN hay dịch vụ bên ngoài; font dùng font có sẵn của hệ điều hành (Segoe UI, Cambria).

Repository: https://github.com/knguyen211006/news-blog-CMS

## 1. Thành viên, phân công và tiến độ

Phân công dưới đây giữ danh sách thành viên và phạm vi phụ trách đã ghi trong repository. Trạng thái hoàn thành phản ánh phần chức năng đã có mã nguồn tích hợp trên `main`. Việc nghiệm thu từng thành viên cần nhóm xác nhận thêm.

| Thành viên | Vai trò | Công việc phụ trách | Sản phẩm bàn giao | Trạng thái hiện tại |
|---|---|---|---|---|
| Nguyễn Khôi Nguyên | Trưởng nhóm | Khung MVC, đăng ký/đăng nhập, phân quyền, quản lý tài khoản, tích hợp Docker và hướng dẫn chạy | `app/Core/`, `AuthController.php`, phần tài khoản trong `AdminController.php`, cấu hình Docker và README | Đã có mã nguồn trên main, chờ nhóm xác nhận nghiệm thu |
| Nguyễn Trí Phong | Thành viên | Trang chủ, danh sách và chi tiết bài, tìm kiếm, lọc chuyên mục/thẻ, lượt xem, giao diện công khai | `PublicController.php`, các View công khai và tài nguyên giao diện liên quan | Đã có mã nguồn trên main, chờ nhóm xác nhận nghiệm thu |
| Nguyễn Lê Duy Ân | Thành viên | Soạn và sửa bài, tải ảnh đại diện, lưu nháp, xem trước, gửi duyệt, xóa mềm | Luồng tác giả trong `PostController.php`, View soạn bài và xử lý bài viết | Đã có mã nguồn trên main, chờ nhóm xác nhận nghiệm thu |
| Lê Tuấn Quy | Thành viên | Duyệt/trả/gỡ bài, chuyên mục, thẻ, dashboard quản trị | Luồng duyệt trong `PostController.php`, dashboard và taxonomy trong `AdminController.php` | Đã có mã nguồn trên main, chờ nhóm xác nhận nghiệm thu |
| Võ Tấn Tài | Thành viên | Bình luận hai cấp, ẩn và kiểm duyệt bình luận, phối hợp rà soát luồng sử dụng, dữ liệu mẫu | `CommentController.php`, phần bình luận trong `AdminController.php` và SQL mẫu | Đã có mã nguồn trên main, chờ nhóm xác nhận nghiệm thu |

### Nhịp độ công việc đề xuất

Nhóm có thể áp dụng lịch dưới đây cho các lần chỉnh sửa tiếp theo. Đây là kế hoạch phối hợp đề xuất để nhóm thống nhất trước khi áp dụng.

| Thời điểm | Người phụ trách | Việc cần cập nhật |
|---|---|---|
| Đầu mỗi ngày làm việc | Từng thành viên | Ghi nhiệm vụ sẽ xử lý và đầu ra cần bàn giao |
| Cuối mỗi ngày làm việc | Từng thành viên | Cập nhật việc đã xong, việc còn vướng và commit hoặc pull request liên quan |
| Khi có phần việc sẵn sàng bàn giao | Thành viên phụ trách và người rà soát | Đối chiếu yêu cầu, ghi nhận lỗi còn lại và kết quả rà soát |
| Cuối mỗi tuần hoặc trước mốc nộp | Trưởng nhóm cùng cả nhóm | Tổng hợp tiến độ, điều chỉnh phần việc và chốt các mục đã nghiệm thu |

### Điều kiện ghi nhận hoàn thành

Một phần việc được ghi nhận hoàn thành khi đã có mã nguồn trên `main`, đáp ứng đầu ra trong bảng phân công, có kết quả rà soát của nhóm và đã xử lý lỗi cản trở sử dụng. Khi chốt, nhóm bổ sung ngày hoàn thành thực tế cùng commit hoặc pull request bàn giao để theo dõi.

## 2. Chức năng chính

- **Khách:** xem bài đã xuất bản, lọc theo chuyên mục và thẻ, tìm kiếm theo tiêu đề/tóm tắt, phân trang.
- **Độc giả (USER):** đăng ký, đăng nhập, bình luận và trả lời (2 cấp), ẩn bình luận của mình, cập nhật hồ sơ.
- **Tác giả (AUTHOR):** soạn bài bằng trình soạn thảo rich text (Pell), tải ảnh đại diện, gắn chuyên mục và thẻ, lưu nháp, xem trước, gửi duyệt, xóa mềm.
- **Quản trị (ADMIN):** duyệt hoặc trả bài (kèm lý do), gỡ bài đã xuất bản, quản lý chuyên mục/thẻ, kiểm duyệt bình luận, cấp quyền và khóa/mở khóa tài khoản, xem dashboard.
- **Lượt xem:** tính theo phiên cho từng bài đã xuất bản, với cửa sổ 1.800 giây kể từ lần tăng gần nhất; mở lại sau cửa sổ này được tính thêm một lượt. Xem trước không làm tăng.

### Vòng đời bài viết

`draft` → (gửi duyệt) → `pending` → (Admin duyệt) → `published`, hoặc (Admin trả kèm lý do) → `rejected` → tác giả sửa và gửi lại.

- Bài `pending` không được sửa. Bài `published` không sửa trực tiếp: Admin gỡ về `draft` trước.
- Xóa mềm giữ lại lịch sử và bình luận; bài đã xóa không hiện công khai.
- Mỗi lần cập nhật tăng số phiên bản (`version`); thao tác trên dữ liệu đã cũ hoặc duyệt trùng bị từ chối bằng HTTP 409.

### Giới hạn dữ liệu (validation phía server)

| Trường | Quy tắc |
|---|---|
| Tiêu đề bài | 5–200 ký tự |
| Tóm tắt | 0–500 ký tự khi lưu nháp; 10–500 ký tự khi gửi duyệt |
| Nội dung | HTML tối đa 100.000 ký tự; văn bản sau khi lọc thẻ 30–100.000 ký tự khi gửi duyệt |
| Lý do trả bài | 5–2.000 ký tự |
| Bình luận | 1–2.000 ký tự, văn bản thuần |
| Ảnh đại diện | JPEG/PNG/WebP, tối đa 2 MB, kiểm tra MIME thực tế |

Bài chỉ gửi duyệt được khi có chuyên mục hợp lệ. Chuyên mục đang có bài không xóa được.

## 3. Vai trò và phân quyền

| Thao tác | Khách | USER | AUTHOR | ADMIN |
|---|---|---|---|---|
| Đọc, tìm kiếm bài công khai | Có | Có | Có | Có |
| Bình luận, trả lời, ẩn bình luận của mình | Không | Có | Có | Có |
| Soạn, sửa, gửi duyệt bài | Không | Không | Bài của mình | Có |
| Duyệt, trả, gỡ bài | Không | Không | Không | Có |
| Quản lý chuyên mục, thẻ, bình luận, tài khoản | Không | Không | Không | Có |

Tài khoản đăng ký mới luôn là USER; chỉ Admin cấp quyền AUTHOR. Không thể hạ quyền hay khóa Admin cuối cùng.

## 4. Yêu cầu môi trường

### Chạy bản mẫu bằng Docker trên Windows

- **Docker Desktop** đang chạy ở chế độ Linux containers, có lệnh `docker compose`.
- **Git** để clone mã nguồn và **PowerShell** để chạy `Start-Docker.ps1`. VS Code là trình soạn thảo tùy chọn.
- **Kết nối Internet ở lần dựng đầu tiên** để tải image và các gói cần cho bản dựng.
- **Cổng 8088 còn trống** nếu dùng địa chỉ mặc định. Nếu cổng đã được dùng, tạo cấu hình bằng `Start-Docker.ps1`, đổi `CMS_PORT` trong `.env` sang cổng còn trống rồi chạy lại.

Docker chạy **PHP 8.2 với Apache** và **MariaDB 10.11** trong hai container. Image web đã có thư viện ứng dụng và cài các PHP extension cần dùng. Cách chạy Docker chỉ cần các công cụ trên máy được liệt kê ở trên.

### Chạy trực tiếp bằng XAMPP

Dùng Apache 2.4 với `mod_rewrite` và `AllowOverride All`, PHP 8.1 trở lên và MySQL 8 hoặc MariaDB 10.4 trở lên. Bật các PHP extension `pdo_mysql`, `mbstring`, `fileinfo`, `dom`, `iconv`, `session` và `openssl`. Xem mục 5.4 để cài đặt.

## 5. Cài đặt và chạy

### 5.1. Chạy bản GitHub bằng Docker trên Windows

1. Mở Docker Desktop ở chế độ Linux containers và chờ Docker Engine khởi động.
2. Clone repository rồi mở thư mục dự án trong VS Code.

```powershell
git clone https://github.com/knguyen211006/news-blog-CMS.git
cd news-blog-CMS
code .
```

3. Chọn **Terminal → New Terminal** trong VS Code. Trong thư mục có `compose.yaml`, chạy:

```powershell
powershell.exe -NoProfile -ExecutionPolicy Bypass -File .\Start-Docker.ps1
```

4. Mở <http://127.0.0.1:8088/>. Dùng `docker compose ps` để xem trạng thái của `web` (PHP/Apache) và `db` (MariaDB).

Script tạo `.env` cục bộ khi chạy lần đầu. MariaDB nạp dữ liệu mẫu từ `database/news_blog_cms.sql` khi volume database còn trống. Tài khoản quản trị của bản mẫu là `admin@newsblog.local`, mật khẩu `Demo@12345`. Lần dựng image đầu tiên cần Internet.

### 5.2. Chạy bản Docker dữ liệu thật của nhóm

Trên máy đã cài bản nội bộ, mở đúng thư mục `C:\BE1\Docker\Goc-Nhin-Docker` trong VS Code. Mở PowerShell tại thư mục đó và chạy:

```powershell
docker compose up -d --wait
```

Mở <http://127.0.0.1:8088/> và dùng tài khoản đã có trong bản nội bộ. Dự án này có tên `goc-nhin-that-sach`. Giữ `.env` đi cùng dự án để lần chạy tiếp theo dùng đúng cấu hình của các volume hiện có. Hãy dừng bản đang dùng cổng 8088 trước khi chuyển giữa bản GitHub và bản nội bộ.

Sau khi sửa mã nguồn, dựng lại image từ đúng thư mục dự án:

```powershell
docker compose up -d --build --wait
```

Dùng `docker compose stop` để dừng và `docker compose start` để chạy lại. Database, ảnh tải lên và dữ liệu ứng dụng nằm trong các volume Docker. Tránh dùng `docker compose down -v` vì tùy chọn `-v` xóa các volume của dự án. Bản Docker dùng MariaDB riêng nên có thể chạy độc lập với XAMPP. Giữ SQL dữ liệu thật, ảnh tải lên và `.env` ở bản nội bộ.

### 5.3. Các địa chỉ trang trên Docker

| Màn hình | Đường dẫn tại `http://127.0.0.1:8088` |
|---|---|
| Trang chủ | `/` |
| Danh sách, tìm kiếm | `/index.php?r=articles` |
| Chi tiết bài | `/index.php?r=article&slug={slug}` |
| Đăng nhập, đăng ký | `/index.php?r=login`, `/index.php?r=register` |
| Hồ sơ cá nhân | `/index.php?r=profile` |
| Bài viết của tôi | `/index.php?r=author/posts` |
| Viết bài | `/index.php?r=author/edit` |
| Dashboard Admin | `/index.php?r=admin` |
| Duyệt bài | `/index.php?r=admin/posts&status=pending` |
| Chuyên mục, thẻ | `/index.php?r=admin/taxonomy` |
| Kiểm duyệt bình luận | `/index.php?r=admin/comments` |
| Quản lý tài khoản | `/index.php?r=admin/users` |

### 5.4. Cài trên máy mới (XAMPP)

1. Giải nén thư mục `news-blog-cms` vào `C:\xampp\htdocs\`, sao cho tồn tại `C:\xampp\htdocs\news-blog-cms\index.php` (không để lồng thêm một cấp thư mục).
2. Mở XAMPP Control Panel, bấm **Start** cho **Apache** và **MySQL**.
3. Vào `http://localhost/phpmyadmin/`, chọn **Import**, nạp file `database/news_blog_cms.sql`. File này tự tạo database `news_blog_cms` (utf8mb4) cùng dữ liệu demo. Không import vào database đã có dữ liệu.
4. Sao chép `config/example.php` thành `config/local.php`, rồi sửa host, port, tên database, user, mật khẩu theo máy. Giữ `'base_path' => '/news-blog-cms'`.
5. Kiểm tra môi trường và kết nối:

```powershell
cd C:\xampp\htdocs\news-blog-cms
& 'C:\xampp\php\php.exe' scripts/check.php
```

Các extension cần thiết phải báo `OK`; script in phiên bản PHP và database, số bản ghi của `users`, `categories`, `tags`, `posts`, `comments`, rồi kết quả `HTML Purifier: <p>safe</p>`. Script không in `PASS` ở mọi dòng.

6. Mở `http://localhost/news-blog-cms/`.

Giữ `config/local.php` ở máy chạy XAMPP vì tệp chứa thông tin kết nối của máy đó.

## 6. Cấu trúc thư mục

| Đường dẫn | Nội dung |
|---|---|
| `index.php`, `config/routes.php` | Điểm vào và định tuyến |
| `app/` | Xử lý yêu cầu, mô hình dữ liệu và giao diện PHP |
| `assets/`, `vendor/` | CSS, JavaScript, ảnh minh họa mẫu và thư viện |
| `Dockerfile`, `compose.yaml`, `docker/` | Cấu hình PHP/Apache và MariaDB trong Docker |
| `database/schema.sql`, `database/news_blog_cms.sql` | Cấu trúc bảng và SQL mẫu để khởi tạo |
| `scripts/check.php` | Công cụ chẩn đoán môi trường |
| `storage/`, `uploads/` | Dữ liệu chạy và ảnh tải lên |
| `THIRD-PARTY-NOTICES.md` | Thông báo giấy phép của thư viện |

## 7. Xử lý sự cố

- Nếu cổng 8088 đã được dùng, đổi `CMS_PORT` trong `.env`, chạy lại Compose rồi mở cổng vừa chọn.
- Nếu dịch vụ Docker chưa sẵn sàng, dùng `docker compose ps` để xem trạng thái và `docker compose logs --tail=100 web db` để xem nhật ký.
- Nếu chạy bằng XAMPP và gặp lỗi 404 hoặc mất CSS, kiểm tra thư mục dự án trong `htdocs` và `base_path` trong `config/local.php`.
