<?php
session_start();
require 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$table = "program_surveys";

$createTableSQL = "CREATE TABLE IF NOT EXISTS $table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$pdo->exec($createTableSQL);

$message = "";
$error = "";

// Ensure uploads directory exists
$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id = intval($_POST['id'] ?? 0);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_tmp = $_FILES['image']['tmp_name'];
        $img_name = basename($_FILES['image']['name']);
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($img_ext, $allowed_ext)) {
            $error = "Invalid image type. Only jpg, jpeg, png, gif allowed.";
        } else {
            $new_img_name = uniqid() . "." . $img_ext;
            $dest = $uploadDir . '/' . $new_img_name;
            if (move_uploaded_file($img_tmp, $dest)) {
                $image_uploaded = true;
            } else {
                $error = "Failed to upload image.";
            }
        }
    } else {
        $image_uploaded = false;
    }

    if (!$error) {
        if (!empty($title) && !empty($description)) {
            if ($id > 0) {
                if ($image_uploaded) {
                    $stmt = $pdo->prepare("UPDATE $table SET title = ?, description = ?, image = ? WHERE id = ?");
                    $success = $stmt->execute([$title, $description, $new_img_name, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE $table SET title = ?, description = ? WHERE id = ?");
                    $success = $stmt->execute([$title, $description, $id]);
                }
                $message = $success ? "Program survey updated successfully." : "Failed to update.";
            } else {
                if ($image_uploaded) {
                    $stmt = $pdo->prepare("INSERT INTO $table (title, description, image) VALUES (?, ?, ?)");
                    $success = $stmt->execute([$title, $description, $new_img_name]);
                    $message = $success ? "New program survey added." : "Failed to add new.";
                } else {
                    $error = "Image is required for new entry.";
                }
            }
        } else {
            $error = "Title and description cannot be empty.";
        }
    }
}

if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $stmt_img = $pdo->prepare("SELECT image FROM $table WHERE id = ?");
    $stmt_img->execute([$del_id]);
    $img_row = $stmt_img->fetch();
    if ($img_row) {
        $img_file = $uploadDir . '/' . $img_row['image'];
        if (file_exists($img_file)) {
            unlink($img_file);
        }
    }
    $stmt_del = $pdo->prepare("DELETE FROM $table WHERE id = ?");
    $stmt_del->execute([$del_id]);
    header("Location: program_surveys.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM $table ORDER BY created_at DESC");
$entries = $stmt->fetchAll();

$edit = false;
$edit_entry = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
    $edit_stmt->execute([$edit_id]);
    $edit_entry = $edit_stmt->fetch();
    if ($edit_entry) {
        $edit = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Program Under Surveys - Admin - PUP Unisan Accreditation</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap');
    * { box-sizing: border-box; }
    body {
        margin: 0; font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #800000 0%, #a52a2a 100%);
        color: white; min-height: 100vh;
        padding: 20px; max-width: 960px; margin-left: auto; margin-right: auto;
    }
    a.back-link {
        color: #ffefe6; text-decoration: none; font-weight: 700;
        display: inline-block; margin-bottom: 20px;
    }
    a.back-link:hover {
        text-decoration: underline;
    }
    h1 {
        margin-bottom: 24px;
    }
    .message, .error {
        background: rgba(255,255,255,0.1);
        padding: 12px 20px;
        margin-bottom: 20px;
        border-radius: 12px;
        font-weight: 700;
    }
    .message { color: #ccffcc; }
    .error { color: #ffbbbb; }
    form {
        background: rgba(255,255,255,0.1);
        padding: 20px;
        border-radius: 16px;
        margin-bottom: 32px;
    }
    label {
        display: block;
        margin-top: 14px;
        font-weight: 600;
    }
    input[type=text], textarea {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: none;
        font-size: 1rem;
        margin-top: 6px;
        font-family: inherit;
    }
    textarea {
        resize: vertical;
        min-height: 80px;
    }
    input[type=file] {
        margin-top: 10px;
        color: #eee;
    }
    button {
        margin-top: 20px;
        background: #a52a2a;
        color: white;
        border: none;
        padding: 14px;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s ease;
    }
    button:hover, button:focus {
        background: #800000;
    }
    .entries {
        display: grid;
        grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
        gap: 20px;
    }
    article.entry {
        background: rgba(255,255,255,0.1);
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    }
    article.entry img {
        width: 100%;
        display: block;
        object-fit: cover;
        max-height: 200px;
    }
    .entry-content {
        padding: 20px;
        flex-grow: 1;
    }
    .entry-header {
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 12px;
    }
    .entry-description {
        font-size: 1rem;
        white-space: pre-line;
    }
    .entry-actions {
        margin-top: auto;
        display: flex;
        gap: 12px;
        padding: 16px 20px;
        background: rgba(0,0,0,0.2);
        justify-content: flex-end;
    }
    .entry-actions a, .entry-actions form button {
        background: #a52a2a;
        color: white;
        border: none;
        padding: 8px 14px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .entry-actions a:hover, .entry-actions form button:hover {
        background: #800000;
    }
    .entry-actions form {
        margin: 0;
        padding: 0;
    }
</style>
</head>
<body>

<a href="admin_home.php" class="back-link">&larr; Back to Dashboard</a>

<h1>Manage Program Under Surveys</h1>

<?php if ($message): ?>
    <div class="message" role="alert"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="error" role="alert"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" aria-label="<?php echo $edit ? 'Edit Program Survey' : 'Add Program Survey'; ?>">
    <input type="hidden" name="id" value="<?php echo $edit ? (int)$edit_entry['id'] : 0; ?>" />
    <label for="title">Title</label>
    <input id="title" name="title" type="text" required placeholder="Enter title" value="<?php echo $edit ? htmlspecialchars($edit_entry['title']) : ''; ?>" />

    <label for="description">Description</label>
    <textarea id="description" name="description" required placeholder="Enter description"><?php echo $edit ? htmlspecialchars($edit_entry['description']) : ''; ?></textarea>

    <label for="image"><?php echo $edit ? 'Change Image (optional)' : 'Image'; ?></label>
    <input id="image" name="image" type="file" accept="image/*" <?php echo $edit ? '' : 'required'; ?> />

    <button type="submit"><?php echo $edit ? 'Update Survey' : 'Add Survey'; ?></button>
    <?php if ($edit): ?>
        <a href="program_surveys.php" style="margin-left:16px; background:#800000; padding:14px 22px; border-radius:12px; color:#fff; font-weight:600; text-decoration:none;">Cancel</a>
    <?php endif; ?>
</form>

<section class="entries" aria-label="List of Program Surveys">
<?php if (!$entries): ?>
    <p>No program surveys found.</p>
<?php else: ?>
    <?php foreach($entries as $entry): ?>
        <article class="entry">
            <img src="uploads/<?php echo htmlspecialchars($entry['image']); ?>" alt="<?php echo htmlspecialchars($entry['title']); ?>" />
            <div class="entry-content">
                <header class="entry-header"><?php echo htmlspecialchars($entry['title']); ?></header>
                <div class="entry-description"><?php echo nl2br(htmlspecialchars($entry['description'])); ?></div>
            </div>
            <div class="entry-actions">
                <a href="program_surveys.php?edit=<?php echo (int)$entry['id']; ?>" aria-label="Edit <?php echo htmlspecialchars($entry['title']); ?>">Edit</a>
                <form method="GET" onsubmit="return confirm('Delete this survey?');" aria-label="Delete <?php echo htmlspecialchars($entry['title']); ?>" >
                    <input type="hidden" name="delete" value="<?php echo (int)$entry['id']; ?>" />
                    <button type="submit">Delete</button>
                </form>
            </div>
        </article>
    <?php endforeach; ?>
<?php endif; ?>
</section>

</body>
</html>
