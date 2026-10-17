
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "student_portal"
);

$conn->set_charset("utf8mb4");

// Private upload directory outside htdocs.
define("UPLOAD_DIR", "C:/xampp/student_uploads/");

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0750, true);
}
?>
