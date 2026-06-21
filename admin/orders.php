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

/* ===========================
   UPDATE ORDER STATUS
=========================== */
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $allowed = ['Pending', 'Preparing', 'Completed', 'Cancelled'];

    if (in_array($status, $allowed)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $status, $order_id);
        if ($stmt->execute()) {
            $success = "Order #$order_id updated to $status!";
        }
    }
}

$orders = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
$adminName = htmlspecialchars($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | Admin</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        :root {
            --gold: #c5a059;
            --dark: #1f1f1f;
            --bg: #f8f9fa;
            --danger: #eb4d4b;
            --blue: #4834d4;
            --success: #2ecc71;
        }

        body { background: var(--bg); font-family: 'Poppins', sans-serif; color: var(--dark); margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }

        /* Header & Nav Styling */
        .admin-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 30px; }
        
        /* Order Grid */
        .orders-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(450px, 1fr)); 
            gap: 25px; 
        }

        .order-card { 
            background: #fff; border-radius: 20px; overflow: hidden; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); display: flex; flex-direction: column;
            border: 1px solid #eee;
        }

        .order-header { 
            padding: 20px; background: #fdfdfd; border-bottom: 1px solid #eee;
            display: flex; justify-content: space-between; align-items: center;
        }

        .order-id { font-weight: 700; font-size: 1.1rem; color: var(--dark); }
        .order-date { font-size: 0.8rem; color: #999; }

        .order-body { padding: 20px; flex-grow: 1; }
        
        /* Mini Table for Items */
        .items-table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 0.9rem; }
        .items-table th { text-align: left; color: #aaa; font-weight: 600; padding-bottom: 10px; border-bottom: 1px solid #f0f0f0; }
        .items-table td { padding: 10px 0; border-bottom: 1px solid #f9f9f9; }

        .total-row { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding-top: 15px; border-top: 2px dashed #eee; }
        .total-amount { font-size: 1.3rem; font-weight: 800; color: var(--gold); }

        /* Status Badges */
        .badge { padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .status-Pending { background: #fff9e6; color: #f1c40f; }
        .status-Preparing { background: #eef2ff; color: var(--blue); }
        .status-Completed { background: #eafaf1; color: var(--success); }
        .status-Cancelled { background: #fff0f0; color: var(--danger); }

        /* Action Area */
        .action-area { 
            padding: 20px; background: #fafafa; border-top: 1px solid #eee;
            display: grid; grid-template-columns: 2fr 1fr; gap: 10px;
        }

        select { 
            padding: 10px; border-radius: 10px; border: 1.5px solid #eee; 
            font-family: inherit; font-weight: 600; cursor: pointer;
        }

        .btn-update { 
            background: var(--dark); color: #fff; border: none; 
            border-radius: 10px; font-weight: 700; cursor: pointer;
            transition: 0.3s;
        }
        .btn-update:hover { background: var(--gold); transform: translateY(-2px); }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; background: #d4edda; color: #155724; }

        @media (max-width: 500px) { .orders-grid { grid-template-columns: 1fr; } .action-area { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="container">
    <div class="admin-header">
        <h1>Active Orders</h1>
        <div style="display:flex; gap: 20px;">
            <a href="dashboard.php" style="color: var(--gold); text-decoration: none; font-weight: 600;">Dashboard</a>
            <a href="menu.php" style="color: var(--gold); text-decoration: none; font-weight: 600;">Menu</a>
        </div>
    </div>

    <?php if($success): ?> <div class="alert"><?php echo $success; ?></div> <?php endif; ?>

    <div class="orders-grid">
        <?php if ($orders->num_rows > 0): ?>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span class="order-id">#<?php echo $order['order_id']; ?></span>
                            <span class="order-date">• <?php echo date("M j, g:i a", strtotime($order['order_date'])); ?></span>
                        </div>
                        <span class="badge status-<?php echo $order['status']; ?>">
                            <?php echo $order['status']; ?>
                        </span>
                    </div>

                    <div class="order-body">
                        <div style="font-weight: 600; margin-bottom: 5px;"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                        
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th style="text-align:right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmtItems = $conn->prepare("SELECT m.item_name, oi.quantity, oi.price FROM order_items oi JOIN menu m ON oi.menu_id = m.menu_id WHERE oi.order_id = ?");
                                $stmtItems->bind_param("i", $order['order_id']);
                                $stmtItems->execute();
                                $items = $stmtItems->get_result();
                                while ($item = $items->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                        <td>× <?php echo $item['quantity']; ?></td>
                                        <td style="text-align:right">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                        <div class="total-row">
                            <span style="font-weight:600; color:#999;">Total Amount</span>
                            <span class="total-amount">₹<?php echo number_format($order['total_amount'], 2); ?></span>
                        </div>
                    </div>

                    <form method="POST" class="action-area">
                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                        <select name="status">
                            <option value="Pending" <?php if($order['status']=="Pending") echo "selected"; ?>>Pending</option>
                            <option value="Preparing" <?php if($order['status']=="Preparing") echo "selected"; ?>>Preparing</option>
                            <option value="Completed" <?php if($order['status']=="Completed") echo "selected"; ?>>Completed</option>
                            <option value="Cancelled" <?php if($order['status']=="Cancelled") echo "selected"; ?>>Cancelled</option>
                        </select>
                        <button type="submit" name="update_status" class="btn-update">Update</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 50px; color: #aaa;">No orders to display.</div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>