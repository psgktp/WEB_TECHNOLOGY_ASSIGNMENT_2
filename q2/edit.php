<?php
require "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id) {
    exit("Invalid student ID.");
}

$stmt = $conn->prepare("SELECT name, roll_no, email, course FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    exit("Student not found.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $roll = trim($_POST["roll_no"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = $_POST["course"] ?? "";

    if (
        $name === "" || strlen($name) > 100 ||
        $roll === "" || strlen($roll) > 30 || !ctype_digit($roll) ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !in_array($course, ["CSE", "IT", "AIML", "ECE"], true)
    ) {
        $message = "Please enter valid student details. Roll number must be numeric.";
    } else {
        try {
            $dept = $course;
            $stmt = $conn->prepare(
                "UPDATE students
                 SET name = ?, roll_no = ?, email = ?, course = ?, department = ?
                 WHERE id = ?"
            );

            $stmt->bind_param("sssssi", $name, $roll, $email, $course, $dept, $id);
            $stmt->execute();

            header("Location: index.php");
            exit();
        } catch (mysqli_sql_exception $e) {
            $message = $e->getCode() === 1062
                ? "Email or roll number already exists."
                : "Update operation failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Student Management System</title>
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
                <div class="stat-icon purple" style="margin: 0 auto 12px; width: 56px; height: 56px;">&#9998;</div>
                <h2>Edit Student Profile</h2>
                <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">
                    Update academic records for Student ID #<?= (int)$id ?>
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
                           maxlength="100" required value="<?= htmlspecialchars($student["name"]) ?>">
                </div>

                <div class="form-group">
                    <label for="roll_no">Roll Number (Numeric)</label>
                    <input type="text" id="roll_no" name="roll_no" class="form-control" 
                           maxlength="30" required value="<?= htmlspecialchars($student["roll_no"]) ?>">
                </div>

                <div class="form-group">
                    <label for="email">Institutional Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           maxlength="150" required value="<?= htmlspecialchars($student["email"]) ?>">
                </div>

                <div class="form-group">
                    <label for="course">Academic Course / Department</label>
                    <select id="course" name="course" class="form-control" required>
                        <?php foreach (["CSE", "IT", "AIML", "ECE"] as $c) { ?>
                            <option value="<?= $c ?>" <?= $student["course"] === $c ? "selected" : "" ?>>
                                <?= $c ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 28px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px;">
                        <span>&#10003;</span> Update Student Profile
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
