<?php
// reset_ledger.php
session_start();
require_once 'includes/db.php';

try {
    // 1. Wipe all existing blocks (removes Phase 5 test data)
    $pdo->exec("TRUNCATE TABLE blocks");

    // 2. Prepare the Genesis Block data
    $index = 0;
    $sid = 'GENESIS';
    $name = 'System Initialization';
    $course = 'None';
    $sem = 'None';
    $grade = 'None';
    $timestamp = '2026-01-01 00:00:00';
    $prev_hash = '0000000000000000000000000000000000000000000000000000000000000000';
    
    // 3. Calculate the TRUE mathematical hash for the Genesis Block
    $data_string = $index . $sid . $name . $course . $sem . $grade . $timestamp . $prev_hash;
    $real_genesis_hash = hash('sha256', $data_string);

    // 4. Insert the mathematically perfect Genesis Block
    $stmt = $pdo->prepare("
        INSERT INTO blocks (block_index, student_id, student_name, course, semester, grade, timestamp, previous_hash, hash) 
        VALUES (:index, :sid, :name, :course, :sem, :grade, :time, :phash, :hash)
    ");
    
    $stmt->execute([
        'index' => $index,
        'sid' => $sid,
        'name' => $name,
        'course' => $course,
        'sem' => $sem,
        'grade' => $grade,
        'time' => $timestamp,
        'phash' => $prev_hash,
        'hash' => $real_genesis_hash
    ]);

    echo "<div style='background: #050814; color: #00ff88; padding: 40px; font-family: sans-serif;'>";
    echo "<h1>✅ Ledger Factory Reset Complete!</h1>";
    echo "<p>The Genesis Block now has a mathematically true SHA-256 hash.</p>";
    echo "<p>All Phase 5 test records have been cleared.</p>";
    echo "<a href='dashboard.php' style='color: #00f3ff;'>Return to Dashboard</a>";
    echo "</div>";

} catch (PDOException $e) {
    echo "Reset Error: " . $e->getMessage();
}
?>