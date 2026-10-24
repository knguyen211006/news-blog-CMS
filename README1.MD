# Góc Nhìn — CMS Tin tức và Blog Đa tác giả (PHP MVC Thuần)

Ứng dụng tin tức và blog đa tác giả xây dựng bằng PHP MVC thuần (Native PHP), kết nối MySQL/MariaDB qua PDO, giao diện tiếng Việt hoàn chỉnh và chạy dưới tiền tố URL `/news-blog-cms/`.

Hệ thống được thiết kế theo tiêu chí **không phụ thuộc thư viện runtime bên ngoài** (không cần Composer, không cần npm, không cần CDN hay dịch vụ đám mây khi vận hành). Đồng thời, hệ thống đã được nâng cấp toàn diện về giao diện chuẩn UX/UI (Design System 8pt, Dark Mode, Double-Submit Guard, 12 Empty States) và trang bị bộ kiểm thử tự động hóa kép (Playwright E2E POM 43/43 PASS và PHP Acceptance 89/89 PASS).

---

## 1. Thư mục làm việc và cách mở dự án

Mở thư mục dự án bằng **Antigravity / VS Code → Open Folder**:

`C:\Users\Nguyen\Documents\news-blog-cms-dev`

Đây là thư mục chứa mã nguồn chính dùng để phát triển, chỉnh sửa và chạy kiểm thử tự động. Ba vị trí liên quan trong môi trường phát triển có mục đích riêng biệt:

| Đường dẫn | Mục đích sử dụng |
|---|---|
| `C:\Users\Nguyen\Documents\news-blog-cms-dev` | **Mã nguồn làm việc chính**; nơi lập trình, chạy E2E, audit UI và commit Git. |
| `C:\xampp\htdocs\news-blog-cms` | **Bản Apache/XAMPP đang chạy** tại `http://localhost/news-blog-cms/`. |
| `C:\Users\Nguyen\Documents\news-blog-cms-submission-*.zip` | **Gói nộp đồ án đóng gói sạch**; không chứa file cấu hình local hay dữ liệu tạm. |

> **Nguyên tắc đồng bộ quan trọng:**  
> Ba vị trí trên hoàn toàn tách biệt. Mọi chỉnh sửa trong thư mục làm việc **không tự động** phản ánh sang Apache `htdocs` hay gói ZIP. Quy trình chuẩn: phát triển trong thư mục dev -> chạy máy chủ kiểm thử tích hợp -> chạy toàn bộ test suite (Playwright & PHP) -> đồng bộ các file cần thiết sang `htdocs` -> đóng gói ZIP bằng script. Tuyệt đối không copy đè cả thư mục `htdocs` vì nơi đó lưu trữ runtime data, upload và cấu hình kết nối riêng.

Repository Git cục bộ (`.git`) nằm trực tiếp tại thư mục làm việc.

---

## 2. Yêu cầu môi trường hệ thống

### Môi trường vận hành CMS (Runtime)
- **Hệ điều hành:** Windows 10/11 (hoặc Linux/macOS tương đương).
- **PHP:** Phiên bản 8.1 trở lên (đã kiểm định và tương thích tối đa trên **PHP 8.2.12**).
- **Web Server:** Apache 2.4 với module `mod_rewrite` đã bật và cấu hình `AllowOverride All` cho thư mục `htdocs`.
- **Cơ sở dữ liệu:** MySQL 8.0+ hoặc MariaDB 10.4 trở lên (khuyên dùng MariaDB đi kèm XAMPP).
- **PHP Extensions bắt buộc:**
  - `pdo_mysql` (kết nối cơ sở dữ liệu qua PDO an toàn)
  - `mbstring` (xử lý chuỗi đa byte UTF-8 tiếng Việt)
  - `fileinfo` (kiểm tra MIME type tệp tin ảnh tải lên)
  - `dom` & `iconv` (hỗ trợ thư viện lọc HTML Purifier)
  - `session` (quản trị phiên làm việc người dùng)
  - `openssl` (sinh chuỗi ngẫu nhiên bảo mật và mã hóa)

### Môi trường kiểm thử tự động (E2E Testing)
- **Node.js & npm:** Chỉ yêu cầu khi chạy bộ kiểm thử giao diện tự động hóa Playwright trong thư mục `e2e/`. Không cần Node.js để chạy web hay phục vụ người dùng.
- **Python 3:** Đã có sẵn trên Windows, dùng để thực thi script đóng gói `scripts/package.py`.

