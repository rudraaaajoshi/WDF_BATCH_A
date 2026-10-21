
<?php
require_once "db.php";

$username = "admin";
$newPassword = "Admin@123";

$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "UPDATE users SET password = ?, role = 'admin' WHERE username = ?"
);
$stmt->bind_param("ss", $hash, $username);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Admin password updated successfully.";
} else {
    echo "No user named 'admin' was updated. Check the username in your users table.";
}

$stmt->close();
$conn->close();
?>
