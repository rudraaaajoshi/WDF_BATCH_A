
<?php
include "db.php";

$message = "";

// Delete student
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])) {
    $id = filter_var($_POST["delete_id"], FILTER_VALIDATE_INT);

    if ($id && $id > 0) {
        $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $message = $stmt->affected_rows > 0
            ? "Student deleted successfully!"
            : "Student not found or could not be deleted.";

        $stmt->close();
    } else {
        $message = "Invalid student ID.";
    }
}

// Search and filter
$search = trim($_GET["search"] ?? "");
$course = trim($_GET["course"] ?? "");

$sql = "SELECT student_id, full_name, email, course FROM students WHERE 1=1";
$params = [];
$types = "";

if ($search !== "") {
    $sql .= " AND (full_name LIKE ? OR email LIKE ?)";
    $term = "%" . $search . "%";
    $params[] = $term;
    $params[] = $term;
    $types .= "ss";
}

if ($course !== "") {
    $sql .= " AND course = ?";
    $params[] = $course;
    $types .= "s";
}

$sql .= " ORDER BY student_id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$courses = $conn->query(
    "SELECT DISTINCT course FROM students ORDER BY course"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f5fa;
            padding: 25px;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }
        h1 { color: #234a84; }
        input, select, button, .add-btn {
            padding: 10px;
            margin: 5px 3px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button, .add-btn {
            background: #2459a9;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        .delete-btn { background: #d93636; }
        .edit-btn {
            background: #16834a;
            color: white;
            padding: 7px 10px;
            text-decoration: none;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th { background: #2459a9; color: white; }
        tr:nth-child(even) { background: #f7f9fc; }
        .message {
            padding: 12px;
            background: #e0f5e7;
            color: #176b36;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Student Management System</h1>

    <?php if ($message !== ""): ?>
        <p class="message">
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <a class="add-btn" href="add.php">+ Add New Student</a>

    <form method="GET">
        <input type="text" name="search"
               placeholder="Search by name or email"
               value="<?= htmlspecialchars($search, ENT_QUOTES, "UTF-8") ?>">

        <select name="course">
            <option value="">All Courses</option>
            <?php while ($c = $courses->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($c["course"], ENT_QUOTES, "UTF-8") ?>"
                    <?= $course === $c["course"] ? "selected" : "" ?>>
                    <?= htmlspecialchars($c["course"], ENT_QUOTES, "UTF-8") ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Search / Filter</button>
        <a href="index.php">Reset</a>
    </form>

    <p>Students shown: <?= $result->num_rows ?></p>

    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Course</th>
            <th>Actions</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($student = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$student["student_id"] ?></td>
                    <td><?= htmlspecialchars($student["full_name"], ENT_QUOTES, "UTF-8") ?></td>
                    <td><?= htmlspecialchars($student["email"], ENT_QUOTES, "UTF-8") ?></td>
                    <td><?= htmlspecialchars($student["course"], ENT_QUOTES, "UTF-8") ?></td>
                    <td>
                        <a class="edit-btn"
                           href="edit.php?id=<?= (int)$student["student_id"] ?>">Edit</a>

                        <form method="POST" style="display:inline"
                              onsubmit="return confirm('Are you sure you want to delete this student?');">
                            <input type="hidden" name="delete_id"
                                   value="<?= (int)$student["student_id"] ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No students found.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
