<?php
require_once 'db.php';
requireLogin();

// Optional: Security check to ensure only Superadmin or Admin can view/change roles.
// Since we don't have session roles set up yet, we'll just allow it for demonstration.

// Handle assigning a new role
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$new_role, $user_id]);
        header("Location: admin_roles.php?msg=" . urlencode("User role updated successfully!"));
        exit;
    } catch (PDOException $e) {
        $error = "Error updating role: " . $e->getMessage();
    }
}

// Fetch all users
try {
    $stmt = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at ASC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    // Fallback if 'role' column doesn't exist yet
    if ($e->getCode() == '42S22') {
        $error = "The 'role' column doesn't exist in the users table. Make sure to run the DB update.";
    } else {
        $error = "Error fetching users: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Roles - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Admin Roles & Users</h1>
        </div>
        
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success" style="background: #ecfdf5; color: #047857; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" style="background: #fef2f2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #ef4444;">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0;">
                <h3 style="margin: 0; color: #1e293b;">System Administrators</h3>
                <a href="register.php" class="btn"><i class="fas fa-user-plus"></i> Add New Admin</a>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User Info</th>
                        <th>Current Role</th>
                        <th>Joined</th>
                        <th>Update Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($users) && count($users) > 0): ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><span style="color: #64748b; font-weight: 500;">#<?= htmlspecialchars($u['id']) ?></span></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                            <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong style="display: block; color: #0f172a; font-size: 1.05rem;"><?= htmlspecialchars($u['username']) ?></strong>
                                            <small style="color: #64748b;"><?= htmlspecialchars($u['email']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        // Default fallback if role is empty
                                        $role = $u['role'] ?? 'Manager';
                                        $roleBg = '#64748b'; // Editor
                                        if ($role == 'Admin') $roleBg = '#8b5cf6'; // Purple
                                        if ($role == 'Manager') $roleBg = '#3b82f6'; // Blue
                                    ?>
                                    <span class="badge" style="background-color: <?= $roleBg ?>; color: white; border: none; padding: 0.35rem 0.75rem;">
                                        <i class="fas fa-shield-alt" style="margin-right: 4px;"></i> <?= htmlspecialchars($role) ?>
                                    </span>
                                </td>
                                <td style="color: #64748b;"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                                <td>
                                    <form method="POST" action="" style="display: flex; gap: 0.5rem; align-items: center;">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <select name="role" style="padding: 0.4rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.9rem;">
                                            <option value="Admin" <?= $role == 'Admin' ? 'selected' : '' ?>>Admin</option>
                                            <option value="Manager" <?= $role == 'Manager' ? 'selected' : '' ?>>Manager</option>
                                            <option value="Editor" <?= $role == 'Editor' ? 'selected' : '' ?>>Editor</option>
                                        </select>
                                        <button type="submit" name="update_role" class="btn" style="padding: 0.4rem 0.75rem; font-size: 0.85rem;"><i class="fas fa-check"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 4rem; color: #64748b;">
                                <i class="fas fa-users-slash fa-4x" style="color: #cbd5e1; margin-bottom: 1.5rem; display:block;"></i>
                                <h3 style="color: #334155; margin-bottom: 0.5rem;">No users found</h3>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
