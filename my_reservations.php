<?php
session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Handle Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] === 'cancel') {
        $stmt = $conn->prepare("UPDATE reservations SET status = 'Cancelled' WHERE reservation_id = ? AND full_name = ?");
    } elseif ($_GET['action'] === 'remove') {
        $stmt = $conn->prepare("DELETE FROM reservations WHERE reservation_id = ? AND full_name = ?");
    }
    $stmt->bind_param("is", $id, $username);
    $stmt->execute();
    header("Location: my_reservations.php");
    exit();
}

// Fetch all reservations
$stmt = $conn->prepare("SELECT * FROM reservations WHERE full_name = ? ORDER BY reservation_date DESC, reservation_time DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Set Timezone explicitly to match your local time (e.g., 'Asia/Kolkata')
date_default_timezone_set('Asia/Kolkata'); 
$current_now = new DateTime();

$active_reservations = [];
$past_reservations = [];

while ($row = $result->fetch_assoc()) {
    $res_datetime = new DateTime($row['reservation_date'] . ' ' . $row['reservation_time']);
    
    // Categorize
    if ($res_datetime < $current_now) {
        $past_reservations[] = $row;
    } else {
        $active_reservations[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations | Restaurant System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        :root { --gold: #c5a059; --dark: #1f1f1f; --bg: #f4f4f9; --white: #ffffff; }
        body { background: var(--bg); font-family: 'Poppins', sans-serif; margin: 0; color: #333; }
        
        .navbar { background: var(--dark); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; color: white; position: sticky; top: 0; z-index: 1000; }
        .navbar h1 span { color: var(--gold); }
        .navbar a { color: white; text-decoration: none; font-size: 0.85rem; margin-left: 20px; font-weight: 500; }

        .container { max-width: 850px; margin: 40px auto; padding: 0 20px; }
        .section-title { font-size: 0.9rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 1px; margin: 30px 0 15px; border-bottom: 2px solid #ddd; padding-bottom: 5px; }

        .res-card {
            background: var(--white); border-radius: 15px; padding: 20px 25px; margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;
            border-left: 5px solid var(--gold); transition: 0.3s;
        }
        
        .res-card.past { opacity: 0.7; border-left-color: #ccc; filter: grayscale(0.5); }

        .res-info h3 { margin: 0; font-size: 1.1rem; color: var(--dark); }
        .res-details { font-size: 0.85rem; color: #777; margin-top: 5px; }

        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-right: 10px; }
        .status-confirmed { background: #eafaf1; color: #2ecc71; }
        .status-pending { background: #fff9e6; color: #f1c40f; }
        .status-cancelled { background: #fff0f0; color: #eb4d4b; }
        .status-finished { background: #eee; color: #666; }

        .actions { display: flex; gap: 10px; align-items: center; }
        .btn-action { text-decoration: none; font-size: 0.75rem; font-weight: 600; padding: 8px 12px; border-radius: 8px; transition: 0.2s; border: none; cursor: pointer; }
        .btn-cancel { color: #eb4d4b; border: 1px solid #eb4d4b; background: transparent; }
        .btn-cancel:hover { background: #eb4d4b; color: white; }
        .btn-remove { color: #888; background: #f0f0f0; }
    </style>
</head>
<body>

<nav class="navbar">
    <h1>RESTAURANT<span>KITCHEN</span></h1>
    <div class="nav-links">
        <a href="customer_dashboard.php">Dashboard</a>
        <a href="order.php">Order Food</a>
        <a href="my_orders.php">My Orders</a>
        <a href="reservation.php">Book Table</a>
        <a href="logout.php" style="color: #ff4757;">Logout</a>
    </div>
</nav>

<div class="container">
    <h2 style="text-align: center;">My Reservations</h2>

    <div class="section-title">Active Reservations</div>
    <?php if (empty($active_reservations)): ?>
        <p style="color:#aaa; font-style:italic;">No active bookings.</p>
    <?php else: ?>
        <?php foreach ($active_reservations as $row): ?>
            <div class="res-card">
                <div class="res-info">
                    <h3>Table for <?php echo $row['people']; ?> People</h3>
                    <div class="res-details">
                        <span>📅 <?php echo date('D, M d, Y', strtotime($row['reservation_date'])); ?></span> | 
                        <span>⏰ <?php echo date('h:i A', strtotime($row['reservation_time'])); ?></span>
                    </div>
                </div>
                <div class="actions">
                    <span class="status-badge status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span>
                    <?php if ($row['status'] !== 'Cancelled'): ?>
                        <a href="?action=cancel&id=<?php echo $row['reservation_id']; ?>" class="btn-action btn-cancel" onclick="return confirm('Cancel this reservation?')">Cancel</a>
                    <?php else: ?>
                        <a href="?action=remove&id=<?php echo $row['reservation_id']; ?>" class="btn-action btn-remove">Clear</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="section-title">Past Reservations</div>
    <?php if (empty($past_reservations)): ?>
        <p style="color:#aaa; font-style:italic;">No past history.</p>
    <?php else: ?>
        <?php foreach ($past_reservations as $row): ?>
            <div class="res-card past">
                <div class="res-info">
                    <h3>Table for <?php echo $row['people']; ?> People</h3>
                    <div class="res-details">
                        <span>📅 <?php echo date('M d, Y', strtotime($row['reservation_date'])); ?></span>
                    </div>
                </div>
                <div class="actions">
                    <span class="status-badge status-finished"><?php echo ($row['status'] == 'Cancelled') ? 'Cancelled' : 'Finished'; ?></span>
                    <a href="?action=remove&id=<?php echo $row['reservation_id']; ?>" class="btn-action btn-remove">Remove</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>