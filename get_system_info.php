<?php
include 'config.php';

header('Content-Type: application/json');

try {
    // Get total customers
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM customers");
    $total_customers = $stmt->fetch()['total'];
    
    // Get total vehicles
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM autos");
    $total_vehicles = $stmt->fetch()['total'];
    
    // Get total bids
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM bids");
    $total_bids = $stmt->fetch()['total'];
    
    echo json_encode([
        'success' => true,
        'total_customers' => $total_customers,
        'total_vehicles' => $total_vehicles,
        'total_bids' => $total_bids,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>