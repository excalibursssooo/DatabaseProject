<?php
include 'config.php';

// check if user is logged in and is admin
if (!isLoggedIn() || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location = $_POST['location'];
    $year = $_POST['year'];
    $make = $_POST['make'];
    $model = $_POST['model'];
    $type = $_POST['type'];
    $mileage = $_POST['mileage'];
    $vin = $_POST['vin'];
    
    try {
        $stmt = $pdo->prepare("CALL AddAuto(?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$location, $year, $make, $model, $type, $mileage, $vin]);
        
        header('Location: index.php?message=Vehicle added successfully!&type=success');
        exit();
    } catch (PDOException $e) {
        $errorMessage = 'Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle - Car Auction System</title>
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
                        <a class="nav-link active" href="add_auto.php">Add Vehicle</a>
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
            <h1 class="text-primary">
                <i class="fas fa-car"></i> Add New Vehicle
            </h1>
            <a href="admin_dashboard.php" class="btn btn-warning">
                <i class="fas fa-crown"></i> Back to Dashboard
            </a>
        </div>

        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-car-circle-exclamation"></i> Vehicle Information
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="add_auto.php">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location" class="form-label">Location *</label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>
                            <div class="mb-3">
                                <label for="year" class="form-label">Year *</label>
                                <input type="number" class="form-control" id="year" name="year" 
                                    min="1900" max="<?php echo date('Y') + 1; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="make" class="form-label">Make *</label>
                                <select class="form-select" id="make" name="make" required>
                                    <option value="">Select Make</option>
                                    <option value="Ford">Ford</option>
                                    <option value="Chevrolet">Chevrolet</option>
                                    <option value="Toyota">Toyota</option>
                                    <option value="Honda">Honda</option>
                                    <option value="Nissan">Nissan</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="model" class="form-label">Model *</label>
                                <input type="text" class="form-control" id="model" name="model" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type *</label>
                                <input type="text" class="form-control" id="type" name="type" required>
                            </div>
                            <div class="mb-3">
                                <label for="mileage" class="form-label">Mileage *</label>
                                <input type="number" class="form-control" id="mileage" name="mileage" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label for="vin" class="form-label">VIN (Vehicle Identification Number) *</label>
                                <input type="text" class="form-control" id="vin" name="vin" required 
                                    pattern="[A-HJ-NPR-Z0-9]{17}" title="17-character VIN number">
                                <div class="form-text">17-character VIN (letters and numbers, excluding I, O and Q)</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus-circle"></i> Add Vehicle
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb"></i> Quick Tips
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Ensure all required fields (marked with *) are filled</li>
                        <li>VIN must be a valid 17-character identifier</li>
                        <li>Year should be between 1900 and next year</li>
                        <li>Mileage should be entered in miles</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            
            form.addEventListener('submit', function(e) {
                const vin = document.getElementById('vin').value;
                const currentYear = new Date().getFullYear();
                const year = document.getElementById('year').value;
                
                // VIN validation (simple version)
                if (vin.length !== 17 || /[IOQ]/i.test(vin)) {
                    e.preventDefault();
                    alert('Please enter a valid 17-character VIN (excluding I, O, and Q)');
                    return false;
                }
                
                // year validation
                if (year < 1900 || year > currentYear + 1) {
                    e.preventDefault();
                    alert('Please enter a valid year between 1900 and ' + (currentYear + 1));
                    return false;
                }
            });
        });
    </script>
</body>
</html>