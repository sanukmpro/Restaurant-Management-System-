<?php
session_start();
require_once __DIR__ . '/config.php';

// If already logged in, redirect based on role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: customer_dashboard.php");
    }
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: " . ($user['role'] === 'admin' ? "admin/dashboard.php" : "customer_dashboard.php"));
                exit();
            } else {
                $error = "The password you entered is incorrect.";
            }
        } else {
            $error = "Account not found with that username.";
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
    <title>Login | Restaurant Management System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        :root {
            --gold: #c5a059;
            --dark: #1f1f1f;
            --bg: #f8f9fa;
            --danger: #eb4d4b;
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

        /* Modern Navbar */
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
            transition: color 0.3s;
        }
        nav a:hover { color: var(--gold); }

        .login-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-card {
            background: #fff;
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            text-align: center;
        }

        .login-card h2 {
            margin: 0 0 10px 0;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .login-card p {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        /* Form Inputs */
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

        .btn-login {
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
        .btn-login:hover {
            background: #333;
            transform: translateY(-2px);
        }

        /* Error Message */
        .error-msg {
            background: #fff0f0;
            color: var(--danger);
            padding: 12px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            border: 1px solid #ffdede;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 0.8rem;
            color: #aaa;
            background: white;
            border-top: 1px solid #eee;
        }

        .signup-link {
            margin-top: 25px;
            font-size: 0.9rem;
            color: #666;
        }
        .signup-link a {
            color: var(--gold);
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>

<nav>
    <a href="login.php" style="color: var(--gold);">Login</a>
    <a href="signup.php">Signup</a>
</nav>

<div class="login-container">
    <div class="login-card">
        <h2>Welcome Back</h2>
        <p>Please enter your details to sign in</p>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter your username" required autofocus>
            </div>
            
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="signup-link">
            Don't have an account? <a href="signup.php">Create one</a>
        </div>
    </div>
</div>

<footer class="footer">
    &copy; 2025 Restaurant Management System | All Rights Reserved
</footer>

</body>
</html>