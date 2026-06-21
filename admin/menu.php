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
   ADD OR UPDATE DISH LOGIC
=========================== */
if (isset($_POST['save_dish'])) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['desc']);
    $price = floatval($_POST['price']);
    $image_url = trim($_POST['image_url']);
    $menu_id = isset($_POST['menu_id']) && !empty($_POST['menu_id']) ? intval($_POST['menu_id']) : null;
    
    $final_image = "";

    // If editing, keep existing image unless a new one is provided
    if ($menu_id) {
        $stmt_img = $conn->prepare("SELECT image FROM menu WHERE menu_id = ?");
        $stmt_img->bind_param("i", $menu_id);
        $stmt_img->execute();
        $res_img = $stmt_img->get_result()->fetch_assoc();
        $final_image = $res_img['image'] ?? "";
    }

    // Process File Upload (Highest Priority)
    if (!empty($_FILES['image_file']['name'])) {
        $target_dir = "../assets/images/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_ext = strtolower(pathinfo($_FILES["image_file"]["name"], PATHINFO_EXTENSION));
        $file_name = time() . "_" . uniqid() . "." . $file_ext;
        
        if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_dir . $file_name)) {
            $final_image = "assets/images/" . $file_name;
        }
    } 
    // Process URL (Second Priority)
    elseif (!empty($image_url)) {
        $final_image = $image_url;
    }

    if ($menu_id) {
        $stmt = $conn->prepare("UPDATE menu SET item_name=?, description=?, price=?, image=? WHERE menu_id=?");
        $stmt->bind_param("ssdsi", $name, $desc, $price, $final_image, $menu_id);
        $success = "Item updated successfully!";
    } else {
        $stmt = $conn->prepare("INSERT INTO menu (item_name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $desc, $price, $final_image);
        $success = "New dish added to menu!";
    }
    $stmt->execute();
}

if (isset($_POST['delete'])) {
    $id = intval($_POST['menu_id']);
    $stmt = $conn->prepare("DELETE FROM menu WHERE menu_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $success = "Item removed.";
}

$result = $conn->query("SELECT * FROM menu ORDER BY menu_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management | Admin</title>
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

        /* Form Card */
        .card-form { 
            background: #fff; padding: 30px; border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.05); margin-bottom: 50px;
        }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }

        input, textarea { 
            width: 100%; padding: 12px; border: 1.5px solid #eee; 
            border-radius: 10px; box-sizing: border-box; font-family: inherit;
            transition: border-color 0.3s;
        }
        input:focus { border-color: var(--gold); outline: none; }

        .btn-save { 
            background: var(--dark); color: #fff; padding: 15px; border: none; 
            border-radius: 10px; width: 100%; cursor: pointer; font-weight: 700;
            font-size: 1rem; margin-top: 15px; transition: transform 0.2s;
        }
        .btn-save:hover { transform: translateY(-2px); background: #333; }

        /* Grid */
        .menu-grid { 
            display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); 
            gap: 25px; 
        }

        .dish-card { 
            background: #fff; border-radius: 20px; overflow: hidden; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.05); display: flex; flex-direction: column;
        }

        .img-container { position: relative; width: 100%; height: 200px; background: #eee; }
        .dish-img { width: 100%; height: 100%; object-fit: cover; }

        .dish-info { padding: 20px; flex-grow: 1; }
        .price-tag { color: var(--gold); font-weight: 700; font-size: 1.2rem; }
        
        /* Unified Action Buttons */
        .btn-group { 
            display: grid; grid-template-columns: 1fr 1fr; 
            gap: 10px; padding: 15px; background: #fdfdfd;
        }
        .btn { 
            padding: 10px; border-radius: 8px; border: none; font-weight: 600; 
            cursor: pointer; text-decoration: none; text-align: center; font-size: 0.85rem;
        }
        .edit { background: #f0f4ff; color: var(--blue); }
        .delete { background: #fff0f0; color: var(--danger); }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
        .success { background: #d4edda; color: #155724; }
    </style>
</head>
<body>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 30px;">
        <h1 style="margin:0">Menu Editor</h1>
        <a href="dashboard.php" style="color: var(--gold); text-decoration: none; font-weight: 600;">← Exit Admin</a>
    </div>

    <?php if($success): ?> <div class="alert success"><?php echo $success; ?></div> <?php endif; ?>

    <div class="card-form">
        <h2 id="form-title" style="margin-top:0">Add New Dish</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="menu_id" id="edit_id">
            <div class="form-grid">
                <div class="full-width">
                    <input type="text" name="name" id="edit_name" placeholder="Dish Name" required>
                </div>
                <div class="full-width">
                    <textarea name="desc" id="edit_desc" rows="2" placeholder="Description"></textarea>
                </div>
                <div>
                    <input type="number" step="0.01" name="price" id="edit_price" placeholder="Price (₹)" required>
                </div>
                <div>
                    <input type="text" name="image_url" id="edit_url" placeholder="Image URL (Optional)">
                </div>
                <div class="full-width">
                    <label style="font-size: 0.8rem; color: #666;">Or Upload Image File:</label>
                    <input type="file" name="image_file" accept="image/*">
                </div>
            </div>
            <button type="submit" name="save_dish" class="btn-save">Save Changes</button>
            <button type="button" onclick="resetForm()" style="width:100%; background:none; border:none; color:#aaa; cursor:pointer; margin-top:10px;">Reset Form</button>
        </form>
    </div>

    <div class="menu-grid">
        <?php while ($row = $result->fetch_assoc()): 
            // Smart Pathing Logic
            $display_img = $row['image'];
            if(empty($display_img)) {
                $display_img = "https://via.placeholder.com/400x300?text=No+Image";
            } elseif(!filter_var($display_img, FILTER_VALIDATE_URL)) {
                $display_img = "../" . $display_img;
            }
        ?>
        <div class="dish-card">
            <div class="img-container">
                <img src="<?php echo $display_img; ?>" class="dish-img" alt="Dish">
            </div>
            <div class="dish-info">
                <div style="display:flex; justify-content:space-between;">
                    <h3 style="margin:0; font-size:1.1rem;"><?php echo htmlspecialchars($row['item_name']); ?></h3>
                    <span class="price-tag">₹<?php echo number_format($row['price'], 2); ?></span>
                </div>
                <p style="color:#777; font-size:0.85rem; line-height:1.4;"><?php echo htmlspecialchars($row['description']); ?></p>
            </div>
            <div class="btn-group">
                <button class="btn edit" onclick='prepareEdit(<?php echo json_encode($row); ?>)'>Edit</button>
                <form method="POST" onsubmit="return confirm('Delete this item?')" style="margin:0">
                    <input type="hidden" name="menu_id" value="<?php echo $row['menu_id']; ?>">
                    <button type="submit" name="delete" class="btn delete" style="width:100%">Delete</button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
    function prepareEdit(data) {
        document.getElementById('form-title').innerText = "Edit: " + data.item_name;
        document.getElementById('edit_id').value = data.menu_id;
        document.getElementById('edit_name').value = data.item_name;
        document.getElementById('edit_desc').value = data.description;
        document.getElementById('edit_price').value = data.price;
        document.getElementById('edit_url').value = data.image;
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    function resetForm() {
        document.getElementById('form-title').innerText = "Add New Dish";
        document.getElementById('edit_id').value = "";
        document.querySelector('form').reset();
    }
</script>

</body>
</html>