<?php
include 'config.php';
include 'Blockchain.php';

// 检查用户是否登录
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}
$blockchain = Blockchain::getInstance($pdo);

$action = $_GET['action'] ?? 'view';

// 获取特定车辆或所有区块
if (isset($_GET['auto_id'])) {
    $blocks = $blockchain->getBlocksByAutoId($_GET['auto_id']);
    $title = "Blockchain for Vehicle #" . $_GET['auto_id'];
} else {
    $blocks = $blockchain->getChain();
    $title = "Blockchain Explorer";
}

$isValid = $blockchain->isValid();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Car Auction System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-primary mb-4">
            <i class="fas fa-link"></i> <?php echo $title; ?>
        </h1>
        <?php 
        if ($_SESSION['is_admin']): 
            echo '<a href="admin_dashboard.php" class="btn btn-warning">
                <i class="fas fa-crown"></i> Back to Dashboard
            </a>';
        else: 
        echo '<a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Main
        </a>';
        endif; 
        ?>
        <div class="alert alert-<?php echo $isValid ? 'success' : 'danger'; ?>">
            Blockchain Status: <strong><?php echo $isValid ? 'VALID' : 'INVALID - TAMPERING DETECTED'; ?></strong>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-search"></i> Search Blockchain
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label for="auto_id" class="form-label">Search by Vehicle ID</label>
                        <input type="number" class="form-control" id="auto_id" name="auto_id" 
                            placeholder="Enter vehicle ID" value="<?php echo $_GET['auto_id'] ?? ''; ?>">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Search</button>
                        <a href="blockchain_explorer.php" class="btn btn-secondary">View All</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-cubes"></i> Blocks (<?php echo count($blocks); ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if (count($blocks) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Block #</th>
                                    <th>Timestamp</th>
                                    <th>Hash</th>
                                    <th>Previous Hash</th>
                                    <th>Nonce</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($blocks as $block): ?>
                                    <tr>
                                        <td><?php echo $block['block_id']; ?></td>
                                        <td><?php echo $block['timestamp']; ?></td>
                                        <td class="text-truncate" style="max-width: 150px;" 
                                            title="<?php echo $block['hash']; ?>">
                                            <?php echo substr($block['hash'], 0, 16) . '...'; ?>
                                        </td>
                                        <td class="text-truncate" style="max-width: 150px;"
                                            title="<?php echo $block['previous_hash']; ?>">
                                            <?php echo substr($block['previous_hash'], 0, 16) . '...'; ?>
                                        </td>
                                        <td><?php echo $block['nonce']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info" 
                                                onclick="viewBlockData(<?php echo htmlspecialchars(json_encode($block)); ?>)">
                                                View Data
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No blocks found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal for block data -->
    <div class="modal fade" id="blockDataModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Block Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <pre id="blockDataContent" class="bg-light p-3"></pre>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewBlockData(block) {
            const modal = new bootstrap.Modal(document.getElementById('blockDataModal'));
            const content = document.getElementById('blockDataContent');
            
            try {
                const data = JSON.parse(block.data);
                content.textContent = JSON.stringify(data, null, 2);
            } catch (e) {
                content.textContent = block.data;
            }
            
            modal.show();
        }
    </script>
</body>
</html>