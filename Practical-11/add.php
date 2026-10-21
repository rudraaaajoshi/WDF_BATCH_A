
<?php
include "db.php";

$message = "";
$name = "";
$email = "";
$course = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");

    if ($name === "" || $email === "" || $course === "") {
        $message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO students (name, email, course) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $name, $email, $course);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.php");
            exit;
        } else {
            $message = "Failed to add student. Please try again.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f5fa;
            padding: 30px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }
        input, select, button {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            margin: 8px 0;
        }
        button {
            background: #2459a9;
            color: white;
            border: none;
            cursor: pointer;
        }
        .message { color: red; }
        a { text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <h2>Add New Student</h2>

    <?php if ($message !== ""): ?>
        <p class="message">
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label>Student Name</label>
        <input type="text" name="name" required maxlength="100"
               placeholder="Enter student name"
               value="<?= htmlspecialchars($name, ENT_QUOTES, "UTF-8") ?>">

        <label>Email</label>
        <input type="email" name="email" required maxlength="100"
               placeholder="Enter email"
               value="<?= htmlspecialchars($email, ENT_QUOTES, "UTF-8") ?>">

        <label>Course</label>
        <select name="course" required>
            <option value="">Select Course</option>
            <?php foreach (["BCA", "BBA", "BSc IT", "BCom"] as $item): ?>
                <option value="<?= $item ?>"
                    <?= $course === $item ? "selected" : "" ?>>
                    <?= $item ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Add Student</button>
    </form>

    <a href="index.php">← Back to Student List</a>
</div>
</body>
</html>
