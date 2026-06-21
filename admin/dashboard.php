<?php
session_start();
require_once __DIR__ . '/../config.php';

// Secure admin protection
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$adminName = htmlspecialchars($_SESSION['username']);

// Optional: Fetch quick stats for a professional touch
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_dishes = $conn->query("SELECT COUNT(*) as count FROM menu")->fetch_assoc()['count'];
$pending_res = $conn->query("SELECT COUNT(*) as count FROM reservations WHERE status='Pending'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Management System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        :root {
            --gold: #c5a059;
            --dark: #1f1f1f;
            --bg: #f8f9fa;
            --white: #ffffff;
        }

        body { background: var(--bg); font-family: 'Poppins', sans-serif; color: var(--dark); margin: 0; }
        
        /* Top Navigation Bar */
        .navbar {
            background: var(--dark);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .navbar h1 { font-size: 1.2rem; margin: 0; font-weight: 700; letter-spacing: 1px; }
        .navbar h1 span { color: var(--gold); }
        .logout-link { color: #ff4757; text-decoration: none; font-size: 0.9rem; font-weight: 600; border: 1px solid #ff4757; padding: 5px 15px; border-radius: 8px; transition: 0.3s; }
        .logout-link:hover { background: #ff4757; color: white; }

        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }

        /* Stats Row */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: var(--white);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border-left: 5px solid var(--gold);
        }
        .stat-card h4 { margin: 0; color: #888; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; }
        .stat-card .value { font-size: 2rem; font-weight: 700; margin-top: 5px; display: block; }

        /* Management Grid */
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        .action-card {
            background: var(--white);
            padding: 40px 30px;
            border-radius: 24px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
            color: var(--dark);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }
        .action-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: var(--gold);
        }
        .icon-box {
            width: 70px;
            height: 70px;
            background: #fdf8ef;
            color: var(--gold);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.8rem;
        }
        .action-card h3 { margin: 10px 0; font-size: 1.25rem; }
        .action-card p { font-size: 0.9rem; color: #777; line-height: 1.5; }

        .welcome-section { margin-bottom: 30px; }
        .welcome-section h2 { margin: 0; font-size: 1.8rem; }
        .welcome-section p { color: #888; margin: 5px 0 0; }
    </style>
</head>
<body>

<nav class="navbar">
    <h1>RESTAURANT<span>ADMIN</span></h1>
    <a href="../logout.php" class="logout-link">Logout (<?php echo $adminName; ?>)</a>
</nav>

<div class="container">
    <div class="welcome-section">
        <h2>Welcome back, <?php echo $adminName; ?>!</h2>
        <p>Here is what's happening in your restaurant today.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h4>Total Orders</h4>
            <span class="value"><?php echo $total_orders; ?></span>
        </div>
        <div class="stat-card">
            <h4>Menu Items</h4>
            <span class="value"><?php echo $total_dishes; ?></span>
        </div>
        <div class="stat-card" style="border-left-color: var(--blue);">
            <h4>Pending Reservations</h4>
            <span class="value"><?php echo $pending_res; ?></span>
        </div>
    </div>

    <div class="admin-grid">
        <a href="users.php" class="action-card">
            <div class="icon-box">👥</div>
            <h3>User Base</h3>
            <p>Manage customer profiles, admin roles, and account security.</p>
        </a>

        <a href="menu.php" class="action-card">
            <div class="icon-box">🍴</div>
            <h3>Dish Gallery</h3>
            <p>Curate your menu, update pricing, and upload dish photography.</p>
        </a>

        <a href="orders.php" class="action-card">
            <div class="icon-box">🛍️</div>
            <h3>Order Desk</h3>
            <p>Monitor real-time customer orders and update kitchen status.</p>
        </a>

        <a href="reservations.php" class="action-card">
            <div class="icon-box">📅</div>
            <h3>Bookings</h3>
            <p>Manage table reservations and seating arrangements.</p>
        </a>
    </div>
</div>

</body>
</html>