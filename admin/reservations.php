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
   UPDATE RESERVATION STATUS
=========================== */
if (isset($_POST['update_status'])) {
    $reservation_id = intval($_POST['reservation_id']);
    $status = $_POST['status'];
    
    // Matched exactly with the dropdown options
    $allowed = ['Confirmed', 'Completed', 'Cancelled', 'Pending'];

    if (in_array($status, $allowed)) {
        $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE reservation_id = ?");
        $stmt->bind_param("si", $status, $reservation_id);
        if ($stmt->execute()) {
            $success = "Reservation #$reservation_id updated to $status!";
        }
        $stmt->close();
    }
}

$result = $conn->query("SELECT * FROM reservations ORDER BY reservation_date DESC, reservation_time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations | Admin</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        :root {
            --gold: #c5a059;
            --dark: #1f1f1f;
            --bg: #f8f9fa;
            --danger: #eb4d4b;
            --blue: #4834d4;
            --success: #2ecc71;
            --warning: #f1c40f;
        }

        body { background: var(--bg); font-family: 'Poppins', sans-serif; color: var(--dark); margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }

        .admin-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 30px; }
        
        /* Grid Layout */
        .res-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); 
            gap: 25px; 
        }

        .res-card { 
            background: #fff; border-radius: 20px; overflow: hidden; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #eee;
            display: flex; flex-direction: column;
        }

        .res-header { 
            padding: 20px; background: #fafafa; border-bottom: 1px solid #eee;
            display: flex; justify-content: space-between; align-items: center;
        }

        .res-body { padding: 25px; flex-grow: 1; }
        
        /* Typography */
        .customer-name { font-size: 1.2rem; font-weight: 700; margin: 0 0 10px 0; }
        .res-detail { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; color: #666; font-size: 0.9rem; }
        .icon { width: 20px; text-align: center; }

        /* Badges */
        .badge { padding: 5px 12px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
        .status-Pending, .status-Booked { background: #fff9e6; color: var(--warning); }
        .status-Confirmed { background: #eef2ff; color: var(--blue); }
        .status-Completed { background: #eafaf1; color: var(--success); }
        .status-Cancelled { background: #fff0f0; color: var(--danger); }

        /* Action Footer */
        .res-footer { 
            padding: 15px 20px; background: #fdfdfd; border-top: 1px solid #eee;
            display: grid; grid-template-columns: 2fr 1fr; gap: 10px;
        }

        select { 
            padding: 10px; border-radius: 10px; border: 1.5px solid #eee; 
            font-family: inherit; font-size: 0.85rem; cursor: pointer;
        }

        .btn-update { 
            background: var(--dark); color: #fff; border: none; 
            border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s;
        }
        .btn-update:hover { background: var(--gold); }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; background: #d4edda; color: #155724; }
    </style>
</head>
<body>

<div class="container">
    <div class="admin-header">
        <h1>Reservations</h1>
        <div style="display:flex; gap: 20px;">
            <a href="dashboard.php" style="color: var(--gold); text-decoration: none; font-weight: 600;">Dashboard</a>
            <a href="orders.php" style="color: var(--gold); text-decoration: none; font-weight: 600;">Orders</a>
        </div>
    </div>

    <?php if($success): ?> <div class="alert"><?php echo $success; ?></div> <?php endif; ?>

    <div class="res-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="res-card">
                    <div class="res-header">
                        <span style="color: #aaa; font-size: 0.8rem; font-weight: 600;">ID: #<?php echo $row['reservation_id']; ?></span>
                        <span class="badge status-<?php echo $row['status']; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </div>

                    <div class="res-body">
                        <h3 class="customer-name"><?php echo htmlspecialchars($row['full_name']); ?></h3>
                        
                        <div class="res-detail">
                            <span class="icon">📅</span>
                            <span><strong>Date:</strong> <?php echo date("D, M j, Y", strtotime($row['reservation_date'])); ?></span>
                        </div>
                        
                        <div class="res-detail">
                            <span class="icon">⏰</span>
                            <span><strong>Time:</strong> <?php echo date("g:i A", strtotime($row['reservation_time'])); ?></span>
                        </div>
                        
                        <div class="res-detail">
                            <span class="icon">👥</span>
                            <span><strong>Party Size:</strong> <?php echo $row['people']; ?> People</span>
                        </div>
                    </div>

                    <form method="POST" class="res-footer">
                        <input type="hidden" name="reservation_id" value="<?php echo $row['reservation_id']; ?>">
                        <select name="status">
                            <option value="Pending" <?php if($row['status']=="Pending") echo "selected"; ?>>Pending</option>
                            <option value="Confirmed" <?php if($row['status']=="Confirmed") echo "selected"; ?>>Confirmed</option>
                            <option value="Completed" <?php if($row['status']=="Completed") echo "selected"; ?>>Completed</option>
                            <option value="Cancelled" <?php if($row['status']=="Cancelled") echo "selected"; ?>>Cancelled</option>
                        </select>
                        <button type="submit" name="update_status" class="btn-update">Update</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 50px; color: #aaa;">No upcoming reservations found.</div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>