<?php
require __DIR__ . "/db.php";

try {
    $stmt = $pdo->prepare(
        "SELECT student_id, full_name, email FROM students ORDER BY student_id"
    );
    $stmt->execute();
    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    http_response_code(500);
    exit("Query failed. Check that the SQL setup completed successfully.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - PDO Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f4f8; padding: 24px; color: #222; }
        main { max-width: 800px; margin: auto; background: white; padding: 24px; border-radius: 10px; }
        h1 { color: #174a7e; }
        .success { padding: 12px; background: #e7f6eb; color: #17652a; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { text-align: left; padding: 10px; border: 1px solid #ddd; }
        th { background: #174a7e; color: white; }
    </style>
</head>
<body>
<main>
    <h1>StudentHub Database Test</h1>
    <p class="success">Connected successfully to the studenthub database using PHP PDO!</p>
    <h2>Students from MySQL</h2>
    <?php if (count($students) > 0): ?>
    <table>
        <tr><th>ID</th><th>Full name</th><th>Email</th></tr>
        <?php foreach ($students as $student): ?>
        <tr>
            <td><?php echo htmlspecialchars((string)$student["student_id"], ENT_QUOTES, "UTF-8"); ?></td>
            <td><?php echo htmlspecialchars($student["full_name"], ENT_QUOTES, "UTF-8"); ?></td>
            <td><?php echo htmlspecialchars($student["email"], ENT_QUOTES, "UTF-8"); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p>No students found yet. Run studenthub.sql in phpMyAdmin first.</p>
    <?php endif; ?>
</main>
</body>
</html>
