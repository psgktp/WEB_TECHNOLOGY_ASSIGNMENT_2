<?php
require "db.php";

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT) ?? filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id) {
    exit("Invalid student ID.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirm_delete"])) {
    // Delete student - foreign key cascade deletes associated files
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT name, roll_no, email, course FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    exit("Student not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student Record</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="navbar">
        <a href="index.php" class="nav-brand">
            <span style="font-size: 22px;">&#127891;</span>
            <span>Student Management System</span>
        </a>
        <a href="index.php" class="btn btn-secondary">
            &larr; Back to Student List
        </a>
    </header>

    <main class="container">
        <div class="form-card" style="text-align: center;">
            <div class="stat-icon red" style="margin: 0 auto 16px; width: 64px; height: 64px; background: #fee2e2; color: #dc2626; font-size: 32px;">
                &#9888;
            </div>

            <h2 style="color: var(--secondary);">Confirm Record Deletion</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin: 10px 0 24px;">
                Are you sure you want to permanently delete the profile for:<br>
                <strong style="color: var(--secondary); font-size: 16px;">
                    <?= htmlspecialchars($student["name"]) ?> (Roll No: <?= htmlspecialchars($student["roll_no"]) ?>)
                </strong><br>
                <span style="font-size: 12px; color: #dc2626;">This action will also remove all associated uploaded verification documents.</span>
            </p>

            <form method="POST">
                <input type="hidden" name="id" value="<?= (int)$id ?>">
                <input type="hidden" name="confirm_delete" value="1">
                
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button type="submit" class="btn btn-primary" style="background: #dc2626; padding: 12px 24px;">
                        <span>&#128465;</span> Yes, Delete Record
                    </button>
                    <a href="index.php" class="btn btn-secondary" style="padding: 12px 24px;">
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
