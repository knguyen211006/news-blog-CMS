# Góc Nhìn — Website quản lý tin tức và blog đa tác giả

Đồ án PHP thuần MVC + MySQL/MariaDB, giao diện tiếng Việt, chạy trong thư mục con **/news-blog-cms/**. Mọi dữ liệu nghiệp vụ nằm trong database. Không cần Composer, npm, CDN, API trả phí hoặc tài khoản dịch vụ ngoài để chạy.

Nhóm: **Nguyễn Khôi Nguyên** (trưởng nhóm), Nguyễn Trí Phong, Nguyễn Lê Duy Ân, Lê Tuấn Quy, Nguyễn Tấn Tài.

## 1. Chạy trên máy hiện tại

XAMPP thực tế: **C:\xampp**. Apache và MariaDB đã chạy. Database **news_blog_cms** đã được tạo và nạp demo. Cấu hình thực đã được tạo ở bản mã nguồn làm việc, không đưa vào Git hoặc ZIP nộp bài.

Do quyền công cụ không ghi được vào htdocs, bản làm việc ở:
`C:\Users\Nguyen\AppData\Local\Temp\news-blog-cms-work\news-blog-cms`

Bản ZIP **news-blog-cms-local.zip** dành riêng cho máy hiện tại có cấu hình local đã xác minh. Bản **news-blog-cms-submission.zip** dùng để nộp hoặc cài trên máy khác, không chứa cấu hình thật, session, log hay dữ liệu kiểm thử.

Để đặt bản local vào đúng vị trí:

1. Trong XAMPP Control Panel, kiểm tra Apache và MySQL có nền xanh, nút bên phải là **Stop**. Nếu chưa chạy, bấm **Start** bên cạnh từng dịch vụ.
2. Mở File Explorer tới thư mục chứa `news-blog-cms-local.zip`.
3. Giải nén ZIP; bên trong có đúng một thư mục `news-blog-cms`.
4. Chép thư mục đó vào `C:\xampp\htdocs`, để file index.php nằm tại **C:\xampp\htdocs\news-blog-cms\index.php**. Nếu thư mục đích đã có dữ liệu, không ghi đè; sao lưu và kiểm tra trước.
5. Mở trình duyệt tới **http://localhost/news-blog-cms/**.
6. Không import lại SQL nếu database hiện tại đã có dữ liệu.

Có thể dùng Windows PowerShell (Start → gõ **Windows PowerShell** → mở) thay cho Explorer:

~~~powershell
if (Test-Path -LiteralPath 'C:\xampp\htdocs\news-blog-cms') {
    throw 'Thu muc dich da ton tai. Khong ghi de.'
}
Expand-Archive -LiteralPath 'C:\Users\Nguyen\AppData\Local\Temp\news-blog-cms-work\news-blog-cms-local.zip' -DestinationPath 'C:\xampp\htdocs'
~~~

Kết quả đúng: trang **góc nhìn.**, các bài demo và ảnh minh họa xuất hiện. Thư mục tạm không phải nơi lưu đồ án lâu dài; hãy giữ bản ZIP và bản trong htdocs.

## 2. Tài khoản demo

Tất cả dùng mật khẩu **Demo@12345**, chỉ dùng cho môi trường local.

| Vai trò | Email | Họ tên |
|---|---|---|
| ADMIN | admin@newsblog.local | Nguyễn Khôi Nguyên |
| AUTHOR | author1@newsblog.local | Nguyễn Lê Duy Ân |
| AUTHOR | author2@newsblog.local | Nguyễn Trí Phong |
| USER | reader1@newsblog.local | Lê Tuấn Quy |
| USER | reader2@newsblog.local | Nguyễn Tấn Tài |

Đây là tài khoản website, không phải tài khoản database. Người đăng ký mới luôn là USER. Admin vào **Tài khoản** để cấp AUTHOR.

## 3. Cài trên một bản XAMPP mới

### A. Kiểm tra môi trường

Cài XAMPP cho Windows có PHP >=8.1 (đã kiểm tra PHP 8.2.12), Apache 2.4, MySQL 8 hoặc MariaDB 10.4+. Mục tiêu thực tế đã chạy là MariaDB 10.4.32; chưa kiểm thử riêng MySQL 8.

Trong Control Panel, Start Apache và MySQL. Mở http://localhost/ và http://localhost/phpmyadmin/. Nếu cổng 80 hoặc 3306 bận, xem log trong XAMPP và xác định dịch vụ đang dùng cổng; không xóa dữ liệu hoặc thay cấu hình dự án khác.

Mở Windows PowerShell và chạy (sửa C:\xampp nếu cài ở nơi khác):

~~~powershell
& 'C:\xampp\php\php.exe' -v
& 'C:\xampp\php\php.exe' -m
& 'C:\xampp\mysql\bin\mysql.exe' --version
~~~

Cần PDO, pdo_mysql, mbstring, fileinfo, dom, iconv, session, openssl. XAMPP hiện tại đã có đủ. Nếu thiếu extension, sao lưu php.ini rồi bật đúng dòng extension tương ứng qua nút **Config → PHP (php.ini)** bên cạnh Apache, lưu và restart Apache. GD không bắt buộc.

Ứng dụng dùng .htaccess để chặn file nội bộ. Bản XAMPP chuẩn cần `LoadModule rewrite_module modules/mod_rewrite.so` và `AllowOverride All` trong Directory của htdocs; máy hiện tại đã có, không cần chỉnh.

### B. Chép mã nguồn

Giải nén **news-blog-cms-submission.zip** vào htdocs. Đường dẫn phải là:
`C:\xampp\htdocs\news-blog-cms\index.php`

Không lồng thành `news-blog-cms\news-blog-cms\index.php`.

### C. Import database bằng phpMyAdmin

1. Mở http://localhost/phpmyadmin/.
2. Đăng nhập bằng tài khoản database thực tế của máy nếu được hỏi. Không mặc định mật khẩu trống.
3. Kiểm tra danh sách bên trái: nếu đã có **news_blog_cms** chứa dữ liệu, dừng import và sao lưu trước. SQL không có DROP nhưng không dùng để nạp đè nhiều lần.
4. Tại trang chính phpMyAdmin, chọn tab **Import / Nhập**.
5. **Choose File / Chọn tệp** → chọn `C:\xampp\htdocs\news-blog-cms\database\news_blog_cms.sql`.
6. Định dạng SQL, bộ ký tự UTF-8. Bấm **Go / Thực hiện**.
7. SQL tự tạo database **news_blog_cms** bằng utf8mb4 rồi tạo 8 bảng. Không cần chọn database của dự án khác.
8. Kết quả đúng: 5 users, 4 categories, 6 tags, 12 posts, 6 comments; có thêm post_tags, post_history và rate_limits.

File SQL lưu mật khẩu dưới dạng bcrypt hash, không lưu mật khẩu dạng văn bản thuần trong bảng.

### D. Tạo cấu hình riêng cho máy

1. Mở thư mục `config`.
2. Sao chép `example.php` thành **local.php**.
3. Mở local.php bằng VS Code hoặc Notepad.
4. Điền db_host, db_port, db_user, db_password theo tài khoản thực của MySQL; db_name là news_blog_cms.
5. Giữ `base_path => '/news-blog-cms'` và session_name mặc định.
6. Lưu file UTF-8. Không dán cấu hình thật lên Git hoặc vào báo cáo.

Nếu đã xác nhận bằng phpMyAdmin rằng máy dùng root với mật khẩu trống, có thể đặt db_user là root và db_password là chuỗi rỗng. Đây là kết luận sau kiểm tra, không phải yêu cầu mặc định. Tốt hơn là tạo tài khoản riêng và cấp SELECT, INSERT, UPDATE, DELETE trên news_blog_cms.*; không cấp quyền toàn máy.

Cấu hình này phải tồn tại ở `config/local.php`, không tạo ở thư mục public khác.

### E. Kiểm tra và chạy

~~~powershell
Set-Location 'C:\xampp\htdocs\news-blog-cms'
& 'C:\xampp\php\php.exe' scripts/check.php
~~~

Kết quả đúng: các extension OK, phiên bản database, số bản ghi demo, `HTML Purifier: <p>safe</p>`. Sau đó mở **http://localhost/news-blog-cms/**.

PHP cần quyền ghi trong `storage` và `uploads`. Session, log và cache nằm trong storage. Không cần bật quyền ghi cả XAMPP.

### F. Cách cài CLI thay cho import thủ công

Chỉ chọn một cách cài. Nếu chưa import database và phpMyAdmin dùng auth_type=config, chạy:

~~~powershell
Set-Location 'C:\xampp\htdocs\news-blog-cms'
& 'C:\xampp\php\php.exe' scripts/install.php --xampp=C:\xampp
~~~

Script đọc cấu hình phpMyAdmin thực tế, kết nối kiểm tra, từ chối database có bảng, import SQL và thử tạo tài khoản database riêng với mật khẩu ngẫu nhiên. Thông tin được ghi vào local.php bị Git bỏ qua; không in mật khẩu.

Nếu đã import SQL bằng phpMyAdmin, có thể dùng `--configure-only` để chỉ tạo local.php từ kết nối đã xác minh:
~~~powershell
& 'C:\xampp\php\php.exe' scripts/install.php --xampp=C:\xampp --configure-only
~~~

Script không ghi đè local.php có sẵn. Nếu phpMyAdmin dùng cookie authentication, làm theo bước D; không sửa phpMyAdmin chỉ để chạy installer. Người có kinh nghiệm có thể cung cấp CMS_DB_HOST, CMS_DB_PORT, CMS_DB_ADMIN_USER, CMS_DB_ADMIN_PASSWORD thay cho --xampp.

## 4. URL và thao tác demo

| Màn hình | URL |
|---|---|
| Trang chủ | http://localhost/news-blog-cms/ |
| Tìm kiếm / danh sách | http://localhost/news-blog-cms/index.php?r=articles |
| Đăng nhập | http://localhost/news-blog-cms/index.php?r=login |
| Đăng ký | http://localhost/news-blog-cms/index.php?r=register |
| Hồ sơ | http://localhost/news-blog-cms/index.php?r=profile |
| Bài của tôi | http://localhost/news-blog-cms/index.php?r=author/posts |
| Viết bài | http://localhost/news-blog-cms/index.php?r=author/edit |
| Dashboard | http://localhost/news-blog-cms/index.php?r=admin |
| Duyệt bài | http://localhost/news-blog-cms/index.php?r=admin/posts&status=pending |
| Chuyên mục | http://localhost/news-blog-cms/index.php?r=admin/taxonomy&kind=categories |
| Thẻ | http://localhost/news-blog-cms/index.php?r=admin/taxonomy&kind=tags |
| Tài khoản | http://localhost/news-blog-cms/index.php?r=admin/users |
| Bình luận | http://localhost/news-blog-cms/index.php?r=admin/comments |

Luồng demo: AUTHOR viết nháp → xem trước → gửi duyệt → ADMIN trả bài kèm lý do → AUTHOR sửa/gửi lại → ADMIN xuất bản → khách tìm/đọc → USER bình luận → ADMIN duyệt bình luận.

Bài published muốn sửa phải được Admin gỡ về draft. Bài pending bị khóa sửa. Các thao tác quan trọng yêu cầu xác nhận trên giao diện và luôn kiểm tra quyền phía server.

Kiểm tra bảo vệ thư mục: các URL sau phải trả **403**, không trả nội dung file:
`/news-blog-cms/config/local.php`, `/news-blog-cms/database/news_blog_cms.sql`, `/news-blog-cms/storage/app.log`, `/news-blog-cms/app/bootstrap.php`, `/news-blog-cms/.git/config`.

## 5. Kiểm thử

Kết quả thực tế trong **docs/TEST-RESULTS.md**; biên bản môi trường trong **docs/ENVIRONMENT.md**. Bài kiểm thử tự động chỉ được chạy trên **news_blog_cms_test**, tuyệt đối không dùng database demo chính.

Tạo database thử nghiệm trống (script sẽ từ chối nếu đã có bảng):
~~~powershell
& 'C:\xampp\php\php.exe' scripts/install.php --xampp=C:\xampp --test
~~~

Mở một cửa sổ PowerShell riêng, vào thư mục dự án rồi chạy:
~~~powershell
$env:CMS_CONFIG = (Resolve-Path '.\config\test.php').Path
& 'C:\xampp\php\php.exe' -S 127.0.0.1:8089 scripts/router.php
~~~

Giữ cửa sổ này mở. Mở PowerShell thứ hai:
~~~powershell
Set-Location 'C:\xampp\htdocs\news-blog-cms'
& 'C:\xampp\php\php.exe' tests/acceptance.php
~~~

Kết quả mong đợi: **88 passed, 0 failed**. Bộ kiểm thử tạo thêm dữ liệu trong database test, giữ lại để điều tra; có thể chạy lại. Không kiểm thử trên dữ liệu sản xuất. Ctrl+C trong cửa sổ server để dừng.

Để kiểm tra cú pháp:
~~~powershell
Get-ChildItem app,config,scripts,tests -Recurse -Filter *.php | ForEach-Object {
    & 'C:\xampp\php\php.exe' -l $_.FullName
}
& 'C:\xampp\php\php.exe' -l index.php
~~~

Kiểm tra bằng trình duyệt và bố cục mobile theo **docs/MANUAL-TESTS.md**. SQL đã được import vào database trống trên XAMPP hiện tại; chưa coi đây là kiểm chứng trên một máy XAMPP mới hoàn toàn.

## 6. Cấu trúc và tài liệu báo cáo

~~~text
news-blog-cms/
├── index.php                    Front controller
├── .htaccess                    Chặn nội bộ và directory listing
├── app/
│   ├── Core/                    PDO, Auth, Security, View, HTTP errors
│   ├── Controllers/             Auth, Public, Post, Admin, Comment
│   ├── Models/                  User, Post, Taxonomy, Comment
│   ├── Views/                   Layout, auth, public, posts, admin
│   ├── bootstrap.php
│   └── helpers.php
├── assets/                      CSS, JS, SVG nội bộ, Bootstrap, Pell
├── config/                      example.php, routes.php, local.php riêng
├── database/                    schema.sql và news_blog_cms.sql
├── docs/                        Kiến trúc, phân công, kiểm thử, sơ đồ
├── scripts/                     Cài đặt, kiểm tra, tạo SQL, đóng gói
├── storage/                     Session, cache, log; không public/Git
├── tests/                       Bộ nghiệm thu HTTP và kết quả JSON
├── uploads/                     Ảnh người dùng, .htaccess cấm thực thi
└── vendor/htmlpurifier/          Thư viện lọc HTML đầy đủ mã nguồn
~~~

- **docs/ARCHITECTURE.md**: kiến trúc, phân công 5 thành viên, schema và quy tắc.
- **docs/FEATURES.md**: chức năng, phạm vi và hạn chế.
- **docs/TEST-RESULTS.md**: các ca và kết quả thực tế.
- **docs/diagrams/**: Use Case PlantUML, ERD và Sequence Mermaid, sửa được bằng văn bản.
- **THIRD-PARTY-NOTICES.md**: phiên bản, nguồn và giấy phép thư viện.
- **docs/FILE-LIST.txt**: danh sách file mã nguồn bàn giao.

## 7. Git và đóng gói nộp đồ án

Máy hiện tại có Git. Repository local đã được khởi tạo; chưa tạo remote hoặc đẩy lên GitHub. Không cần Git để chạy website.

Nếu dùng bản ZIP trên máy khác, mở PowerShell trong thư mục dự án:
~~~powershell
git init -b main
git status
git add .
git status
~~~

Kiểm tra local.php, test.php, session/log và ảnh upload thực không được staged. Nếu Git chưa biết danh tính, đặt tên/email của **chính bạn** bằng git config, rồi `git commit -m "Initial news blog CMS"`. Không đặt danh tính giả. Khi thêm GitHub remote của nhóm, chỉ đẩy mã đã kiểm tra không có bí mật.

Đóng gói: trong PowerShell chạy script **scripts/package.ps1** (nếu chính sách PowerShell của máy cho phép) hoặc dùng chức năng ZIP của Explorer và tự loại các file riêng:

~~~powershell
.\scripts\package.ps1 -OutputPath '..\news-blog-cms-submission-new.zip'
~~~

ZIP nộp có mã nguồn, thư viện, database và tài liệu; không có config/local.php, config/test.php, .git, session/log, ảnh upload và JSON runtime. Không nộp **news-blog-cms-local.zip** vì bản đó chứa cấu hình máy hiện tại.

Máy hiện tại chặn chạy file PowerShell .ps1. Không cần đổi chính sách hệ thống: Python 3 đã có sẵn và có thể đóng gói bằng thư viện chuẩn:
~~~powershell
python scripts/package.py --output ..\news-blog-cms-submission-new.zip
~~~
Nếu máy khác không có Python, dùng ZIP trong Explorer như hướng dẫn trên; Python không cần cho website.

## 8. Khi có lỗi

- 404 trang chủ: kiểm tra vị trí index.php và tên thư mục; tránh giải nén lồng hai lần.
- CSS/ảnh lỗi: base_path phải là /news-blog-cms; thử trực tiếp /news-blog-cms/assets/css/app.css.
- 503 chưa cấu hình: tạo config/local.php từ example.php.
- 500: xem storage/app.log; không bật display_errors để lộ SQL/mật khẩu.
- Không kết nối DB: bật MySQL, xác minh host/cổng/user/password bằng phpMyAdmin; kiểm tra db_name.
- CSRF 419: tải lại form, kiểm tra cookie trình duyệt và quyền ghi storage/sessions.
- 409: bài/bình luận vừa thay đổi ở một phiên khác; tải lại trước khi thao tác.
- 429: chờ cửa sổ giới hạn 20 giây cho bình luận hoặc 15 phút cho login.
- Không upload được: kiểm tra ảnh thật <=2 MB và quyền ghi uploads; php.ini upload_max_filesize tối thiểu 2M, post_max_size lớn hơn 2M.
- Lỗi Aria/mysql.db trên máy hiện tại: đọc docs/ENVIRONMENT.md. Không xóa thư mục mysql/data hoặc chạy công cụ sửa dữ liệu khi chưa sao lưu.

Để chạy lại mỗi ngày: mở XAMPP Control Panel → Start Apache và MySQL → mở URL trang chủ. Không import SQL mỗi lần chạy.
