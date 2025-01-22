<?php
session_start();
include 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $hourly_rate = $_POST['hourly_rate'];
    $role = $_POST['role'];

    $sql = "INSERT INTO employees (name, email, password, hourly_rate, role) VALUES ('$name', '$email', '$password', '$hourly_rate', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo "New employee created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Employee</title>
</head>
<body>
    <h2>Register Employee</h2>
    <form method="post" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <label for="hourly_rate">Hourly Rate:</label>
        <input type="number" step="0.01" id="hourly_rate" name="hourly_rate" required><br><br>
        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="admin">Admin</option>
            <option value="employee">Employee</option>
        </select><br><br>
        <input type="submit" value="Register">
    </form>
</body>
</html>
