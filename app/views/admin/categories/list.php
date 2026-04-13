<h2 class="admin-title">QUẢN LÝ DANH MỤC</h2>

<div id="status-msg" style="display:none; padding:10px; margin-bottom:15px; border-radius:4px;"></div>

<div class="admin-toolbar">
    <div class="admin-controls">
        <a id="btnAdd" href="<?= URLROOT ?>admin/categories/add" class="btn btn-add btn-disabled" style="pointer-events:none;">➕ Thêm danh mục</a>
        <button type="button" id="btnDeleteSelected" class="btn btn-delete btn-disabled" disabled>🗑️ Xoá 0 mục</button>
    </div>

    <div class="search-box">
        <input type="text" id="keyword" class="search-input" placeholder="Tìm theo tên...">
        <button type="button" id="btnSearch" class="btn btn-OK">🔍 Tìm kiếm</button>
        <select id="filterId" class="search-input">
            <option value="0">-- Tất cả danh mục --</option>
        </select>
    </div>
</div>

<div class="table-scroll">
    <table class="admin-table">
        <thead>
            <tr>
                <th width="40"><input type="checkbox" id="checkAll" disabled></th>
                <th width="50">ID</th>
                <th>Tên danh mục</th>
                <th>Cấp độ</th>
                <th>Người phụ trách</th>
                <th width="150">Thao tác</th>
            </tr>
        </thead>
        <tbody id="apiTableBody">
            <tr><td colspan="6" style="text-align: center; padding: 40px;">Đang tải...</td></tr>
        </tbody>
    </table>
</div>

<script>const BASE_URL = '<?= URLROOT ?>';</script>
<script src="<?= URLROOT ?>assets/js/admin/categories.js"></script>
