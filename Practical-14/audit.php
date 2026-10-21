
<?php
session_start();
require_once "db.php";

// Allow admin users only
if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: login.php");
    exit;
}

// Fetch audit logs with usernames
$sql = "SELECT audit_logs.id, audit_logs.action,
               audit_logs.details, audit_logs.created_at,
               users.username
        FROM audit_logs
        LEFT JOIN users ON audit_logs.user_id = users.id
        ORDER BY audit_logs.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Audit Log</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f4f9;
            margin: 0;
        }
        header {
            background: #243b64;
            color: white;
            padding: 18px 25px;
        }
        nav {
            background: white;
            padding: 15px 25px;
        }
        nav a {
            margin-right: 18px;
            color: #243b64;
            text-decoration: none;
            font-weight: bold;
        }
        main {
            padding: 25px;
        }
        .table-container {
            overflow-x: auto;
            background: white;
            padding: 15px;
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #243b64;
            color: white;
        }
    </style>
</head>
<body>

<header>
    <h2>StudentHub - Audit Log</h2>
</header>

<nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="audit.php">Audit Log</a>
    <a href="logout.php">Logout</a>
</nav>

<main>
    <h2>System Activity History</h2>

    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Action</th>
                <th>Details</th>
                <th>Date and Time</th>
            </tr>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int)$row["id"] ?></td>
                        <td><?= htmlspecialchars($row["username"] ?? "Unknown") ?></td>
                        <td><?= htmlspecialchars($row["action"]) ?></td>
                        <td><?= htmlspecialchars($row["details"] ?? "") ?></td>
                        <td><?= htmlspecialchars($row["created_at"]) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No audit logs found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</main>

</body>
</html>
