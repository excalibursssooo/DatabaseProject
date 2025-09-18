<?php
session_start();

$host = 'localhost';
$dbname = 'car_auction';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 检查用户是否登录
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// 获取当前用户信息
// 获取当前用户信息（包含客户信息）
function getCurrentUser() {
    global $pdo;
    if (isLoggedIn()) {
        $stmt = $pdo->prepare("
            SELECT u.*, c.* 
            FROM users u 
            LEFT JOIN customers c ON u.customer_id = c.customer_id 
            WHERE u.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

// 通用函数：显示数据库结果表格
function displayResults($results, $emptyMessage = "No data found.") {
    if (count($results) > 0) {
        echo '<div class="table-responsive">';
        echo '<table class="table table-striped table-bordered">';
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
        echo '<div class="alert alert-info">' . $emptyMessage . '</div>';
    }
}

?>