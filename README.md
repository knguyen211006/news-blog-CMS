# Góc Nhìn — CMS tin tức và blog đa tác giả

Đồ án môn BackEnd1 — Nhóm 6 — Đề tài 4: Hệ thống quản lý tin tức và blog multi-author (CMS thu nhỏ).

Ứng dụng viết bằng **PHP MVC thuần** (không framework), kết nối MySQL/MariaDB qua PDO, giao diện tiếng Việt. Khi vận hành không cần Composer, npm, CDN hay dịch vụ bên ngoài; font dùng font có sẵn của hệ điều hành (Segoe UI, Cambria).

Repository: https://github.com/knguyen211006/news-blog-CMS

## 1. Thành viên và phân công

| Thành viên | Vai trò | Phụ trách |
|---|---|---|
| Nguyễn Khôi Nguyên | Trưởng nhóm | Khung MVC, xác thực, phân quyền, quản lý tài khoản; tích hợp, README |
| Nguyễn Trí Phong | Thành viên | Giao diện công khai, tìm kiếm, lọc, đếm lượt xem |
| Nguyễn Lê Duy Ân | Thành viên | Soạn bài, ảnh đại diện, CRUD bài viết, gửi duyệt |
| Lê Tuấn Quy | Thành viên | Duyệt bài, chuyên mục, thẻ, dashboard |
| Võ Tấn Tài | Thành viên | Bình luận phân cấp, kiểm duyệt; điều phối kiểm thử, dữ liệu demo |

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

- PHP 8.1 trở lên (đã chạy trên PHP 8.2.12).
- MySQL 8 hoặc MariaDB 10.4 trở lên (đã chạy trên MariaDB 10.4.32 của XAMPP).
- Apache 2.4 với `mod_rewrite` và `AllowOverride All` cho thư mục chứa dự án.
- PHP extension: `pdo_mysql`, `mbstring`, `fileinfo`, `dom`, `iconv`, `session`, `openssl`.
- Node.js và npm: **chỉ** cần nếu muốn chạy kiểm thử giao diện Playwright trong `e2e/`, không cần để chạy website.
- Python 3: chỉ cần để chạy `scripts/package.py` (đóng gói).

Kiểm tra nhanh PHP (đường dẫn dưới đây là mặc định của XAMPP trên Windows, đổi theo máy của bạn):

```powershell
& 'C:\xampp\php\php.exe' -v
& 'C:\xampp\php\php.exe' -m
```

## 5. Cài đặt và chạy

### 5.1. Cài trên máy mới (XAMPP)

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

Lưu ý: `config/local.php` và `config/test.php` chứa thông tin kết nối riêng của từng máy, **không** được commit và **không** nằm trong gói ZIP nộp. Có thể dùng `scripts/install.php` để cài tự động; script từ chối ghi đè database đã có bảng hoặc file cấu hình đã tồn tại.

### 5.2. Tài khoản demo

Đây là tài khoản của website (bảng `users`), không phải tài khoản MySQL. Mật khẩu chung: `Demo@12345`. Chỉ dùng cho môi trường demo local.

| Vai trò | Email | Họ tên |
|---|---|---|
| ADMIN | `admin@newsblog.local` | Nguyễn Khôi Nguyên |
| AUTHOR | `author1@newsblog.local` | Nguyễn Lê Duy Ân |
| AUTHOR | `author2@newsblog.local` | Nguyễn Trí Phong |
| USER | `reader1@newsblog.local` | Lê Tuấn Quy |
| USER | `reader2@newsblog.local` | Võ Tấn Tài |

### 5.3. Các địa chỉ trang

Ứng dụng dùng một front controller (`index.php`), định tuyến bằng tham số `?r=`:

