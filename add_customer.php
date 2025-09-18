<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Customer - Car Auction System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-info">
                <i class="fas fa-user-plus"></i> Add New Customer
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

        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-circle"></i> Customer Information
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="process_add_customer.php">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address *</label>
                                <input type="text" class="form-control" id="address" name="address" required>
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="state" class="form-label">State *</label>
                                <input type="text" class="form-control" id="state" name="state" maxlength="2" required>
                            </div>
                            <div class="mb-3">
                                <label for="zip_code" class="form-label">Zip Code *</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-info btn-lg text-white">
                            <i class="fas fa-save"></i> Add Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="add_auto.php" class="btn btn-outline-info">
                <i class="fas fa-plus-circle"></i> Add Vehicle Instead
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>