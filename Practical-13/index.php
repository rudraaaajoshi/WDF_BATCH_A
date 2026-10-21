
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 13 - Student CRUD</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 25px;
        }

        .container {
            max-width: 950px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #17365d;
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }

        input, button {
            padding: 11px;
            box-sizing: border-box;
        }

        button {
            cursor: pointer;
            border: none;
            border-radius: 5px;
            color: white;
            background: #1769aa;
        }

        .actions {
            display: flex;
            gap: 5px;
        }

        .delete {
            background: #d32f2f;
        }

        .edit {
            background: #388e3c;
        }

        .cancel {
            background: #666;
        }

        .message {
            margin: 15px 0;
            font-weight: bold;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #17365d;
            color: white;
        }

        @media (max-width: 600px) {
            form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
<div class="container">
    <h1>Student Management System</h1>
    <p>Add, view, update, and delete students without refreshing the page.</p>

    <form id="studentForm">
        <input type="hidden" id="student_id">

        <input type="text" id="full_name"
               placeholder="Enter student name" required>

        <input type="email" id="email"
               placeholder="Enter email address" required>

        <input type="text" id="course"
               placeholder="Enter course (e.g. BCA)" required>

        <button type="submit" id="saveBtn">Add Student</button>
        <button type="button" id="cancelBtn"
                class="cancel" hidden>Cancel Edit</button>
    </form>

    <p id="message" class="message" aria-live="polite"></p>
    <p id="loading">Loading students...</p>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentTable"></tbody>
        </table>
    </div>
</div>

<script>
const api = "api.php";

const form = document.getElementById("studentForm");
const message = document.getElementById("message");
const loading = document.getElementById("loading");
const table = document.getElementById("studentTable");
const saveBtn = document.getElementById("saveBtn");
const cancelBtn = document.getElementById("cancelBtn");

let students = [];

function showMessage(text, isError = false) {
    message.textContent = text;
    message.style.color = isError ? "#c62828" : "#2e7d32";
}

async function requestAPI(method, data = null) {
    const options = { method };

    if (data !== null) {
        options.headers = { "Content-Type": "application/json" };
        options.body = JSON.stringify(data);
    }

    const response = await fetch(api, options);
    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || "Request failed.");
    }

    return result;
}

function addCell(row, value) {
    const cell = document.createElement("td");
    cell.textContent = value ?? "";
    row.appendChild(cell);
}

function renderStudents() {
    table.replaceChildren();

    if (students.length === 0) {
        const row = document.createElement("tr");
        const cell = document.createElement("td");
        cell.colSpan = 5;
        cell.textContent = "No students found.";
        row.appendChild(cell);
        table.appendChild(row);
        return;
    }

    students.forEach(student => {
        const row = document.createElement("tr");

        addCell(row, student.student_id);
        addCell(row, student.full_name);
        addCell(row, student.email);
        addCell(row, student.course);

        const cell = document.createElement("td");
        const actions = document.createElement("div");
        actions.className = "actions";

        const editBtn = document.createElement("button");
        editBtn.textContent = "Edit";
        editBtn.className = "edit";
        editBtn.type = "button";
        editBtn.addEventListener("click", () => editStudent(student));

        const deleteBtn = document.createElement("button");
        deleteBtn.textContent = "Delete";
        deleteBtn.className = "delete";
        deleteBtn.type = "button";
        deleteBtn.addEventListener("click", () => deleteStudent(student.student_id));

        actions.append(editBtn, deleteBtn);
        cell.appendChild(actions);
        row.appendChild(cell);
        table.appendChild(row);
    });
}

async function loadStudents() {
    loading.hidden = false;
    loading.textContent = "Loading students...";

    try {
        const result = await requestAPI("GET");
        students = result.data || [];
        renderStudents();
        loading.hidden = true;
    } catch (error) {
        loading.textContent = "Could not load students.";
        showMessage(error.message, true);
    }
}

form.addEventListener("submit", async function(event) {
    event.preventDefault();

    const id = document.getElementById("student_id").value;

    const data = {
        action: id ? "update" : "add",
        full_name: document.getElementById("full_name").value.trim(),
        email: document.getElementById("email").value.trim(),
        course: document.getElementById("course").value.trim()
    };

    if (id) {
        data.student_id = Number(id);
    }

    saveBtn.disabled = true;
    showMessage("Saving...");

    try {
        const result = await requestAPI("POST", data);
        showMessage(result.message);
        form.reset();
        document.getElementById("student_id").value = "";
        saveBtn.textContent = "Add Student";
        cancelBtn.hidden = true;
        await loadStudents();
    } catch (error) {
        showMessage(error.message, true);
    } finally {
        saveBtn.disabled = false;
    }
});

function editStudent(student) {
    document.getElementById("student_id").value = student.student_id;
    document.getElementById("full_name").value = student.full_name;
    document.getElementById("email").value = student.email;
    document.getElementById("course").value = student.course;

    saveBtn.textContent = "Update Student";
    cancelBtn.hidden = false;
    showMessage("Editing student ID " + student.student_id);
    form.scrollIntoView({ behavior: "smooth" });
}

cancelBtn.addEventListener("click", function() {
    form.reset();
    document.getElementById("student_id").value = "";
    saveBtn.textContent = "Add Student";
    cancelBtn.hidden = true;
    showMessage("");
});

async function deleteStudent(id) {
    if (!confirm("Are you sure you want to delete this student?")) {
        return;
    }

    showMessage("Deleting...");

    try {
        const result = await requestAPI("POST", {
            action: "delete",
            student_id: Number(id)
        });

        showMessage(result.message);
        await loadStudents();
    } catch (error) {
        showMessage(error.message, true);
    }
}

loadStudents();
</script>
</body>
</html>
    