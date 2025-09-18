<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maximum Bids - Car Auction System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-warning">
                <i class="fas fa-chart-line"></i> Maximum Bids
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
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-money-bill-wave"></i> Current Maximum Bids
                </h5>
            </div>
            <div class="card-body">
                <?php
                try {
                    $stmt = $pdo->prepare("CALL MaxBid()");
                    $stmt->execute();
                    $results = $stmt->fetchAll();
                    
                    if (count($results) > 0) {
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-striped table-hover">';
                        echo '<thead class="table-dark"><tr>';
                        foreach(array_keys($results[0]) as $column) {
                            echo '<th>' . htmlspecialchars($column) . '</th>';
                        }
                        echo '</tr></thead><tbody>';
                        
                        foreach($results as $row) {
                            echo '<tr>';
                            foreach($row as $value) {
                                echo '<td>' . htmlspecialchars($value) . '</td>';
                            }
                            echo '</tr>';
                        }
                        
                        echo '</tbody></table>';
                        echo '</div>';
                    } else {
                        echo '<div class="alert alert-info text-center">';
                        echo '<i class="fas fa-info-circle fa-2x mb-3"></i><br>';
                        echo 'No bids found for any vehicles.';
                        echo '</div>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>