Kiểm tra nhanh phiên bản và extensions qua PowerShell:

```powershell
& 'C:\xampp\php\php.exe' -v
& 'C:\xampp\php\php.exe' -m
```

---

## 3. Cấu hình và cơ sở dữ liệu

Các file cấu hình chứa thông tin kết nối thực tế được cô lập hoàn toàn, không đưa lên Git và không nằm trong gói ZIP nộp:

- `config/example.php`: File cấu hình mẫu an toàn, không chứa mật khẩu thật.
- `config/local.php`: File cấu hình kết nối database demo **`news_blog_cms`** (dùng cho Apache/XAMPP).
- `config/test.php`: File cấu hình kết nối database kiểm thử độc lập **`news_blog_cms_test`** (dùng cho Acceptance Test và E2E Test).

### Cài đặt Database Demo cho máy mới (`news_blog_cms`)

1. Mở phpMyAdmin tại `http://localhost/phpmyadmin/`.
2. Chọn thẻ **Import**, duyệt đến tệp `database/news_blog_cms.sql` và nhấn **Import**. Tệp SQL tự động tạo database `news_blog_cms` với bảng mã `utf8mb4_unicode_ci` cùng dữ liệu bài viết, chuyên mục, thẻ và tài khoản mẫu.
3. Sao chép `config/example.php` thành `config/local.php`, cập nhật `db_user` và `db_password` phù hợp với máy chủ local. Giữ nguyên `'base_path' => '/news-blog-cms'`.
4. Kiểm tra cấu hình và kết nối:

```powershell
Set-Location 'C:\Users\Nguyen\Documents\news-blog-cms-dev'
& 'C:\xampp\php\php.exe' scripts/check.php
```

### Cài đặt Database Test cô lập (`news_blog_cms_test`)

Để phục vụ kiểm thử tự động mà không làm hỏng dữ liệu demo:

```powershell
Set-Location 'C:\Users\Nguyen\Documents\news-blog-cms-dev'
& 'C:\xampp\php\php.exe' scripts/install.php --xampp=C:\xampp --test
```

Lệnh trên tự động tạo cơ sở dữ liệu `news_blog_cms_test`, nạp schema chuẩn và sinh file `config/test.php`. Nếu database hoặc file cấu hình đã tồn tại, script sẽ an toàn từ chối ghi đè.

---

## 4. Danh sách tài khoản demo

Tất cả tài khoản demo dưới đây là tài khoản người dùng của website (bảng `users`), không phải tài khoản quản trị MySQL. Mật khẩu dùng chung cho môi trường thử nghiệm: **`Demo@12345`**.

| Vai trò (Role) | Email đăng nhập | Họ và tên | Chức năng chính |
|---|---|---|---|
| **ADMIN** | `admin@newsblog.local` | Nguyễn Khôi Nguyên | Quản trị tối cao: duyệt/trả/gỡ bài, quản lý chuyên mục & thẻ, kiểm duyệt bình luận, phân quyền người dùng. |
| **AUTHOR** | `author1@newsblog.local` | Nguyễn Lê Duy Ân | Tác giả 1: soạn thảo bài viết, tải ảnh bìa, gắn thẻ, lưu nháp, gửi yêu cầu duyệt bài. |
| **AUTHOR** | `author2@newsblog.local` | Nguyễn Trí Phong | Tác giả 2: kiểm thử quyền sở hữu bài viết riêng biệt giữa các tác giả. |
| **USER** | `reader1@newsblog.local` | Lê Tuấn Quy | Độc giả 1: đọc bài viết, gửi bình luận cấp 1 và phản hồi cấp 2. |
| **USER** | `reader2@newsblog.local` | Nguyễn Tấn Tài | Độc giả 2: tương tác bình luận độc lập, cập nhật hồ sơ cá nhân. |

> **Lưu ý bảo mật:**  
> Tài khoản mới tự đăng ký qua giao diện sẽ mặc định nhận vai trò **USER**. Chỉ Quản trị viên (ADMIN) mới có quyền nâng cấp tài khoản USER lên AUTHOR trong bảng điều khiển.

