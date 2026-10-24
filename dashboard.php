<!-- dashboard.php -->
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch live statistics
$stats = [];
$stats['total_blocks'] = $pdo->query("SELECT COUNT(*) FROM blocks")->fetchColumn();
$stats['total_students'] = $pdo->query("SELECT COUNT(DISTINCT student_id) FROM blocks WHERE block_index > 0")->fetchColumn();
$stats['total_records'] = $pdo->query("SELECT COUNT(*) FROM blocks WHERE block_index > 0")->fetchColumn();

// Fetch latest 5 records for the table
$recent_records = $pdo->query("SELECT * FROM blocks WHERE block_index > 0 ORDER BY block_index DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all blocks for the visualizer (oldest to newest)
$chain_blocks = $pdo->query("SELECT * FROM blocks ORDER BY block_index ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AcademicChain</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header"><i class="fa-solid fa-link"></i> <span class="logo-text">AcademicChain</span></div>
        <ul class="nav-links">
            <li class="active"><a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> <span>Dashboard</span></a></li>
            <li><a href="students.php"><i class="fa-solid fa-users"></i> <span>Students</span></a></li>
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

        <div class="dashboard-grid">
            <div class="stat-card">
                <i class="fa-solid fa-users stat-icon"></i>
                <div class="stat-info">
                    <h3>Total Students</h3>
                    <div class="stat-value" data-target="<?php echo $stats['total_students']; ?>">0</div>
                </div>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-file-contract stat-icon"></i>
                <div class="stat-info">
                    <h3>Academic Records</h3>
                    <div class="stat-value" data-target="<?php echo $stats['total_records']; ?>">0</div>
                </div>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-cubes stat-icon"></i>
                <div class="stat-info">
                    <h3>Ledger Blocks</h3>
                    <div class="stat-value" data-target="<?php echo $stats['total_blocks']; ?>">0</div>
                </div>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-check-double stat-icon" style="color: #00ff88;"></i>
                <div class="stat-info">
                    <h3>Chain Status</h3>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #00ff88; font-family: 'Rajdhani', sans-serif;">SECURE</div>
                </div>
            </div>
        </div>

        <h2 class="section-title">Recent Academic Records</h2>
        <div class="glass-panel">
            <table>
                <thead>
                    <tr>
                        <th>Block #</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Grade</th>
                        <th>Block Hash</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_records as $rec): ?>
                    <tr>
                        <td><span class="badge badge-success"><?php echo $rec['block_index']; ?></span></td>
                        <td><?php echo htmlspecialchars($rec['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($rec['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($rec['course']); ?></td>
                        <td style="color: var(--cyan); font-weight: bold;"><?php echo htmlspecialchars($rec['grade']); ?></td>
                        <td style="font-family: monospace; font-size: 0.8rem; color: var(--text-muted);"><?php echo substr($rec['hash'], 0, 16); ?>...</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h2 class="section-title">Live Ledger Visualizer</h2>
        <div class="glass-panel">
            <div class="blockchain-visualizer">
                <?php foreach ($chain_blocks as $index => $block): ?>
                    <div class="block">
                        <div class="block-header">
                            <strong>Block <?php echo $block['block_index']; ?> <?php echo $block['block_index'] == 0 ? '(Genesis)' : ''; ?></strong>
                            <span><?php echo date('h:i A', strtotime($block['timestamp'])); ?></span>
                        </div>
                        <div class="hash-line">Prev: <span title="<?php echo $block['previous_hash']; ?>"><?php echo substr($block['previous_hash'], 0, 12); ?>...</span></div>
                        <div class="hash-line">Hash: <span style="color: var(--cyan);" title="<?php echo $block['hash']; ?>"><?php echo substr($block['hash'], 0, 12); ?>...</span></div>
                    </div>
                    
                    <?php if ($index < count($chain_blocks) - 1): ?>
                        <i class="fa-solid fa-link chain-link"></i>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
    <script src="assets/js/main.js"></script>
</body>
</html>