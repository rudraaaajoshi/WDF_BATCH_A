// Practical 4 - JavaScript, DOM Manipulation, Event Handling and localStorage
// This one file is used by all the pages of StudentHub Portal.
// Each part first checks "does this element exist on this page?" before using it.


// ---------- 1. DARK MODE BUTTON (works on every page) ----------

// create a button using JavaScript (DOM manipulation)
var themeBtn = document.createElement("button");
themeBtn.id = "themeBtn";
document.body.appendChild(themeBtn);

// when page loads, check what the user chose last time (localStorage)
if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark");
    themeBtn.textContent = "Light Mode";
} else {
    themeBtn.textContent = "Dark Mode";
}

// when the button is clicked, switch the theme and remember it
themeBtn.addEventListener("click", function () {
    document.body.classList.toggle("dark");

    if (document.body.classList.contains("dark")) {
        localStorage.setItem("theme", "dark");
        themeBtn.textContent = "Light Mode";
    } else {
        localStorage.setItem("theme", "light");
        themeBtn.textContent = "Dark Mode";
    }
});


// ---------- 2. LOGIN PAGE ----------

var loginForm = document.getElementById("loginForm");

if (loginForm) {
    var studentId = document.getElementById("studentid");
    var password = document.getElementById("password");
    var remember = document.getElementById("remember");
    var showPass = document.getElementById("showPass");

    // if student ID was saved before, fill it automatically
    var savedId = localStorage.getItem("savedStudentId");
    if (savedId) {
        studentId.value = savedId;
        remember.checked = true;
    }

    // show / hide password
    showPass.addEventListener("click", function () {
        if (showPass.checked) {
            password.type = "text";
        } else {
            password.type = "password";
        }
    });

    // when login form is submitted
    loginForm.addEventListener("submit", function (event) {
        event.preventDefault(); // stop page from reloading

        if (remember.checked) {
            localStorage.setItem("savedStudentId", studentId.value);
        } else {
            localStorage.removeItem("savedStudentId");
        }

        alert("Welcome, " + studentId.value + "! Login successful.");
    });
}


// ---------- 3. REGISTRATION PAGE ----------

var regForm = document.getElementById("regForm");

if (regForm) {
    var address = document.getElementById("address");
    var charCount = document.getElementById("charCount");

    // show how many characters typed in address box (live)
    address.addEventListener("input", function () {
        charCount.textContent = address.value.length + " characters";
    });

    // save the student data in localStorage when form is submitted
    regForm.addEventListener("submit", function (event) {
        event.preventDefault();

        var student = {
            name: document.getElementById("name").value,
            enrollment: document.getElementById("enrollment").value,
            email: document.getElementById("email").value,
            mobile: document.getElementById("mobile").value,
            semester: document.getElementById("semester").value,
            course: document.getElementById("course").value
        };

        // localStorage can store only text, so convert object to text
        localStorage.setItem("student", JSON.stringify(student));

        alert("Registration saved for " + student.name);
        regForm.reset();
        charCount.textContent = "0 characters";
    });
}


// ---------- 4. ATTENDANCE PAGE ----------

var attTable = document.getElementById("attTable");

if (attTable) {
    var rows = attTable.getElementsByTagName("tr");

    // calculate percentage and status for every row (skip row 0 = heading)
    for (var i = 1; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName("td");

        var total = Number(cells[1].textContent);
        var attended = Number(cells[2].textContent);
        var percent = Math.round((attended / total) * 100);

        cells[3].textContent = percent + "%";

        if (percent >= 75) {
            cells[4].textContent = "Eligible";
            cells[4].style.color = "green";
        } else {
            cells[4].textContent = "Low Attendance";
            cells[4].style.color = "red";
        }
    }

    // search box: show only the subjects that match the typed text
    var searchBox = document.getElementById("searchBox");

    searchBox.addEventListener("keyup", function () {
        var text = searchBox.value.toLowerCase();

        for (var j = 1; j < rows.length; j++) {
            var subject = rows[j].getElementsByTagName("td")[0].textContent.toLowerCase();

            if (subject.indexOf(text) > -1) {
                rows[j].style.display = "";
            } else {
                rows[j].style.display = "none";
            }
        }
    });
}


// ---------- 5. FAQ PAGE (click question to show / hide answer) ----------

var questions = document.getElementsByClassName("question");

for (var k = 0; k < questions.length; k++) {
    // hide all answers in the beginning
    questions[k].nextElementSibling.style.display = "none";

    questions[k].addEventListener("click", function () {
        var answer = this.nextElementSibling;

        if (answer.style.display === "none") {
            answer.style.display = "block";
        } else {
            answer.style.display = "none";
        }
    });
}
