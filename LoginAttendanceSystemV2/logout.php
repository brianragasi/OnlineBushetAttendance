<?php
session_start(); // Start the session (must be on all pages using sessions)

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page (or any other page you want)
header("Location: login.php");
exit();
?>