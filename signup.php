<?php
session_start();
require 'connection.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $message = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $message = "Username already taken.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($insert->execute([$username, $hash])) {
                $message = "Signup successful! You may now <a href='login.php' style='color: white; text-decoration: underline;'>login</a>.";
            } else {
                $message = "Signup failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Signup - PUP Unisan Accreditation</title>
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
        <h1>Admin Signup</h1>
        <?php if ($message): ?>
            <div class="message" aria-live="polite" role="alert"><?php echo $message ?></div>
        <?php endif; ?>
        <form method="POST" novalidate>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" autocomplete="username" required minlength="4" maxlength="50" placeholder="Enter username" />

            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="new-password" required minlength="6" placeholder="Enter password" />

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" required minlength="6" placeholder="Confirm password" />

            <button type="submit">Sign Up</button>
        </form>
        <p style="margin-top: 20px;">Already have an account? <a href="login.php">Login here</a>.</p>
    </div>

    <script>
        // Simple client-side validation for password match
        const form = document.querySelector('form');
        form.addEventListener('submit', e => {
            const pw = document.getElementById('password').value;
            const cpw = document.getElementById('confirm_password').value;
            if (pw !== cpw) {
                e.preventDefault();
                alert('Passwords do not match.');
            }
        });
    </script>
</body>
</html>

