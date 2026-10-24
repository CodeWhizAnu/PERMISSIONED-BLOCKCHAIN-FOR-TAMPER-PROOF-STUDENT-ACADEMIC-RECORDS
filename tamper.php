<!-- tamper.php -->
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

try {
    // We intentionally change Block 1's grade to something drastically different
    // to guarantee the hash breaks, no matter what the original grade was.
    $stmt = $pdo->prepare("UPDATE blocks SET grade = 'F (HACKED)' WHERE block_index = 1");
    $stmt->execute();

    echo "<div style='background-color: #050814; color: #ff4757; font-family: monospace; padding: 40px; text-align: center; height: 100vh;'>";
    echo "<h1><i class='fa-solid fa-skull'></i> HACK SUCCESSFUL</h1>";
    echo "<p>Block 1 has been maliciously altered in the database. The grade is now F (HACKED).</p>";
    echo "<p>Returning to verification engine to view the damage...</p>";
    echo "</div>";

    // Wait 3 seconds, then send the user back to the verification page
    header("refresh:3;url=verify.php");

} catch (PDOException $e) {
    echo "Tamper Error: " . $e->getMessage();
}
?>