<?php
session_start();
require_once __DIR__ . '/config.php';

// Protect page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$success = "";
$error = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_booking'])) {
    $fullName = $_SESSION['username']; // Using session name to link to "My Reservations"
    $people = intval($_POST['people']);
    $resDate = $_POST['res_date'];
    $resTime = $_POST['res_time'];

    if ($people > 0 && !empty($resDate) && !empty($resTime)) {
        $stmt = $conn->prepare("INSERT INTO reservations (full_name, people, reservation_date, reservation_time, status) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("siss", $fullName, $people, $resDate, $resTime);
        
        if ($stmt->execute()) {
            $success = "Table reserved successfully! Check 'My Reservations' for status.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    } else {
        $error = "Please fill in all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Table | Restaurant System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        :root { --gold: #c5a059; --dark: #1f1f1f; --bg: #f4f4f9; --white: #ffffff; }
        
        body { background: var(--bg); font-family: 'Poppins', sans-serif; margin: 0; }
        
        /* Navbar */
        .navbar { background: var(--dark); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; color: white; position: sticky; top: 0; z-index: 1000; }
        .navbar h1 { font-size: 1.2rem; margin: 0; letter-spacing: 1px; }
        .navbar h1 span { color: var(--gold); }
        .navbar a { color: white; text-decoration: none; font-size: 0.85rem; margin-left: 20px; font-weight: 500; }

        .container { max-width: 500px; margin: 60px auto; padding: 0 20px; }
        
        .booking-card {
            background: var(--white);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            text-align: center;
        }

        .booking-card h2 { margin-bottom: 10px; font-weight: 700; color: var(--dark); }
        .booking-card p { color: #888; font-size: 0.9rem; margin-bottom: 30px; }

        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 8px; text-transform: uppercase; }
        
        input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: 1.5px solid #eee;
            font-family: inherit;
            box-sizing: border-box;
            transition: 0.3s;
        }
        input:focus { border-color: var(--gold); outline: none; background: #fffdf9; }

        .btn-submit {
            width: 100%;
            background: var(--dark);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-submit:hover { background: var(--gold); color: var(--dark); transform: translateY(-2px); }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; }
        .success { background: #eafaf1; color: #2ecc71; border: 1px solid #d4edda; }
        .error { background: #fff0f0; color: #eb4d4b; border: 1px solid #f8d7da; }
    </style>
</head>
<body>

<nav class="navbar">
    <h1>RESTAURANT<span>KITCHEN</span></h1>
    <div class="nav-links">
        <a href="customer_dashboard.php">Dashboard</a>
        <a href="order.php">Order Food</a>
        <a href="my_reservations.php">My Reservations</a>
        <a href="logout.php" style="color: #ff4757;">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="booking-card">
        <h2>Reserve a Table</h2>
        <p>Join us for an unforgettable dining experience.</p>

        <?php if($success): ?> <div class="alert success"><?php echo $success; ?></div> <?php endif; ?>
        <?php if($error): ?> <div class="alert error"><?php echo $error; ?></div> <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Number of Guests</label>
                <input type="number" name="people" placeholder="How many people?" min="1" max="20" required>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="res_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label>Preferred Time</label>
                <input type="time" name="res_time" required>
            </div>

            <button type="submit" name="submit_booking" class="btn-submit">Confirm Booking</button>
        </form>
    </div>
</div>

</body>
</html>