---

## 5. Xem trước và phát triển giao diện (Preview Server & Apache)

### Chế độ xem trước độc lập (Khuyên dùng khi lập trình)

Để xem trước các thay đổi giao diện, CSS và JavaScript trực tiếp trong thư mục `news-blog-cms-dev` mà không cần sao chép sang XAMPP và không ảnh hưởng dữ liệu demo, khởi động máy chủ PHP tích hợp (Built-in Server) với database test:

```powershell
Set-Location 'C:\Users\Nguyen\Documents\news-blog-cms-dev'
$env:CMS_CONFIG = (Resolve-Path '.\config\test.php').Path
& 'C:\xampp\php\php.exe' -S 127.0.0.1:8090 scripts/router.php
```

Giữ cửa sổ PowerShell hoạt động và truy cập:

👉 **`http://127.0.0.1:8090/news-blog-cms/`**

Nhấn `Ctrl + C` khi muốn dừng máy chủ.

### Chế độ vận hành chuẩn qua Apache / XAMPP

Khi muốn kiểm tra qua máy chủ web Apache hoàn chỉnh:
1. Mở XAMPP Control Panel, đảm bảo **Apache** và **MySQL** đang ở trạng thái `Running`.
2. Truy cập: 👉 **`http://localhost/news-blog-cms/`**
3. Apache phục vụ mã nguồn nằm trong `C:\xampp\htdocs\news-blog-cms`. Sau khi kiểm tra hoàn tất trên máy chủ phát triển, đồng bộ các tệp cần thiết từ thư mục dev sang `htdocs`.

---

## 6. Chức năng hệ thống và địa chỉ các trang (URLs)

Hệ thống sử dụng cơ chế Front Controller (`index.php`) định tuyến qua tham số truy vấn `?r=...`:

| Trang chức năng | Mục đích và đối tượng | URL trên hệ thống |
|---|---|---|
| **Trang chủ** | Tin tức nổi bật, bài viết mới nhất, phân trang | `http://localhost/news-blog-cms/` |
| **Tìm kiếm / Danh sách** | Lọc theo từ khóa, chuyên mục, thẻ | `http://localhost/news-blog-cms/index.php?r=articles` |
| **Chi tiết bài viết** | Đọc nội dung bài, tác giả, bình luận 2 cấp | `http://localhost/news-blog-cms/index.php?r=article&slug={slug}` |
| **Đăng nhập** | Xác thực người dùng, bảo vệ brute-force | `http://localhost/news-blog-cms/index.php?r=login` |
| **Đăng ký** | Đăng ký tài khoản độc giả mới | `http://localhost/news-blog-cms/index.php?r=register` |
| **Hồ sơ cá nhân** | Đổi mật khẩu, cập nhật thông tin | `http://localhost/news-blog-cms/index.php?r=profile` |
| **Bài viết của tôi** | Tác giả quản lý danh sách bài viết riêng | `http://localhost/news-blog-cms/index.php?r=author/posts` |
| **Soạn thảo / Sửa bài** | Editor Pell tích hợp, upload ảnh, chọn thẻ | `http://localhost/news-blog-cms/index.php?r=author/edit` |
| **Xem trước bài viết** | Tác giả xem trước bài nháp an toàn | `http://localhost/news-blog-cms/index.php?r=post/preview&id={id}` |
| **Dashboard Quản trị** | Số liệu thống kê, bài chờ duyệt, nhật ký | `http://localhost/news-blog-cms/index.php?r=admin` |
| **Duyệt bài viết** | Phê duyệt, từ chối kèm lý do, gỡ bài | `http://localhost/news-blog-cms/index.php?r=admin/posts&status=pending` |
| **Quản lý chuyên mục/thẻ**| Thêm, sửa slug, xóa an toàn (RESTRICT/CASCADE)| `http://localhost/news-blog-cms/index.php?r=admin/taxonomy` |
| **Kiểm duyệt bình luận** | Duyệt bình luận chờ duyệt, ẩn bình luận | `http://localhost/news-blog-cms/index.php?r=admin/comments` |
| **Quản trị người dùng** | Cấp quyền tác giả, khóa/mở khóa tài khoản | `http://localhost/news-blog-cms/index.php?r=admin/users` |

