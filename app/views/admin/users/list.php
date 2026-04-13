<h2 class="admin-title">QUẢN LÝ TÀI KHOẢN</h2>

<div id="status-msg" class="alert alert-success" style="display: none; margin-bottom: 15px;"></div>

<div class="admin-toolbar">
    <div class="admin-controls">
        <a href="<?= URLROOT ?>admin/users/add" class="btn btn-add">➕ Thêm người dùng</a>
        <button type="button" id="btnDeleteSelected" class="btn btn-delete btn-disabled" disabled>
            🗑️ Xoá 0 user
        </button>
    </div>

    <div class="search-box">
        <form id="searchForm" class="search-form">
            <input type="text" id="searchInput" name="search" placeholder="Tìm kiếm user..." class="search-input">
            <button type="submit" class="btn btn-OK">🔍 Tìm Kiếm</button>
            <button type="button" id="btnResetSearch" class="btn btn-view">🔄 Làm Mới</button>
        </form>
    </div>
</div>

<div class="table-scroll">
    <table class="admin-table">
        <thead>
            <tr>
                <th><input type="checkbox" id="checkAll"></th>
                <th>ID</th>
                <th>Username</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody id="apiTableBody">
            <tr><td colspan="7" style="text-align: center; padding: 40px;">Đang tải dữ liệu từ API...</td></tr>
        </tbody>
    </table>
</div>

<script>
    const BASE_URL = '<?= URLROOT ?>';
</script>
<script src="<?= URLROOT ?>assets/js/admin/users.js"></script>
