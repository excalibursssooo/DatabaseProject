<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        echo $user;
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            header('Location: index.php?message=Login successful!&type=success');
            exit();
        } else {
            header('Location: login.php?error=Invalid username or password');
            exit();
        }
    } catch (PDOException $e) {
        header('Location: login.php?error=Database error: ' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>