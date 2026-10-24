<?php
// fix_admin.php
require_once 'includes/db.php';

// The password we want to use
$password = 'admin123';

// Generate a valid BCRYPT hash using your local PHP environment
$real_hash = password_hash($password, PASSWORD_BCRYPT);

try {
    // Update the existing admin account
    $stmt = $pdo->prepare("UPDATE admins SET password_hash = :hash WHERE username = 'admin'");
    $stmt->execute(['hash' => $real_hash]);
    
    echo "<h1>✅ Administrator password updated successfully!</h1>";
    echo "<p>You can now log in with:</p>";
    echo "<ul><li>Username: <b>admin</b></li><li>Password: <b>admin123</b></li></ul>";
    echo "<a href='login.php'>Click here to go to the Login Page</a>";
} catch (PDOException $e) {
    echo "Error updating password: " . $e->getMessage();
}
?>