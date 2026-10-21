
<?php
session_start();
require_once "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, username, password, role FROM users WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];
            
            $update = $conn->prepare(
                "UPDATE users SET last_login = NOW() WHERE id = ?"
            );
            $update->bind_param("i", $user["id"]);
            $update->execute();
            $update->close();

            $audit = $conn->prepare(
                "INSERT INTO audit_logs (user_id, action, details)
                 VALUES (?, 'LOGIN', ?)"
            );
            $details = "User logged in";
            $audit->bind_param("is", $user["id"], $details);
            $audit->execute();
            $audit->close();

            if ($user["role"] === "admin") {
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Access denied. Admin account required.";
                session_unset();
                session_destroy();
            }
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - StudentHub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f7;
            margin: 0;
            padding: 0;
        }
        .login-box {
            width: 350px;
            max-width: calc(100% - 60px);
            margin: 90px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px #ccc;
        }
        h2 {
            text-align: center;
            color: #243b64;
        }
        input {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 11px;
            background: #243b64;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .error {
            color: #c62828;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>StudentHub Admin Login</h2>

        <?php if ($error !== ""): ?>
            <p class="error">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username"
                   placeholder="Enter username" required>

            <input type="password" name="password"
                   placeholder="Enter password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
