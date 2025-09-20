<?php
include 'config.php';
include 'Blockchain.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auto_id = $_POST['auto_id'];
    $customer_id = $_POST['customer_id'];
    $bid_amount = $_POST['bid_amount'];
    
    try {
        // 初始化区块链
        $blockchain = Blockchain::getInstance($pdo);
        
        
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
        
        // 检查投标金额是否高于当前最高价
        $stmt = $pdo->prepare("SELECT COALESCE(MAX(bid_amount), 0) as max_bid FROM bids WHERE auto_id = ?");
        $stmt->execute([$auto_id]);
        $max_bid = $stmt->fetch(PDO::FETCH_ASSOC)['max_bid'];
        
        if ($bid_amount <= $max_bid) {
            throw new Exception('Bid amount must be higher than current maximum bid: $' . $max_bid);
        }
        // 创建区块链交易数据
        $timestamp = time();
        $bidData = [
            'type' => 'bid',
            'auto_id' => $auto_id,
            'customer_id' => $customer_id,
            'bid_amount' => $bid_amount,
            'timestamp' => $timestamp
        ];
        // 挖矿并添加区块
        $blockHash = $blockchain->mineBlock($bidData, $auto_id, $customer_id);
        
        // 插入投标记录并关联区块链哈希
        $stmt = $pdo->prepare("
            INSERT INTO bids (auto_id, customer_id, bid_amount, bid_date, block_hash) 
            VALUES (?, ?, ?, NOW(), ?)
        ");
        $stmt->execute([$auto_id, $customer_id, $bid_amount, $blockHash]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Bid placed successfully!',
            'block_hash' => $blockHash
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>