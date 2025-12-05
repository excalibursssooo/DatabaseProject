
<?php
include 'config.php';

// check if admin is logged in
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
    <title>View Customers - Car Auction System</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-info">
                <i class="fas fa-users"></i> All Customers
            </h1>
            <a href="admin_dashboard.php" class="btn btn-warning">
                <i class="fas fa-crown"></i> Back to Dashboard
            </a>
        </div>

        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-circle"></i> Customer List
                </h5>
            </div>
            <div class="card-body">
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM customers ORDER BY customer_id DESC");
                    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($customers) > 0) {
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-striped table-hover">';
                        echo '<thead class="table-dark"><tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Zip Code</th>
                        </tr></thead><tbody>';
                        
                        foreach($customers as $customer) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($customer['customer_id']) . '</td>';
                            echo '<td>' . htmlspecialchars($customer['first_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($customer['last_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($customer['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($customer['phone'] ?? 'N/A') . '</td>';
                            echo '<td>' . htmlspecialchars($customer['address']) . '</td>';
                            echo '<td>' . htmlspecialchars($customer['city']) . '</td>';
                            echo '<td>' . htmlspecialchars($customer['state']) . '</td>';
                            echo '<td>' . htmlspecialchars($customer['zip_code']) . '</td>';
                            echo '</tr>';
                        }
                        
                        echo '</tbody></table>';
                        echo '</div>';
                    } else {
                        echo '<div class="alert alert-info text-center">';
                        echo '<i class="fas fa-info-circle fa-2x mb-3"></i><br>';
                        echo 'No customers found in the database.';
                        echo '</div>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="add_customer.php" class="btn btn-info text-white">
                <i class="fas fa-user-plus"></i> Add New Customer
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
