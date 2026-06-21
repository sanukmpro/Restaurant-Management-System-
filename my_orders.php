<?php
session_start();
require_once __DIR__ . '/config.php';

// Protect page (customer only)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Get orders for this customer
$stmt = $conn->prepare("
    SELECT order_id, total_amount, status, order_date 
    FROM orders 
    WHERE customer_name = ? 
    ORDER BY order_date DESC
");
$stmt->bind_param("s", $username);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Order History | Restaurant System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        :root { --gold: #c5a059; --dark: #1f1f1f; --bg: #f4f4f9; --white: #ffffff; }
        
        body { background: var(--bg); font-family: 'Poppins', sans-serif; margin: 0; color: #333; }
        
        /* Navbar */
        .navbar { background: var(--dark); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; color: white; position: sticky; top: 0; z-index: 1000; }
        .navbar h1 { font-size: 1.2rem; margin: 0; letter-spacing: 1px; }
        .navbar h1 span { color: var(--gold); }
        .navbar a { color: white; text-decoration: none; font-size: 0.85rem; margin-left: 20px; font-weight: 500; transition: 0.3s; }
        .navbar a:hover { color: var(--gold); }

        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        .page-header { text-align: center; margin-bottom: 40px; }
        .page-header h2 { font-size: 2rem; font-weight: 700; margin: 0; color: var(--dark); }

        /* Order Card */
        .order-card {
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .order-id { font-size: 1.1rem; font-weight: 700; color: var(--dark); }
        .order-date { font-size: 0.85rem; color: #888; }
        
        /* Status Badges */
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-pending { background: #fff9e6; color: #f1c40f; }
        .status-completed { background: #eafaf1; color: #2ecc71; }
        .status-cancelled { background: #fff0f0; color: #eb4d4b; }

        /* Item Table */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; font-size: 0.8rem; color: #aaa; text-transform: uppercase; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        td { padding: 12px 0; font-size: 0.95rem; border-bottom: 1px solid #fafafa; }
        
        .total-row {
            margin-top: 20px;
            padding-top: 15px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            border-top: 2px solid #f0f0f0;
        }
        .total-label { font-size: 0.9rem; color: #888; margin-right: 15px; }
        .total-price { font-size: 1.4rem; font-weight: 700; color: var(--gold); }

        .empty-state { text-align: center; padding: 60px; color: #888; }
        .btn-order-now { background: var(--gold); color: var(--dark); padding: 10px 25px; border-radius: 8px; text-decoration: none; font-weight: 700; display: inline-block; margin-top: 15px; }
    </style>
</head>
<body>

<nav class="navbar">
    <h1>RESTAURANT<span>SYSTEM</span></h1>
    <div class="nav-links">
        <a href="customer_dashboard.php">Dashboard</a>
        <a href="order.php">Order Food</a>
        <a href="logout.php" style="color: #ff4757;">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h2>Order History</h2>
    </div>

    <?php if ($orders->num_rows > 0): ?>
        <?php while ($order = $orders->fetch_assoc()): ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-id">Order #<?php echo $order['order_id']; ?></div>
                        <div class="order-date"><?php echo date('M d, Y | h:i A', strtotime($order['order_date'])); ?></div>
                    </div>
                    <?php 
                        $statusClass = 'status-' . strtolower($order['status']);
                    ?>
                    <span class="status-badge <?php echo $statusClass; ?>">
                        <?php echo htmlspecialchars($order['status']); ?>
                    </span>
                </div>

                <?php
                $stmtItems = $conn->prepare("
                    SELECT m.item_name, oi.quantity, oi.price 
                    FROM order_items oi
                    JOIN menu m ON oi.menu_id = m.menu_id
                    WHERE oi.order_id = ?
                ");
                $stmtItems->bind_param("i", $order['order_id']);
                $stmtItems->execute();
                $items = $stmtItems->get_result();
                ?>

                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th style="text-align: right;">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $items->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                <td>x<?php echo $item['quantity']; ?></td>
                                <td style="text-align: right;">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div class="total-row">
                    <span class="total-label">Grand Total</span>
                    <span class="total-price">₹<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <p>You haven't placed any orders yet.</p>
            <a href="order.php" class="btn-order-now">Place Your First Order</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>