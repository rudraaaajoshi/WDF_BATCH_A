<?php
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if ($name === "" || $email === "" || $phone === "") {
        $message = "All fields are required.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $message = "Phone number must contain exactly 10 digits.";
        $messageType = "error";
    } else {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . "students.csv";
        $file = fopen($filePath, "a");

        if ($file === false) {
            $message = "Unable to open the CSV file. Check folder permissions.";
            $messageType = "error";
        } else {
            if (flock($file, LOCK_EX)) {
                // Protect CSV users from spreadsheet formula injection.
                $csvName = preg_match('/^[=+\-@]/', $name) ? "'" . $name : $name;
                $csvEmail = preg_match('/^[=+\-@]/', $email) ? "'" . $email : $email;

                $saved = fputcsv($file, [$csvName, $csvEmail, $phone]);
                fflush($file);
                flock($file, LOCK_UN);

                if ($saved !== false) {
                    $message = "Registration successful! Your details have been saved.";
                    $messageType = "success";
                    // Clear the form after a successful save.
                    $name = $email = $phone = "";
                } else {
                    $message = "Unable to save your record. Please try again.";
                    $messageType = "error";
                }
            } else {
                $message = "Unable to lock the CSV file. Please try again.";
                $messageType = "error";
            }
            fclose($file);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Registration - Practical 7</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 30px 16px;
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            color: #222;
        }
        .container {
            max-width: 480px;
            margin: 20px auto;
            padding: 28px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,.08);
        }
        h1 {
            margin-top: 0;
            text-align: center;
            color: #174a7e;
            font-size: 27px;
        }
        p.subtitle { text-align: center; color: #555; }
        label { display: block; margin-top: 16px; font-weight: bold; }
        input {
            display: block;
            width: 100%;
            margin-top: 6px;
            padding: 11px;
            border: 1px solid #bbb;
            border-radius: 5px;
            font-size: 16px;
        }
        input:focus { outline: 2px solid #9bc5ed; border-color: #174a7e; }
        button {
            width: 100%;
            margin-top: 22px;
            padding: 12px;
            border: 0;
            border-radius: 5px;
            background: #174a7e;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover { background: #10375d; }
        .message { padding: 11px; border-radius: 5px; text-align: center; }
        .success { background: #e6f6e9; color: #17652a; }
        .error { background: #fdeaea; color: #a21b1b; }
        .note { margin-top: 18px; font-size: 13px; color: #666; line-height: 1.5; }
    </style>
</head>
<body>
    <main class="container">
        <h1>StudentHub Registration</h1>
        <p class="subtitle">Practical 7: PHP Form Processing</p>

        <?php if ($message !== ""): ?>
            <p class="message <?php echo htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="name">Student Name:</label>
            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your name"
                value="<?php echo htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                required
            >

            <label for="email">Email Address:</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                required
            >

            <label for="phone">Phone Number:</label>
            <input
                type="tel"
                id="phone"
                name="phone"
                placeholder="Enter 10-digit phone number"
                value="<?php echo htmlspecialchars($phone ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                inputmode="numeric"
                maxlength="10"
                pattern="[0-9]{10}"
                required
            >

            <button type="submit">Register</button>
        </form>

        <p class="note">
            Your details are validated by PHP on the server and saved in
            <strong>students.csv</strong> after a successful submission.
        </p>
    </main>
</body>
</html>
