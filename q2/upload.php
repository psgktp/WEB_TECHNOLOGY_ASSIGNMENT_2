<?php
require "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id) {
    exit("Invalid student ID.");
}

$stmt = $conn->prepare("SELECT id, name, roll_no, course FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    exit("Student not found.");
}

$message = "";
$messageType = "danger";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $file = $_FILES["document"] ?? null;

    if (!$file || $file["error"] !== UPLOAD_ERR_OK) {
        $message = "Please select a valid file to upload.";
    } elseif ($file["size"] > 5 * 1024 * 1024) {
        $message = "File size exceeds the 5 MB limit.";
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file["tmp_name"]);

        $allowed = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "application/pdf" => "pdf"
        ];

        if (!isset($allowed[$mime])) {
            $message = "Invalid file type. Only JPG, PNG, and PDF files are allowed.";
        } else {
            $stored = bin2hex(random_bytes(16)) . "." . $allowed[$mime];
            $path = UPLOAD_DIR . $stored;

            if (move_uploaded_file($file["tmp_name"], $path)) {
                try {
                    $original = basename($file["name"]);
                    $size = (int)$file["size"];
                    $category = strpos($mime, "image/") === 0 ? "profile_photo" : "document";

                    $stmt = $conn->prepare(
                        "INSERT INTO student_files
                         (student_id, original_name, stored_name, file_type, file_category, file_size, file_path)
                         VALUES (?, ?, ?, ?, ?, ?, ?)"
                    );

                    $relPath = "uploads/" . $stored;
                    $stmt->bind_param(
                        "issssis", $id, $original, $stored, $mime, $category, $size, $relPath
                    );
                    $stmt->execute();

                    $message = "Document uploaded and verified successfully!";
                    $messageType = "success";
                } catch (Throwable $e) {
                    if (is_file($path)) {
                        unlink($path);
                    }
                    error_log($e->getMessage());
                    $message = "Database error: Could not save file metadata.";
                }
            } else {
                $message = "Server error: Unable to move uploaded file.";
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
    <title>Upload Document - <?= htmlspecialchars($student["name"]) ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dropzone-box {
            border: 2px dashed #cbd5e1;
            border-radius: var(--radius-md);
            padding: 36px 20px;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            margin-bottom: 20px;
        }
        .dropzone-box:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }
        .dropzone-icon {
            font-size: 38px;
            color: var(--primary);
            margin-bottom: 8px;
        }
        input[type="file"] {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .file-chosen-text {
            display: none;
            margin-top: 10px;
            font-weight: 700;
            color: var(--primary);
            font-size: 13px;
        }
    </style>
</head>
<body>

    <header class="navbar">
        <a href="index.php" class="nav-brand">
            <span style="font-size: 22px;">&#127891;</span>
            <span>Student Management System</span>
        </a>
        <a href="view.php?id=<?= (int)$id ?>" class="btn btn-secondary">
            &larr; View Student Profile
        </a>
    </header>

    <main class="container">
        <div class="form-card">
            <div style="text-align: center; margin-bottom: 24px;">
                <div class="stat-icon green" style="margin: 0 auto 12px; width: 56px; height: 56px;">&#11014;</div>
                <h2>Upload Document / Photo</h2>
                <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">
                    Uploading for <strong><?= htmlspecialchars($student["name"]) ?></strong> (Roll No: <?= htmlspecialchars($student["roll_no"]) ?>)
                </p>
            </div>

            <?php if ($message) { ?>
                <div class="alert alert-<?= $messageType ?>">
                    <span><?= $messageType === "success" ? "&#10003;" : "&#9888;" ?></span>
                    <div><?= htmlspecialchars($message) ?></div>
                </div>
            <?php } ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="dropzone-box">
                    <div class="dropzone-icon">&#128193;</div>
                    <h4 style="color: var(--secondary); font-size: 15px;">Choose Image or PDF Document</h4>
                    <p style="color: var(--text-muted); font-size: 12px; margin-top: 4px;">
                        Supported types: JPG, PNG, PDF &bull; Maximum file size: 5 MB
                    </p>
                    <input type="file" name="document" id="fileDoc" accept=".jpg,.jpeg,.png,.pdf" required>
                    <div id="fileChosen" class="file-chosen-text"></div>
                </div>

                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px;">
                        <span>&#11014;</span> Start Secure Upload
                    </button>
                    <a href="view.php?id=<?= (int)$id ?>" class="btn btn-secondary" style="padding: 12px 20px;">
                        Done / View
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2026 Student Management System</p>
    </footer>

    <script>
        document.getElementById('fileDoc').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const f = this.files[0];
                const kb = (f.size / 1024).toFixed(1);
                const el = document.getElementById('fileChosen');
                el.textContent = "Selected: " + f.name + " (" + kb + " KB)";
                el.style.display = "block";
            }
        });
    </script>
</body>
</html>
