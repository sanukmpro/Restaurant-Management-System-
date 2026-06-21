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

// Handle Order Submission (Logic remains the same)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['place_order'])) {
    $username = $_SESSION['username'];
    $items = $_POST['items'] ?? [];
    $total = 0;
    $orderItems = [];

    foreach ($items as $menu_id => $quantity) {
        $quantity = intval($quantity);
        if ($quantity > 0) {
            $stmt = $conn->prepare("SELECT item_name, price FROM menu WHERE menu_id = ?");
            $stmt->bind_param("i", $menu_id);
            $stmt->execute();
            $menu = $stmt->get_result()->fetch_assoc();

            if ($menu) {
                $price = $menu['price'];
                $total += $price * $quantity;
                $orderItems[] = ['menu_id' => $menu_id, 'quantity' => $quantity, 'price' => $price];
            }
        }
    }

    if (!empty($orderItems)) {
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, phone, total_amount, status, order_date) VALUES (?, 'Guest', ?, 'Pending', NOW())");
        $stmt->bind_param("sd", $username, $total);
        $stmt->execute();
        $order_id = $conn->insert_id;

        $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, menu_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($orderItems as $item) {
            $stmtItem->bind_param("iiid", $order_id, $item['menu_id'], $item['quantity'], $item['price']);
            $stmtItem->execute();
        }
        $success = "Order placed! Total: ₹" . number_format($total, 2);
    } else {
        $error = "Please select at least one item.";
    }
}

