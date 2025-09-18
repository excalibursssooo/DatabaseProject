<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip_code = $_POST['zip_code'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    try {
        $stmt = $pdo->prepare("CALL AddCustomer(?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $address, $city, $state, $zip_code, $email, $phone]);
        
        header('Location: index.php?message=Customer added successfully!&type=success');
        exit();
    } catch (PDOException $e) {
        $errorMessage = 'Error: ' . $e->getMessage();
        header('Location: add_customer.php?message=' . urlencode($errorMessage) . '&type=error');
        exit();
    }
} else {
    header('Location: add_customer.php');
    exit();
}
?>