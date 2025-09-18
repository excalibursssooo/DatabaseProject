<?php
include 'config.php';

// 检查用户是否登录且是管理员
if (!isLoggedIn() || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

// 处理用户删除
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ? AND user_id != ?");
    $stmt->execute([$user_id, $_SESSION['user_id']]);
    header('Location: manage_users.php?message=User deleted successfully');
    exit();
}

// 获取所有用户
$stmt = $pdo->query("SELECT u.*, c.first_name, c.last_name FROM users u LEFT JOIN customers c ON u.customer_id = c.customer_id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Car Auction System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="admin_dashboard.php" class="btn btn-warning">
                <i class="fas fa-crown"></i> Back to Dashboard
            </a>
        </div>
        <h2>Manage Users</h2>
        
        <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Customer</th>
                    <th>Admin</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td>
                        <?php if ($user['customer_id']): ?>
                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                        (ID: <?php echo $user['customer_id']; ?>)
                        <?php else: ?>
                        <span class="text-muted">Not linked</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($user['is_admin']): ?>
                        <span class="badge bg-danger">Yes</span>
                        <?php else: ?>
                        <span class="badge bg-secondary">No</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $user['created_at']; ?></td>
                    <td>
                        <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                        <a href="manage_users.php?delete=<?php echo $user['user_id']; ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>