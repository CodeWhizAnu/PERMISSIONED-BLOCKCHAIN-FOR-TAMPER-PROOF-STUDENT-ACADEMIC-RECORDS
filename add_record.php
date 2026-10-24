<!-- add_record.php -->
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $student_name = trim($_POST['student_name']);
    $course = trim($_POST['course']);
    $semester = trim($_POST['semester']);
    $grade = trim($_POST['grade']);
    
    if ($student_id && $student_name && $course && $semester && $grade) {
        try {
            // 1. Fetch the absolute latest block to get its index and hash
            $stmt = $pdo->query("SELECT block_index, hash FROM blocks ORDER BY block_index DESC LIMIT 1");
            $last_block = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $new_index = (int)$last_block['block_index'] + 1;
            $prev_hash = $last_block['hash']; // This links our new block to the chain!
            
            $timestamp = date('Y-m-d H:i:s');
            
            // 2. Deterministic Serialization: Combine all data exactly the same way every time
            $data_string = $new_index . $student_id . $student_name . $course . $semester . $grade . $timestamp . $prev_hash;
            
            // 3. Generate the SHA-256 Hash
            $new_hash = hash('sha256', $data_string);

            // 4. Insert the cryptographically linked record
            $insert_stmt = $pdo->prepare("
                INSERT INTO blocks (block_index, student_id, student_name, course, semester, grade, timestamp, previous_hash, hash) 
                VALUES (:index, :sid, :sname, :course, :sem, :grade, :time, :phash, :hash)
            ");
            
            $insert_stmt->execute([
                'index' => $new_index,
                'sid' => htmlspecialchars($student_id),
                'sname' => htmlspecialchars($student_name),
                'course' => htmlspecialchars($course),
                'sem' => htmlspecialchars($semester),
                'grade' => htmlspecialchars($grade),
                'time' => $timestamp,
                'phash' => $prev_hash,
                'hash' => $new_hash
            ]);
            
            $message = "Block #$new_index securely hashed and added to the ledger!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Ledger Error: " . $e->getMessage();
            $message_type = "error";
        }
    } else {
        $message = "All fields are required.";
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Record - AcademicChain</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: var(--text-muted); font-size: 0.9rem; }
        .form-control { width: 100%; padding: 12px; background: rgba(5,8,20,0.6); border: 1px solid var(--glass-border); border-radius: 8px; color: #fff; outline: none; font-family: 'Outfit', sans-serif; transition: 0.3s; }
        .form-control:focus { border-color: var(--cyan); box-shadow: 0 0 10px rgba(0, 243, 255, 0.2); }
        .btn-submit { padding: 12px 25px; background: linear-gradient(90deg, var(--cyan), var(--purple)); border: none; border-radius: 8px; color: #fff; font-weight: 600; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-submit:hover { box-shadow: 0 0 15px rgba(189, 0, 255, 0.4); transform: translateY(-2px); }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; }
        .alert-success { background: rgba(0, 255, 136, 0.1); color: #00ff88; border: 1px solid rgba(0, 255, 136, 0.3); }
        .alert-error { background: rgba(255, 71, 87, 0.1); color: #ff4757; border: 1px solid rgba(255, 71, 87, 0.3); }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header"><i class="fa-solid fa-link"></i> <span class="logo-text">AcademicChain</span></div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> <span>Dashboard</span></a></li>
            <li><a href="students.php"><i class="fa-solid fa-users"></i> <span>Students</span></a></li>
            <li class="active"><a href="add_record.php"><i class="fa-solid fa-file-circle-plus"></i> <span>Add Record</span></a></li>
            <li><a href="verify.php"><i class="fa-solid fa-shield-halved"></i> <span>Verify Record</span></a></li>
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

        <h2 class="section-title">Add New Academic Record</h2>
        <div class="glass-panel" style="max-width: 800px;">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>"><i class="fa-solid fa-circle-info"></i> <?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Student ID (e.g., STU-1001)</label>
                        <input type="text" name="student_id" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="student_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Course Name</label>
                        <input type="text" name="course" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Semester / Year</label>
                        <input type="text" name="semester" class="form-control" placeholder="e.g. Sem 5 - 2026" required>
                    </div>
                </div>
                <div class="form-group" style="max-width: 50%;">
                    <label>Final Grade</label>
                    <input type="text" name="grade" class="form-control" placeholder="e.g. A+" required>
                </div>
                <button type="submit" class="btn-submit"><i class="fa-solid fa-database"></i> Stage Record</button>
            </form>
        </div>
    </main>
    <script src="assets/js/main.js"></script>
</body>
</html>