### Quy trình vòng đời bài viết (Publishing Lifecycle)
1. **Soạn thảo (Draft):** Tác giả tạo bài viết mới. Mọi bài viết khởi tạo luôn ở trạng thái `draft`.
2. **Gửi duyệt (Pending):** Khi hoàn tất tiêu đề (5-200 ký tự), tóm tắt (10-500 ký tự), nội dung đã lọc thẻ (tối thiểu 30 ký tự) và chuyên mục hợp lệ, tác giả bấm **Gửi duyệt bài**.
3. **Phê duyệt hoặc Trả bài:**
   - **Xuất bản (Published):** Admin kiểm duyệt nội dung và phê duyệt; bài viết được công khai trên trang chủ với thời điểm `published_at`.
   - **Trả bài (Rejected):** Admin từ chối kèm lý do bắt buộc (5-2000 ký tự). Bài viết quay về tác giả để chỉnh sửa và gửi duyệt lại.
4. **Gỡ bài (Unpublish):** Bài đã xuất bản không được sửa trực tiếp. Admin phải thực hiện thao tác gỡ bài về trạng thái `draft` trước khi cho phép biên tập viên chỉnh sửa.
5. **Khóa chống xung đột phiên bản (Optimistic Locking):** Mỗi lần cập nhật sẽ tăng trường `version`. Khi lưu, truy vấn sử dụng `SELECT ... FOR UPDATE` và kiểm tra version; nếu phát hiện dữ liệu đã bị sửa đổi ở phiên khác, hệ thống lập tức cảnh báo lỗi `409 Conflict`.

---

## 7. Nâng cấp trải nghiệm người dùng (UX/UI Design System & Accessibility)

Giao diện của hệ thống đã được tái thiết kế toàn diện, mang lại cảm giác chuyên nghiệp, hiện đại và chuẩn mực:

1. **Design System & 8pt Grid:**
   - Bảng biến màu HSL chuẩn mực, phân cấp tương phản cao theo chuẩn **WCAG AA**.
   - Spacing đồng bộ theo hệ số 8pt (`0.5rem`, `1rem`, `1.5rem`, `2rem`, `3rem`).
   - Hệ thống bo góc mềm mại, đổ bóng elevation đa tầng, hiệu ứng hover mượt mà.
   - **Typography chuẩn tiếng Việt:** Kết hợp hài hòa giữa **Inter** (giao diện, nút bấm, nội dung tóm tắt) và **Lora** & **Merriweather** (font serif biên tập cao cấp cho toàn bộ tiêu đề `h1, h2, h3` và nội dung chi tiết bài viết).
   - **Khắc phục lỗi vỡ dấu tiếng Việt:** Chuẩn hóa `letter-spacing: normal` kết hợp `font-feature-settings: "kern" 1, "liga" 1;` triệt tiêu hoàn toàn lỗi tách rời dấu thanh phức hợp (Mark Detachment bug) trên mọi trình duyệt Chromium/Windows.

2. **Chế độ Sáng / Tối (Light & Dark Theme):**
   - Hỗ trợ đầy đủ theme Sáng và Tối với công tắc chuyển đổi tức thì trên thanh điều hướng.
   - Tự động nhận diện thiết lập `prefers-color-scheme` của hệ điều hành.
   - Ghi nhớ trạng thái người dùng qua `localStorage` để duy trì giao diện nhất quán giữa các trang.

3. **Cơ chế chống gửi lặp thông minh (Double-Submit Guard):**
   - Tự động khóa và hiển thị trạng thái xử lý trên form khi người dùng nhấn submit.
   - Bảo toàn chính xác tên nút submit được bấm (`name`/`value`) để backend PHP nhận diện đúng hành động (ví dụ: "Lưu nháp" vs "Gửi duyệt").
   - Xử lý mượt mà khi người dùng nhấn **Cancel** trên hộp thoại xác nhận `window.confirm()` (mở lại nút bấm ngay lập tức mà không bị kẹt trạng thái disable).

