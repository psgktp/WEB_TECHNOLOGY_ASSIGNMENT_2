<?php
require "db.php";

// Fetch students and check for profile image attachment
$sql = "SELECT s.id, s.name, s.roll_no, s.email, s.course,
               (SELECT f.id FROM student_files f 
                WHERE f.student_id = s.id AND f.file_type LIKE 'image/%' 
                ORDER BY f.id ASC LIMIT 1) AS image_file_id,
               (SELECT COUNT(*) FROM student_files f WHERE f.student_id = s.id) AS files_count
        FROM students s 
        ORDER BY s.id ASC";

$result = $conn->query($sql);

// Compute portal metrics
$totalStudents = $result->num_rows;
$totalFilesRes = $conn->query("SELECT COUNT(*) AS total_files FROM student_files");
$totalFiles = $totalFilesRes ? $totalFilesRes->fetch_assoc()["total_files"] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records - Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Top Navigation Header -->
    <header class="navbar">
        <a href="index.php" class="nav-brand">
            <span style="font-size: 22px;">&#127891;</span>
            <span>Student Management System</span>
            <span class="brand-badge">PHP &amp; MySQL</span>
        </a>
        <a href="add.php" class="btn btn-primary">
            <span>&#43;</span> Add New Student
        </a>
    </header>

    <main class="container">

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <h2>Student Academic Records</h2>
                <p>Manage enrolled students, uploaded verification proofs, and academic departmental data.</p>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon blue">&#128101;</div>
                <div>
                    <div class="stat-number"><?= $totalStudents ?></div>
                    <div class="stat-label">Enrolled Students</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">&#128193;</div>
                <div>
                    <div class="stat-number"><?= $totalFiles ?></div>
                    <div class="stat-label">Uploaded Documents</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">&#127891;</div>
                <div>
                    <div class="stat-number">4 Departments</div>
                    <div class="stat-label">CSE, IT, AIML, ECE</div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card-table">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Student Profile</th>
                            <th>Roll Number</th>
                            <th>Course / Department</th>
                            <th>Attached Proofs</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { 
                            $initials = strtoupper(substr($row["name"], 0, 2));
                        ?>
                        <tr>
                            <!-- Visual Student Profile with Avatar/Image -->
                            <td>
                                <div class="student-cell">
                                    <?php if ($row["image_file_id"]) { ?>
                                        <img src="stream.php?id=<?= (int)$row["image_file_id"] ?>" 
                                             alt="<?= htmlspecialchars($row["name"]) ?>" 
                                             class="avatar-mini">
                                    <?php } else { ?>
                                        <div class="avatar-initials-mini"><?= $initials ?></div>
                                    <?php } ?>
                                    <div>
                                        <span class="student-name-text"><?= htmlspecialchars($row["name"]) ?></span>
                                        <span class="student-email-text"><?= htmlspecialchars($row["email"]) ?></span>
                                    </div>
                                </div>
                            </td>

                            <!-- Numeric Roll Number -->
                            <td>
                                <span class="badge-roll"><?= htmlspecialchars($row["roll_no"]) ?></span>
                            </td>

                            <!-- Course Badge -->
                            <td>
                                <span class="badge-course <?= htmlspecialchars($row["course"]) ?>">
                                    <?= htmlspecialchars($row["course"]) ?>
                                </span>
                            </td>

                            <!-- Attached Files Count -->
                            <td>
                                <span style="font-size: 13px; color: var(--text-muted);">
                                    &#128196; <?= (int)$row["files_count"] ?> file(s)
                                </span>
                            </td>

                            <!-- Action Buttons -->
                            <td style="text-align: right;">
                                <div class="action-group" style="justify-content: flex-end;">
                                    <a href="view.php?id=<?= (int)$row["id"] ?>" class="btn btn-sm btn-view">
                                        &#128065; View
                                    </a>
                                    <a href="upload.php?id=<?= (int)$row["id"] ?>" class="btn btn-sm btn-upload">
                                        &#11014; Upload
                                    </a>
                                    <a href="edit.php?id=<?= (int)$row["id"] ?>" class="btn btn-sm btn-edit">
                                        &#9998; Edit
                                    </a>
                                    <a href="delete.php?id=<?= (int)$row["id"] ?>" class="btn btn-sm btn-delete">
                                        &#128465; Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <footer class="footer">
        <p>&copy; 2026 Student Management System &bull; Built with PHP, MySQL &amp; Apache Server</p>
    </footer>

</body>
</html>