| Màn hình | URL |
|---|---|
| Trang chủ | `/news-blog-cms/` |
| Danh sách, tìm kiếm | `/news-blog-cms/index.php?r=articles` |
| Chi tiết bài | `/news-blog-cms/index.php?r=article&slug={slug}` |
| Đăng nhập, đăng ký | `/news-blog-cms/index.php?r=login`, `?r=register` |
| Hồ sơ cá nhân | `/news-blog-cms/index.php?r=profile` |
| Bài viết của tôi (Tác giả) | `/news-blog-cms/index.php?r=author/posts` |
| Viết bài / sửa bài | `/news-blog-cms/index.php?r=author/edit` |
| Dashboard Admin | `/news-blog-cms/index.php?r=admin` |
| Duyệt bài | `/news-blog-cms/index.php?r=admin/posts&status=pending` |
| Chuyên mục, thẻ | `/news-blog-cms/index.php?r=admin/taxonomy` |
| Kiểm duyệt bình luận | `/news-blog-cms/index.php?r=admin/comments` |
| Quản lý tài khoản | `/news-blog-cms/index.php?r=admin/users` |

Mở trang Admin khi chưa đăng nhập sẽ được chuyển về trang đăng nhập; người đã đăng nhập nhưng không đủ quyền nhận lỗi 403.

## 6. Kiểm thử

Kiểm thử chạy trên **database riêng `news_blog_cms_test`**, không chạy trên database demo.

**Cảnh báo:** acceptance và mỗi lượt Playwright tự dọn và nạp lại database test trước khi chạy. Chỉ dùng với `news_blog_cms_test` qua `config/test.php`; dữ liệu thêm thủ công trong database test sẽ bị xóa.

### 6.1. Tạo cấu hình và database test (một lần)

Chỉ chạy khi `news_blog_cms_test` chưa có bảng. Lệnh tạo database test và ghi `config/test.php`:

```powershell
cd <thư-mục-dự-án>
& 'C:\xampp\php\php.exe' scripts/install.php --xampp=C:\xampp --test
```

### 6.2. Kiểm thử chấp nhận PHP (acceptance)

Terminal 1, khởi động máy chủ test:

```powershell
cd <thư-mục-dự-án>
$env:CMS_CONFIG = (Resolve-Path '.\config\test.php').Path
& 'C:\xampp\php\php.exe' -S 127.0.0.1:8089 scripts/router.php
```

Terminal 2, chạy bộ kiểm thử:

```powershell
cd <thư-mục-dự-án>
& 'C:\xampp\php\php.exe' tests/acceptance.php 'http://127.0.0.1:8089/news-blog-cms/'
```

Bộ này gồm 89 ca (xác thực, phân quyền theo vai trò và quyền sở hữu, CSRF, lọc HTML/XSS, SQL injection qua tìm kiếm, quy trình duyệt bài, chống duyệt trùng bằng `version`, bình luận, upload ảnh giả, giới hạn đăng nhập và bình luận, xóa mềm). Lần kiểm chứng ngày 26/10/2026 đạt **89 PASS / 0 FAIL**; Playwright ba lượt liên tiếp đều **43 passed / 0 failed**, HTTP smoke **39 passed / 0 failed**. Nút chọn file Dark mode đạt tương phản **12,5685:1** trên desktop/mobile. Kết quả và phạm vi xác minh: `docs/TEST-RESULTS.md`. Bộ test tạo dữ liệu trong database test; dừng máy chủ bằng `Ctrl+C` khi xong.

### 6.3. Kiểm tra cú pháp PHP

```powershell
Get-ChildItem app,config,scripts,tests -Recurse -Filter *.php |
    ForEach-Object { & 'C:\xampp\php\php.exe' -l $_.FullName }
& 'C:\xampp\php\php.exe' -l index.php
```

### 6.4. Kiểm thử giao diện Playwright (tùy chọn)

Bộ Playwright theo mô hình Page Object nằm trong `e2e/`, chạy trên Chromium desktop 1280px và mobile 375px; địa chỉ và tài khoản đọc từ `e2e/.env` (sao chép từ `e2e/.env.example`). Hướng dẫn cài đặt và chạy: xem `e2e/README.md`. Kết quả ghi trong `docs/TEST-RESULTS.md`.

Kiểm tra thủ công trên trình duyệt: `docs/MANUAL-TESTS.md`.

