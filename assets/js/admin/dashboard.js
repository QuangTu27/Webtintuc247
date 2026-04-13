document.addEventListener("DOMContentLoaded", function() { 
    const dashboardUI = document.getElementById('dashboardUI');
    if (dashboardUI) {
        loadDashboard(); 
    }
});

async function loadDashboard() {
    try {
        const response = await fetch(BASE_URL + 'admin/dashboard/data');
        const result = await response.json();

        if (result.status === 'success') {
            const dbWelcome = document.getElementById('dbWelcome');
            if (dbWelcome) dbWelcome.innerText = '👋 Xin chào, ' + result.data.name + '!';
            
            const dbNewsCount = document.getElementById('dbNewsCount');
            if (dbNewsCount) dbNewsCount.innerText = result.data.counts.news;
            
            const dbPendingCount = document.getElementById('dbPendingCount');
            if (dbPendingCount) dbPendingCount.innerText = result.data.counts.pending;
            
            const dbCatsCount = document.getElementById('dbCatsCount');
            if (dbCatsCount) dbCatsCount.innerText = result.data.counts.cats;

            if (result.data.role === 'admin') {
                const dbUsersCount = document.getElementById('dbUsersCount');
                if (dbUsersCount) dbUsersCount.innerText = result.data.counts.users;
                
                const dbUsersCard = document.getElementById('dbUsersCard');
                if (dbUsersCard) dbUsersCard.style.display = 'block';
            }

            let phtml = '';
            if (result.data.pending_list && result.data.pending_list.length > 0) {
                phtml = `<table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                            <thead><tr style="background: #f8f9fa; color: #555; text-align: left;">
                                <th style="padding: 10px; border-bottom: 2px solid #eee;">Tiêu đề</th>
                                <th style="padding: 10px; border-bottom: 2px solid #eee;">Ngày gửi</th>
                                <th style="padding: 10px; border-bottom: 2px solid #eee;">Hành động</th>
                            </tr></thead><tbody>`;
                result.data.pending_list.forEach(item => {
                    phtml += `<tr>
                        <td style="padding: 12px 10px; border-bottom: 1px solid #eee;">
                            <a href="${BASE_URL}admin/news/edit/${item.id}" style="text-decoration: none; color: #333; font-weight: 500;">
                                ${escapeHtml(item.tieude)}
                            </a>
                        </td>
                        <td style="padding: 12px 10px; border-bottom: 1px solid #eee; color: #777; font-size: 14px;">
                            ${new Date(item.ngaydang).toLocaleString('vi-VN')}
                        </td>
                        <td style="padding: 12px 10px; border-bottom: 1px solid #eee;">
                            <a href="${BASE_URL}admin/news/edit/${item.id}" style="padding: 5px 10px; background: #28a745; color: white; border-radius: 4px; text-decoration: none; font-size: 12px; margin-right: 5px;">Duyệt</a>
                        </td>
                    </tr>`;
                });
                phtml += `</tbody></table>`;
            } else {
                phtml = `<p style="text-align: center; color: #999; margin-top: 20px;">Không có bài viết nào đang chờ duyệt.</p>`;
            }
            
            const dbPendingTasks = document.getElementById('dbPendingTasks');
            if (dbPendingTasks) dbPendingTasks.innerHTML = phtml;

            const dbLoading = document.getElementById('dbLoading');
            if (dbLoading) dbLoading.style.display = 'none';
            
            dashboardUI.style.display = 'block';
        }
    } catch(e) {
        console.error("Dashboard error:", e);
    }
}

function escapeHtml(unsafe) { 
    return (unsafe||'').toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); 
}
