<!-- heal_ledger.php -->
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

try {
    // Fetch all blocks
    $stmt = $pdo->query("SELECT * FROM blocks ORDER BY block_index ASC");
    $chain = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $previous_hash = null;

    foreach ($chain as $block) {
        $index = $block['block_index'];
        
        // If it's the genesis block, just grab its hash to start the chain
        if ($index == 0) {
            $previous_hash = $block['hash'];
            continue;
        }

        // For all other blocks, recalculate the hash with the current (tampered) data
        $data_string = $index . $block['student_id'] . $block['student_name'] . $block['course'] . $block['semester'] . $block['grade'] . $block['timestamp'] . $previous_hash;
        $new_hash = hash('sha256', $data_string);

        // Update the database with the new valid hash and links
        $update = $pdo->prepare("UPDATE blocks SET previous_hash = :phash, hash = :hash WHERE block_index = :index");
        $update->execute([
            'phash' => $previous_hash,
            'hash' => $new_hash,
            'index' => $index
        ]);

        // Set this new hash as the previous_hash for the next loop
        $previous_hash = $new_hash;
    }

    echo "<div style='background: #050814; color: #00ff88; padding: 40px; font-family: sans-serif; text-align: center;'>";
    echo "<h1><i class='fa-solid fa-wand-magic-sparkles'></i> Chain Healed (History Rewritten)</h1>";
    echo "<p>All cryptographic hashes have been recalculated to match the tampered data.</p>";
    echo "<p>The ledger is now 'Secure' again, proving the danger of a centralized database.</p>";
    echo "<a href='verify.php' style='color: #00f3ff; text-decoration: none; font-weight: bold;'>Return to Verification Engine</a>";
    echo "</div>";

} catch (PDOException $e) {
    echo "Healing Error: " . $e->getMessage();
}
?>