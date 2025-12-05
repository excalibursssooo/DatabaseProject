<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'] ?? null;
    
    // Validate if passwords match
    if ($password !== $confirm_password) {
        header('Location: register.php?error=Passwords do not match');
        exit();
    }
    
    // validate username uniqueness
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            throw new Exception('Username already exists');
        }
        
        // validate email uniqueness
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            throw new Exception('Email already exists');
        }
        
        // create customer record
        $stmt = $pdo->prepare("
            INSERT INTO customers (first_name, last_name, email, phone, address, city, state, zip_code) 
            VALUES (?, ?, ?, ?, 'Not provided', 'Not provided', 'NA', '00000')
        ");
        $stmt->execute([$first_name, $last_name, $email, $phone]);
        $customer_id = $pdo->lastInsertId();
        
        // create user account
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, customer_id) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $customer_id]);
        
        $pdo->commit();
        
        header('Location: login.php?message=Registration successful. Please login.');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        header('Location: register.php?error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: register.php');
    exit();
}
?>