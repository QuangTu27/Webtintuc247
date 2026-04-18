# 📰 WebTinTuc247

> Hệ thống đọc tin tức trực tuyến, xây dựng bằng PHP thuần theo mô hình MVC.

---

## 📑 Mục lục

1. [Giới thiệu dự án](#1--giới-thiệu-dự-án)
2. [Công nghệ sử dụng](#2--công-nghệ-sử-dụng)
3. [Cấu trúc thư mục](#3--cấu-trúc-thư-mục)
4. [Kiến trúc & Luồng hoạt động](#4--kiến-trúc--luồng-hoạt-động)
5. [Phân quyền hệ thống](#5--phân-quyền-hệ-thống)
6. [Các Module chính](#6--các-module-chính)
7. [Hướng dẫn cài đặt](#7--hướng-dẫn-cài-đặt)
8. [Hướng dẫn làm việc nhóm](#8--hướng-dẫn-làm-việc-nhóm)
9. [Vấn đề hiện tại](#9--vấn-đề-hiện-tại)
10. [Đề xuất cải tiến](#10--đề-xuất-cải-tiến)

---

## 1. 🚀 Giới thiệu dự án

**WebTinTuc247** là hệ thống đọc và quản lý tin tức trực tuyến, bao gồm hai phân hệ độc lập:

- **Trang người dùng (Site):** Đọc tin, tìm kiếm, bình luận, lưu bài viết, xem lịch sử, quản lý hồ sơ cá nhân.
- **Trang quản trị (Admin):** Quản lý bài viết, danh mục, người dùng, quảng cáo, bình luận; thống kê dashboard.

### 👥 Đối tượng sử dụng

| Vai trò | Mô tả |
|---|---|
| `admin` | Quản trị viên toàn quyền: quản lý user, duyệt bài, cấu hình hệ thống |
| `editor` / `bien_tap` / `tongbien_tap` | Biên tập viên: đăng bài, duyệt bài trong phạm vi danh mục được phân công |
| `user` | Người dùng thông thường: đọc tin, bình luận, lưu bài |

---

## 2. 🛠 Công nghệ sử dụng

| Thành phần | Công nghệ |
|---|---|
| **Backend** | PHP 8.x (MVC tự xây dựng, không dùng framework) |
| **Frontend** | HTML5, CSS3, JavaScript thuần (ES6+) |
| **Giao tiếp** | Fetch API → JSON (toàn bộ data được load động, không reload trang) |
| **Database** | MySQL (qua PDO, prepared statements) |
| **Web server** | Apache + XAMPP (`.htaccess` mod_rewrite) |
| **Icon** | Font Awesome 5 (CDN) |
| **CSS** | Vanilla CSS, tổ chức thành nhiều file theo module |

> **Không dùng thư viện ngoài:** Không Composer, không npm, không jQuery, không framework JS.

---

## 3. 📂 Cấu trúc thư mục

```
webtintuc247/
│
├── index.php                  # Entry point duy nhất của toàn bộ ứng dụng
├── .htaccess                  # Redirect mọi request về index.php (mod_rewrite)
│
├── config/
│   └── config.php             # Hằng số: DB_HOST, DB_NAME, BASE_URL, APPROOT...
│
├── app/                       # Toàn bộ logic MVC
│   ├── core/
│   │   ├── App.php            # Router: phân tích URL → gọi đúng Controller/Method
│   │   ├── Controller.php     # Base class: model(), view(), getClientViewData()
│   │   └── Database.php       # Singleton PDO wrapper
│   │
│   ├── controllers/
│   │   ├── admin/             # Controllers cho trang quản trị (namespace: admin)
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── NewsController.php
│   │   │   ├── CategoriesController.php
│   │   │   ├── UsersController.php
│   │   │   ├── CommentsController.php
│   │   │   ├── AdsController.php
│   │   │   └── ProfileController.php
│   │   └── site/              # Controllers cho trang người dùng (namespace: site)
│   │       ├── AuthController.php
│   │       ├── HomeController.php
│   │       ├── NewsController.php
│   │       ├── CategoriesController.php
│   │       ├── SearchController.php
│   │       └── UserController.php
│   │
│   ├── models/                # Tầng dữ liệu (truy vấn DB)
│   │   ├── AuthModel.php
│   │   ├── DashboardModel.php
│   │   ├── NewsModel.php          # CRUD bài viết (cho admin)
│   │   ├── NewsDetailModel.php    # Chi tiết bài viết, like, comment (cho site)
│   │   ├── SiteModel.php          # Dữ liệu hiển thị trang site (home, danh mục, search)
│   │   ├── CategoriesModel.php
│   │   ├── UserModel.php
│   │   ├── UserProfileModel.php   # Hồ sơ, bookmark, lịch sử, đổi mật khẩu
│   │   ├── AdminProfileModel.php
│   │   ├── AdsModel.php
│   │   └── CommentModel.php
│   │
│   └── views/
│       ├── admin/             # Giao diện trang quản trị
│       │   ├── layouts/       # header.php, footer.php
│       │   ├── auth/
│       │   ├── dashboard.php
│       │   ├── news/          # list.php, add.php, edit.php
│       │   ├── categories/
│       │   ├── users/
│       │   ├── comments/
│       │   ├── ads/
│       │   └── profile.php
│       └── site/              # Giao diện trang người dùng
│           ├── layouts/       # header.php, footer.php, auth_modal.php
│           ├── home.php
│           ├── news_detail.php
│           ├── categories.php
│           ├── search.php
│           └── users/
│               └── profile.php
│
└── assets/                    # Static files (public)
    ├── css/
    │   ├── admin/             # base, button, form, layout, table, login CSS
    │   └── site/              # header, home, profile, search, auth... CSS
    ├── js/
    │   ├── admin/             # dashboard, news, categories, users, ads, comments JS
    │   └── site/              # home, news_detail, categories, search, profile, auth_modal JS
    └── images/
        ├── news/              # Ảnh thumbnail bài viết (upload)
        └── avatars/           # Ảnh đại diện người dùng (upload)
```

---

## 4. ⚙️ Kiến trúc & Luồng hoạt động

### 4.1 Luồng Request MVC

```
Trình duyệt
    │
    ▼
.htaccess  →  Redirect mọi URL về  →  index.php
                                           │
                                     session_start()
                                     require config.php
                                     autoload classes
                                           │
                                           ▼
                                       App.php (Router)
                                           │
                              Phân tích $_GET['url']
                              (ví dụ: "admin/news/add")
                                           │
                             ┌─────────────┴──────────────┐
                             │                            │
                       url[0] = "admin"           url[0] = lainhe
                       namespace = admin          namespace = site
                             │                            │
                       DashboardController         HomeController
                       (mặc định admin)            (mặc định site)
                             │
                    Tìm Controller file
                    → gọi method tương ứng
                             │
                    Controller → Model → DB
                             │
                    Controller → view() → HTML
                             │
                             ▼
                       Response HTML / JSON
```

### 4.2 Router (`App.php`)

URL pattern: `/{controller}/{method}/{params}`

| URL | Controller | Method | Params |
|---|---|---|---|
| `/` | HomeController | index() | — |
| `/news/5` | NewsController (site) | index() | 5 |
| `/news/detail/5` | NewsController | detail() | 5 |
| `/categories/3/data` | CategoriesController | index() | 3, 'data' |
| `/search/data` | SearchController | data() | — |
| `/admin/news` | NewsController (admin) | index() | — |
| `/admin/news/store` | NewsController (admin) | store() | — |

> **Lưu ý:** Admin namespace được kích hoạt khi `url[0] === 'admin'`, sau đó `url[0]` bị bỏ đi và phần còn lại được xử lý như site.

### 4.3 Controller

- Mỗi Controller extends `Controller` (base class).
- `Controller::model($name)` → `require_once` model file + `new $model()`.
- `Controller::view($path, $data)` → `extract($data)` + `require_once` view file.
- `Controller::getClientViewData()` → trả về `menuItems`, `avatar`, `displayName`, `username` dùng chung cho mọi site view.

### 4.4 Model & Database

- `Database` là **Singleton PDO**. Cấu hình `PDO::FETCH_OBJ` — tất cả kết quả trả về là `stdClass` object.
- Tất cả query dùng **prepared statements** (`?` placeholder) — an toàn SQL Injection.
- Mỗi Model chỉ xử lý 1 domain (SRP): `NewsModel` chỉ CRUD bài viết, `SiteModel` chỉ phục vụ dữ liệu hiển thị site, v.v.

### 4.5 View & Frontend

Views chứa HTML skeleton + tag `<script>` nhỏ khai báo hằng số như `BASE_URL`, `nId` (news ID). Toàn bộ logic JS nằm trong file riêng tại `assets/js/`.

**Pattern chuẩn của site:**

```
View (PHP) → in ra HTML khung rỗng + khai báo BASE_URL
    ↓
JS file fetch() → GET/POST JSON API
    ↓
Controller → trả về JSON
    ↓
JS render() → cập nhật DOM
```

### 4.6 Session & Authentication

Hệ thống tách biệt hoàn toàn session Admin và Client bằng **prefix convention**:

| Prefix | Dùng cho | Các biến |
|---|---|---|
| `admin_*` | Trang quản trị | `admin_logged_in`, `admin_id`, `admin_username`, `admin_hoten`, `admin_role`, `admin_avatar` |
| `client_*` | Trang người dùng | `client_logged_in`, `client_id`, `client_username`, `client_hoten`, `client_role`, `client_avatar` |

**Luồng Login Admin:**
```
POST /admin/auth/loginSubmit
  → AuthModel::findAdminByCredentials()  [role NOT IN ('user')]
  → Set $_SESSION['admin_*']
  → Redirect /admin/dashboard
```

**Luồng Login Client:**
```
POST /auth/login  (Fetch API / JSON)
  → AuthModel::findUserByCredentials()
  → Set $_SESSION['client_*']
  → JSON response → JS redirect
```

**Bảo vệ Admin route:** Mỗi admin controller kiểm tra `$_SESSION['admin_logged_in'] === true` trong `__construct()` → redirect nếu không hợp lệ.

**Bảo vệ Client API:** `UserController::requireLogin()` kiểm tra session client → trả JSON error nếu chưa đăng nhập.

---

## 5. 🔐 Phân quyền hệ thống

### Các role trong hệ thống

| Role | Mô tả | Vị trí lưu |
|---|---|---|
| `admin` | Toàn quyền | `tbl_users.role` |
| `tongbien_tap` | Tổng biên tập | `tbl_users.role` |
| `bien_tap` | Biên tập viên | `tbl_users.role` |
| `editor` | Biên tập (alias) | `tbl_users.role` |
| `user` | Người dùng thường | `tbl_users.role` |

### Phân quyền theo module

| Module | `admin` | `editor`/biên tập | `user` |
|---|---|---|---|
| Dashboard | ✅ | ✅ | ❌ |
| Quản lý bài viết | ✅ Toàn bộ | ✅ Trong danh mục được phân | ❌ |
| Duyệt bài | ✅ | ✅ | ❌ |
| Quản lý User | ✅ | ❌ (chỉ xem profile bản thân) | ❌ |
| Quản lý Danh mục | ✅ | ✅ Được phân công | ❌ |
| Quản lý Quảng cáo | ✅ | ✅ | ❌ |
| Quản lý Bình luận | ✅ | ✅ | ❌ |
| Đọc tin, bình luận | ✅ | ✅ | ✅ |
| Lưu bài, hồ sơ | ✅ | ✅ | ✅ |

### Cách kiểm tra quyền

```php
// Admin controller: kiểm tra trong __construct()
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . URLROOT . 'admin/auth/login');
    exit;
}

// Kiểm tra role bổ sung (ví dụ: chỉ admin mới quản lý user)
if ($role !== 'admin' && $action !== 'profile') {
    // Trả lỗi hoặc redirect
}
```

### ⚡ Điểm mạnh & Hạn chế

**Điểm mạnh:**
- Session isolation tuyệt đối (`admin_*` vs `client_*`) — không thể cross-login.
- Admin route bảo vệ ở mọi controller bằng `__construct()`.

**Hạn chế:**
- Chưa có middleware/guard tập trung — logic kiểm tra quyền phân tán trong từng constructor.
- Role check dùng `in_array()` hard-code — khó mở rộng khi thêm role mới.
- **Mật khẩu lưu dạng plaintext** trong `AuthModel` — chưa dùng `password_hash()`/`password_verify()` nhất quán.

---

## 6. 📦 Các Module chính

### 6.1 Module Home (Trang chủ)

| | |
|---|---|
| **Chức năng** | Hiển thị danh sách tin mới, pagination, tin xem nhiều, quảng cáo banner |
| **Controller** | `site/HomeController.php` |
| **Model** | `SiteModel.php` |
| **View** | `site/home.php` |
| **JS** | `assets/js/site/home.js` |
| **API endpoint** | `GET /home/data?page=N` |

### 6.2 Module News (Tin tức chi tiết)

| | |
|---|---|
| **Chức năng** | Xem nội dung, like, bookmark, bình luận, chia sẻ |
| **Controller** | `site/NewsController.php` |
| **Model** | `NewsDetailModel.php`, `UserProfileModel.php` |
| **View** | `site/news_detail.php` |
| **JS** | `assets/js/site/news_detail.js` |
| **API endpoints** | `GET /news/detail/{id}` · `POST /news/like` · `POST /news/save` · `POST /news/comment` · `POST /news/comment/edit` · `POST /news/comment/delete` |

### 6.3 Module Categories (Danh mục)

| | |
|---|---|
| **Chức năng** | Lọc tin theo danh mục cha/con, pagination, top xem nhiều |
| **Controller** | `site/CategoriesController.php` |
| **Model** | `SiteModel.php`, `CategoriesModel.php` |
| **View** | `site/categories.php` |
| **JS** | `assets/js/site/categories.js` |
| **API endpoint** | `GET /categories/{id}/data?page=N` |

### 6.4 Module Search (Tìm kiếm)

| | |
|---|---|
| **Chức năng** | Tìm kiếm toàn văn (title + content), pagination |
| **Controller** | `site/SearchController.php` |
| **Model** | `SiteModel.php` |
| **View** | `site/search.php` |
| **JS** | `assets/js/site/search.js` |
| **API endpoint** | `GET /search/data?keyword=...&page=N` |

### 6.5 Module User Profile (Hồ sơ người dùng)

| | |
|---|---|
| **Chức năng** | Xem/sửa thông tin, đổi avatar, đổi mật khẩu, xem bình luận, xem/bỏ bookmark, lịch sử xem tin |
| **Controller** | `site/UserController.php` |
| **Model** | `UserProfileModel.php`, `UserModel.php` |
| **View** | `site/users/profile.php` |
| **JS** | `assets/js/site/profile.js` |
| **API endpoints** | `GET /user/info` · `POST /user/update-name` · `POST /user/update-email` · `POST /user/update-avatar` · `POST /user/change-password` · `GET /user/bookmarks` · `POST /user/delete-bookmark` · `GET /user/comments` · `POST /user/delete-comment` · `GET /user/history` · `POST /user/clear-history` |

### 6.6 Module Auth — Site

| | |
|---|---|
| **Chức năng** | Đăng nhập, đăng ký, đăng xuất người dùng |
| **Controller** | `site/AuthController.php` |
| **Model** | `AuthModel.php` |
| **View** | `site/layouts/auth_modal.php` (modal popup) |
| **JS** | `assets/js/site/auth_modal.js` |

### 6.7 Module Admin — News

| | |
|---|---|
| **Chức năng** | CRUD bài viết, duyệt/ẩn bài, upload ảnh thumbnail |
| **Controller** | `admin/NewsController.php` |
| **Model** | `NewsModel.php` |
| **View** | `admin/news/list.php`, `add.php`, `edit.php` |
| **JS** | `assets/js/admin/news.js` |

### 6.8 Module Admin — Users

| | |
|---|---|
| **Chức năng** | CRUD người dùng, phân role, bảo vệ xóa chính mình |
| **Controller** | `admin/UsersController.php` |
| **Model** | `UserModel.php` |
| **View** | `admin/users/list.php`, `add.php`, `edit.php` |
| **JS** | `assets/js/admin/users.js` |

### 6.9 Module Admin — Categories

| | |
|---|---|
| **Chức năng** | CRUD danh mục, phân cấp cha/con, gán editor phụ trách |
| **Controller** | `admin/CategoriesController.php` |
| **Model** | `CategoriesModel.php` |
| **View** | `admin/categories/` |
| **JS** | `assets/js/admin/categories.js` |

### 6.10 Module Admin — Ads (Quảng cáo)

| | |
|---|---|
| **Chức năng** | CRUD quảng cáo (ảnh/video), phân vị trí hiển thị, bật/tắt |
| **Controller** | `admin/AdsController.php` |
| **Model** | `AdsModel.php` |
| **View** | `admin/ads/` |
| **JS** | `assets/js/admin/ads.js` |

### 6.11 Module Admin — Comments

| | |
|---|---|
| **Chức năng** | Xem, xóa bình luận theo bài viết |
| **Controller** | `admin/CommentsController.php` |
| **Model** | `CommentModel.php` |
| **View** | `admin/comments/` |
| **JS** | `assets/js/admin/comments.js` |

---

## 7. 💻 Hướng dẫn cài đặt

**Cấu hình kết nối**

Mở `config/config.php` và điều chỉnh:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // Mật khẩu MySQL của bạn
define('DB_NAME', 'web_tintuc');
define('BASE_URL', 'http://localhost/webtintuc247/');

**Khởi động XAMPP và truy cập**

```
Trang người dùng : http://localhost/webtintuc247/
Trang quản trị   : http://localhost/webtintuc247/admin
```

> **Tài khoản mặc định:** Kiểm tra dữ liệu seed trong file SQL nhận từ team.

---

## 8. 🤝 Hướng dẫn làm việc nhóm

### Quy tắc chung

- ❌ **Không commit trực tiếp vào `main`** — mọi thay đổi phải qua Pull Request.
- ✅ Mỗi tính năng/fix tạo **branch riêng**.
- ✅ Mô tả rõ PR: thay đổi gì, ảnh hưởng module nào.
- ✅ Review ít nhất 1 người trước khi merge.

### Đặt tên branch

```
feature/ten-tinh-nang       # Thêm tính năng mới
fix/mo-ta-bug               # Sửa lỗi
refactor/ten-module         # Refactor code
docs/ten-tai-lieu           # Cập nhật tài liệu
```

### Cấu trúc commit message

```
[type] mô tả ngắn gọn

Loại: feat | fix | refactor | docs | style | chore
```

Ví dụ:
```
[feat] Thêm endpoint /search/data cho SearchController
[fix] Sửa lỗi bookmark không lưu do sai endpoint
[refactor] Gộp code lặp lấy user data vào getClientViewData()
```

### Quy tắc thêm code mới

| Loại | Đặt ở đâu | Ghi chú |
|---|---|---|
| Controller mới | `app/controllers/site/` hoặc `app/controllers/admin/` | Extends `Controller` |
| Model mới | `app/models/` | Inject `Database::getInstance()` trong constructor |
| JS mới | `assets/js/site/` hoặc `assets/js/admin/` | Include từ view tương ứng |
| CSS mới | `assets/css/site/` hoặc `assets/css/admin/` | Thêm `@import` vào `main.css` / `style_admin.css` |

---

## 9. ⚠️ Vấn đề hiện tại

### 🔴 Bảo mật

| Vấn đề | Chi tiết | File liên quan |
|---|---|---|
| **Mật khẩu plaintext** | `AuthModel` so sánh password trực tiếp, không dùng `password_hash()` | `AuthModel.php` |
| **Upload chưa đủ kiểm tra** | Chỉ kiểm tra extension, không kiểm tra MIME type thực sự | `admin/NewsController.php`, `site/UserController.php` |
| **XSS** | `noidung` bài viết lưu HTML thô, render bằng `innerHTML` | `news_detail.js` |

### 🟠 Kiến trúc

| Vấn đề | Chi tiết |
|---|---|
| **Auth guard phân tán** | Mỗi admin controller tự kiểm tra session trong `__construct()` — không có middleware tập trung |
| **Role check hard-code** | `in_array($role, ['admin', 'tongbien_tap', ...])` rải rác trong controller |
| **Workflow duyệt bài** | Trạng thái bài viết lưu dạng string (`cho_duyet`, `da_dang`, `ban_nhap`) — chưa có bảng workflow |

### 🟡 Code quality

| Vấn đề | Chi tiết |
|---|---|
| **Lịch sử xem dùng Cookie** | Lưu trữ `viewed_news_` bằng Cookie phía client — dễ giả mạo |
| **Không có error handler toàn cục** | Lỗi PHP sẽ hiển thị thô ra trình duyệt |

---

## 10. 🚀 Đề xuất cải tiến

### 🔴 Ngắn hạn (ưu tiên cao)

1. **Hash mật khẩu:** Migrate toàn bộ sang `password_hash(PASSWORD_DEFAULT)` + `password_verify()`.

2. **Upload MIME check:**
   ```php
   $finfo = finfo_open(FILEINFO_MIME_TYPE);
   $mime  = finfo_file($finfo, $_FILES['file']['tmp_name']);
   $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
   if (!in_array($mime, $allowedMimes)) { /* reject */ }
   ```

3. **Middleware Auth tập trung:** Tạo `AdminMiddleware::requireLogin()` và `requireRole($roles[])` dùng chung cho mọi admin controller thay vì code trong từng `__construct()`.

### 🟠 Trung hạn

4. **Global error handler:** Thêm `set_exception_handler()` trong `index.php` để bắt lỗi, trả JSON hoặc trang lỗi thân thiện.

5. **Chuẩn hóa API response:** Tạo class `ApiResponse` với `success($data)` và `error($message, $code)`.

6. **Lịch sử xem bằng DB:** Thay Cookie bằng bảng `tbl_view_history (user_id, news_id, viewed_at)` để chính xác và bảo mật hơn.

### 🟡 Dài hạn

7. **Router tường minh:** Tạo `routes.php` định nghĩa URL → Controller::method thay vì dùng convention ngầm.

8. **BaseModel:** Tạo class `BaseModel` với CRUD chung để giảm code lặp giữa các model.

9. **Paginator:** Tạo class tái sử dụng thay vì tính `$offset`, `$totalPages` trong mọi controller.

10. **Environment config:** Tách `.env` hoặc `config.local.php` để không commit thông tin DB lên Git.

---

## 📝 Bản quyền

Dự án nội bộ — dành cho mục đích học tập và phát triển.