4. **12 Trạng thái rỗng chuyên dụng (Empty States):**
   - Trang bị hình minh họa SVG ngữ nghĩa, thông điệp hướng dẫn rõ ràng và nút bấm hành động (CTA) cho tất cả các tình huống không có dữ liệu:
     - Danh sách bài viết rỗng trên trang chủ.
     - Kết quả tìm kiếm không tìm thấy bài viết.
     - Tác giả chưa có bài viết nào hoặc không có bài viết theo bộ lọc.
     - Hàng đợi bài chờ duyệt rỗng trong trang quản trị.
     - Chưa có chuyên mục / thẻ nào trong hệ thống.
     - Danh sách bình luận trống trên bài viết hoặc hàng đợi duyệt bình luận rỗng.
     - Danh sách tài khoản người dùng rỗng.
     - Trang lỗi 404 / 500 thân thiện với người dùng.

---

## 8. Toàn diện kiểm thử và kiểm chứng tự động (Testing Suites)

Hệ thống sở hữu bộ kiểm thử đa tầng bao phủ từ logic nghiệp vụ, backend contracts đến giao diện trình duyệt thực tế:

```
Kiểm thử tự động kép:
├── 1. PHP Acceptance Suite (tests/acceptance.php)        --> 89 Passed / 0 Failed
├── 2. Playwright E2E Automation (e2e/)                   --> 43 Passed / 0 Failed
├── 3. View Contract Audit (tests/view-contract.php)      --> 0 Missing Contracts
├── 4. Empty State Verification (tests/view-states.php)   --> 12/12 States Passed
├── 5. PHP Syntax Linting (Views + Core + Controllers)    --> 47/47 Files Clean
└── 6. TypeScript Typecheck (e2e/tsconfig.json)           --> 0 Errors
```

### 8.1. Kiểm thử chấp nhận PHP (Acceptance Tests)
Kiểm tra 89 kịch bản nghiệp vụ backend: phân quyền, transaction, CSRF, XSS filter, SQL Injection, chống duyệt trùng, rate limit, xóa mềm:

```powershell
# Bước 1: Khởi động máy chủ test tại terminal 1
Set-Location 'C:\Users\Nguyen\Documents\news-blog-cms-dev'
$env:CMS_CONFIG = (Resolve-Path '.\config\test.php').Path
& 'C:\xampp\php\php.exe' -S 127.0.0.1:8089 scripts/router.php

# Bước 2: Chạy test tại terminal 2
Set-Location 'C:\Users\Nguyen\Documents\news-blog-cms-dev'
& 'C:\xampp\php\php.exe' tests/acceptance.php 'http://127.0.0.1:8089/news-blog-cms/'
```

### 8.2. Kiểm thử giao diện tự động hóa Playwright (E2E Tests)
Kiểm tra luồng người dùng trên trình duyệt thật (Desktop 1280×800 và Mobile 375×812, kiểm tra responsive 768px, kiểm tra truy cập chuẩn axe WCAG AA):

```powershell
Set-Location 'C:\Users\Nguyen\Documents\news-blog-cms-dev\e2e'
npm.cmd ci
if (!(Test-Path .env)) { Copy-Item .env.example .env }
$env:PLAYWRIGHT_BROWSERS_PATH = Join-Path (Get-Location) '.browsers'
npx.cmd playwright install chromium
npm.cmd run typecheck
npm.cmd test
```

### 8.3. Kiểm tra toàn vẹn hợp đồng giao diện và trạng thái rỗng
Đảm bảo quá trình nâng cấp giao diện không làm mất bất kỳ biểu thức PHP hay thuộc tính form (`href`, `action`, `name`) nào:

```powershell
Set-Location 'C:\Users\Nguyen\Documents\news-blog-cms-dev'
& 'C:\xampp\php\php.exe' tests/view-contract.php
& 'C:\xampp\php\php.exe' tests/view-states.php
```

### 8.4. Kiểm tra cú pháp toàn bộ tệp tin PHP (PHP Lint)

```powershell
Get-ChildItem app,config,scripts,tests -Recurse -Filter *.php |
    ForEach-Object { & 'C:\xampp\php\php.exe' -l $_.FullName }
& 'C:\xampp\php\php.exe' -l index.php
```

---

## 9. Cơ chế bảo vệ, bảo mật và xử lý tệp tin

- **Bảo vệ qua cấu hình Apache (`.htaccess`):**
  - Tắt hoàn toàn tính năng duyệt thư mục (`Options -Indexes`).
  - Chặn triệt để mọi truy cập trực tiếp từ bên ngoài vào các thư mục và tệp tin nhạy cảm: `config/`, `database/`, `storage/`, `vendor/`, `tests/`, `.git*`, `*.md`, `*.json`, `*.lock`.
