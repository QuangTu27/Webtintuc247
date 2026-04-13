<div class="admin-content">
    <h2 class="admin-title">QUẢN LÝ BÌNH LUẬN</h2>

    <div class="admin-toolbar">
        <div class="admin-controls"></div>

        <div class="search-box">
            <form class="search-form" id="searchCommentForm">
                <select id="catId" class="search-input" style="width: 200px; cursor: pointer;">
                    <option value="0">-- Tất cả Chuyên mục --</option>
                </select>

                <input type="text" id="keyword" placeholder="Tìm bài viết..." class="search-input">
                <button type="submit" class="btn btn-OK">🔍 Lọc</button>
                <button type="button" class="btn btn-view" id="btnRefreshComments">🔄 Làm mới</button>
            </form>
        </div>
    </div>

    <div class="table-scroll">
        <table class="admin-table">
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <th>Tiêu đề bài viết</th>
                    <th>Chuyên mục</th>
                    <th>Ngày đăng</th>
                    <th width="100">Bình luận</th>
                    <th width="120">Thao tác</th>
                </tr>
            </thead>
            <tbody id="apiTableBody">
                <tr><td colspan="6" style="text-align: center; padding: 40px;">Đang tải dữ liệu API...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    const BASE_URL = '<?= URLROOT ?>';
</script>
<script src="<?= URLROOT ?>assets/js/admin/comments.js"></script>
