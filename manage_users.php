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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Car Auction Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_auto.php">Add Vehicle</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_customer.php">Add Customer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_customers.php">View Customers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="manage_users.php">Manage Users</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user-circle"></i> My Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">View Site</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>      
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-info"><i class="fas fa-users"></i> Manage Users</h1>
            <a href="admin_dashboard.php" class="btn btn-warning">
                <i class="fas fa-crown"></i> Back to Dashboard
            </a>
        </div>        
        <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-circle"></i> User List
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
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
        </div>
    </div>
</body>
</html>