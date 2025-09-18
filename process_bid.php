<?php
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auto_id = $_POST['auto_id'];
    $customer_id = $_POST['customer_id'];
    $bid_amount = $_POST['bid_amount'];
    
    try {
        // 检查车辆是否存在
        $stmt = $pdo->prepare("SELECT auto_id FROM autos WHERE auto_id = ? AND is_available = TRUE");
        $stmt->execute([$auto_id]);
        if ($stmt->rowCount() === 0) {
            throw new Exception('Vehicle not found or not available for bidding.');
        }
        
        // 检查客户是否存在
        $stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE customer_id = ?");
        $stmt->execute([$customer_id]);
        if ($stmt->rowCount() === 0) {
            throw new Exception('Customer not found.');
        }
        
        // 插入投标
        $stmt = $pdo->prepare("INSERT INTO bids (auto_id, customer_id, bid_amount, bid_date) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$auto_id, $customer_id, $bid_amount]);
        
        echo json_encode(['success' => true, 'message' => 'Bid placed successfully!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>