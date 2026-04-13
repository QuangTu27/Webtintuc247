<div class="admin-content">
    <a href="<?= URLROOT ?>admin/comments" class="btn btn-Cancel" style="margin-bottom: 20px; display: inline-block;">
        ⬅ Quay lại
    </a>

    <div id="status-msg" style="display:none; padding:10px; margin-bottom:15px; border-radius:4px;" class="alert alert-success"></div>

    <h2 class="admin-title">
        <span class="sub-title" id="newsTitle">Đang tải bài viết...</span>
    </h2>

    <div class="table-scroll">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Người bình luận</th>
                    <th>Nội dung</th>
                    <th>Loại</th>
                    <th>Ngày bình</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="apiCommentsBody">
                <tr><td colspan="6" style="text-align: center; padding: 40px;">Đang lấy dữ liệu bình luận từ API...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    const BASE_URL = '<?= URLROOT ?>';
    const newsId = <?= $newsId ?? 0 ?>;
</script>
<script src="<?= URLROOT ?>assets/js/admin/comments.js"></script>
