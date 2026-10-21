<?php
// Practical 8: StudentHub PDO database connection
$host = "localhost";
$dbname = "studenthub";
$username = "root";
$password = ""; // Default XAMPP setup often has an empty root password.

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit("Database connection failed. Check MySQL and db.php settings.");
}
?>
