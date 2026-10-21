
<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

require "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email or password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, username, email, password, role
             FROM users WHERE email = ?"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["last_activity"] = time();

            $update = $conn->prepare(
                "UPDATE users SET last_login = NOW() WHERE id = ?"
            );
            $update->bind_param("i", $user["id"]);
            $update->execute();
            $update->close();

            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudentHub Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f7;
            padding: 30px 15px;
        }
        .box {
            max-width: 380px;
            margin: 40px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
        }
        h2 { text-align: center; color: #234b83; }
        label { display: block; margin-top: 15px; }
        input {
            box-sizing: border-box;
            width: 100%;
            padding: 11px;
            margin-top: 6px;
        }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            color: white;
            background: #234b83;
            border: 0;
            cursor: pointer;
        }
        .error { color: #a11; }
    </style>
</head>
<body>
<div class="box">
    <h2>StudentHub Login</h2>

    <?php if ($message !== ""): ?>
        <p class="error">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password"
               name="password" required>

        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
