// 系统信息函数
function showSystemInfo() {
    const modal = new bootstrap.Modal(document.getElementById('systemInfoModal'));
    const content = document.getElementById('systemInfoContent');
    
    // 显示加载状态
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading system information...</p>
        </div>
    `;
    
    modal.show();
    
    // 模拟加载系统信息
    setTimeout(() => {
        fetch('get_system_info.php')
            .then(response => response.json())
            .then(data => {
                content.innerHTML = `
                    <div class="system-stats">
                        <div class="row text-center mb-4">
                            <div class="col-4">
                                <div class="stat-item">
                                    <h3 class="text-primary">${data.total_customers || 0}</h3>
                                    <small>Total Customers</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <h3 class="text-success">${data.total_vehicles || 0}</h3>
                                    <small>Total Vehicles</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <h3 class="text-warning">${data.total_bids || 0}</h3>
                                    <small>Total Bids</small>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> System is running normally.
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Error loading system information: ${error.message}
                    </div>
                `;
            });
    }, 1000);
}

// 投标提交函数
function submitBid() {
    const formData = new FormData();
    formData.append('auto_id', document.getElementById('bid_auto_id').value);
    formData.append('customer_id', document.getElementById('bid_customer_id').value);
    formData.append('bid_amount', document.getElementById('bid_amount').value);
    
    const resultDiv = document.getElementById('bidResult');
    resultDiv.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Processing...</div>';
    
    fetch('process_bid.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            // 清空表单
            document.getElementById('bid_auto_id').value = '';
            document.getElementById('bid_customer_id').value = '';
            document.getElementById('bid_amount').value = '';
            
            // 3秒后关闭模态框
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('place-bid-modal')).hide();
            }, 3000);
        } else {
            resultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    });
}

// 页面加载完成后的初始化
document.addEventListener('DOMContentLoaded', function() {
    // 显示欢迎消息
    console.log('Silent Car Auction System loaded successfully');
    
    // 检查URL参数显示消息
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    const type = urlParams.get('type');
    
    if (message && type) {
        showNotification(message, type);
    }
});

// 显示通知函数
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '1050';
    notification.style.minWidth = '300px';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // 5秒后自动消失
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}