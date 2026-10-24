# Góc Nhìn — CMS tin tức và blog đa tác giả

Ứng dụng PHP MVC thuần với MySQL/MariaDB, giao diện tiếng Việt và URL chạy dưới `/news-blog-cms/`. Không cần Composer, npm, CDN hay dịch vụ bên ngoài.

## 1. Thư mục làm việc và cách mở dự án

Mở thư mục sau bằng **Antigravity → Open Folder**:

`C:\Users\Nguyen\Documents\news-blog-cms-dev`

Đây là bản mã nguồn dùng để chỉnh sửa. Các vị trí liên quan có mục đích khác nhau:

| Đường dẫn | Mục đích |
|---|---|
| `C:\Users\Nguyen\Documents\news-blog-cms-dev` | Mã nguồn làm việc; sửa và kiểm thử trong Antigravity |
| `C:\xampp\htdocs\news-blog-cms` | Bản Apache/XAMPP đang chạy tại `http://localhost/news-blog-cms/` |
| `C:\Users\Nguyen\Documents\news-blog-cms-submission-20261023-v2.zip` | Gói nộp đã tạo trước đó; không chỉnh sửa trực tiếp |

Ba vị trí này là các bản riêng. Thay đổi trong thư mục làm việc **không tự động** xuất hiện trên trang Apache hay trong ZIP. Hãy xem trước thay đổi bằng máy chủ phát triển, chạy test, rồi mới đồng bộ và đóng gói lại. Không chép đè cả thư mục `htdocs` vì nơi đó có cấu hình kết nối và dữ liệu runtime riêng.

Repository Git local nằm trong thư mục làm việc. Chưa cấu hình remote GitHub.

## 2. Yêu cầu môi trường

- Windows và PHP 8.1 trở lên; đã kiểm tra trên PHP 8.2.12.
- Apache 2.4 và MySQL 8 hoặc MariaDB 10.4 trở lên.
- Các PHP extension: `pdo_mysql`, `mbstring`, `fileinfo`, `dom`, `iconv`, `session`, `openssl`.
- Để chạy bằng Apache, bật `mod_rewrite` và `AllowOverride All` cho thư mục `htdocs`.

Máy phát triển hiện dùng XAMPP tại `C:\xampp`; Apache và MariaDB đang chạy. Kiểm tra CLI:

```powershell
& 'C:\xampp\php\php.exe' -v
& 'C:\xampp\php\php.exe' -m
```

## 3. Cấu hình và database

Các cấu hình kết nối máy local không được commit và không nằm trong ZIP nộp:

- `config/example.php` là mẫu không chứa thông tin đăng nhập thật.
- `config/local.php` kết nối database demo `news_blog_cms`.
- `config/test.php` kết nối database riêng `news_blog_cms_test`.

Trên máy mới, sao chép `config/example.php` thành `config/local.php`, rồi điền host, port, database, user và password theo MySQL/MariaDB trên máy đó. Giữ `base_path` là `/news-blog-cms`. Không đưa file `local.php` hoặc `test.php` lên Git hay gửi kèm ZIP.

### Cài database demo trên máy mới

1. Mở phpMyAdmin tại `http://localhost/phpmyadmin/`.
2. Chọn **Import** và nạp `database/news_blog_cms.sql` vào database mới. Tệp SQL tự tạo database `news_blog_cms` với charset `utf8mb4`.
3. Tạo `config/local.php` theo hướng dẫn trên với thông tin kết nối thực tế.
4. Chạy kiểm tra:

```powershell
Set-Location 'C:\xampp\htdocs\news-blog-cms'
& 'C:\xampp\php\php.exe' scripts/check.php
```

Không import lại vào database đã có dữ liệu. Nếu dùng `scripts/install.php`, script sẽ dừng khi database đích đã có bảng và không ghi đè cấu hình có sẵn.

### Database đang dùng trên máy phát triển

Database demo là `news_blog_cms`; database nghiệm thu là `news_blog_cms_test`. Không chạy acceptance test trên database demo. Các thông tin trong `config/local.php` và `config/test.php` chỉ dành cho máy local.

## 4. Tài khoản demo

Các tài khoản dưới đây là tài khoản **website**, không phải tài khoản database. Mật khẩu dùng cho môi trường demo local: **`Demo@12345`**.

| Vai trò | Email | Tên |
|---|---|---|
| ADMIN | `admin@newsblog.local` | Nguyễn Khôi Nguyên |
| AUTHOR | `author1@newsblog.local` | Nguyễn Lê Duy Ân |
| AUTHOR | `author2@newsblog.local` | Nguyễn Trí Phong |
| USER | `reader1@newsblog.local` | Lê Tuấn Quy |
| USER | `reader2@newsblog.local` | Nguyễn Tấn Tài |

Không sử dụng các tài khoản/mật khẩu demo trên hệ thống công khai. Tài khoản đăng ký mới có vai trò USER; Admin có thể cấp quyền AUTHOR.

## 5. Xem trước thay đổi giao diện

