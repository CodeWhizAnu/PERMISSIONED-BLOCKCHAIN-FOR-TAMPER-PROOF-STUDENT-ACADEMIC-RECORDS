<!-- login.php -->
<?php
session_start();
require_once 'includes/db.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // Prepare statement to prevent SQL Injection
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password against the hash
        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true); // Security measure against session fixation
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AcademicChain</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-container {
            display: flex; justify-content: center; align-items: center; min-height: 100vh; width: 100%;
        }
        .login-box {
            width: 100%; max-width: 400px; text-align: center;
        }
        .login-box h1 {
            color: var(--cyan); margin-bottom: 30px; font-family: 'Rajdhani', sans-serif;
        }
        .form-group {
            margin-bottom: 20px; text-align: left;
        }
        .form-group label {
            display: block; margin-bottom: 8px; color: var(--text-muted); font-size: 0.9rem;
        }
        .form-control {
            width: 100%; padding: 15px; background: rgba(5,8,20,0.6); border: 1px solid var(--glass-border);
            border-radius: 8px; color: #fff; font-family: 'Outfit', sans-serif; outline: none; transition: 0.3s;
        }
        .form-control:focus {
            border-color: var(--cyan); box-shadow: 0 0 10px rgba(0, 243, 255, 0.2);
        }
        .btn-login {
            width: 100%; padding: 15px; background: linear-gradient(90deg, var(--cyan), var(--purple));
            border: none; border-radius: 8px; color: #fff; font-weight: 700; font-family: 'Rajdhani', sans-serif;
            font-size: 1.2rem; cursor: pointer; transition: 0.3s; margin-top: 10px;
        }
        .btn-login:hover {
            box-shadow: 0 0 15px rgba(189, 0, 255, 0.4); transform: translateY(-2px);
        }
        .error-msg {
            color: #ff4757; background: rgba(255, 71, 87, 0.1); padding: 10px; border-radius: 5px;
            margin-bottom: 20px; border: 1px solid rgba(255, 71, 87, 0.3); font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="glass-panel login-box">
            <h1><i class="fa-solid fa-link"></i> AcademicChain</h1>
            <?php if ($error): ?>
                <div class="error-msg"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Administrator Username</label>
                    <input type="text" name="username" class="form-control" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn-login">Initialize Session <i class="fa-solid fa-arrow-right"></i></button>
            </form>
        </div>
    </div>
</body>
</html>