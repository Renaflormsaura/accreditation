<?php
require_once '../connection.php';
requireAdmin();

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - PUP Accreditation Admin</title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar Navigation -->
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                    <span>PUP Admin</span>
                </div>
            </div>
            
            <ul class="sidebar-menu">
                <!-- Dashboard -->
                <li class="menu-item <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>">
                    <a href="#" class="menu-link" data-toggle="dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </a>
                    <ul class="submenu" id="dashboard-submenu">
                        <li><a href="dashboard-home.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="dashboard-updates.php"><i class="fas fa-sync-alt"></i> Updates</a></li>
                    </ul>
                </li>
                
                <!-- Posts -->
                <li class="menu-item <?php echo ($currentPage == 'posts') ? 'active' : ''; ?>">
                    <a href="#" class="menu-link" data-toggle="posts">
                        <i class="fas fa-edit"></i>
                        <span>Posts</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </a>
                    <ul class="submenu" id="posts-submenu">
                        <li><a href="posts-all.php"><i class="fas fa-list"></i> All Posts</a></li>
                        <li><a href="posts-add.php"><i class="fas fa-plus"></i> Add Post</a></li>
                        <li><a href="posts-categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                    </ul>
                </li>
                
                <!-- Media -->
                <li class="menu-item <?php echo ($currentPage == 'media') ? 'active' : ''; ?>">
                    <a href="#" class="menu-link" data-toggle="media">
                        <i class="fas fa-photo-video"></i>
                        <span>Media</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </a>
                    <ul class="submenu" id="media-submenu">
                        <li class="submenu-parent">
                            <a href="#" data-toggle="library"><i class="fas fa-folder"></i> Library <i class="fas fa-chevron-down"></i></a>
                            <ul class="sub-submenu" id="library-submenu">
                                <li><a href="media-all.php"><i class="fas fa-th"></i> All Media</a></li>
                                <li><a href="media-dates.php"><i class="fas fa-calendar"></i> Dates</a></li>
                                <li class="submenu-parent">
                                    <a href="#" data-toggle="allmedia"><i class="fas fa-filter"></i> Filter <i class="fas fa-chevron-down"></i></a>
                                    <ul class="sub-submenu" id="allmedia-submenu">
                                        <li><a href="media-images.php"><i class="fas fa-image"></i> Images</a></li>
                                        <li><a href="media-audio.php"><i class="fas fa-music"></i> Audio</a></li>
                                        <li><a href="media-video.php"><i class="fas fa-video"></i> Video</a></li>
                                        <li><a href="media-documents.php"><i class="fas fa-file-alt"></i> Documents</a></li>
                                        <li><a href="media-spreadsheets.php"><i class="fas fa-table"></i> Spreadsheets</a></li>
                                        <li><a href="media-archives.php"><i class="fas fa-archive"></i> Archives</a></li>
                                        <li><a href="media-unattached.php"><i class="fas fa-unlink"></i> Unattached</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li><a href="media-upload.php"><i class="fas fa-upload"></i> Add Media File</a></li>
                    </ul>
                </li>
                
                <!-- Pages -->
                <li class="menu-item <?php echo ($currentPage == 'pages') ? 'active' : ''; ?>">
                    <a href="#" class="menu-link" data-toggle="pages">
                        <i class="fas fa-file"></i>
                        <span>Pages</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </a>
                    <ul class="submenu" id="pages-submenu">
                        <li><a href="pages-all.php"><i class="fas fa-list"></i> All Pages</a></li>
                        <li><a href="pages-add.php"><i class="fas fa-plus"></i> Add Page</a></li>
                    </ul>
                </li>
                
                <!-- Comments -->
                <li class="menu-item <?php echo ($currentPage == 'comments') ? 'active' : ''; ?>">
                    <a href="comments.php" class="menu-link">
                        <i class="fas fa-comments"></i>
                        <span>Comments</span>
                        <?php
                        // Get pending comments count
                        $stmt = $db->prepare("SELECT COUNT(*) as count FROM comments WHERE status = 'pending'");
                        $stmt->execute();
                        $pendingCount = $stmt->fetch()['count'];
                        if ($pendingCount > 0) {
                            echo '<span class="badge">' . $pendingCount . '</span>';
                        }
                        ?>
                    </a>
                </li>
                
                <!-- Users -->
                <li class="menu-item <?php echo ($currentPage == 'users') ? 'active' : ''; ?>">
                    <a href="#" class="menu-link" data-toggle="users">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </a>
                    <ul class="submenu" id="users-submenu">
                        <li><a href="users-all.php"><i class="fas fa-list"></i> All Users</a></li>
                        <li><a href="users-add.php"><i class="fas fa-user-plus"></i> Add User</a></li>
                        <li><a href="users-profile.php"><i class="fas fa-user-cog"></i> Profile</a></li>
                    </ul>
                </li>
            </ul>
            
            <!-- User Info -->
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-details">
                        <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <span class="user-role"><?php echo ucfirst($_SESSION['user_role']); ?></span>
                    </div>
                    <a href="logout.php" class="logout-btn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </nav>
        
        <!-- Main Content Area -->
        <main class="admin-main">
            <!-- Top Bar -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title"><?php echo $pageTitle; ?></h1>
                </div>
                <div class="header-right">
                    <a href="../" class="view-site-btn" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        View Site
                    </a>
                    <div class="admin-notifications">
                        <i class="fas fa-bell"></i>
                        <?php if ($pendingCount > 0): ?>
                        <span class="notification-badge"><?php echo $pendingCount; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </header>
            
            <!-- Dashboard Content -->
            <div class="admin-content">
                <div class="dashboard-widgets">
                    <!-- Quick Stats -->
                    <div class="widget-row">
                        <div class="stat-widget">
                            <div class="stat-icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="stat-info">
                                <?php
                                $stmt = $db->prepare("SELECT COUNT(*) as count FROM posts WHERE status = 'published'");
                                $stmt->execute();
                                $postsCount = $stmt->fetch()['count'];
                                ?>
                                <h3><?php echo $postsCount; ?></h3>
                                <p>Published Posts</p>
                            </div>
                        </div>
                        
                        <div class="stat-widget">
                            <div class="stat-icon">
                                <i class="fas fa-file"></i>
                            </div>
                            <div class="stat-info">
                                <?php
                                $stmt = $db->prepare("SELECT COUNT(*) as count FROM pages WHERE status = 'published'");
                                $stmt->execute();
                                $pagesCount = $stmt->fetch()['count'];
                                ?>
                                <h3><?php echo $pagesCount; ?></h3>
                                <p>Published Pages</p>
                            </div>
                        </div>
                        
                        <div class="stat-widget">
                            <div class="stat-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $pendingCount; ?></h3>
                                <p>Pending Comments</p>
                            </div>
                        </div>
                        
                        <div class="stat-widget">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <?php
                                $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
                                $stmt->execute();
                                $usersCount = $stmt->fetch()['count'];
                                ?>
                                <h3><?php echo $usersCount; ?></h3>
                                <p>Active Users</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="widget-row">
                        <div class="activity-widget">
                            <h3>Recent Posts</h3>
                            <div class="activity-list">
                                <?php
                                $stmt = $db->prepare("SELECT p.*, u.username FROM posts p 
                                                    LEFT JOIN users u ON p.author_id = u.id 
                                                    ORDER BY p.created_at DESC LIMIT 5");
                                $stmt->execute();
                                $recentPosts = $stmt->fetchAll();
                                
                                foreach ($recentPosts as $post): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h4><a href="posts-edit.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h4>
                                        <p>by <?php echo htmlspecialchars($post['username']); ?> • <?php echo date('M j, Y', strtotime($post['created_at'])); ?></p>
                                    </div>
                                    <div class="activity-status">
                                        <span class="status-badge status-<?php echo $post['status']; ?>"><?php echo ucfirst($post['status']); ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="activity-widget">
                            <h3>Recent Comments</h3>
                            <div class="activity-list">
                                <?php
                                $stmt = $db->prepare("SELECT c.*, p.title as post_title FROM comments c 
                                                    LEFT JOIN posts p ON c.post_id = p.id 
                                                    ORDER BY c.created_at DESC LIMIT 5");
                                $stmt->execute();
                                $recentComments = $stmt->fetchAll();
                                
                                foreach ($recentComments as $comment): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-comment"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h4><?php echo htmlspecialchars($comment['author_name']); ?></h4>
                                        <p><?php echo htmlspecialchars(substr($comment['content'], 0, 100)) . '...'; ?></p>
                                        <small>on "<?php echo htmlspecialchars($comment['post_title']); ?>"</small>
                                    </div>
                                    <div class="activity-status">
                                        <span class="status-badge status-<?php echo $comment['status']; ?>"><?php echo ucfirst($comment['status']); ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/js/admin-script.js"></script>
</body>
</html>