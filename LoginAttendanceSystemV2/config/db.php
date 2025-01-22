<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "payrollsystem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set MySQL to use UTC timezone
$conn->query("SET time_zone = '+00:00'");
?>