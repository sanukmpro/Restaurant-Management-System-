<?php
session_start();
require_once __DIR__ . '/../config.php';

// 1. Strict Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$success = "";
$error = "";
$currentAdminId = $_SESSION['user_id'];

/* ===========================
   DELETE USER LOGIC
=========================== */
if (isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);

    if ($user_id == $currentAdminId) {
        $error = "Security Alert: You cannot delete your own active session.";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $success = "User account #$user_id has been permanently removed.";
        } else {
            $error = "Database Error: Could not remove user.";
        }
    }
}

// Fetch all non-admin users
$result = $conn->query("SELECT * FROM users WHERE role != 'admin' ORDER BY user_id DESC");
$total_users = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Admin Panel</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        :root {
            --gold: #c5a059;
            --dark: #1f1f1f;
            --bg: #f8f9fa;
            --danger: #eb4d4b;
            --blue: #4834d4;
        }

        body { background: var(--bg); font-family: 'Poppins', sans-serif; color: var(--dark); margin: 0; padding: 0; }
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }

        /* Header Styling */
        .admin-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 30px; }
        
        /* User Grid */
        .user-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); 
            gap: 20px; 
        }

        .user-card { 
            background: #fff; border-radius: 20px; padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #eee;
            position: relative; transition: transform 0.3s;
        }
        .user-card:hover { transform: translateY(-5px); }

        /* Avatar Circle */
        .avatar {
            width: 50px; height: 50px; background: #f0f0f0; 
            border-radius: 50%; display: flex; align-items: center; 
            justify-content: center; font-weight: 700; color: var(--gold);
            font-size: 1.2rem; margin-bottom: 15px; border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .username { font-size: 1.1rem; font-weight: 700; margin: 0; display: block; }
        .email { color: #888; font-size: 0.85rem; margin-top: 5px; word-break: break-all; }

        /* Role Badges */
        .badge { 
            position: absolute; top: 20px; right: 20px;
            padding: 4px 12px; border-radius: 50px; font-size: 0.7rem; 
            font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .role-customer { background: #eef2ff; color: var(--blue); }
        .role-staff { background: #eafaf1; color: #2ecc71; }

        /* Delete Button - Fixed Width */
        .btn-delete { 
            width: 100%; margin-top: 20px; padding: 12px; 
            background: #fff0f0; color: var(--danger); border: none; 
            border-radius: 12px; font-weight: 700; cursor: pointer;
            transition: 0.3s; font-size: 0.9rem;
        }
        .btn-delete:hover { background: var(--danger); color: #fff; }

        /* Feedback Messages */
        .alert { padding: 15px; border-radius: 12px; margin-bottom: 25px; text-align: center; font-size: 0.9rem; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .stats-bar { color: #888; font-size: 0.9rem; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="admin-header">
        <h1>Manage Users</h1>
        <div style="display:flex; gap: 20px;">
            <a href="dashboard.php" style="color: var(--gold); text-decoration: none; font-weight: 600;">Dashboard</a>
            <a href="menu.php" style="color: var(--gold); text-decoration: none; font-weight: 600;">Menu</a>
        </div>
    </div>

    <?php if($success): ?> <div class="alert success"><?php echo $success; ?></div> <?php endif; ?>
    <?php if($error): ?> <div class="alert error"><?php echo $error; ?></div> <?php endif; ?>

    <div class="stats-bar">
        Showing <strong><?php echo $total_users; ?></strong> registered customers/staff members.
    </div>

    <div class="user-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): 
                $firstLetter = strtoupper(substr($row['username'], 0, 1));
            ?>
                <div class="user-card">
                    <span class="badge role-<?php echo strtolower($row['role']); ?>">
                        <?php echo htmlspecialchars($row['role']); ?>
                    </span>
                    
                    <div class="avatar"><?php echo $firstLetter; ?></div>
                    
                    <span class="username"><?php echo htmlspecialchars($row['username']); ?></span>
                    <div class="email"><?php echo htmlspecialchars($row['email']); ?></div>
                    
                    <div style="font-size: 0.75rem; color: #bbb; margin-top: 15px;">User ID: #<?php echo $row['user_id']; ?></div>

                    <form method="POST" onsubmit="return confirm('Permanent Action: Delete this user profile?')" style="margin:0">
                        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                        <button type="submit" name="delete_user" class="btn-delete">Delete Account</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: #aaa; background: #fff; border-radius: 20px;">
                No users found in the database.
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>