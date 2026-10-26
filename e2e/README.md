# Playwright E2E — PHP MVC

Kiểm thử mã nguồn workspace qua máy chủ PHP tự khởi động, chỉ dùng
`config/test.php` / `news_blog_cms_test`. Máy chủ dừng khi kết thúc test.
Không cần chép mã sang XAMPP `htdocs`.

## Chạy trên Windows

```powershell
Set-Location '<thư-mục-dự-án>\e2e'
npm.cmd ci
# Chỉ sao chép nếu chưa có .env; giữ thông tin riêng khi đã cấu hình.
if (!(Test-Path .env)) { Copy-Item .env.example .env }
$env:PLAYWRIGHT_BROWSERS_PATH = Join-Path (Get-Location) '.browsers'
npx.cmd playwright install chromium
npm.cmd run typecheck
npm.cmd test
npm.cmd run report
```

Điền `BASE_URL` (kèm `/news-blog-cms/` và dấu `/` cuối), `PHP_BINARY`,
`ADMIN_EMAIL`, `ADMIN_PASSWORD`, tài khoản cố ý sai và dữ liệu lọc vào `.env`.
Không có fallback URL/tài khoản trong code. Biến môi trường của CI ưu tiên hơn `.env`.
Mẫu dùng dữ liệu demo trong database test; các chuyên mục/thẻ phải tồn tại và có
bài công khai. Port mẫu là 8092; đổi port trong `.env` nếu đang được sử dụng.

## Phạm vi

- Desktop Chromium 1280 × 800, Mobile Chromium 375 × 812; thêm kiểm tra 768px.
- HomePage: trang chủ, danh sách, tìm kiếm có kết quả/rỗng, reset, chuyên mục/thẻ,
  line-clamp và ảnh 16:9.
- PostPage: nội dung, metadata, ảnh chi tiết, liên kết đăng nhập để bình luận.
- LoginPage: lỗi PHP 422, thành công và URL thật, required-field validation,
  focus bàn phím; trang đăng ký được kiểm tra accessibility.
- AdminPage: lọc trạng thái, tạo nháp qua Pell, kiểm tra nội dung đã lưu,
  xóa mềm đúng bản nháp vừa tạo, hủy xác nhận; dashboard, taxonomy, tài khoản,
  bình luận và hồ sơ được đo responsive, tương phản và vùng chạm 44px.
- axe chạy các luật WCAG A/AA, có kiểm tra `color-contrast`; không tắt luật để
  né lỗi. Kiểm tra tự động không thay thế đánh giá khả năng truy cập thủ công.

Setup đăng nhập một lần và lưu cookie PHP vào `.auth/admin.json`; test quản trị
tái sử dụng `storageState`. Test chạy tuần tự để tránh cạnh tranh dữ liệu phiên.
Trạng thái khóa submit được ghi ngay sau listener của ứng dụng, trong khi POST
thật được giữ ở tầng mạng, rồi tiếp tục đến PHP. Cách này kiểm tra trạng thái
trang cũ trước khi navigation SSR thay DOM. Không dùng thời gian chờ cố định,
không giả lập phản hồi thành công. Bản nháp E2E được xóa mềm theo hành vi CMS;
lịch sử biên tập và bản ghi đã xóa vẫn còn trong database test.

Trace: `on-first-retry`; screenshot: `only-on-failure`. Muốn trace khi tái hiện lỗi:

```powershell
npm.cmd test -- --retries=1
```

`.env`, cookie đăng nhập, browser binaries, node_modules và báo cáo runtime đã
được loại khỏi Git và gói ZIP của `scripts/package.py`.

Tài liệu tham chiếu: [Playwright authentication](https://playwright.dev/docs/auth),
[webServer](https://playwright.dev/docs/test-webserver),
[axe accessibility testing](https://playwright.dev/docs/accessibility-testing).

## Kiểm tra PHP bổ sung (từ thư mục gốc dự án)

```powershell
& 'C:\xampp\php\php.exe' tests/view-contract.php
& 'C:\xampp\php\php.exe' tests/view-states.php
```

`view-contract.php` đối chiếu biểu thức PHP và thuộc tính `href/action/name` với
Git HEAD, bỏ qua thay đổi khoảng trắng. Chạy trước khi commit nâng cấp UI;
sau commit, HEAD là baseline mới. `view-states.php` render dữ liệu rỗng qua Views
thật và kiểm tra icon/nội dung/CTA; chỉ đọc database test, không xóa dữ liệu để
tạo trạng thái rỗng.
