
<?php
require_once "db.php";

$username = "admin";
$email = "admin@studenthub.com";
$password = password_hash("Admin@123", PASSWORD_DEFAULT);
$role = "admin";

$stmt = $conn->prepare(
    "INSERT INTO users (username, email, password, role)
     VALUES (?, ?, ?, ?)"
);

$stmt->bind_param("ssss", $username, $email, $password, $role);

if ($stmt->execute()) {
    echo "Admin account created successfully!";
    echo "<br>Username: admin";
    echo "<br>Password: Admin@123";
} else {
    echo "Error: " . htmlspecialchars($stmt->error);
}

$stmt->close();
$conn->close();
?>
