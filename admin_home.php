<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard - PUP Unisan Accreditation</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap');
    * {
        box-sizing: border-box;
    }
    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #800000 0%, #a52a2a 100%);
        color: white;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    header {
        background: rgba(128,0,0,0.9);
        padding: 16px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.5);
    }
    header h1 {
        margin: 0;
        font-weight: 700;
        font-size: 1.8rem;
    }
    nav {
        display: flex;
        gap: 24px;
    }
    nav a {
        color: #ffefe6;
        text-decoration: none;
        font-weight: 600;
        padding: 8px 12px;
        border-radius: 8px;
        transition: background-color 0.3s ease;
    }
    nav a:hover, nav a:focus {
        background-color: #a52a2a;
        outline: none;
    }
    main {
        flex-grow: 1;
        padding: 32px;
        max-width: 1200px;
        margin: auto;
    }
    .welcome {
        font-size: 1.4rem;
        margin-bottom: 32px;
    }
    footer {
        background: rgba(128,0,0,0.8);
        text-align: center;
        padding: 16px;
        font-size: 0.9rem;
        color: #ffefe6;
    }
    @media (max-width: 768px) {
        header {
            flex-direction: column;
            gap: 16px;
            align-items: flex-start;
        }
        nav {
            width: 100%;
            flex-wrap: wrap;
        }
        nav a {
            flex: 1 1 45%;
            text-align: center;
        }
        main {
            padding: 16px;
        }
    }
</style>
</head>
<body>
    <header>
        <h1>PUP Unisan Admin Panel</h1>
        <nav aria-label="Admin Navigation">
            <a href="admin_home.php" aria-current="page">Home</a>
            <a href="administrator.php">Administrator</a>
            <a href="program_surveys.php">Program Under Surveys</a>
            <a href="area_surveys.php">Area Under Surveys</a>
            <a href="certificate_authenticity.php">Certificate of Authenticity</a>
            <a href="about_pup_unisan.php">About PUP Unisan</a>
            <a href="logout.php" style="color:#ffcccc;">Logout</a>
        </nav>
    </header>
    <main>
        <p class="welcome">Welcome to the PUP Unisan Accreditation Admin Panel. Use the navigation links above to manage content.</p>
        <section aria-label="Dashboard Overview">
            <h2>Dashboard Overview</h2>
            <p>Here you can manage each section’s content including adding, editing, and deleting content and pictures.</p>
            <ul>
                <li><strong>Administrator:</strong> Manage admin users and profiles.</li>
                <li><strong>Program Under Surveys:</strong> Upload, edit, delete program-related content and images.</li>
                <li><strong>Area Under Surveys:</strong> Manage survey area content and images.</li>
                <li><strong>Certificate of Authenticity:</strong> Update certificate related content and images.</li>
                <li><strong>About PUP Unisan:</strong> Manage information about PUP Unisan.</li>
            </ul>
        </section>
    </main>
    <footer>
        &copy; <?php echo date('Y'); ?> PUP Unisan Accreditation Portal. All rights reserved.
    </footer>
</body>
</html>

