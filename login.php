<?php
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {
        $error = "Please enter username and password.";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Setup Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { display: block; }
        .auth-wrapper { display: flex; align-items: center; justify-content: center; min-height: 100vh; background-color: var(--bg-main); }
        .auth-card { width: 100%; max-width: 400px; padding: 2.5rem 2rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);}
        .auth-card .logo { text-align: center; margin-bottom: 2rem; font-size: 2rem; color: var(--primary); font-weight: 700; }
        .auth-links { text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: var(--text-muted); }
        .auth-links a { color: var(--primary); text-decoration: none; font-weight: 600; transition: text-decoration 0.2s;}
        .auth-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="card auth-card">
            <div class="logo"><i class="fas fa-layer-group"></i> Admin UI</div>
            <h2 style="text-align: center; margin-bottom: 1.5rem; font-size: 1.25rem; color: #1e293b;">Sign in to your account</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Enter username">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Enter password">
                </div>
                <button type="submit" class="btn" style="width: 100%; padding: 0.75rem;"><i class="fas fa-sign-in-alt"></i>&nbsp; Sign In</button>
            </form>
            
            <div class="auth-links">
                &larr; <a href="../">Back to Store Website</a>
            </div>
        </div>
    </div>
</body>
</html>
