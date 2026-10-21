
<?php
include "db.php";

$message = "";

$id = filter_var($_GET["id"] ?? $_POST["id"] ?? null, FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $conn->close();
    die("Invalid student ID. Please go back to the student list.");
}

// Update student
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
            "UPDATE students SET name = ?, email = ?, course = ? WHERE id = ?"
        );
        $stmt->bind_param("sssi", $name, $email, $course, $id);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.php");
            exit;
        } else {
            $message = "Failed to update student.";
            $stmt->close();
        }
    }
}

// Get existing student details
$stmt = $conn->prepare(
    "SELECT name, email, course FROM students WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    $conn->close();
    die("Student not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student["name"] = $name;
    $student["email"] = $email;
    $student["course"] = $course;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
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
            background: #16834a;
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
    <h2>Edit Student</h2>

    <?php if ($message !== ""): ?>
        <p class="message">
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="id" value="<?= (int)$id ?>">

        <label>Student Name</label>
        <input type="text" name="name" required maxlength="100"
               value="<?= htmlspecialchars($student["name"], ENT_QUOTES, "UTF-8") ?>">

        <label>Email</label>
        <input type="email" name="email" required maxlength="100"
               value="<?= htmlspecialchars($student["email"], ENT_QUOTES, "UTF-8") ?>">

        <label>Course</label>
        <select name="course" required>
            <?php foreach (["BCA", "BBA", "BSc IT", "BCom"] as $item): ?>
                <option value="<?= $item ?>"
                    <?= $student["course"] === $item ? "selected" : "" ?>>
                    <?= $item ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Update Student</button>
    </form>

    <a href="index.php">← Back to Student List</a>
</div>
</body>
</html>

<?php $conn->close(); ?>

