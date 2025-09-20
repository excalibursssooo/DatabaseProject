<?php 
include 'config.php';

// 检查用户是否登录
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// 检查用户是否是管理员，如果是，重定向到管理员仪表板
if ($_SESSION['is_admin']) {
    header('Location: admin_dashboard.php');
    exit();
}

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blockchain Car Auction System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Car Auction System</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="index.php">Home</a>
                <a class="nav-link" href="profile.php">My Profile</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <header class="text-center mb-5">
            <h1 class="display-4 text-primary fw-bold">Silent Car Auction System</h1>
            <p class="lead text-muted">Merchant Savings Society - Vehicle Auction Management</p>
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! 
                <?php if ($_SESSION['is_admin']) echo '<span class="badge bg-danger">Admin</span>'; ?>
            </div>
        </header>

        <!-- 功能卡片网格 -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <!-- 查看车辆 -->
            <div class="col">
                <div class="card h-100 feature-card">
                    <div class="card-body text-center">
                        <i class="fas fa-car fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">View Available Vehicles</h5>
                        <p class="card-text">Browse available vehicles by make and filter your search</p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <a href="view_vehicles.php" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i> Browse Vehicles
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 投标管理 -->
            <div class="col">
                <div class="card h-100 feature-card">
                    <div class="card-body text-center">
                        <i class="fas fa-gavel fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Bid Management</h5>
                        <p class="card-text">Place bids and view current maximum bids</p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <a href="#place-bid-modal" class="btn btn-warning" data-bs-toggle="modal">
                                <i class="fas fa-hand-point-up"></i> Place Bid
                            </a>
                            <a href="max_bid.php" class="btn btn-outline-warning">
                                <i class="fas fa-chart-line"></i> View Max Bids
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 结果查询 -->
            <div class="col">
                <div class="card h-100 feature-card">
                    <div class="card-body text-center">
                        <i class="fas fa-trophy fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Auction Results</h5>
                        <p class="card-text">Check winners and losers for specific vehicles</p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid">
                            <a href="#view-results-modal" class="btn btn-success" data-bs-toggle="modal">
                                <i class="fas fa-medal"></i> View Results
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 查看结果模态框 -->
        <div class="modal fade" id="view-results-modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">View Auction Results</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                    <form action="winners_losers.php" method="GET">
                        <div class="mb-3">
                            <label for="auto_id" class="form-label">Enter Auto ID</label>
                            <input type="number" class="form-control" id="auto_id" name="auto_id" required min="1">
                            <div class="form-text">Enter the vehicle ID to see auction results</div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">View Results</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- 投标模态框 -->
        <div class="modal fade" id="place-bid-modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Place a Bid</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="bidForm">
                            <div class="mb-3">
                                <label for="bid_auto_id" class="form-label">Auto ID</label>
                                <input type="number" class="form-control" id="bid_auto_id" name="auto_id" required min="1">
                            </div>
                            <div class="mb-3">
                                <label for="bid_customer_id" class="form-label">Customer ID</label>
                                <input type="number" class="form-control" id="bid_customer_id" name="customer_id" 
                                    value="<?php echo $currentUser['customer_id'] ?? ''; ?>" 
                                    <?php echo isset($currentUser['customer_id']) ? 'readonly' : 'required'; ?>>
                                <?php if (!isset($currentUser['customer_id'])): ?>
                                <div class="form-text">Please enter your customer ID</div>
                                <?php else: ?>
                                <div class="form-text">Your customer ID (readonly)</div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="bid_amount" class="form-label">Bid Amount ($)</label>
                                <input type="number" class="form-control" id="bid_amount" name="bid_amount" step="0.01" min="0" required>
                            </div>
                        </form>
                        <div id="bidResult"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-warning" onclick="submitBid()">Place Bid</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- 区块链浏览器 -->
        <div class="col">
            <div class="card h-100 feature-card">
                <div class="card-body text-center">
                    <i class="fas fa-link fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Blockchain Explorer</h5>
                    <p class="card-text">View all transactions on the blockchain</p>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-grid">
                        <a href="blockchain_explorer.php" class="btn btn-outline-info">
                            <i class="fas fa-cubes"></i> View Blockchain
                        </a>
                    </div>
                </div>
            </div>
</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 投标提交函数
        function submitBid() {
            const formData = new FormData(document.getElementById('bidForm'));
            const resultDiv = document.getElementById('bidResult');
            
            fetch('process_bid.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `<div class="alert alert-success mt-3">${data.message}</div>`;
                    setTimeout(() => {
                        $('#place-bid-modal').modal('hide');
                        resultDiv.innerHTML = '';
                    }, 2000);
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-danger mt-3">${data.message}</div>`;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `<div class="alert alert-danger mt-3">Error placing bid: ${error}</div>`;
            });
        }
    </script>
</body>
</html>