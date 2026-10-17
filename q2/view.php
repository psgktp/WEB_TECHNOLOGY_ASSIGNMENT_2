<?php
require "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id) {
    exit("Invalid student ID.");
}

$stmt = $conn->prepare("SELECT id, name, roll_no, email, course FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    exit("Student not found.");
}

// Fetch all uploaded documents & images for this student
$stmt = $conn->prepare("SELECT id, original_name, stored_name, file_type, file_size, uploaded_at FROM student_files WHERE student_id = ? ORDER BY id ASC");
$stmt->bind_param("i", $id);
$stmt->execute();
$filesResult = $stmt->get_result();

// Check if student has a profile image for hero banner
$stmt = $conn->prepare("SELECT id FROM student_files WHERE student_id = ? AND file_type LIKE 'image/%' ORDER BY id ASC LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$avatarFile = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($student["name"]) ?> - Profile &amp; Documents</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-hero {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 28px;
            box-shadow: var(--shadow-sm);
        }
        .hero-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #eff6ff;
            box-shadow: var(--shadow-md);
        }
        .hero-initials {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 800;
            box-shadow: var(--shadow-md);
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 14px;
        }
        .info-pill {
            background: #f8fafc;
            border: 1px solid var(--border);
            padding: 10px 14px;
            border-radius: var(--radius-sm);
        }
        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 600;
            color: var(--text-muted);
        }
        .info-val {
            font-size: 14px;
            font-weight: 700;
            color: var(--secondary);
            margin-top: 2px;
        }
    </style>
</head>
<body>

    <header class="navbar">
        <a href="index.php" class="nav-brand">
            <span style="font-size: 22px;">&#127891;</span>
            <span>Student Management System</span>
        </a>
        <div style="display: flex; gap: 10px;">
            <a href="upload.php?id=<?= (int)$id ?>" class="btn btn-primary btn-sm">
                <span>&#11014;</span> Upload Document
            </a>
            <a href="index.php" class="btn btn-secondary btn-sm">
                &larr; Back to Student List
            </a>
        </div>
    </header>

    <main class="container">

        <!-- Profile Hero Banner with Visual Avatar -->
        <div class="profile-hero">
            <?php if ($avatarFile) { ?>
                <img src="stream.php?id=<?= (int)$avatarFile["id"] ?>" 
                     alt="<?= htmlspecialchars($student["name"]) ?>" 
                     class="hero-img">
            <?php } else { ?>
                <div class="hero-initials"><?= strtoupper(substr($student["name"], 0, 2)) ?></div>
            <?php } ?>

            <div style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <h2 style="font-size: 24px; font-weight: 800; color: var(--secondary);">
                        <?= htmlspecialchars($student["name"]) ?>
                    </h2>
                    <span class="badge-roll">Roll No: <?= htmlspecialchars($student["roll_no"]) ?></span>
                    <span class="badge-course <?= htmlspecialchars($student["course"]) ?>">
                        <?= htmlspecialchars($student["course"]) ?>
                    </span>
                </div>

                <div class="info-grid">
                    <div class="info-pill">
                        <div class="info-label">Institutional Email</div>
                        <div class="info-val"><?= htmlspecialchars($student["email"]) ?></div>
                    </div>
                    <div class="info-pill">
                        <div class="info-label">Degree Course</div>
                        <div class="info-val"><?= htmlspecialchars($student["course"]) ?></div>
                    </div>
                    <div class="info-pill">
                        <div class="info-label">Record Identifier</div>
                        <div class="info-val">Student ID #<?= (int)$student["id"] ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Uploaded Images & Documents Section -->
        <div class="page-header" style="margin-bottom: 12px;">
            <div class="page-title">
                <h3>Uploaded Images &amp; Documents Gallery</h3>
                <p>Verified student identity cards, photos, and academic certificates.</p>
            </div>
            <a href="upload.php?id=<?= (int)$id ?>" class="btn btn-sm btn-upload">
                &#43; Upload Another File
            </a>
        </div>

        <?php if ($filesResult->num_rows === 0) { ?>
            <div style="background: #ffffff; border: 1.5px dashed var(--border); border-radius: var(--radius-md); padding: 36px; text-align: center; color: var(--text-muted);">
                <div style="font-size: 36px; margin-bottom: 8px;">&#128193;</div>
                <h4>No Documents Uploaded Yet</h4>
                <p style="font-size: 13px; margin-top: 4px;">Upload identity proofs, photos, or grade sheets for this student.</p>
                <a href="upload.php?id=<?= (int)$id ?>" class="btn btn-primary btn-sm" style="margin-top: 14px;">
                    Upload First Document
                </a>
            </div>
        <?php } else { ?>
            <!-- Rich Visual Gallery of Uploaded Files -->
            <div class="gallery-grid">
                <?php while ($file = $filesResult->fetch_assoc()) { 
                    $isImage = strpos($file["file_type"], "image/") === 0;
                    $isPdf = strpos($file["file_type"], "pdf") !== false;
                ?>
                    <div class="file-card">
                        <!-- Direct Visual Preview for Images -->
                        <div class="file-card-preview">
                            <?php if ($isImage) { ?>
                                <img src="stream.php?id=<?= (int)$file["id"] ?>" 
                                     alt="<?= htmlspecialchars($file["original_name"]) ?>"
                                     loading="lazy">
                            <?php } else if ($isPdf) { ?>
                                <div class="file-icon-placeholder" style="color: #dc2626;">&#128196;</div>
                            <?php } else { ?>
                                <div class="file-icon-placeholder" style="color: #2563eb;">&#128193;</div>
                            <?php } ?>
                        </div>

                        <div class="file-card-body">
                            <div class="file-card-title" title="<?= htmlspecialchars($file["original_name"]) ?>">
                                <?= htmlspecialchars($file["original_name"]) ?>
                            </div>
                            <div class="file-card-sub">
                                <span><?= strtoupper(explode('/', $file["file_type"])[1] ?? 'FILE') ?></span> &bull; 
                                <span><?= number_format($file["file_size"] / 1024, 1) ?> KB</span> &bull;
                                <span><?= date("d M Y", strtotime($file["uploaded_at"])) ?></span>
                            </div>
                        </div>

                        <div class="file-card-footer">
                            <a href="stream.php?id=<?= (int)$file["id"] ?>" target="_blank" class="btn btn-sm btn-view">
                                &#128065; View
                            </a>
                            <a href="stream.php?id=<?= (int)$file["id"] ?>&download=1" class="btn btn-sm btn-secondary">
                                &#11015; Download
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

    </main>

    <footer class="footer">
        <p>&copy; 2026 Student Management System</p>
    </footer>

</body>
</html>
