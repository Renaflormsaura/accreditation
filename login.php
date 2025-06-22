<?php
session_start();
require 'connection.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $message = "Please enter both username and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            // Redirect to admin home/dashboard
            header("Location: admin_home.php");
            exit();
        } else {
            $message = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login - PUP Unisan Accreditation</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap');

        * {
            box-sizing: border-box;
        }
        body {
            background: linear-gradient(135deg, #800000 0%, #a52a2a 100%);
            font-family: 'Inter', sans-serif;
            margin: 0;
            color: white;
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .container {
            background: rgba(255, 255, 255, 0.05);
            padding: 40px 32px;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        h1 {
            margin-bottom: 24px;
            font-weight: 700;
            font-size: 2rem;
            color: #ffefe6;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        label {
            text-align: left;
            font-weight: 600;
            margin-bottom: 4px;
        }
        input[type="text"],
        input[type="password"] {
            padding: 12px 16px;
            border-radius: 8px;
            border: none;
            font-size: 1rem;
            outline: none;
            transition: box-shadow 0.2s ease;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            box-shadow: 0 0 8px 2px #a52a2a;
        }
        button {
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: #a52a2a;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover, button:focus {
            background: #800000;
        }
        .message {
            margin-top: 20px;
            font-weight: 600;
            color: #ffe6e6;
            background: rgba(255,255,255,0.1);
            padding: 12px 16px;
            border-radius: 10px;
        }
        a {
            color: #ffe6e6;
            text-decoration: none;
        }
        a:hover, a:focus {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container" role="main">
        <h1>Admin Login</h1>
        <?php if ($message): ?>
            <div class="message" aria-live="polite" role="alert"><?php echo $message ?></div>
        <?php endif; ?>
        <form method="POST" novalidate>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" autocomplete="username" required minlength="4" maxlength="50" placeholder="Enter username" />

            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required minlength="6" placeholder="Enter password" />

            <button type="submit">Login</button>
        </form>
        <p style="margin-top: 20px;">Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </div>

    <script>
        // Client-side basic validation
        const form = document.querySelector('form');
        form.addEventListener('submit', e => {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in both fields.');
            }
        });
    </script>
</body>
</html>