- **An toàn thư mục tải lên (`uploads/.htaccess`):**
  - Vô hiệu hóa trình biên dịch PHP trong thư mục tải lên, ngăn chặn hoàn toàn nguy cơ thực thi webshell.
  - Kiểm tra MIME type thực tế bằng `mime_content_type` và tính toàn vẹn của ảnh bằng `getimagesize`.
  - Chỉ chấp nhận các định dạng ảnh an toàn: JPEG, PNG, WebP với dung lượng tối đa **2 MB** và kích thước tối đa 8000×8000 pixel.
- **Bảo mật xác thực & phiên làm việc (Session & Auth):**
  - Tạo mới Session ID (`session_regenerate_id(true)`) ngay sau khi đăng nhập thành công.
  - Cấu hình cookie phiên với các cờ `HttpOnly`, `SameSite=Lax`, và `Secure` (khi chạy HTTPS).
  - Kiểm tra lại trạng thái tài khoản từ database ở mỗi lượt request; tài khoản bị khóa sẽ mất quyền ngay lập tức.
- **Phòng chống các lỗ hổng web phổ biến:**
  - **SQL Injection:** 100% truy vấn dữ liệu sử dụng PDO Prepared Statements có tham số hóa.
  - **Cross-Site Scripting (XSS):**
    - Nội dung văn bản thông thường được mã hóa qua hàm `e()` (`htmlspecialchars`).
    - Nội dung phong phú (Rich Text) từ trình soạn thảo được thanh lọc nghiêm ngặt qua thư viện **HTML Purifier**, chỉ cho phép danh sách thẻ trắng an toàn (p, b, i, u, h2, h3, ul, ol, li, blockquote, pre, code, a), loại bỏ triệt để `<script>`, `<iframe>`, mã nhúng độc hại và các thuộc tính sự kiện JavaScript.
  - **CSRF Protection:** Xác thực mã token CSRF đối với toàn bộ các phương thức `POST`.
- **Hệ thống giới hạn tần suất (Rate Limiting):**
  - Đăng nhập: Tối đa 5 lần sai / tài khoản / 15 phút; tối đa 25 lần sai / IP / 15 phút.
  - Đăng ký: Tối đa 12 lượt đăng ký / IP / giờ.
  - Bình luận: Giới hạn tối thiểu 20 giây giữa hai lần gửi bình luận liên tiếp của cùng một tài khoản.

---

## 10. Cấu trúc mã nguồn và danh mục tài liệu

