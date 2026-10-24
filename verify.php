<!-- verify.php -->
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch the entire chain
$stmt = $pdo->query("SELECT * FROM blocks ORDER BY block_index ASC");
$chain = $stmt->fetchAll(PDO::FETCH_ASSOC);

$is_chain_valid = true;
$broken_block_index = null;
$verification_results = [];
$previous_block_hash = null;

foreach ($chain as $block) {
    $current_index = $block['block_index'];
    $status = 'valid';
    $error_msg = '';

    // 1. Verify the link to the previous block (Skip for Genesis block)
    if ($current_index > 0 && $block['previous_hash'] !== $previous_block_hash) {
        $status = 'invalid';
        $error_msg = "Chain broken! Previous hash does not match Block " . ($current_index - 1);
        $is_chain_valid = false;
    }

    // 2. Verify the integrity of the current block's data
    // We recreate the exact string used in add_record.php (Phase 6)
    $data_string = $block['block_index'] . $block['student_id'] . $block['student_name'] . $block['course'] . $block['semester'] . $block['grade'] . $block['timestamp'] . $block['previous_hash'];
    
    $recalculated_hash = hash('sha256', $data_string);

    if ($recalculated_hash !== $block['hash']) {
        $status = 'invalid';
        $error_msg = "Data tampered! Recomputed hash does not match stored hash.";
        $is_chain_valid = false;
    }

    // If this is the first block to fail, record it
    if ($status === 'invalid' && $broken_block_index === null) {
        $broken_block_index = $current_index;
    }

    $verification_results[] = [
        'block_index' => $current_index,
        'student_name' => $block['student_name'],
        'stored_hash' => substr($block['hash'], 0, 16) . '...',
        'status' => $status,
        'error' => $error_msg
    ];

    // Store current hash to check against the next block's "previous_hash"
    $previous_block_hash = $block['hash'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Ledger - AcademicChain</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-banner { padding: 20px; border-radius: 10px; margin-bottom: 30px; display: flex; align-items: center; gap: 15px; font-size: 1.2rem; font-weight: 600; }
        .banner-secure { background: rgba(0, 255, 136, 0.1); color: #00ff88; border: 1px solid #00ff88; }
        .banner-tampered { background: rgba(255, 71, 87, 0.1); color: #ff4757; border: 1px solid #ff4757; }
        .status-icon { font-size: 2rem; }
        .row-invalid { background: rgba(255, 71, 87, 0.05); }
        .row-invalid td { color: #ff4757 !important; }
        .btn-tamper { display: inline-block; padding: 10px 20px; background: rgba(255, 71, 87, 0.2); border: 1px solid #ff4757; color: #ff4757; text-decoration: none; border-radius: 5px; font-weight: bold; transition: 0.3s; margin-bottom: 20px;}
        .btn-tamper:hover { background: #ff4757; color: #fff; box-shadow: 0 0 15px rgba(255, 71, 87, 0.4); }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header"><i class="fa-solid fa-link"></i> <span class="logo-text">AcademicChain</span></div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> <span>Dashboard</span></a></li>
            <li><a href="students.php"><i class="fa-solid fa-users"></i> <span>Students</span></a></li>
            <li><a href="add_record.php"><i class="fa-solid fa-file-circle-plus"></i> <span>Add Record</span></a></li>
            <li class="active"><a href="verify.php"><i class="fa-solid fa-shield-halved"></i> <span>Verify Record</span></a></li>
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

        <h2 class="section-title">Cryptographic Integrity Check</h2>
        
        <?php if ($is_chain_valid): ?>
            <div class="status-banner banner-secure">
                <i class="fa-solid fa-shield-check status-icon"></i>
                <div>
                    <div>SYSTEM SECURE</div>
                    <div style="font-size: 0.9rem; font-weight: 400; color: var(--text-muted);">All cryptographic hashes and block links are intact. No tampering detected.</div>
                </div>
            </div>
            <a href="tamper.php" class="btn-tamper"><i class="fa-solid fa-user-ninja"></i> Simulate Malicious Database Edit (Tamper Demo)</a>
        <?php else: ?>
            <div class="status-banner banner-tampered">
                <i class="fa-solid fa-triangle-exclamation status-icon"></i>
                <div>
                    <div>BREACH DETECTED</div>
                    <div style="font-size: 0.9rem; font-weight: 400;">Integrity failure detected at Block <?php echo $broken_block_index; ?>. The ledger cannot be trusted.</div>
                </div>
            </div>
        <?php endif; ?>

        <div class="glass-panel">
            <table>
                <thead>
                    <tr>
                        <th>Block #</th>
                        <th>Record Identifier</th>
                        <th>Stored Hash Signature</th>
                        <th>Integrity Status</th>
                        <th>Diagnostic Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($verification_results as $res): ?>
                    <tr class="<?php echo $res['status'] === 'invalid' ? 'row-invalid' : ''; ?>">
                        <td><span class="badge <?php echo $res['status'] === 'valid' ? 'badge-success' : ''; ?>" style="<?php echo $res['status'] === 'invalid' ? 'border: 1px solid #ff4757;' : ''; ?>"><?php echo $res['block_index']; ?></span></td>
                        <td><?php echo htmlspecialchars($res['student_name']); ?></td>
                        <td style="font-family: monospace; font-size: 0.85rem;"><?php echo $res['stored_hash']; ?></td>
                        <td>
                            <?php if ($res['status'] === 'valid'): ?>
                                <i class="fa-solid fa-check" style="color: #00ff88;"></i> Valid
                            <?php else: ?>
                                <i class="fa-solid fa-xmark"></i> Invalid
                            <?php endif; ?>
                        </td>
                        <td style="font-size: 0.85rem;"><?php echo $res['error'] ?: 'Hash matched.'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script src="assets/js/main.js"></script>
</body>
</html>