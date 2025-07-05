<?php
require_once 'config.php';

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Redirect if not admin
function adminOnly() {
    if (!isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit();
    }
}

// Redirect if admin tries to access user pages
function userOnly() {
    if (isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
        exit();
    }
}
?>
