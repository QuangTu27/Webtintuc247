<div class="dashboard-container" id="dashboardUI" style="display: none;">
    <div class="welcome-banner" style="background: linear-gradient(135deg, #0a9e54 0%, #28a745 100%); padding: 30px; border-radius: 12px; color: white; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);">
        <h2 style="margin: 0 0 10px 0; font-size: 24px;" id="dbWelcome">👋 Xin chào!</h2>
        <p style="margin: 0; opacity: 0.9;">Chúc bạn một ngày làm việc hiệu quả. Dưới đây là tổng quan hệ thống hôm nay.</p>
    </div>

    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #007bff;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 id="dbNewsCount" style="margin: 0; font-size: 28px; color: #333;">0</h3>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Tổng bài viết</p>
                </div>
                <i class="fas fa-newspaper" style="font-size: 30px; color: #007bff; opacity: 0.2;"></i>
            </div>
        </div>

        <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #ffc107;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 id="dbPendingCount" style="margin: 0; font-size: 28px; color: #333;">0</h3>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Bài chờ duyệt</p>
                </div>
                <i class="fas fa-clock" style="font-size: 30px; color: #ffc107; opacity: 0.3;"></i>
            </div>
        </div>

        <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #28a745;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 id="dbCatsCount" style="margin: 0; font-size: 28px; color: #333;">0</h3>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Chuyên mục</p>
                </div>
                <i class="fas fa-list" style="font-size: 30px; color: #28a745; opacity: 0.2;"></i>
            </div>
        </div>

        <div class="stat-card" id="dbUsersCard" style="display: none; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #17a2b8;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 id="dbUsersCount" style="margin: 0; font-size: 28px; color: #333;">0</h3>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Thành viên</p>
                </div>
                <i class="fas fa-users" style="font-size: 30px; color: #17a2b8; opacity: 0.2;"></i>
            </div>
        </div>
    </div>

    <div class="recent-tasks" style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; font-size: 18px; color: #333;">
            <i class="fas fa-tasks" style="color: #ffc107; margin-right: 10px;"></i> Bài viết mới cần duyệt
        </h3>
        <div id="dbPendingTasks"></div>
    </div>
</div>

<div id="dbLoading" style="text-align:center; padding:50px;">Đang tải dữ liệu Bảng Điều Khiển...</div>

<script>
    const BASE_URL = '<?= URLROOT ?>';
</script>
<script src="<?= URLROOT ?>assets/js/admin/dashboard.js"></script>
