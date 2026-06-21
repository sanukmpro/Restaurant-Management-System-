<?php
session_start();

// Check if user is logged in AND is customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Restaurant Management System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        :root {
            --gold: #c5a059;
            --dark: #1f1f1f;
            --bg: #f8f9fa;
            --white: #ffffff;
        }

        body { background: var(--bg); font-family: 'Poppins', sans-serif; color: var(--dark); margin: 0; }
        
        /* Modern Top Navigation */
        .navbar {
            background: var(--dark);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .navbar h1 { font-size: 1.1rem; margin: 0; font-weight: 700; letter-spacing: 1px; }
        .navbar h1 span { color: var(--gold); }
        
        .nav-links { display: flex; gap: 20px; align-items: center; }
        .nav-links a { color: white; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: 0.3s; }
        .nav-links a:hover { color: var(--gold); }
        
        .logout-btn { 
            color: #ff4757 !important; 
            border: 1px solid #ff4757; 
            padding: 5px 12px; 
            border-radius: 8px; 
        }
        .logout-btn:hover { background: #ff4757; color: white !important; }

        .container { max-width: 1100px; margin: 50px auto; padding: 0 20px; }

        /* Welcome Header */
        .welcome-hero { margin-bottom: 40px; }
        .welcome-hero h2 { font-size: 2rem; margin: 0; font-weight: 700; }
        .welcome-hero p { color: #888; margin: 5px 0 0; font-size: 1rem; }

        /* Action Cards Grid */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .action-card {
            background: var(--white);
            padding: 40px 30px;
            border-radius: 24px;
            text-decoration: none;
            color: var(--dark);
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 20px rgba(0,0,0,0.03);
            border: 1px solid #eee;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .action-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border-color: var(--gold);
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: #fdf8ef;
            color: var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 20px;
            transition: 0.3s;
        }
        .action-card:hover .icon-circle { background: var(--gold); color: white; }

        .action-card h3 { margin: 10px 0; font-size: 1.3rem; font-weight: 700; }
        .action-card p { font-size: 0.9rem; color: #777; line-height: 1.6; margin-bottom: 20px; }
        
        .btn-action {
            background: var(--dark);
            color: white;
            padding: 10px 25px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: 0.3s;
        }
        .action-card:hover .btn-action { background: var(--gold); }

        .footer {
            margin-top: 80px;
            text-align: center;
            padding: 30px;
            color: #aaa;
            font-size: 0.8rem;
            border-top: 1px solid #eee;
        }

        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; flex-direction: column; gap: 15px; }
            .nav-links { flex-wrap: wrap; justify-content: center; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <h1>RESTAURANT<span>SYSTEM</span></h1>
    <div class="nav-links">
        <a href="customer_dashboard.php">Home</a>
        <a href="order.php">Menu</a>
        <a href="my_orders.php">My Orders</a>
        <a href="reservation.php">Reservations</a>
        <a href="logout.php" class="logout-btn">Logout (<?php echo $username; ?>)</a>
    </div>
</nav>

<div class="container">
    <div class="welcome-hero">
        <h2>Hello, <?php echo $username; ?> 👋</h2>
        <p>What would you like to do today?</p>
    </div>

    <div class="action-grid">
        <a href="order.php" class="action-card">
            <div class="icon-circle">🍲</div>
            <h3>Order Food</h3>
            <p>Browse our gourmet menu and have delicious meals delivered to your door.</p>
            <span class="btn-action">Browse Menu</span>
        </a>

        <a href="my_orders.php" class="action-card">
            <div class="icon-circle">📜</div>
            <h3>My Orders</h3>
            <p>Track your current active orders or review your previous meal history.</p>
            <span class="btn-action">View History</span>
        </a>

        <a href="reservation.php" class="action-card">
            <div class="icon-circle">🍷</div>
            <h3>Book a Table</h3>
            <p>Secure your spot for a fine dining experience. Fast and easy reservations.</p>
            <span class="btn-action">Reserve Now</span>
        </a>
    </div>
</div>

<footer class="footer">
    &copy; 2025 Restaurant Management System | Designed for Excellence
</footer>

</body>
</html>