$menus = $conn->query("SELECT * FROM menu");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Online | Restaurant System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        :root { --gold: #c5a059; --dark: #1f1f1f; --bg: #f8f9fa; }
        body { background: var(--bg); font-family: 'Poppins', sans-serif; margin: 0; padding-bottom: 120px; }
        
        .navbar { background: var(--dark); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; color: white; position: sticky; top: 0; z-index: 1000; }
        .navbar h1 span { color: var(--gold); }
        .navbar a { color: white; text-decoration: none; font-size: 0.85rem; margin-left: 20px; font-weight: 500; }

        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .menu-header { margin-bottom: 30px; text-align: center; }
/* Reverted Navbar to original compact size */
.navbar { 
    background: var(--dark); 
    padding: 10px 40px; /* Reduced vertical padding from 15px to 10px */
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    color: white; 
    position: sticky; 
    top: 0; 
    z-index: 1000; 
}

.navbar h1 { 
    font-size: 1.3rem; /* Slightly smaller font to match previous scale */
    margin: 0; 
    letter-spacing: 1px;
}

.navbar h1 span { 
    color: var(--gold); 
}

.navbar a { 
    color: white; 
    text-decoration: none; 
    font-size: 0.85rem; 
    margin-left: 20px; 
    font-weight: 500; 
    transition: 0.3s; 
}

.navbar a:hover { 
    color: var(--gold); 
}        
        /* Search Bar Styling */
        .search-container { max-width: 500px; margin: 0 auto 40px; position: relative; }
        #menuSearch {
            width: 100%; padding: 15px 25px; border-radius: 30px; border: 1px solid #ddd;
            font-family: inherit; font-size: 1rem; box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            outline: none; transition: 0.3s;
        }
        #menuSearch:focus { border-color: var(--gold); box-shadow: 0 5px 20px rgba(197, 160, 89, 0.2); }

        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }

        .food-card {
            background: #fff; border-radius: 20px; overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.03); border: 1px solid #eee;
            transition: 0.3s; display: flex; flex-direction: column;
        }
        .food-card:hover { transform: translateY(-5px); border-color: var(--gold); }

        .food-image { width: 100%; height: 180px; object-fit: cover; background: #eee; border-bottom: 1px solid #eee; }
        .food-info { padding: 20px; flex-grow: 1; }
        .food-name { font-size: 1.1rem; font-weight: 700; margin-bottom: 5px; color: var(--dark); }
        .food-price { color: var(--gold); font-weight: 700; font-size: 1.2rem; }
        
        .qty-wrapper { background: #fafafa; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f0f0f0; }
        .qty-input { width: 60px; padding: 8px; border-radius: 8px; border: 1.5px solid #ddd; text-align: center; font-weight: 600; }

        .summary-bar { position: fixed; bottom: 0; left: 0; right: 0; background: var(--dark); color: white; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; z-index: 2000; }
        .total-display { font-size: 1.4rem; font-weight: 700; color: var(--gold); }
        .btn-order { background: var(--gold); color: var(--dark); border: none; padding: 12px 35px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        
        .alert { max-width: 600px; margin: 0 auto 30px; padding: 15px; border-radius: 12px; text-align: center; }
        .success { background: #eafaf1; color: #2ecc71; }
    </style>
</head>
<body>

<nav class="navbar">
    <h1>RESTAURANT<span>SYSTEM</span></h1>
    <div class="nav-links">
        <a href="customer_dashboard.php">Dashboard</a>
        <a href="my_orders.php">My Orders</a>
        <a href="logout.php" style="color: #ff4757;">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="menu-header">
        <h2>Gourmet Menu</h2>
        <p>Explore our kitchen's finest creations.</p>
    </div>

    <div class="search-container">
        <input type="text" id="menuSearch" placeholder="Search for your favorite food (e.g. Biriyani, Burger)...">
    </div>

    <?php if($success): ?> <div class="alert success"><?php echo $success; ?></div> <?php endif; ?>

    <form method="POST" id="orderForm">
        <div class="menu-grid" id="menuGrid">
            <?php while ($row = $menus->fetch_assoc()): ?>
                <div class="food-card">
                    <?php 
                        $rawImg = $row['image'];
                        if (filter_var($rawImg, FILTER_VALIDATE_URL)) { $imgSrc = $rawImg; }
                        elseif (!empty($rawImg)) { $imgSrc = "images/" . $rawImg; }
                        else { $imgSrc = "images/default-food.jpg"; }
                    ?>
                    <img src="<?php echo $imgSrc; ?>" class="food-image" onerror="this.onerror=null;this.src='https://via.placeholder.com/300x200?text=Food+Image';">

                    <div class="food-info">
                        <div class="food-name"><?php echo htmlspecialchars($row['item_name']); ?></div>
                        <div class="food-price">₹<?php echo number_format($row['price'], 2); ?></div>
                    </div>
                    
                    <div class="qty-wrapper">
                        <span style="font-size: 0.75rem; color: #888; font-weight:700;">QTY</span>
                        <input type="number" name="items[<?php echo $row['menu_id']; ?>]" 
                               class="qty-input" data-price="<?php echo $row['price']; ?>" 
                               min="0" value="0">
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="summary-bar">
            <div>
                <span style="color: #aaa; font-size: 0.8rem; display: block;">ESTIMATED TOTAL</span>
                <span class="total-display">₹<span id="liveTotal">0.00</span></span>
            </div>
            <button type="submit" name="place_order" class="btn-order">Place Order Now</button>
        </div>
    </form>
</div>

<script>
    const qtyInputs = document.querySelectorAll('.qty-input');
    const totalDisplay = document.getElementById('liveTotal');
    const searchInput = document.getElementById('menuSearch');
    const foodCards = document.querySelectorAll('.food-card');

    // 1. Live Total Calculation
    function calculateTotal() {
        let total = 0;
        qtyInputs.forEach(input => {
            const price = parseFloat(input.getAttribute('data-price'));
            const quantity = parseInt(input.value) || 0;
            total += price * quantity;
        });
        totalDisplay.innerText = total.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    }

    qtyInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    // 2. Real-time Search Filter
    searchInput.addEventListener('input', function() {
        const query = searchInput.value.toLowerCase();

        foodCards.forEach(card => {
            const foodName = card.querySelector('.food-name').innerText.toLowerCase();
            if (foodName.includes(query)) {
                card.style.display = 'flex'; // Show card
            } else {
                card.style.display = 'none'; // Hide card
            }
        });
    });
</script>

</body>
</html>