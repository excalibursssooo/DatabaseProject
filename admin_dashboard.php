<?php
include 'config.php';

// 检查用户是否登录且是管理员
if (!isLoggedIn() || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Car Auction System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                        <a class="nav-link active" href="admin_dashboard.php">Dashboard</a>
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
                        <a class="nav-link" href="manage_users.php">Manage Users</a>
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
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Vehicles</h5>
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) FROM autos");
                        $count = $stmt->fetchColumn();
                        ?>
                        <p class="card-text display-4"><?php echo $count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Bids</h5>
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) FROM bids");
                        $count = $stmt->fetchColumn();
                        ?>
                        <p class="card-text display-4"><?php echo $count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Customers</h5>
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) FROM customers");
                        $count = $stmt->fetchColumn();
                        ?>
                        <p class="card-text display-4"><?php echo $count; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Vehicles</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Make</th>
                                        <th>Model</th>
                                        <th>Year</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM autos ORDER BY auto_id DESC LIMIT 5");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>
                                            <td>{$row['auto_id']}</td>
                                            <td>{$row['make']}</td>
                                            <td>{$row['model']}</td>
                                            <td>{$row['year']}</td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="view_vehicles.php" class="btn btn-sm btn-outline-primary">View All Vehicles</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Bids</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Bid ID</th>
                                        <th>Auto ID</th>
                                        <th>Customer ID</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM bids ORDER BY bid_id DESC LIMIT 5");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>
                                            <td>{$row['bid_id']}</td>
                                            <td>{$row['auto_id']}</td>
                                            <td>{$row['customer_id']}</td>
                                            <td>\${$row['bid_amount']}</td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="max_bid.php" class="btn btn-sm btn-outline-primary">View All Bids</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="add_auto.php" class="btn btn-primary me-md-2">
                                <i class="fas fa-plus"></i> Add New Vehicle
                            </a>
                            <a href="manage_users.php" class="btn btn-info me-md-2">
                                <i class="fas fa-users-cog"></i> Manage Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>