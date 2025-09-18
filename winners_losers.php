<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Winners and Losers - Car Auction System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Winners and Losers</h1>
        
        <a href="index.php" class="btn btn-secondary mb-4">← Back to Main</a>
        
        <?php
        if (isset($_GET['auto_id'])) {
            $auto_id = intval($_GET['auto_id']);
            
            try {
                $stmt = $pdo->prepare("CALL WinnersAndLosers(?)");
                $stmt->execute([$auto_id]);
                $results = $stmt->fetchAll();
                
                if (count($results) > 0) {
                    echo '<h3>Results for Auto ID: ' . $auto_id . '</h3>';
                    displayResults($results);
                } else {
                    echo '<div class="alert alert-info">No bids found for Auto ID: ' . $auto_id . '</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
            }
        } else {
            echo '<div class="alert alert-warning">Please provide an Auto ID.</div>';
        }
        ?>
    </div>
</body>
</html>