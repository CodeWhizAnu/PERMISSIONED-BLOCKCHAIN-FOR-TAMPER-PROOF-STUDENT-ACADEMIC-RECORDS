<!-- students.php -->
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all academic records, skipping the Genesis block (index 0)
$stmt = $pdo->query("SELECT * FROM blocks WHERE block_index > 0 ORDER BY block_index DESC");
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records - AcademicChain</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header"><i class="fa-solid fa-link"></i> <span class="logo-text">AcademicChain</span></div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> <span>Dashboard</span></a></li>
            <li class="active"><a href="students.php"><i class="fa-solid fa-users"></i> <span>Students</span></a></li>
            <li><a href="add_record.php"><i class="fa-solid fa-file-circle-plus"></i> <span>Add Record</span></a></li>
            <li><a href="#"><i class="fa-solid fa-shield-halved"></i> <span>Verify Record</span></a></li>
        </ul>
        <ul class="nav-links" style="flex-grow: 0;">
            <li><a href="logout.php" style="color: #ff4757;"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <nav class="top-nav">
            <button class="toggle-btn" id="toggle-sidebar"><i class="fa-solid fa-bars-staggered"></i></button>
            <div class="admin-profile"><span><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span><img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" alt="Admin"></div>
        </nav>

        <h2 class="section-title">Academic Database</h2>
        <div class="glass-panel">
            <?php if (count($records) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Block #</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Grade</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $row): ?>
                            <tr>
                                <td><span class="badge badge-success"><?php echo $row['block_index']; ?></span></td>
                                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['course']); ?></td>
                                <td><?php echo htmlspecialchars($row['semester']); ?></td>
                                <td style="font-weight: bold; color: var(--cyan);"><?php echo htmlspecialchars($row['grade']); ?></td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);"><?php echo $row['timestamp']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-muted); text-align: center; padding: 20px;">No academic records found yet. Add one to start the chain.</p>
            <?php endif; ?>
        </div>
    </main>
    <script src="assets/js/main.js"></script>
</body>
</html>