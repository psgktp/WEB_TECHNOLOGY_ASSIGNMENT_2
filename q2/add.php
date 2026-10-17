<?php
require "db.php";

$message = "";
$messageType = "danger";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $roll = trim($_POST["roll_no"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = $_POST["course"] ?? "";

    $courses = ["CSE", "IT", "AIML", "ECE"];

    if (
        $name === "" || strlen($name) > 100 ||
        $roll === "" || strlen($roll) > 30 || !ctype_digit($roll) ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 150 ||
        !in_array($course, $courses, true)
    ) {
        $message = "Please enter valid student details. Roll number must be numeric.";
    } else {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO students (name, roll_no, email, course, department)
                 VALUES (?, ?, ?, ?, ?)"
            );

            // Department matches course
            $dept = $course;
            $stmt->bind_param("sssss", $name, $roll, $email, $course, $dept);
            $stmt->execute();

            $studentId = $conn->insert_id;
            header("Location: upload.php?id=" . $studentId . "&new=1");
            exit();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $message = "Error: Institutional email or roll number already exists.";
            } else {
                error_log($e->getMessage());
                $message = "Unable to register student record.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="navbar">
        <a href="index.php" class="nav-brand">
            <span style="font-size: 22px;">&#127891;</span>
            <span>Student Management System</span>
        </a>
        <a href="index.php" class="btn btn-secondary">
            &larr; Back to Directory
        </a>
    </header>

    <main class="container">
        <div class="form-card">
            <div style="text-align: center; margin-bottom: 24px;">
                <div class="stat-icon blue" style="margin: 0 auto 12px; width: 56px; height: 56px;">&#128100;</div>
                <h2>Add Student Profile</h2>
                <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">
                    Enter verified student information to register into MySQL
                </p>
            </div>

            <?php if ($message) { ?>
                <div class="alert alert-danger">
                    <span>&#9888;</span>
                    <div><?= htmlspecialchars($message) ?></div>
                </div>
            <?php } ?>

            <form method="POST">
                <div class="form-group">
                    <label for="name">Student Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           placeholder="e.g. Aarav Sharma" maxlength="100" required autofocus>
                </div>

                <div class="form-group">
                    <label for="roll_no">Roll Number (Numeric)</label>
                    <input type="text" id="roll_no" name="roll_no" class="form-control" 
                           placeholder="e.g. 1006" maxlength="30" required>
                </div>

                <div class="form-group">
                    <label for="email">Institutional Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="e.g. aarav@example.edu" maxlength="150" required>
                </div>

                <div class="form-group">
                    <label for="course">Academic Course / Department</label>
                    <select id="course" name="course" class="form-control" required>
                        <option value="">Select Degree Course</option>
                        <option value="CSE">Computer Science &amp; Engineering (CSE)</option>
                        <option value="IT">Information Technology (IT)</option>
                        <option value="AIML">Artificial Intelligence &amp; Data Science (AIML)</option>
                        <option value="ECE">Electronics &amp; Communication (ECE)</option>
                    </select>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 28px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px;">
                        <span>&#10003;</span> Save &amp; Upload Proofs
                    </button>
                    <a href="index.php" class="btn btn-secondary" style="padding: 12px 20px;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2026 Student Management System</p>
    </footer>

</body>
</html>
