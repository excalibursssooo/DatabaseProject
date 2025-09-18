<?php
include 'config.php';

// 检查用户是否登录
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$currentUser = getCurrentUser();

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip_code = $_POST['zip_code'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];
    
    try {
        // 更新customers表
        $stmt = $pdo->prepare("
            UPDATE customers 
            SET first_name = ?, last_name = ?, address = ?, city = ?, state = ?, zip_code = ?, email = ?, phone = ?
            WHERE customer_id = ?
        ");
        $stmt->execute([$first_name, $last_name, $address, $city, $state, $zip_code, $email, $phone, $currentUser['customer_id']]);
        
        // 更新users表
        $stmt = $pdo->prepare("
            UPDATE users 
            SET username = ?
            WHERE user_id = ?
        ");
        $stmt->execute([$username, $_SESSION['user_id']]);
        
        // 更新session中的用户名
        $_SESSION['username'] = $username;
        
        $successMessage = 'Profile updated successfully!';
    } catch (PDOException $e) {
        $errorMessage = 'Error updating profile: ' . $e->getMessage();
    }
    
    // 重新获取用户信息
    $currentUser = getCurrentUser();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Car Auction System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
    <?php else: ?>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">Car Auction System</a>
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="index.php">Home</a>
                    <a class="nav-link active" href="profile.php">My Profile</a>
                    <a class="nav-link" href="logout.php">Logout</a>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary">
                <i class="fas fa-user-circle"></i> My Profile
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
        </div>

        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-edit"></i> Personal Information
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="profile.php">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Account Information</h5>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                    value="<?php echo htmlspecialchars($currentUser['username']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Personal Details</h5>
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                    value="<?php echo htmlspecialchars($currentUser['first_name'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                    value="<?php echo htmlspecialchars($currentUser['last_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5>Contact Information</h5>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                    value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                    value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Address Information</h5>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                    value="<?php echo htmlspecialchars($currentUser['address'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                    value="<?php echo htmlspecialchars($currentUser['city'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state" 
                                    value="<?php echo htmlspecialchars($currentUser['state'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="zip_code" class="form-label">Zip Code</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code" 
                                    value="<?php echo htmlspecialchars($currentUser['zip_code'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-history"></i> Account Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>User ID:</strong> <?php echo $currentUser['user_id']; ?></p>
                        <p><strong>Customer ID:</strong> <?php echo $currentUser['customer_id'] ?? 'Not linked'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Account Created:</strong> <?php echo $currentUser['created_at']; ?></p>
                        <p><strong>Account Type:</strong> 
                            <?php echo ($currentUser['is_admin'] ? 'Administrator' : 'Standard User'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>