<?php
session_start();

// Define secure access constant for views
define('SECURE_ACCESS', true);

require __DIR__ . '/../src/db.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['level'])) {
    header("Location: ../public/login.php");
    exit;
}

require 'layout_admin.php';
?>

