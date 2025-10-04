<?php
session_start();
include('include/config.php');

// Unset all worker session variables
unset($_SESSION['wlogin']);
unset($_SESSION['wid']);
unset($_SESSION['wname']);
unset($_SESSION['wdept']);

// Destroy the session
session_destroy();

// Redirect to login page
header('location:index.php');
exit;
?>