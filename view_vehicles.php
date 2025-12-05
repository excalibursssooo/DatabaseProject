<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Vehicles - Car Auction System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary">
                <i class="fas fa-car"></i> Available Vehicles
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

        <!-- 筛选表单 -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-filter"></i> Filter Vehicles
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="view_vehicles.php">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="make" class="form-label">Make</label>
                                <select class="form-select" id="make" name="make">
                                    <option value="">All Makes</option>
                                    <option value="Ford" <?php echo (isset($_GET['make']) && $_GET['make'] == 'Ford') ? 'selected' : ''; ?>>Ford</option>
                                    <option value="Chevrolet" <?php echo (isset($_GET['make']) && $_GET['make'] == 'Chevrolet') ? 'selected' : ''; ?>>Chevrolet</option>
                                    <option value="Toyota" <?php echo (isset($_GET['make']) && $_GET['make'] == 'Toyota') ? 'selected' : ''; ?>>Toyota</option>
                                    <option value="Honda" <?php echo (isset($_GET['make']) && $_GET['make'] == 'Honda') ? 'selected' : ''; ?>>Honda</option>
                                    <option value="Nissan" <?php echo (isset($_GET['make']) && $_GET['make'] == 'Nissan') ? 'selected' : ''; ?>>Nissan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <input type="text" class="form-control" id="type" name="type" value="<?php echo isset($_GET['type']) ? htmlspecialchars($_GET['type']) : ''; ?>" placeholder="e.g. Sedan, SUV">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="year" class="form-label">Year</label>
                                <input type="number" class="form-control" id="year" name="year" value="<?php echo isset($_GET['year']) ? htmlspecialchars($_GET['year']) : ''; ?>" placeholder="e.g. 2020" min="1900" max="<?php echo date('Y') + 1; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                        <a href="view_vehicles.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Vehicle List -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i> Vehicle List
                </h5>
            </div>
            <div class="card-body">
                <?php
                try {
                    // build dynamic query based on filters
                    $sql = "SELECT * FROM autos WHERE is_available = TRUE";
                    $params = [];
                    
                    if (isset($_GET['make']) && !empty($_GET['make'])) {
                        $sql .= " AND make = ?";
                        $params[] = $_GET['make'];
                    }
                    
                    if (isset($_GET['type']) && !empty($_GET['type'])) {
                        $sql .= " AND type LIKE ?";
                        $params[] = '%' . $_GET['type'] . '%';
                    }
                    
                    if (isset($_GET['year']) && !empty($_GET['year'])) {
                        $sql .= " AND year = ?";
                        $params[] = $_GET['year'];
                    }

                    if (isset($_GET['auction_end_date']) && !empty($_GET['auction_end_date'])) {
                        $sql .= " AND auction_end_date = ?";
                        $params[] = $_GET['auction_end_date'];
                    }
                    
                    $sql .= " ORDER BY make, model, year DESC";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    $results = $stmt->fetchAll();
                    
                    if (count($results) > 0) {
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-striped table-hover">';
                        echo '<thead class="table-dark"><tr>
                            <th>ID</th>
                            <th>Make</th>
                            <th>Model</th>
                            <th>Year</th>
                            <th>Type</th>
                            <th>Mileage</th>
                            <th>Location</th>
                            <th>Auction End Date</th>
                        </tr></thead><tbody>';
                        
                        foreach($results as $row) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['auto_id']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['make']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['model']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['year']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['type']) . '</td>';
                            echo '<td>' . number_format($row['mileage']) . ' miles</td>';
                            echo '<td>' . htmlspecialchars($row['location']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['auction_end_date']) . '</td>';
                            echo '</tr>';
                        }
                        
                        echo '</tbody></table>';
                        echo '</div>';
                    } else {
                        echo '<div class="alert alert-info text-center">';
                        echo '<i class="fas fa-info-circle fa-2x mb-3"></i><br>';
                        echo 'No vehicles found matching your criteria.';
                        echo '</div>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="max_bid.php" class="btn btn-outline-warning">
                <i class="fas fa-chart-line"></i> View Max Bids
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>