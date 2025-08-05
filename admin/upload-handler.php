<?php
require_once '../connection.php';
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['file'];
$originalFilename = $file['name'];
$tmpPath = $file['tmp_name'];
$fileSize = $file['size'];
$mimeType = $file['type'];

// Validate file size (50MB max)
$maxFileSize = 50 * 1024 * 1024; // 50MB
if ($fileSize > $maxFileSize) {
    echo json_encode(['success' => false, 'message' => 'File size exceeds 50MB limit']);
    exit;
}

// Validate file type
if (!isAllowedFileType($originalFilename)) {
    echo json_encode(['success' => false, 'message' => 'File type not allowed']);
    exit;
}

// Get file type category
$fileType = getFileType($originalFilename);

// Create secure filename
$fileExtension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
$secureFilename = generateSecureFilename($originalFilename, $fileExtension);

// Create upload directory structure
$uploadBaseDir = '/workspace/uploads';
$yearMonth = date('Y/m');
$uploadDir = $uploadBaseDir . '/' . $yearMonth;

if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
        exit;
    }
}

$filePath = $uploadDir . '/' . $secureFilename;
$relativeFilePath = 'uploads/' . $yearMonth . '/' . $secureFilename;

// Move uploaded file
if (!move_uploaded_file($tmpPath, $filePath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
    exit;
}

// Set proper file permissions
chmod($filePath, 0644);

// Generate additional metadata for images
$additionalData = [];
if ($fileType === 'image') {
    $imageInfo = getimagesize($filePath);
    if ($imageInfo) {
        $additionalData['width'] = $imageInfo[0];
        $additionalData['height'] = $imageInfo[1];
        
        // Create thumbnail for images
        $thumbnailPath = createThumbnail($filePath, $uploadDir, $secureFilename);
        if ($thumbnailPath) {
            $additionalData['thumbnail'] = $thumbnailPath;
        }
    }
}

try {
    // Save to database
    $stmt = $db->prepare("
        INSERT INTO media (
            filename, original_filename, file_path, file_type, mime_type, 
            file_size, title, uploaded_by, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $title = pathinfo($originalFilename, PATHINFO_FILENAME);
    
    $stmt->execute([
        $secureFilename,
        $originalFilename,
        $relativeFilePath,
        $fileType,
        $mimeType,
        $fileSize,
        $title,
        $_SESSION['user_id']
    ]);
    
    $mediaId = $db->lastInsertId();
    
    // Log the upload
    error_log("File uploaded: {$originalFilename} -> {$relativeFilePath} by user {$_SESSION['user_id']}");
    
    echo json_encode([
        'success' => true,
        'message' => 'File uploaded successfully',
        'media_id' => $mediaId,
        'filename' => $secureFilename,
        'original_filename' => $originalFilename,
        'file_path' => $relativeFilePath,
        'file_type' => $fileType,
        'file_size' => $fileSize,
        'additional_data' => $additionalData
    ]);
    
} catch (Exception $e) {
    // Remove uploaded file if database insert fails
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    error_log("Upload database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}

function generateSecureFilename($originalFilename, $extension) {
    $basename = pathinfo($originalFilename, PATHINFO_FILENAME);
    
    // Clean the basename
    $basename = preg_replace('/[^a-zA-Z0-9\-_]/', '', $basename);
    $basename = substr($basename, 0, 50); // Limit length
    
    // Add timestamp and random string for uniqueness
    $timestamp = time();
    $random = substr(md5(uniqid(rand(), true)), 0, 8);
    
    return $basename . '_' . $timestamp . '_' . $random . '.' . $extension;
}

function createThumbnail($sourcePath, $uploadDir, $filename) {
    $thumbnailDir = $uploadDir . '/thumbnails';
    if (!file_exists($thumbnailDir)) {
        if (!mkdir($thumbnailDir, 0755, true)) {
            return false;
        }
    }
    
    $thumbnailPath = $thumbnailDir . '/thumb_' . $filename;
    $thumbnailSize = 300; // Max width/height for thumbnail
    
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }
    
    $sourceWidth = $imageInfo[0];
    $sourceHeight = $imageInfo[1];
    $sourceType = $imageInfo[2];
    
    // Calculate thumbnail dimensions
    if ($sourceWidth > $sourceHeight) {
        $thumbWidth = $thumbnailSize;
        $thumbHeight = intval($sourceHeight * $thumbnailSize / $sourceWidth);
    } else {
        $thumbHeight = $thumbnailSize;
        $thumbWidth = intval($sourceWidth * $thumbnailSize / $sourceHeight);
    }
    
    // Create source image resource
    switch ($sourceType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) {
        return false;
    }
    
    // Create thumbnail image
    $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
    
    // Preserve transparency for PNG and GIF
    if ($sourceType == IMAGETYPE_PNG || $sourceType == IMAGETYPE_GIF) {
        imagealphablending($thumbImage, false);
        imagesavealpha($thumbImage, true);
        $transparent = imagecolorallocatealpha($thumbImage, 255, 255, 255, 127);
        imagefilledrectangle($thumbImage, 0, 0, $thumbWidth, $thumbHeight, $transparent);
    }
    
    // Resize image
    imagecopyresampled(
        $thumbImage, $sourceImage,
        0, 0, 0, 0,
        $thumbWidth, $thumbHeight,
        $sourceWidth, $sourceHeight
    );
    
    // Save thumbnail
    $success = false;
    switch ($sourceType) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($thumbImage, $thumbnailPath, 85);
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($thumbImage, $thumbnailPath, 8);
            break;
        case IMAGETYPE_GIF:
            $success = imagegif($thumbImage, $thumbnailPath);
            break;
    }
    
    // Clean up
    imagedestroy($sourceImage);
    imagedestroy($thumbImage);
    
    if ($success) {
        chmod($thumbnailPath, 0644);
        return str_replace('/workspace/', '', $thumbnailPath);
    }
    
    return false;
}
?>