`php tests/view-states.php` đã xác minh **12 trạng thái rỗng qua 10 ca render View** ngày 26/10/2026: không có PHP warning, mỗi trạng thái có icon/nội dung/CTA. Đây là kiểm tra DOM phía server, không phải xác minh hình ảnh của cả 12 trạng thái trên trình duyệt.

## 7. Xem trước giao diện không ảnh hưởng database demo

Khi chỉnh giao diện, có thể chạy máy chủ PHP tích hợp với database test, không cần chép sang Apache:

```powershell
cd <thư-mục-dự-án>
$env:CMS_CONFIG = (Resolve-Path '.\config\test.php').Path
& 'C:\xampp\php\php.exe' -S 127.0.0.1:8090 scripts/router.php
```

Mở `http://127.0.0.1:8090/news-blog-cms/`. Dữ liệu test vẫn có thể bị thay đổi khi thao tác trên website.

## 8. Bảo mật

- **SQL injection:** mọi truy vấn dùng PDO prepared statements.
- **XSS:** nội dung thường được escape khi hiển thị; nội dung rich text lọc bằng HTML Purifier theo danh sách thẻ cho phép (loại `<script>`, `<iframe>`, thuộc tính sự kiện, URL `javascript:`); bình luận là văn bản thuần.
- **CSRF:** thao tác gửi form để ghi dữ liệu dùng POST và kiểm tra token; thiếu token trả HTTP 419. GET không xóa hay sửa dữ liệu nội dung; ngoại lệ là đếm lượt xem bài.
- **Phiên đăng nhập:** đổi session ID sau khi đăng nhập; cookie `HttpOnly`, `SameSite=Lax`; kiểm tra lại trạng thái tài khoản ở mỗi yêu cầu nên tài khoản bị khóa mất quyền ngay. Mật khẩu được băm, không lưu dạng thuần.
- **Giới hạn tần suất:** đăng nhập sai nhiều lần và bình luận liên tiếp bị chặn bằng HTTP 429.
- **Upload ảnh:** chỉ nhận JPEG/PNG/WebP tối đa 2 MB, kiểm tra MIME thực tế, đổi tên ngẫu nhiên; `uploads/.htaccess` khai báo chặn thực thi PHP, cần xác minh trên Apache đích.
- **Apache `.htaccess`:** cấu hình khai báo tắt liệt kê thư mục và chặn truy cập trực tiếp vào cấu hình, mã nguồn, SQL, log, tài liệu. Chưa xác minh lại trên Apache trong Giai đoạn C; kết quả HTTP qua PHP router không xác nhận các quy tắc này. Khi triển khai, các URL sau phải trả 403 hoặc 404 và không lộ nội dung:
  - `/news-blog-cms/config/local.php`
  - `/news-blog-cms/.gitignore`
  - `/news-blog-cms/README.md`
  - `/news-blog-cms/database/`
  - `/news-blog-cms/vendor/`
  - `/news-blog-cms/storage/`

## 9. Cơ sở dữ liệu

Có 8 bảng InnoDB, charset utf8mb4: `users`, `categories`, `tags`, `posts`, `post_tags` (quan hệ nhiều–nhiều bài viết và thẻ), `comments` (tự tham chiếu `parent_id` cho trả lời), `post_history` (nhật ký thao tác trên bài viết), `rate_limits` (đếm giới hạn tần suất). Cấu trúc ở `database/schema.sql`; dữ liệu demo (5 tài khoản, 4 chuyên mục, 6 thẻ, 12 bài, 6 bình luận) ở `database/news_blog_cms.sql`. Chi tiết thiết kế: `docs/ARCHITECTURE.md`.

## 10. Cấu trúc thư mục

