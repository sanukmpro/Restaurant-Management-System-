<?php
session_start();
require_once __DIR__ . '/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: customer_dashboard.php");
    exit();
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "That username is already taken!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'customer')");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = "Account created successfully! You can now log in.";
            } else {
                $error = "Database error. Please try again later.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Restaurant System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        :root {
            --gold: #c5a059;
            --dark: #1f1f1f;
            --bg: #f8f9fa;
            --danger: #eb4d4b;
            --success: #2ecc71;
        }

        body { 
            background: var(--bg); 
            font-family: 'Poppins', sans-serif; 
            color: var(--dark); 
            margin: 0; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh;
        }

        /* Navigation */
        nav {
            background: var(--dark);
            padding: 15px 40px;
            display: flex;
            justify-content: center;
            gap: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        nav a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }
        nav a:hover { color: var(--gold); }

        .form-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .auth-card {
            background: #fff;
            width: 100%;
            max-width: 450px;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            text-align: center;
        }

        .auth-card h2 { margin: 0 0 10px 0; font-size: 1.8rem; font-weight: 700; }
        .auth-card p { color: #888; font-size: 0.9rem; margin-bottom: 30px; }

        /* Inputs */
        .input-group { margin-bottom: 20px; text-align: left; }
        label { font-size: 0.8rem; font-weight: 700; color: #555; margin-bottom: 5px; display: block; }
        
        input {
            width: 100%;
            padding: 14px;
            border: 1.5px solid #eee;
            border-radius: 12px;
            box-sizing: border-box;
            font-family: inherit;
            transition: all 0.3s;
            background: #fdfdfd;
        }
        input:focus {
            outline: none;
            border-color: var(--gold);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(197, 160, 89, 0.1);
        }

        .btn-signup {
            background: var(--dark);
            color: white;
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-signup:hover { background: #333; transform: translateY(-2px); }

        /* Alerts */
        .alert {
            padding: 12px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }
        .alert-error { background: #fff0f0; color: var(--danger); border-color: #ffdede; }
        .alert-success { background: #eafaf1; color: var(--success); border-color: #d4edda; }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 0.8rem;
            color: #aaa;
            background: white;
            border-top: 1px solid #eee;
        }

        .login-link { margin-top: 25px; font-size: 0.9rem; color: #666; }
        .login-link a { color: var(--gold); text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>

<nav>
    <a href="login.php">Login</a>
    <a href="signup.php" style="color: var(--gold);">Signup</a>
</nav>

<div class="form-container">
    <div class="auth-card">
        <h2>Join Us</h2>
        <p>Create your account to start ordering</p>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <div style="margin-top:10px;"><a href="login.php" style="color:inherit; font-weight:700;">Click here to login</a></div>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Choose a username" required>
            </div>
            
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="example@mail.com" required>
            </div>
            
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Min. 6 characters" required>
            </div>

            <button type="submit" class="btn-signup">Create Account</button>
        </form>
        <?php endif; ?>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</div>

<footer class="footer">
    &copy; 2025 Restaurant Management System | All Rights Reserved
</footer>

</body>
</html>