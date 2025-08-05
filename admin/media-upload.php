<?php
require_once '../connection.php';
requireAdmin();

$pageTitle = 'Add Media File';
$currentPage = 'media';
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
        <!-- Include Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content Area -->
        <main class="admin-main">
            <!-- Include Header -->
            <?php include 'includes/header.php'; ?>
            
            <!-- Content -->
            <div class="admin-content">
                <div class="content-header">
                    <h1><?php echo $pageTitle; ?></h1>
                    <p>Upload files to your media library. Drag and drop files or click to select.</p>
                </div>
                
                <div class="upload-container">
                    <div class="upload-area" id="uploadArea">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-text">Drop files to upload</div>
                        <div class="upload-hint">or click to select files</div>
                        <input type="file" id="fileInput" multiple accept="image/*,audio/*,video/*,.pdf,.doc,.docx,.txt,.xls,.xlsx,.csv,.zip,.rar,.7z" style="display: none;">
                    </div>
                    
                    <div class="upload-info">
                        <h3>Supported File Types:</h3>
                        <div class="file-types">
                            <div class="file-type">
                                <i class="fas fa-image"></i>
                                <span>Images</span>
                                <small>JPG, PNG, GIF, WebP</small>
                            </div>
                            <div class="file-type">
                                <i class="fas fa-music"></i>
                                <span>Audio</span>
                                <small>MP3, WAV, OGG</small>
                            </div>
                            <div class="file-type">
                                <i class="fas fa-video"></i>
                                <span>Video</span>
                                <small>MP4, AVI, MOV, WMV</small>
                            </div>
                            <div class="file-type">
                                <i class="fas fa-file-alt"></i>
                                <span>Documents</span>
                                <small>PDF, DOC, DOCX, TXT</small>
                            </div>
                            <div class="file-type">
                                <i class="fas fa-table"></i>
                                <span>Spreadsheets</span>
                                <small>XLS, XLSX, CSV</small>
                            </div>
                            <div class="file-type">
                                <i class="fas fa-archive"></i>
                                <span>Archives</span>
                                <small>ZIP, RAR, 7Z</small>
                            </div>
                        </div>
                        
                        <div class="upload-limits">
                            <p><strong>Maximum file size:</strong> 50MB per file</p>
                            <p><strong>Maximum files:</strong> 20 files at once</p>
                        </div>
                    </div>
                </div>
                
                <div class="upload-queue" id="uploadQueue" style="display: none;">
                    <h3>Upload Queue</h3>
                    <div class="queue-items" id="queueItems"></div>
                </div>
                
                <div class="recent-uploads">
                    <h3>Recent Uploads</h3>
                    <div class="media-grid" id="recentUploads">
                        <?php
                        // Get recent uploads
                        $stmt = $db->prepare("SELECT * FROM media WHERE uploaded_by = ? ORDER BY created_at DESC LIMIT 12");
                        $stmt->execute([$_SESSION['user_id']]);
                        $recentMedia = $stmt->fetchAll();
                        
                        if (empty($recentMedia)): ?>
                            <div class="no-media">
                                <i class="fas fa-folder-open"></i>
                                <p>No files uploaded yet</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recentMedia as $media): ?>
                                <div class="media-item" data-id="<?php echo $media['id']; ?>">
                                    <div class="media-thumbnail">
                                        <?php if ($media['file_type'] === 'image'): ?>
                                            <img src="<?php echo htmlspecialchars($media['file_path']); ?>" 
                                                 alt="<?php echo htmlspecialchars($media['alt_text']); ?>">
                                        <?php else: ?>
                                            <i class="fas fa-<?php echo getFileIcon($media['file_type']); ?>"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="media-info">
                                        <div class="media-title"><?php echo htmlspecialchars($media['title'] ?: $media['original_filename']); ?></div>
                                        <div class="media-meta">
                                            <span><?php echo formatFileSize($media['file_size']); ?></span>
                                            <span><?php echo date('M j, Y', strtotime($media['created_at'])); ?></span>
                                        </div>
                                        <div class="media-actions">
                                            <a href="#" class="btn btn-sm btn-secondary" onclick="editMedia(<?php echo $media['id']; ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="#" class="btn btn-sm btn-danger" onclick="deleteMedia(<?php echo $media['id']; ?>)">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Media Edit Modal -->
    <div class="modal" id="mediaModal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Media</h3>
                <button class="modal-close" onclick="closeMediaModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="mediaEditForm">
                    <input type="hidden" id="mediaId" name="media_id">
                    
                    <div class="form-row">
                        <label for="mediaTitle">Title</label>
                        <div class="form-field">
                            <input type="text" id="mediaTitle" name="title">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label for="mediaAlt">Alt Text</label>
                        <div class="form-field">
                            <input type="text" id="mediaAlt" name="alt_text">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label for="mediaCaption">Caption</label>
                        <div class="form-field">
                            <textarea id="mediaCaption" name="caption" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <label for="mediaDescription">Description</label>
                        <div class="form-field">
                            <textarea id="mediaDescription" name="description" rows="4"></textarea>
                        </div>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeMediaModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Media</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="assets/js/admin-script.js"></script>
    <script>
        // Media upload specific functionality
        document.addEventListener('DOMContentLoaded', function() {
            initMediaUpload();
        });
        
        function initMediaUpload() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('fileInput');
            const uploadQueue = document.getElementById('uploadQueue');
            const queueItems = document.getElementById('queueItems');
            
            // File input change
            fileInput.addEventListener('change', function(e) {
                handleFiles(e.target.files);
            });
            
            // Drag and drop
            uploadArea.addEventListener('click', () => fileInput.click());
            
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });
            
            uploadArea.addEventListener('dragleave', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                handleFiles(e.dataTransfer.files);
            });
        }
        
        function handleFiles(files) {
            if (files.length === 0) return;
            
            const uploadQueue = document.getElementById('uploadQueue');
            const queueItems = document.getElementById('queueItems');
            
            uploadQueue.style.display = 'block';
            queueItems.innerHTML = '';
            
            Array.from(files).forEach((file, index) => {
                const queueItem = createQueueItem(file, index);
                queueItems.appendChild(queueItem);
                uploadFile(file, index);
            });
        }
        
        function createQueueItem(file, index) {
            const item = document.createElement('div');
            item.className = 'queue-item';
            item.id = `queue-item-${index}`;
            
            const fileIcon = getFileIconClass(file.type);
            
            item.innerHTML = `
                <div class="queue-item-icon">
                    <i class="fas fa-${fileIcon}"></i>
                </div>
                <div class="queue-item-info">
                    <div class="queue-item-name">${file.name}</div>
                    <div class="queue-item-size">${formatFileSize(file.size)}</div>
                </div>
                <div class="queue-item-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 0%"></div>
                    </div>
                    <div class="progress-text">0%</div>
                </div>
                <div class="queue-item-status">
                    <i class="fas fa-clock text-warning"></i>
                </div>
            `;
            
            return item;
        }
        
        function uploadFile(file, index) {
            const formData = new FormData();
            formData.append('file', file);
            
            const xhr = new XMLHttpRequest();
            
            // Progress tracking
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    updateProgress(index, percentComplete);
                }
            });
            
            // Success/Error handling
            xhr.addEventListener('load', () => {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        updateStatus(index, 'success');
                        refreshRecentUploads();
                    } else {
                        updateStatus(index, 'error', response.message);
                    }
                } else {
                    updateStatus(index, 'error', 'Upload failed');
                }
            });
            
            xhr.addEventListener('error', () => {
                updateStatus(index, 'error', 'Network error');
            });
            
            xhr.open('POST', 'upload-handler.php');
            xhr.send(formData);
        }
        
        function updateProgress(index, percent) {
            const item = document.getElementById(`queue-item-${index}`);
            if (!item) return;
            
            const progressFill = item.querySelector('.progress-fill');
            const progressText = item.querySelector('.progress-text');
            
            progressFill.style.width = percent + '%';
            progressText.textContent = Math.round(percent) + '%';
        }
        
        function updateStatus(index, status, message = '') {
            const item = document.getElementById(`queue-item-${index}`);
            if (!item) return;
            
            const statusIcon = item.querySelector('.queue-item-status i');
            const progressText = item.querySelector('.progress-text');
            
            switch (status) {
                case 'success':
                    statusIcon.className = 'fas fa-check-circle text-success';
                    progressText.textContent = 'Complete';
                    break;
                case 'error':
                    statusIcon.className = 'fas fa-exclamation-circle text-danger';
                    progressText.textContent = message || 'Error';
                    break;
            }
        }
        
        function refreshRecentUploads() {
            fetch('get-recent-uploads.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('recentUploads').innerHTML = data.html;
                }
            })
            .catch(error => {
                console.error('Error refreshing uploads:', error);
            });
        }
        
        function editMedia(mediaId) {
            fetch(`get-media-details.php?id=${mediaId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('mediaId').value = data.media.id;
                    document.getElementById('mediaTitle').value = data.media.title || '';
                    document.getElementById('mediaAlt').value = data.media.alt_text || '';
                    document.getElementById('mediaCaption').value = data.media.caption || '';
                    document.getElementById('mediaDescription').value = data.media.description || '';
                    
                    document.getElementById('mediaModal').style.display = 'flex';
                }
            })
            .catch(error => {
                console.error('Error loading media details:', error);
            });
        }
        
        function closeMediaModal() {
            document.getElementById('mediaModal').style.display = 'none';
        }
        
        function deleteMedia(mediaId) {
            if (!confirm('Are you sure you want to delete this media file?')) {
                return;
            }
            
            fetch('delete-media.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: mediaId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Media deleted successfully', 'success');
                    refreshRecentUploads();
                } else {
                    showNotification('Error deleting media: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Error deleting media', 'error');
            });
        }
        
        // Media edit form submission
        document.getElementById('mediaEditForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('update-media.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Media updated successfully', 'success');
                    closeMediaModal();
                    refreshRecentUploads();
                } else {
                    showNotification('Error updating media: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Error updating media', 'error');
            });
        });
        
        // Utility functions
        function getFileIconClass(mimeType) {
            if (mimeType.startsWith('image/')) return 'image';
            if (mimeType.startsWith('audio/')) return 'music';
            if (mimeType.startsWith('video/')) return 'video';
            if (mimeType.includes('pdf')) return 'file-pdf';
            if (mimeType.includes('word') || mimeType.includes('document')) return 'file-word';
            if (mimeType.includes('sheet') || mimeType.includes('excel')) return 'file-excel';
            if (mimeType.includes('zip') || mimeType.includes('rar')) return 'archive';
            return 'file';
        }
        
        function formatFileSize(bytes) {
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            if (bytes === 0) return '0 Bytes';
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        }
    </script>
    
    <style>
        .upload-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .upload-info {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
        }
        
        .upload-info h3 {
            margin-bottom: 20px;
            color: #23282d;
        }
        
        .file-types {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .file-type {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 15px;
            border: 1px solid #f1f1f1;
            border-radius: 4px;
        }
        
        .file-type i {
            font-size: 24px;
            color: #0073aa;
            margin-bottom: 8px;
        }
        
        .file-type span {
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .file-type small {
            color: #666;
            font-size: 12px;
        }
        
        .upload-limits {
            padding-top: 20px;
            border-top: 1px solid #f1f1f1;
        }
        
        .upload-limits p {
            margin-bottom: 8px;
            font-size: 14px;
            color: #666;
        }
        
        .upload-queue {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .queue-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .queue-item:last-child {
            border-bottom: none;
        }
        
        .queue-item-icon {
            width: 40px;
            height: 40px;
            background: #f1f1f1;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .queue-item-icon i {
            font-size: 18px;
            color: #666;
        }
        
        .queue-item-info {
            flex: 1;
            margin-right: 15px;
        }
        
        .queue-item-name {
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .queue-item-size {
            font-size: 12px;
            color: #666;
        }
        
        .queue-item-progress {
            width: 200px;
            margin-right: 15px;
        }
        
        .progress-bar {
            background: #f1f1f1;
            border-radius: 4px;
            height: 6px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        
        .progress-fill {
            background: #0073aa;
            height: 100%;
            transition: width 0.3s ease;
        }
        
        .progress-text {
            font-size: 12px;
            text-align: center;
            color: #666;
        }
        
        .queue-item-status {
            width: 24px;
        }
        
        .recent-uploads {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
        }
        
        .recent-uploads h3 {
            margin-bottom: 20px;
            color: #23282d;
        }
        
        .no-media {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .no-media i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }
        
        .media-actions {
            margin-top: 10px;
            display: flex;
            gap: 5px;
        }
        
        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
        }
        
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }
        
        .modal-content {
            background: #fff;
            border-radius: 4px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            margin: 0;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #666;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .text-success { color: #00a32a; }
        .text-danger { color: #d63638; }
        .text-warning { color: #dba617; }
        
        @media (max-width: 768px) {
            .upload-container {
                grid-template-columns: 1fr;
            }
            
            .file-types {
                grid-template-columns: 1fr;
            }
            
            .queue-item-progress {
                width: 150px;
            }
        }
    </style>
</body>
</html>

<?php
function getFileIcon($fileType) {
    $icons = [
        'image' => 'image',
        'audio' => 'music',
        'video' => 'video',
        'document' => 'file-alt',
        'spreadsheet' => 'table',
        'archive' => 'archive'
    ];
    
    return $icons[$fileType] ?? 'file';
}

function formatFileSize($bytes) {
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    if ($bytes == 0) return '0 Bytes';
    $i = floor(log($bytes) / log(1024));
    return round($bytes / pow(1024, $i), 2) . ' ' . $sizes[$i];
}
?>