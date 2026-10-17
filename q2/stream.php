<?php
require "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit("Invalid file ID.");
}

$stmt = $conn->prepare("SELECT stored_name, file_type, original_name FROM student_files WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$file = $stmt->get_result()->fetch_assoc();

if (!$file) {
    http_response_code(404);
    exit("File record not found.");
}

$path = UPLOAD_DIR . $file["stored_name"];
if (!is_file($path)) {
    http_response_code(404);
    exit("Physical file missing on disk.");
}

header("Content-Type: " . $file["file_type"]);
header("Content-Length: " . filesize($path));
header("Cache-Control: private, max-age=3600");

$download = isset($_GET["download"]) && $_GET["download"] === "1";
$disposition = $download ? "attachment" : "inline";
header("Content-Disposition: " . $disposition . '; filename="' . basename($file["original_name"]) . '"');

readfile($path);
exit();
?>
