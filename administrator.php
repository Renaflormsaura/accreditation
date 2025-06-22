<?php
session_start();
require 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all admin users
$stmt = $pdo->query("SELECT id, username FROM users ORDER BY id ASC");
$admins = $stmt->fetchAll();

// Handle deletion
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    if ($del_id !== $_SESSION['user_id']) { // prevent self deletion
        $del_stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $del_stmt->execute([$del_id]);
        header("Location: administrator.php");
        exit();
    } else {
        $error = "You cannot delete your own account.";
    }
}

// Handle add admin form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $new_username = trim($_POST['username']);
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_username) || empty($new_password) || empty($confirm_password)) {
        $message = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Check if username exists
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$new_username]);
        if ($check->fetch()) {
            $message = "Username already taken.";
        } else {
            $hashed_pw = password_hash($new_password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO users (username, password) VALUES (?,?)");
            if ($insert->execute([$new_username, $hashed_pw])) {
                header("Location: administrator.php");
                exit();
            } else {
                $message = "Failed to add new admin.";
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
<title>Administrator Management - PUP Unisan Accreditation</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap');
    * { box-sizing: border-box; }
    body {
        margin: 0; font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #800000 0%, #a52a2a 100%);
        color: white; min-height: 100vh;
        padding: 20px; max-width: 900px; margin-left: auto; margin-right: auto;
    }
    header h1 {
        margin-bottom: 20px;
    }
    a.back-link {
        color: #ffefe6; text-decoration: none; font-weight: 700;
        display: inline-block; margin-bottom: 20px;
    }
    a.back-link:hover {
        text-decoration: underline;
    }
    table {
        width: 100%; border-collapse: collapse; margin-bottom: 30px;
    }
    th, td {
        padding: 12px 15px; border-bottom: 1px solid #a52a2a; text-align: left;
    }
    th {
        background-color: rgba(255,255,255,0.1);
    }
    button.delete-btn {
        background: #a52a2a; color: white;
        border: none;
        padding: 6px 12px; border-radius: 8px;
        cursor: pointer; font-weight: 600;
        transition: background-color 0.3s ease;
    }
    button.delete-btn:hover {
        background: #800000;
    }
    form {
        background: rgba(255,255,255,0.1);
        padding: 24px;
        border-radius: 16px;
        max-width: 400px;
        margin-top: 20px;
    }
    form label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
    }
    form input {
        width: 100%; padding: 10px 14px;
        border-radius: 10px; border: none; margin-bottom: 18px;
        font-size: 1rem;
    }
    form button {
        background: #a52a2a; color: white; font-weight: 700;
        font-size: 1rem; border: none; padding: 12px;
        width: 100%; border-radius: 12px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    form button:hover {
        background: #800000;
    }
    .message {
        margin-bottom: 20px;
        padding: 10px 15px;
        background: rgba(255,230,230,0.8);
        color: #800000;
        border-radius: 10px;
        font-weight: 600;
    }
</style>
</head>
<body>
    <a href="admin_home.php" class="back-link">&larr; Back to Dashboard</a>
    <header>
        <h1>Administrator Management</h1>
    </header>

    <?php if (!empty($message)): ?>
        <div class="message" role="alert"><?php echo htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="message" role="alert"><?php echo htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <section aria-label="Current admin users">
        <h2>Admins List</h2>
        <table>
            <thead>
                <tr><th>ID</th><th>Username</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?php echo $admin['id']; ?></td>
                        <td><?php echo htmlspecialchars($admin['username']); ?></td>
                        <td>
                            <?php if ($admin['id'] !== $_SESSION['user_id']): ?>
                                <form method="GET" onsubmit="return confirm('Are you sure you want to delete this admin?');" style="display:inline;">
                                    <input type="hidden" name="delete" value="<?php echo $admin['id']; ?>" />
                                    <button class="delete-btn" type="submit">Delete</button>
                                </form>
                            <?php else: ?>
                                <em>(You)</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($admins)): ?>
                   <tr><td colspan="3" style="text-align:center;">No admin users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section aria-label="Add new admin user">
        <h2>Add New Admin</h2>
        <form method="POST" novalidate>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required minlength="4" maxlength="50" placeholder="Enter username" />

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="6" placeholder="Enter password" />

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="6" placeholder="Confirm password" />

            <button type="submit" name="add_admin">Add Admin</button>
        </form>
    </section>

    <script>
        // Simple client-side pw match validation
        document.querySelector('form').addEventListener('submit', e => {
            const pw = document.getElementById('password').value;
            const cpw = document.getElementById('confirm_password').value;
            if(pw !== cpw) {
                e.preventDefault();
                alert('Passwords do not match.');
            }
        });
    </script>
</body>
</html>

