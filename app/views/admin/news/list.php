<div class="admin-header-inline">
    <h2 class="admin-title">QUẢN LÝ TIN TỨC</h2>
</div>

<div id="status-msg" style="display:none; padding:10px; margin-bottom:15px; border-radius:4px;" class="alert alert-success"></div>

<div class="admin-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div class="list-actions">
        <a href="<?= URLROOT ?>admin/news/add" class="btn btn-add">➕ Viết bài mới</a>
        <button type="button" id="btnDeleteSelected" class="btn btn-delete btn-disabled" disabled>🗑️ Xoá 0 bài</button>
    </div>

    <div class="search-box">
        <select id="filterCategory" class="search-input">
            <option value="0">-- Tất cả danh mục --</option>
        </select>
        <button type="button" id="btnFilter" class="btn btn-OK">🔍 Lọc</button>
        <button type="button" id="btnResetFilter" class="btn btn-view">🔄</button>
    </div>
</div>

<div class="table-scroll">
    <table class="admin-table">
        <thead>
            <tr>
                <th width="40"><input type="checkbox" id="checkAll" disabled></th>
                <th width="50">ID</th>
                <th width="80">Ảnh</th>
                <th>Tiêu đề &amp; Thông tin</th>
                <th>Danh mục</th>
                <th width="120">Trạng thái</th>
                <th width="150">Thao tác</th>
            </tr>
        </thead>
        <tbody id="apiTableBody">
            <tr><td colspan="7" style="text-align: center; padding: 40px;">Đang tải dữ liệu...</td></tr>
        </tbody>
    </table>
</div>

<style>
    .btn-icon { display: inline-flex; justify-content: center; align-items: center; width: 36px; height: 36px; border-radius: 6px; text-decoration: none; font-size: 16px; transition: 0.2s; border: none; cursor: pointer; }
    .btn-approve { background: #d4edda; } .btn-approve:hover { background: #28a745; }
    .btn-hide { background: #a1e4ec; } .btn-hide:hover { background: #16c1d8; }
    .btn-edit { background: #f7eebf; } .btn-edit:hover { background: #f5d003; color: #fff; }
    .status-badge { padding: 5px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; white-space:nowrap; display:inline-block; min-width:90px; text-align:center; }
</style>

<script>
    const BASE_URL = '<?= URLROOT ?>';
</script>
<script src="<?= URLROOT ?>assets/js/admin/news.js"></script>
