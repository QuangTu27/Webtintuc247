<h2 class="admin-title">QUẢN LÝ QUẢNG CÁO</h2>

<div id="status-msg" class="alert alert-success" style="display:none; padding: 12px; margin-bottom: 20px; border-radius: 4px; font-weight: bold;"></div>

<div class="admin-toolbar">
    <div class="admin-controls">
        <a href="<?= URLROOT ?>admin/ads/add" class="btn btn-add">➕ Thêm quảng cáo</a>
        <button type="button" id="btnDeleteSelected" class="btn btn-delete btn-disabled" disabled>🗑️ Xoá 0 quảng cáo</button>
    </div>

    <div class="search-box">
        <form class="search-form">
            <input type="text" id="searchInput" placeholder="Tìm tiêu đề, vị trí..." class="search-input">
            <button type="submit" class="btn btn-OK">🔍 Tìm Kiếm</button>
            <button type="button" class="btn btn-view">🔄 Làm Mới</button>
        </form>
    </div>
</div>

<div class="table-scroll">
    <table class="admin-table">
        <thead>
            <tr>
                <th width="40"><input type="checkbox" id="checkAll"></th>
                <th>ID</th>
                <th>Tiêu đề</th>
                <th>Media</th>
                <th>Link</th>
                <th>Vị trí</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody id="apiTableBody">
            <tr><td colspan="8" style="text-align: center; padding: 40px;">Đang tải dữ liệu API...</td></tr>
        </tbody>
    </table>
</div>

<script>
    const BASE_URL = '<?= URLROOT ?>';
</script>
<script src="<?= URLROOT ?>assets/js/admin/ads.js"></script>
