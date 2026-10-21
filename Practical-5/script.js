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

    // ----- Practical 5: validation -----

    // show an error message under a field and make its border red
    function showError(id, message) {
        document.getElementById(id + "Error").textContent = message;
        var field = document.getElementById(id);
        if (field) {
            field.classList.add("invalid");
            field.classList.remove("valid");
        }
    }

    // remove the error message and make border green
    function clearError(id) {
        document.getElementById(id + "Error").textContent = "";
        var field = document.getElementById(id);
        if (field) {
            field.classList.remove("invalid");
            field.classList.add("valid");
        }
    }

    // each function returns true if the field is OK, false if not
    function checkName() {
        var v = document.getElementById("name").value.trim();
        if (v === "") { showError("name", "Please enter your full name."); return false; }
        if (v.length < 3) { showError("name", "Name must be at least 3 letters."); return false; }
        if (!/^[A-Za-z ]+$/.test(v)) { showError("name", "Name can have only letters and spaces."); return false; }
        clearError("name");
        return true;
    }

    function checkEnrollment() {
        var v = document.getElementById("enrollment").value.trim();
        if (v === "") { showError("enrollment", "Please enter your enrollment number."); return false; }
        if (!/^[0-9]{2}[A-Za-z]{2,4}[0-9]{3}$/.test(v)) {
            showError("enrollment", "Format should be like 26IT001 (2 digits, 2-4 letters, 3 digits).");
            return false;
        }
        clearError("enrollment");
        return true;
    }

    function checkEmail() {
        var v = document.getElementById("email").value.trim();
        if (v === "") { showError("email", "Please enter your email."); return false; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) { showError("email", "Enter a valid email like name@example.com."); return false; }
        clearError("email");
        return true;
    }

    function checkMobile() {
        var v = document.getElementById("mobile").value.trim();
        if (v === "") { showError("mobile", "Please enter your mobile number."); return false; }
        if (!/^[6-9][0-9]{9}$/.test(v)) { showError("mobile", "Mobile must be 10 digits and start with 6, 7, 8 or 9."); return false; }
        clearError("mobile");
        return true;
    }

    function checkGender() {
        var chosen = document.querySelector('input[name="gender"]:checked');
        if (!chosen) { showError("gender", "Please select your gender."); return false; }
        clearError("gender");
        return true;
    }

    function checkSemester() {
        if (document.getElementById("semester").value === "") { showError("semester", "Please select a semester."); return false; }
        clearError("semester");
        return true;
    }

    function checkCourse() {
        if (document.getElementById("course").value === "") { showError("course", "Please select a course."); return false; }
        clearError("course");
        return true;
    }

    function checkAddress() {
        var v = document.getElementById("address").value.trim();
        if (v.length < 10) { showError("address", "Address must be at least 10 characters."); return false; }
        clearError("address");
        return true;
    }

    function checkPassword() {
        var v = document.getElementById("password").value;
        if (v === "") { showError("password", "Please create a password."); return false; }
        if (v.length < 6) { showError("password", "Password must be at least 6 characters."); return false; }
        if (!/[0-9]/.test(v) || !/[A-Za-z]/.test(v)) { showError("password", "Password must contain both letters and numbers."); return false; }
        clearError("password");
        return true;
    }

    function checkConfirm() {
        var v = document.getElementById("confirm").value;
        if (v === "") { showError("confirm", "Please confirm your password."); return false; }
        if (v !== document.getElementById("password").value) { showError("confirm", "Passwords do not match."); return false; }
        clearError("confirm");
        return true;
    }

    // check each field when the user leaves it (blur) - gives instant feedback
    document.getElementById("name").addEventListener("blur", checkName);
    document.getElementById("enrollment").addEventListener("blur", checkEnrollment);
    document.getElementById("email").addEventListener("blur", checkEmail);
    document.getElementById("mobile").addEventListener("blur", checkMobile);
    document.getElementById("semester").addEventListener("change", checkSemester);
    document.getElementById("course").addEventListener("change", checkCourse);
    document.getElementById("address").addEventListener("blur", checkAddress);
    document.getElementById("password").addEventListener("blur", checkPassword);
    document.getElementById("confirm").addEventListener("blur", checkConfirm);
    document.getElementById("male").addEventListener("change", checkGender);
    document.getElementById("female").addEventListener("change", checkGender);
    document.getElementById("other").addEventListener("change", checkGender);

    // when the form is submitted
    regForm.addEventListener("submit", function (event) {
        event.preventDefault();

        // run ALL checks (no shortcuts, so every error is shown together)
        var results = [
            checkName(), checkEnrollment(), checkEmail(), checkMobile(),
            checkGender(), checkSemester(), checkCourse(), checkAddress(),
            checkPassword(), checkConfirm()
        ];

        var successMsg = document.getElementById("successMsg");

        if (results.indexOf(false) !== -1) {
            // something is wrong: go to the first field with an error
            successMsg.textContent = "";
            var firstBad = document.querySelector(".invalid");
            if (firstBad) {
                firstBad.focus();
            }
            return;
        }

        // all fine: save the student data in localStorage (password is NOT saved)
        var student = {
            name: document.getElementById("name").value.trim(),
            enrollment: document.getElementById("enrollment").value.trim().toUpperCase(),
            email: document.getElementById("email").value.trim(),
            mobile: document.getElementById("mobile").value.trim(),
            gender: document.querySelector('input[name="gender"]:checked').value,
            semester: document.getElementById("semester").value,
            course: document.getElementById("course").value,
            address: document.getElementById("address").value.trim()
        };

        // localStorage can store only text, so convert object to text
        localStorage.setItem("student", JSON.stringify(student));

        regForm.reset(); // this also triggers the reset handler below
        successMsg.textContent = "Registration successful! Welcome, " + student.name + ".";
    });

    // reset (Clear button): remove all error messages and colors
    regForm.addEventListener("reset", function () {
        var errors = regForm.querySelectorAll(".error");
        for (var e = 0; e < errors.length; e++) {
            errors[e].textContent = "";
        }
        var boxes = regForm.querySelectorAll(".valid, .invalid");
        for (var b = 0; b < boxes.length; b++) {
            boxes[b].classList.remove("valid", "invalid");
        }
        document.getElementById("successMsg").textContent = "";
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
