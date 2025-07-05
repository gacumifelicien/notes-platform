<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

session_start();
session_unset();
session_destroy();

header('Location: ' . BASE_URL . '/login.php');
exit();
?>