```text
news-blog-cms/
├── index.php                      # Front Controller tiếp nhận mọi yêu cầu
├── .htaccess                      # Cấu hình bảo vệ thư mục và định tuyến Apache
├── app/
│   ├── bootstrap.php              # Khởi tạo phiên, hằng số, nạp cấu hình và helpers
│   ├── helpers.php                # Các hàm tiện ích toàn cục (escape, csrf, url, flash)
│   ├── Core/
│   │   ├── Database.php           # Quản lý kết nối PDO Singleton
│   │   ├── Auth.php               # Đăng nhập, phân quyền, kiểm tra phiên
│   │   ├── Security.php           # Xử lý CSRF, HTML Purifier, kiểm duyệt tải ảnh
│   │   └── View.php               # Trình kết xuất giao diện Views và layouts
│   ├── Controllers/
│   │   ├── AuthController.php     # Đăng ký, đăng nhập, đổi mật khẩu, hồ sơ
│   │   ├── PublicController.php   # Trang chủ, tìm kiếm, đọc bài, đếm lượt xem
│   │   ├── PostController.php     # Tác giả tạo bài, sửa bài, lưu nháp, gửi duyệt
│   │   ├── AdminController.php    # Quản trị bài viết, chuyên mục, thẻ, tài khoản
│   │   └── CommentController.php  # Bình luận 2 cấp, kiểm duyệt bình luận
│   ├── Models/
│   │   ├── User.php               # Quản lý tài khoản, mật khẩu, phân quyền
│   │   ├── Post.php               # Quản lý bài viết, trạng thái, version, slug
│   │   ├── Taxonomy.php           # Quản lý chuyên mục và thẻ
│   │   └── Comment.php            # Quản lý cấu trúc cây bình luận và duyệt
│   └── Views/
│       ├── layout.php             # Giao diện khung chính (Header, Footer, Dark Mode)
│       ├── error.php              # Giao diện thông báo lỗi (403, 404, 500)
│       ├── auth/                  # Màn hình đăng nhập, đăng ký, hồ sơ cá nhân
│       ├── public/                # Màn hình trang chủ, danh sách bài, chi tiết bài
│       ├── posts/                 # Màn hình danh sách bài viết tác giả, form soạn thảo
│       ├── admin/                 # Màn hình dashboard, duyệt bài, taxonomy, người dùng
│       └── partials/              # Các thành phần tái sử dụng (card, pagination, empty)
├── assets/
│   ├── css/app.css                # Toàn bộ CSS Design System, Dark Mode, Responsive
│   ├── js/app.js                  # Xử lý Dark Mode, Double-Submit Guard, Pell Editor
│   └── vendor/                    # Thư viện giao diện nhúng sẵn nội bộ
├── config/
│   ├── example.php                # Cấu hình mẫu
│   ├── routes.php                 # Bản đồ định tuyến URL
│   ├── local.php                  # Cấu hình database demo local (không đưa lên Git)
│   └── test.php                   # Cấu hình database test cô lập (không đưa lên Git)
├── database/
│   ├── schema.sql                 # Cấu trúc 8 bảng dữ liệu InnoDB utf8mb4
│   └── news_blog_cms.sql          # Bản xuất dữ liệu mẫu hoàn chỉnh
├── docs/
│   ├── ARCHITECTURE.md            # Kiến trúc chi tiết, phân công nhóm, thiết kế bảng
│   ├── FEATURES.md                # Danh sách chức năng và giới hạn phạm vi
│   ├── TEST-RESULTS.md            # Biên bản kết quả kiểm thử nghiệm thu 89 test cases
│   ├── MANUAL-TESTS.md            # Hướng dẫn quy trình kiểm thử thủ công qua UI
│   ├── ENVIRONMENT.md             # Ghi chú môi trường, lưu ý MySQL và XAMPP
│   ├── GEMINI-REVIEW.md           # Báo cáo nghiệm thu độc lập và bàn giao kỹ thuật
│   ├── UX-UI-VERIFICATION.md      # Kết quả kiểm chứng tự động giao diện và E2E
│   └── diagrams/                  # Sơ đồ thiết kế (ERD, Use Case, Sequence Diagrams)
├── e2e/                           # Bộ kiểm thử tự động hóa Playwright E2E POM
│   ├── playwright.config.ts       # Cấu hình Playwright test runner
│   ├── pages/                     # Các Page Object Models (HomePage, AdminPage...)
│   └── tests/                     # Kịch bản kiểm thử Desktop, Mobile và A11y
├── scripts/
│   ├── install.php                # Script cài đặt cơ sở dữ liệu và sinh cấu hình
│   ├── check.php                  # Script chẩn đoán kết nối và kiểm tra môi trường
│   ├── router.php                 # Router xử lý file tĩnh cho PHP Built-in Server
│   └── package.py                 # Script đóng gói tệp nộp ZIP tiêu chuẩn
├── storage/                       # Thư mục lưu trữ nội bộ (logs, sessions)
├── tests/                         # Bộ kiểm thử PHP (acceptance, contract, states)
├── uploads/                       # Thư mục chứa ảnh tải lên của bài viết
└── vendor/htmlpurifier/           # Thư viện lọc HTML độc hại đầy đủ mã nguồn
```

---

## 11. Quản lý phiên bản Git và quy trình đóng gói (Packaging)

Tất cả các tệp nhạy cảm (`config/local.php`, `config/test.php`), dữ liệu phiên (`storage/sessions/`), nhật ký (`storage/app.log`), ảnh upload thử nghiệm, mã nguồn phụ thuộc (`node_modules/`), browser binaries (`.browsers/`) và báo cáo kết quả kiểm thử đều được cấu hình loại trừ qua `.gitignore`.

### Tạo gói ZIP nộp bài tiêu chuẩn

Thực thi script đóng gói bằng Python (đã có sẵn trong Windows):