Để xem mã nguồn trong thư mục Documents mà không ghi đè bản Apache, chạy máy chủ PHP tích hợp với database test. Mở PowerShell tại thư mục dự án:

```powershell
Set-Location 'C:\Users\Nguyen\Documents\news-blog-cms-dev'
$env:CMS_CONFIG = (Resolve-Path '.\config\test.php').Path
& 'C:\xampp\php\php.exe' -S 127.0.0.1:8090 scripts/router.php
```

Giữ cửa sổ PowerShell mở và truy cập:

`http://127.0.0.1:8090/news-blog-cms/`

Khi kết thúc, nhấn `Ctrl+C` trong cửa sổ máy chủ. Cấu hình test giúp tránh dùng nhầm database demo, nhưng dữ liệu test vẫn có thể bị thay đổi bởi thao tác trên website.

Để chạy website qua Apache/XAMPP, mở `http://localhost/news-blog-cms/`. Apache phục vụ bản trong `htdocs`, không phải thư mục Documents. Sau khi thay đổi giao diện, nên kiểm tra trên máy chủ phát triển trước; chỉ đồng bộ các file cần thiết sang `htdocs` sau khi xác nhận thay đổi.

## 6. Chức năng và địa chỉ trang

| Màn hình | URL trên Apache |
|---|---|
| Trang chủ | `http://localhost/news-blog-cms/` |
| Danh sách/tìm kiếm | `http://localhost/news-blog-cms/index.php?r=articles` |
| Đăng nhập | `http://localhost/news-blog-cms/index.php?r=login` |
| Đăng ký | `http://localhost/news-blog-cms/index.php?r=register` |
| Bài viết của tôi | `http://localhost/news-blog-cms/index.php?r=author/posts` |
| Viết bài | `http://localhost/news-blog-cms/index.php?r=author/edit` |
| Dashboard Admin | `http://localhost/news-blog-cms/index.php?r=admin` |
| Duyệt bài | `http://localhost/news-blog-cms/index.php?r=admin/posts&status=pending` |
| Kiểm duyệt bình luận | `http://localhost/news-blog-cms/index.php?r=admin/comments` |

Luồng chính: AUTHOR tạo nháp, xem trước và gửi duyệt; ADMIN có thể trả bài kèm lý do hoặc xuất bản; USER gửi bình luận để ADMIN kiểm duyệt. Bài đã xuất bản cần được Admin gỡ về bản nháp trước khi sửa.

## 7. Kiểm thử

Bộ Playwright POM cho giao diện nằm trong [`e2e/`](e2e/README.md), chạy trên
Chromium desktop 1280px và mobile 375px, có thêm kiểm tra 768px và axe WCAG AA.
Các biến URL/tài khoản lấy từ `e2e/.env`; máy chủ tự dùng database test riêng.
Hướng dẫn cài và chạy, kiểm tra trạng thái rỗng và bảo toàn PHP nằm trong
[`e2e/README.md`](e2e/README.md). Node/npm chỉ cần cho E2E, không phải để chạy CMS.

Kết quả kiểm thử được ghi trong `docs/TEST-RESULTS.md`; hướng dẫn kiểm tra giao diện thủ công ở `docs/MANUAL-TESTS.md`. Kết quả nghiệm thu hiện được ghi nhận là **89 passed, 0 failed**, chạy trên database riêng `news_blog_cms_test`.

### Tạo cấu hình và database test trên máy mới

Chỉ chạy lệnh sau khi xác nhận database `news_blog_cms_test` chưa có bảng. Lệnh `--test` tạo database test từ SQL và ghi kết nối vào `config/test.php`; script từ chối ghi đè database có bảng hoặc file cấu hình đã tồn tại:

```powershell
Set-Location 'C:\Users\Nguyen\Documents\news-blog-cms-dev'
& 'C:\xampp\php\php.exe' scripts/install.php --xampp=C:\xampp --test
```

Lệnh này cần quyền quản trị database để tạo database và tài khoản ứng dụng test. Nếu phpMyAdmin không dùng xác thực `config`, hãy xem hướng dẫn biến môi trường và cách cài trong `scripts/install.php`; không thay đổi thông tin xác thực MySQL chỉ để chạy test.

### Chạy bộ acceptance

Mở PowerShell thứ nhất:

```powershell
Set-Location 'C:\Users\Nguyen\Documents\news-blog-cms-dev'
$env:CMS_CONFIG = (Resolve-Path '.\config\test.php').Path
& 'C:\xampp\php\php.exe' -S 127.0.0.1:8089 scripts/router.php
```

Mở PowerShell thứ hai:

```powershell
Set-Location 'C:\Users\Nguyen\Documents\news-blog-cms-dev'
& 'C:\xampp\php\php.exe' tests/acceptance.php 'http://127.0.0.1:8089/news-blog-cms/'
```

Chỉ chạy acceptance với `config/test.php` và `news_blog_cms_test`; test tạo dữ liệu trong database test. Không sao chép cấu hình local sang test và tuyệt đối không chạy bộ test trên database demo hay database sản xuất. Nhấn `Ctrl+C` để dừng máy chủ.

