# Kiểm tra URL và tài nguyên sau bàn giao

Thời điểm: 2026-10-23T17:12:35+02:00

Đích kiểm tra: máy chủ PHP cục bộ tạm, dưới đường dẫn `/news-blog-cms/`.

32 PASS / 0 FAIL. Kiểm thử HTTP này không xác nhận bố cục hoặc tương tác JavaScript bằng trình duyệt.

| Đường dẫn | Mong đợi | HTTP thực tế | Kết quả |
|---|---|---|---|
| / | 200 | 200 | PASS |
| index.php?r=articles | 200 | 200 | PASS |
| index.php?r=articles&category=cong-nghe | 200 | 200 | PASS |
| index.php?r=articles&tag=php | 200 | 200 | PASS |
| index.php?r=articles&page=2 | 200 | 200 | PASS |
| index.php?r=article&slug=xay-dung-thoi-quen-doc | 200 | 200 | PASS |
| index.php?r=article&slug=nhat-ky-hoc-sql | 404 | 404 | PASS |
| index.php?r=login | 200 | 200 | PASS |
| index.php?r=register | 200 | 200 | PASS |
| index.php?r=admin | 401 | 401 | PASS |
| assets/css/app.css | 200 | 200 | PASS |
| assets/js/app.js | 200 | 200 | PASS |
| assets/vendor/bootstrap/bootstrap.min.css | 200 | 200 | PASS |
| assets/vendor/pell/pell.min.js | 200 | 200 | PASS |
| assets/vendor/pell/pell.min.css | 200 | 200 | PASS |
| assets/images/cover-1.svg | 200 | 200 | PASS |
| assets/images/cover-2.svg | 200 | 200 | PASS |
| assets/images/cover-3.svg | 200 | 200 | PASS |
| assets/images/cover-4.svg | 200 | 200 | PASS |
| config/local.php | 403 | 403 | PASS |
| config/example.php | 403 | 403 | PASS |
| config/routes.php | 403 | 403 | PASS |
| database/news_blog_cms.sql | 403 | 403 | PASS |
| storage/app.log | 403 | 403 | PASS |
| app/bootstrap.php | 403 | 403 | PASS |
| vendor/htmlpurifier/library/HTMLPurifier.php | 403 | 403 | PASS |
| scripts/install.php | 403 | 403 | PASS |
| tests/acceptance.php | 403 | 403 | PASS |
| .git/config | 403 | 403 | PASS |
| .env | 403 | 403 | PASS |
| README.md | 403 | 403 | PASS |
| uploads/fake.php | 403 | 403 | PASS |