```powershell
Set-Location 'C:\Users\Nguyen\Documents\news-blog-cms-dev'
python scripts/package.py --output 'C:\Users\Nguyen\Documents\news-blog-cms-submission-final.zip'
```

Script sẽ tự động gom toàn bộ mã nguồn hợp lệ vào thư mục gốc `news-blog-cms/` trong tệp ZIP, đồng thời từ chối ghi đè nếu tệp đích đã tồn tại.

Sau khi tạo gói, kiểm tra tính toàn vẹn và mã băm SHA-256 của tệp:

```powershell
Get-FileHash 'C:\Users\Nguyen\Documents\news-blog-cms-submission-final.zip' -Algorithm SHA256
```

---

## 12. Cẩm nang xử lý sự cố thường gặp (Troubleshooting)

- **Mã lỗi 404 Not Found:**
  - Kiểm tra xem thư mục dự án trong `htdocs` có bị lồng hai lần không (ví dụ: `htdocs/news-blog-cms/news-blog-cms`).
  - Kiểm tra cài đặt `AllowOverride All` trong tệp cấu hình `httpd.conf` của Apache để tệp `.htaccess` có hiệu lực.
- **Mất định dạng CSS hoặc không tải được ảnh:**
  - Xác minh giá trị `'base_path' => '/news-blog-cms'` trong file `config/local.php`.
  - Mở trực tiếp link `http://localhost/news-blog-cms/assets/css/app.css` để kiểm tra quyền đọc file của Apache.
- **Lỗi 503 Service Unavailable (Chưa cấu hình):**
  - Tạo tệp `config/local.php` bằng cách sao chép từ `config/example.php` và điền thông tin kết nối database thực tế.
- **Lỗi 500 Internal Server Error:**
  - Kiểm tra tệp nhật ký `storage/app.log` để biết chi tiết nguyên nhân. Không bật `display_errors` ra trình duyệt trên môi trường thực tế.
- **Lỗi không kết nối được Database (PDOException):**
  - Mở XAMPP Control Panel và đảm bảo dịch vụ MySQL đang chạy (màu xanh lá).
  - Kiểm tra lại cổng kết nối (mặc định là `3306`), tên database, tên người dùng (`root`) và mật khẩu.
- **Lỗi 419 Page Expired (CSRF Token Mismatch):**
  - Tải lại trang (F5) để nhận mã token mới.
  - Kiểm tra quyền ghi của PHP đối với thư mục lưu trữ phiên `storage/`.
- **Lỗi 409 Conflict:**
  - Bài viết vừa được cập nhật bởi một người dùng khác hoặc trong một tab trình duyệt khác. Hãy tải lại trang để nạp phiên bản mới nhất trước khi chỉnh sửa.
- **Lỗi 429 Too Many Requests:**
  - Hệ thống đã kích hoạt cơ chế chống spam (thử đăng nhập sai quá 5 lần hoặc gửi bình luận liên tiếp dưới 20 giây). Hãy chờ hết thời gian khóa và thử lại.
- **Lỗi tải ảnh đại diện thất bại:**
  - Đảm bảo tệp tải lên là định dạng ảnh hợp lệ (JPG, PNG, WebP) và dung lượng không vượt quá **2 MB**.
  - Kiểm tra quyền ghi (Write Permission) của thư mục `uploads/`.
- **Lỗi hiển thị chữ / Vỡ dấu tiếng Việt (ví dụ `trồ `ng`, `bằ `ng`):**
  - Hiện tượng dấu thanh bị tách rời thành ký tự riêng biệt thường xảy ra khi trình duyệt còn lưu cache tệp CSS cũ.
  - Nhấn tổ hợp phím **`Ctrl + F5`** (hoặc `Shift + F5`) trên trình duyệt để xóa cache CSS. Mã nguồn hiện tại đã được cấu hình font **Lora** và chuẩn hóa `letter-spacing` giúp hiển thị tiếng Việt hoàn hảo 100%.
- **Sự cố bảng dữ liệu MariaDB / Aria Engine:**
  - Tham khảo chi tiết tại `docs/ENVIRONMENT.md`. Tuyệt đối không xóa thư mục `mysql/data` hay can thiệp vào các bảng hệ thống khi chưa có bản sao lưu dữ liệu an toàn.