Kiểm tra cú pháp PHP:

```powershell
Get-ChildItem app,config,scripts,tests -Recurse -Filter *.php |
    ForEach-Object { & 'C:\xampp\php\php.exe' -l $_.FullName }
& 'C:\xampp\php\php.exe' -l index.php
```

## 8. Bảo vệ và tải file

Apache dùng `.htaccess` để tắt directory listing và chặn file/thư mục nội bộ. Các đường dẫn cấu hình, mã nguồn, SQL, log và tài liệu riêng không được phục vụ trực tiếp. Thư mục `uploads` không cho phép thực thi script; ứng dụng xác thực loại MIME và giới hạn kích thước ảnh.

Khi kiểm tra trên Apache, các URL sau phải trả 403 hoặc 404 và không để lộ nội dung:

- `/news-blog-cms/config/local.php`
- `/news-blog-cms/.gitignore`
- `/news-blog-cms/README.md`
- `/news-blog-cms/database/`
- `/news-blog-cms/vendor/`
- `/news-blog-cms/storage/`

## 9. Cấu trúc và tài liệu

```text
news-blog-cms/
├── index.php                 Front controller
├── app/
│   ├── Core/                 PDO, Auth, Security, View, HTTP errors
│   ├── Controllers/          Auth, Public, Post, Admin, Comment
│   ├── Models/               User, Post, Taxonomy, Comment
│   └── Views/                Layout, auth, public, posts, admin
├── assets/                   CSS, JavaScript, ảnh SVG và thư viện giao diện
├── config/                   example.php, routes.php; local/test là cấu hình riêng
├── database/                 Schema và dữ liệu demo
├── docs/                     Kiến trúc, kiểm thử và hướng dẫn thủ công
├── scripts/                  Cài đặt, kiểm tra, định tuyến và đóng gói
├── storage/                  Session, cache, log
├── tests/                    Acceptance và HTTP smoke test
├── uploads/                  Ảnh tải lên
└── vendor/htmlpurifier/      Thư viện lọc HTML
```

- `docs/ARCHITECTURE.md`: kiến trúc và mô hình dữ liệu.
- `docs/FEATURES.md`: chức năng và phạm vi.
- `docs/TEST-RESULTS.md`: kết quả kiểm thử.
- `docs/MANUAL-TESTS.md`: checklist kiểm thử trên trình duyệt.
- `docs/ENVIRONMENT.md`: ghi chú môi trường và giới hạn đã biết.
- `THIRD-PARTY-NOTICES.md`: nguồn và giấy phép thư viện.
- `docs/FILE-LIST.txt`: manifest file bàn giao.

## 10. Git và đóng gói

File `config/local.php`, `config/test.php`, dữ liệu session/log, ảnh upload runtime và kết quả JSON được loại khỏi Git hoặc ZIP đóng gói. Trước khi commit, kiểm tra `git status` và không stage cấu hình hay dữ liệu riêng.

Tạo ZIP mới bằng Python có sẵn trong Windows, với đường dẫn output chưa tồn tại:

```powershell
Set-Location 'C:\Users\Nguyen\Documents\news-blog-cms-dev'
python scripts/package.py --output 'C:\Users\Nguyen\Documents\news-blog-cms-submission-new.zip'
```

Script từ chối ghi đè file ZIP đã tồn tại. Gói nộp không chứa `.git`, `config/local.php`, `config/test.php`, session, log, ảnh upload runtime hoặc kết quả JSON. Sau khi tạo gói, xác nhận file tồn tại, kiểm tra nội dung và tính SHA-256:

```powershell
Get-FileHash 'C:\Users\Nguyen\Documents\news-blog-cms-submission-new.zip' -Algorithm SHA256
```

Không nộp ZIP local chứa cấu hình máy hoặc một bản ZIP cũ có tên gần giống.

## 11. Xử lý sự cố

- **404:** kiểm tra vị trí `index.php`, tên thư mục và việc giải nén có bị lồng thư mục hay không.
- **CSS/ảnh không tải:** kiểm tra `base_path` là `/news-blog-cms` và URL tài nguyên.
- **503 chưa cấu hình:** tạo `config/local.php` từ `config/example.php`.
- **500:** xem `storage/app.log`; không bật hiển thị lỗi ra trình duyệt.
- **Không kết nối database:** kiểm tra MySQL/MariaDB đang chạy và xác minh host, port, database, user, password.
- **419 CSRF:** tải lại form và kiểm tra cookie/quyền ghi thư mục session.
- **409:** dữ liệu vừa được thay đổi ở phiên khác; tải lại trước khi thao tác.
- **429:** đã chạm giới hạn đăng nhập hoặc bình luận; chờ rồi thử lại.
- **Upload thất bại:** dùng ảnh JPEG/PNG/WebP hợp lệ, tối đa 2 MB và kiểm tra quyền ghi `uploads`.
- **Lỗi Aria hoặc bảng quyền MySQL:** sao lưu và nhờ quản trị viên XAMPP xử lý; không xóa `mysql/data` hoặc tự sửa bảng hệ thống.