```text
news-blog-cms/
├── index.php                 Front controller
├── .htaccess                 Chặn file nội bộ, tắt liệt kê thư mục
├── app/
│   ├── bootstrap.php, helpers.php
│   ├── Core/                 DB, Auth, Security, View, HttpException
│   ├── Controllers/          Auth, Public, Post, Admin, Comment
│   ├── Models/               User, Post, Taxonomy, Comment
│   └── Views/                Layout, auth, public, posts, admin, partials
├── assets/                   CSS, JavaScript, ảnh SVG, thư viện soạn thảo Pell
├── config/                   example.php, routes.php (local.php và test.php là cấu hình riêng, không nộp)
├── database/                 schema.sql, news_blog_cms.sql
├── docs/                     Kiến trúc, chức năng, kết quả kiểm thử, kiểm thử thủ công, môi trường
├── e2e/                      Kiểm thử giao diện Playwright (tùy chọn)
├── scripts/                  install.php, check.php, router.php, package.py
├── storage/                  Session, cache, log
├── tests/                    acceptance.php, view-contract.php, view-states.php, support/
├── uploads/                  Ảnh tải lên
└── vendor/htmlpurifier/      Thư viện lọc HTML
```

Tài liệu: `docs/ARCHITECTURE.md` (kiến trúc, bảng dữ liệu), `docs/FEATURES.md` (chức năng, phạm vi), `docs/TEST-RESULTS.md` (kết quả kiểm thử), `docs/MANUAL-TESTS.md` (kiểm thử thủ công), `docs/ENVIRONMENT.md` (môi trường, giới hạn đã biết), `docs/FILE-LIST.txt` (danh sách file bàn giao), `THIRD-PARTY-NOTICES.md` (giấy phép thư viện).

## 11. Git và đóng gói

`config/local.php`, `config/test.php`, `e2e/.env`, `node_modules/`, trình duyệt của Playwright, session, log, ảnh upload và kết quả kiểm thử dạng JSON được loại khỏi Git và gói nộp. Trước khi commit, kiểm tra `git status` để không đưa cấu hình riêng vào.

Tạo gói nộp (đường dẫn output phải chưa tồn tại, script không ghi đè):

```powershell
cd <thư-mục-dự-án>
python scripts/package.py --output '<đường-dẫn>\news-blog-cms-submission.zip'
Get-FileHash '<đường-dẫn>\news-blog-cms-submission.zip' -Algorithm SHA256
```

Sau khi tạo, mở thử ZIP để xác nhận không có `config/local.php`, `config/test.php`, `.git`; rồi giải nén vào thư mục sạch, làm theo mục 5.1 để chắc chắn cài từ ZIP chạy được.

## 12. Xử lý sự cố

- **404:** kiểm tra `index.php` nằm trực tiếp trong `news-blog-cms` (không bị lồng hai cấp), và Apache có `AllowOverride All`, `mod_rewrite` bật.
- **Mất CSS hoặc ảnh:** kiểm tra `'base_path' => '/news-blog-cms'` trong `config/local.php`; thử mở `http://localhost/news-blog-cms/assets/css/app.css`.
- **503 chưa cấu hình:** tạo `config/local.php` từ `config/example.php`.
- **500:** xem `storage/app.log`; không bật hiển thị lỗi ra trình duyệt.
- **Không kết nối được database:** kiểm tra MySQL đang chạy; xác minh host, port, tên database, user, mật khẩu.
- **419:** tải lại trang để lấy token mới; kiểm tra quyền ghi thư mục `storage/`.
- **409:** dữ liệu vừa được thay đổi ở phiên khác; tải lại rồi thao tác lại.
- **429:** đã chạm giới hạn đăng nhập hoặc bình luận; chờ rồi thử lại.
- **Upload thất bại:** dùng ảnh JPEG/PNG/WebP hợp lệ, tối đa 2 MB; kiểm tra quyền ghi `uploads/`.
- **Tiêu đề tiếng Việt bị tách dấu:** nhấn `Ctrl+F5` để xóa cache CSS. Giao diện dùng font hệ thống Cambria (tiêu đề) và Segoe UI (nội dung) có sẵn trên Windows; trên máy không có các font này, trình duyệt dùng font dự phòng nên hình dạng chữ có thể khác.
- **Lỗi bảng Aria hoặc bảng quyền của MySQL:** xem `docs/ENVIRONMENT.md`; sao lưu trước, không xóa `mysql/data`.
