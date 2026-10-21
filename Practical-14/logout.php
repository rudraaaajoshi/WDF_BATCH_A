
<?php
session_start();
require_once "db.php";

// Record logout in audit log
if (isset($_SESSION["user_id"])) {
    $userId = $_SESSION["user_id"];

    $stmt = $conn->prepare(
        "INSERT INTO audit_logs (user_id, action, details)
         VALUES (?, 'LOGOUT', ?)"
    );

    $details = "User logged out";
    $stmt->bind_param("is", $userId, $details);
    $stmt->execute();
    $stmt->close();
}

// Clear session
$_SESSION = [];
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
?>
