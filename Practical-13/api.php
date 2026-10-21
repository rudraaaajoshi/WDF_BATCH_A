
<?php
header("Content-Type: application/json; charset=UTF-8");

include "db.php";

$method = $_SERVER["REQUEST_METHOD"];

function sendResponse($success, $message, $data = null, $code = 200) {
    http_response_code($code);

    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);

    exit;
}

if ($method == "GET") {
    $result = $conn->query(
        "SELECT student_id, full_name, email, course
         FROM students ORDER BY student_id DESC"
    );

    if (!$result) {
        sendResponse(false, "Could not fetch students.", null, 500);
    }

    $students = [];

    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    sendResponse(true, "Students fetched successfully.", $students);
}

if ($method == "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!is_array($input)) {
        sendResponse(false, "Invalid JSON data.", null, 400);
    }

    $action = $input["action"] ?? "";

    if ($action == "add") {
        $name = trim($input["full_name"] ?? "");
        $email = trim($input["email"] ?? "");
        $course = trim($input["course"] ?? "");

        if ($name == "" || $email == "" || $course == "") {
            sendResponse(false, "All fields are required.", null, 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(false, "Enter a valid email address.", null, 400);
        }

        $stmt = $conn->prepare(
            "INSERT INTO students (full_name, email, course)
             VALUES (?, ?, ?)"
        );

        if (!$stmt) {
            sendResponse(false, "Database query failed.", null, 500);
        }

        $stmt->bind_param("sss", $name, $email, $course);

        if ($stmt->execute()) {
            sendResponse(true, "Student added successfully.");
        } else {
            if ($stmt->errno == 1062) {
                sendResponse(false, "This email already exists.", null, 409);
            }

            sendResponse(false, "Could not add student.", null, 500);
        }
    }

    if ($action == "update") {
        $id = filter_var($input["student_id"] ?? null, FILTER_VALIDATE_INT);
        $name = trim($input["full_name"] ?? "");
        $email = trim($input["email"] ?? "");
        $course = trim($input["course"] ?? "");

        if (!$id || $id < 1 || $name == "" || $email == "" || $course == "") {
            sendResponse(false, "Enter a valid ID and complete all fields.", null, 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(false, "Enter a valid email address.", null, 400);
        }

        $stmt = $conn->prepare(
            "UPDATE students
             SET full_name = ?, email = ?, course = ?
             WHERE student_id = ?"
        );

        if (!$stmt) {
            sendResponse(false, "Database query failed.", null, 500);
        }

        $stmt->bind_param("sssi", $name, $email, $course, $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows == 0) {
                $check = $conn->prepare(
                    "SELECT student_id FROM students WHERE student_id = ?"
                );
                $check->bind_param("i", $id);
                $check->execute();
                $check->store_result();

                if ($check->num_rows == 0) {
                    $check->close();
                    sendResponse(false, "Student not found.", null, 404);
                }

                $check->close();
            }

            sendResponse(true, "Student updated successfully.");
        } else {
            if ($stmt->errno == 1062) {
                sendResponse(false, "This email already exists.", null, 409);
            }

            sendResponse(false, "Could not update student.", null, 500);
        }
    }

    if ($action == "delete") {
        $id = filter_var($input["student_id"] ?? null, FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            sendResponse(false, "Enter a valid student ID.", null, 400);
        }

        $stmt = $conn->prepare(
            "DELETE FROM students WHERE student_id = ?"
        );
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows == 0) {
                sendResponse(false, "Student not found.", null, 404);
            }

            sendResponse(true, "Student deleted successfully.");
        }

        sendResponse(false, "Could not delete student.", null, 500);
    }

    sendResponse(false, "Invalid action.", null, 400);
}

sendResponse(false, "Method not allowed.", null, 405);
?>
