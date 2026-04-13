<div class="admin-container">
    <div class="admin-header-inline">
        <h2 class="admin-title">SỬA DANH MỤC </h2>
        <div style="width: 140px;"></div>
    </div>

    <div id="status-msg" style="display:none; padding:10px; margin-bottom:15px;" class="alert alert-warning"></div>
    <div id="loading" style="text-align:center; padding: 20px;">Đang tải dữ liệu...</div>

    <form id="categoryForm" class="admin-form" style="display:none;">
        <div class="form-group">
            <label>*Tên danh mục</label>
            <input type="text" id="catName" required>
        </div>

        <div class="form-group">
            <label>Thuộc danh mục (Cha)</label>
            <select id="parentId"></select>
        </div>

        <div class="form-group" id="managerGroup"></div>

        <div class="btn-group-center">
            <button type="submit" class="btn btn-OK">💾 Cập nhật </button>
            <a href="<?= URLROOT ?>admin/categories" class="btn btn-Cancel">❌ Huỷ</a>
        </div>
    </form>
</div>

<script>
    const BASE_URL = '<?= URLROOT ?>';
    const CATEGORY_ID = <?= $data['id'] ?? 0 ?>;
</script>
<script src="<?= URLROOT ?>assets/js/admin/categories.js"></script>
