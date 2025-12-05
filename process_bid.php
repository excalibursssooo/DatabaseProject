<?php
include 'config.php';
include 'Blockchain.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auto_id = $_POST['auto_id'];
    $customer_id = $_POST['customer_id'];
    $bid_amount = $_POST['bid_amount'];
    
    // Begin transaction
    $pdo->beginTransaction();
    
    try {
        // Initialize blockchain
        $blockchain = Blockchain::getInstance($pdo);
        
        // Check if vehicle exists and is available
        $stmt = $pdo->prepare("SELECT auto_id FROM autos WHERE auto_id = ? AND is_available = TRUE");
        $stmt->execute([$auto_id]);
        if ($stmt->rowCount() === 0) {
            throw new Exception('Vehicle not found or not available for bidding.');
        }
        
        // check if customer exists
        $stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE customer_id = ?");
        $stmt->execute([$customer_id]);
        if ($stmt->rowCount() === 0) {
            throw new Exception('Customer not found.');
        }
        
        // check if bid amount is higher than current max bid
        $stmt = $pdo->prepare("SELECT COALESCE(MAX(bid_amount), 0) as max_bid FROM bids WHERE auto_id = ?");
        $stmt->execute([$auto_id]);
        $max_bid = $stmt->fetch(PDO::FETCH_ASSOC)['max_bid'];
        
        if ($bid_amount <= $max_bid) {
            throw new Exception('Bid amount must be higher than current maximum bid: $' . $max_bid);
        }
        
        // block data
        $timestamp = time();
        $bidData = [
            'type' => 'bid',
            'auto_id' => $auto_id,
            'customer_id' => $customer_id,
            'bid_amount' => $bid_amount,
            'timestamp' => $timestamp
        ];
        
        // Mine block and add to blockchain - all checks passed now
        $blockHash = $blockchain->mineBlock($bidData, $auto_id, $customer_id);
        
        // Insert bid record and associate blockchain hash
        $stmt = $pdo->prepare("
            INSERT INTO bids (auto_id, customer_id, bid_amount, bid_date, block_hash) 
            VALUES (?, ?, ?, NOW(), ?)
        ");
        $stmt->execute([$auto_id, $customer_id, $bid_amount, $blockHash]);
        
        // Commit transaction
        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Bid placed successfully!',
            'block_hash' => $blockHash
        ]);
        
    } catch (PDOException $e) {
        // Rollback transaction
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        // Rollback transaction
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>