
<?php
session_start();
require_once "db.php";

// Allow admin users only
if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: login.php");
    exit;
}

// Get total users
$userResult = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalUsers = $userResult->fetch_assoc()["total"];

// Get total students
$studentResult = $conn->query("SELECT COUNT(*) AS total FROM students");
$totalStudents = $studentResult->fetch_assoc()["total"];

// Get total events
$eventResult = $conn->query("SELECT COUNT(*) AS total FROM events");
$totalEvents = $eventResult->fetch_assoc()["total"];

// Get latest registrations
$registrationResult = $conn->query("SELECT * FROM registrations LIMIT 5");

// Record dashboard visit
$audit = $conn->prepare(
    "INSERT INTO audit_logs (user_id, action, details) VALUES (?, 'VIEW_DASHBOARD', ?)"
);
$details = "Admin viewed dashboard";
$audit->bind_param("is", $_SESSION["user_id"], $details);
$audit->execute();
$audit->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f1f4f9;
        }
        header {
            background: #243b64;
            color: white;
            padding: 18px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
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
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 18px;
        }
        .card {
            background: white;
            padding: 22px;
            border-radius: 8px;
            box-shadow: 0 2px 8px #ddd;
        }
        .number {
            font-size: 30px;
            font-weight: bold;
            color: #243b64;
        }
        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            margin-top: 20px;
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
    <h2>StudentHub Admin Dashboard</h2>
    <span>Welcome, <?= htmlspecialchars($_SESSION["username"]) ?></span>
</header>

<nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="audit.php">Audit Log</a>
    <a href="logout.php">Logout</a>
</nav>

<main>
    <h2>Dashboard Overview</h2>

    <div class="cards">
        <div class="card">
            <h3>Total Users</h3>
            <div class="number"><?= (int)$totalUsers ?></div>
        </div>

        <div class="card">
            <h3>Total Students</h3>
            <div class="number"><?= (int)$totalStudents ?></div>
        </div>

        <div class="card">
            <h3>Total Events</h3>
            <div class="number"><?= (int)$totalEvents ?></div>
        </div>
    </div>

    <h2>Latest Registrations</h2>

    <table>
        <tr>
            <th>Registration Details</th>
        </tr>

        <?php if ($registrationResult && $registrationResult->num_rows > 0): ?>
            <?php while ($row = $registrationResult->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php
                        foreach ($row as $key => $value) {
                            echo "<strong>" . htmlspecialchars((string)$key) . ":</strong> ";
                            echo htmlspecialchars((string)($value ?? ""));
                            echo " &nbsp; ";
                        }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td>No registrations found.</td>
            </tr>
        <?php endif; ?>
    </table>
</main>

</body>
</html>
