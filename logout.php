<?php
include 'config.php';

session_destroy();
header('Location: login.php?message=You have been logged out successfully');
exit();
?>