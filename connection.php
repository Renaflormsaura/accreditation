<?php
// Database connection for PUP Accreditation System
// Configure these settings according to your XAMPP setup

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'pup_accreditation');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password is empty

// Security settings
define('SECURE_KEY', 'pup_accreditation_secure_key_2024');
define('SESSION_TIMEOUT', 3600); // 1 hour

class Database {
    private $connection;
    private static $instance = null;
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Global database connection function
function getDB() {
    return Database::getInstance()->getConnection();
}

// Security functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Session management
function startSecureSession() {
    if (session_status() == PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_strict_mode', 1);
        session_start();
    }
}

function isLoggedIn() {
    startSecureSession();
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /admin/login.php');
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'super_admin') {
        header('Location: /admin/unauthorized.php');
        exit();
    }
}

function requireSuperAdmin() {
    requireLogin();
    if ($_SESSION['user_role'] !== 'super_admin') {
        header('Location: /admin/unauthorized.php');
        exit();
    }
}

// File upload security
function isAllowedFileType($filename, $allowedTypes = []) {
    $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $defaultAllowed = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'document' => ['pdf', 'doc', 'docx', 'txt'],
        'spreadsheet' => ['xls', 'xlsx', 'csv'],
        'audio' => ['mp3', 'wav', 'ogg'],
        'video' => ['mp4', 'avi', 'mov', 'wmv'],
        'archive' => ['zip', 'rar', '7z']
    ];
    
    if (empty($allowedTypes)) {
        $allowedTypes = array_merge(...array_values($defaultAllowed));
    }
    
    return in_array($fileExtension, $allowedTypes);
}

function getFileType($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $documentTypes = ['pdf', 'doc', 'docx', 'txt'];
    $spreadsheetTypes = ['xls', 'xlsx', 'csv'];
    $audioTypes = ['mp3', 'wav', 'ogg'];
    $videoTypes = ['mp4', 'avi', 'mov', 'wmv'];
    $archiveTypes = ['zip', 'rar', '7z'];
    
    if (in_array($extension, $imageTypes)) return 'image';
    if (in_array($extension, $documentTypes)) return 'document';
    if (in_array($extension, $spreadsheetTypes)) return 'spreadsheet';
    if (in_array($extension, $audioTypes)) return 'audio';
    if (in_array($extension, $videoTypes)) return 'video';
    if (in_array($extension, $archiveTypes)) return 'archive';
    
    return 'unknown';
}

// Initialize database connection
try {
    $db = getDB();
} catch (Exception $e) {
    die("Failed to connect to database. Please check your XAMPP configuration.");
}
?>
