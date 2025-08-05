// PUP Accreditation Admin Panel JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin panel
    initSidebar();
    initDropdowns();
    initFileUpload();
    initTables();
    initForms();
});

// Sidebar functionality
function initSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    const main = document.querySelector('.admin-main');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            main.classList.toggle('sidebar-open');
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('active');
                main.classList.remove('sidebar-open');
            }
        }
    });
}

// Dropdown menu functionality
function initDropdowns() {
    const menuLinks = document.querySelectorAll('.menu-link[data-toggle]');
    
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('data-toggle');
            const submenu = document.getElementById(targetId + '-submenu');
            const menuItem = this.closest('.menu-item');
            const arrow = this.querySelector('.arrow');
            
            if (submenu) {
                // Close all other submenus at the same level
                const allMenuItems = document.querySelectorAll('.menu-item');
                allMenuItems.forEach(item => {
                    if (item !== menuItem) {
                        item.classList.remove('active');
                        const otherSubmenu = item.querySelector('.submenu');
                        if (otherSubmenu) {
                            otherSubmenu.classList.remove('active');
                        }
                    }
                });
                
                // Toggle current submenu
                menuItem.classList.toggle('active');
                submenu.classList.toggle('active');
                
                // Rotate arrow
                if (arrow) {
                    arrow.style.transform = menuItem.classList.contains('active') ? 
                        'rotate(180deg)' : 'rotate(0deg)';
                }
            }
        });
    });
    
    // Handle sub-submenu toggles
    const subMenuLinks = document.querySelectorAll('.submenu-parent > a[data-toggle]');
    
    subMenuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('data-toggle');
            const subSubmenu = document.getElementById(targetId + '-submenu');
            const parent = this.closest('.submenu-parent');
            const arrow = this.querySelector('.fas.fa-chevron-down');
            
            if (subSubmenu) {
                // Close other sub-submenus
                const allSubMenus = document.querySelectorAll('.sub-submenu');
                allSubMenus.forEach(menu => {
                    if (menu !== subSubmenu) {
                        menu.classList.remove('active');
                    }
                });
                
                // Toggle current sub-submenu
                subSubmenu.classList.toggle('active');
                
                // Rotate arrow
                if (arrow) {
                    arrow.style.transform = subSubmenu.classList.contains('active') ? 
                        'rotate(180deg)' : 'rotate(0deg)';
                }
            }
        });
    });
}

// File upload functionality
function initFileUpload() {
    const uploadAreas = document.querySelectorAll('.upload-area');
    
    uploadAreas.forEach(area => {
        const fileInput = area.querySelector('input[type="file"]');
        
        if (!fileInput) return;
        
        // Click to upload
        area.addEventListener('click', () => {
            fileInput.click();
        });
        
        // Drag and drop
        area.addEventListener('dragover', (e) => {
            e.preventDefault();
            area.classList.add('dragover');
        });
        
        area.addEventListener('dragleave', (e) => {
            e.preventDefault();
            area.classList.remove('dragover');
        });
        
        area.addEventListener('drop', (e) => {
            e.preventDefault();
            area.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileUpload(files, area);
            }
        });
        
        // File input change
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileUpload(e.target.files, area);
            }
        });
    });
}

// Handle file upload
function handleFileUpload(files, uploadArea) {
    const formData = new FormData();
    
    Array.from(files).forEach((file, index) => {
        formData.append(`files[]`, file);
    });
    
    // Show upload progress
    showUploadProgress(uploadArea);
    
    // Upload files via AJAX
    fetch('upload-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideUploadProgress(uploadArea);
        
        if (data.success) {
            showNotification('Files uploaded successfully!', 'success');
            
            // Refresh media grid if exists
            const mediaGrid = document.querySelector('.media-grid');
            if (mediaGrid) {
                refreshMediaGrid();
            }
        } else {
            showNotification('Upload failed: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideUploadProgress(uploadArea);
        showNotification('Upload error: ' + error.message, 'error');
    });
}

// Show upload progress
function showUploadProgress(uploadArea) {
    const progressBar = document.createElement('div');
    progressBar.className = 'upload-progress';
    progressBar.innerHTML = `
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
        <div class="progress-text">Uploading...</div>
    `;
    
    uploadArea.appendChild(progressBar);
    uploadArea.style.pointerEvents = 'none';
}

// Hide upload progress
function hideUploadProgress(uploadArea) {
    const progressBar = uploadArea.querySelector('.upload-progress');
    if (progressBar) {
        progressBar.remove();
    }
    uploadArea.style.pointerEvents = 'auto';
}

// Table functionality
function initTables() {
    // Bulk actions
    const bulkActionSelects = document.querySelectorAll('.bulk-actions select');
    const bulkActionButtons = document.querySelectorAll('.bulk-actions .btn');
    
    bulkActionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const select = this.parentElement.querySelector('select');
            const action = select.value;
            const checkedItems = document.querySelectorAll('.wp-list-table input[type="checkbox"]:checked');
            
            if (action === '' || checkedItems.length === 0) {
                showNotification('Please select an action and items to perform it on.', 'warning');
                return;
            }
            
            if (confirm(`Are you sure you want to ${action} ${checkedItems.length} item(s)?`)) {
                performBulkAction(action, checkedItems);
            }
        });
    });
    
    // Select all checkbox
    const selectAllCheckboxes = document.querySelectorAll('.select-all');
    
    selectAllCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const table = this.closest('table');
            const itemCheckboxes = table.querySelectorAll('tbody input[type="checkbox"]');
            
            itemCheckboxes.forEach(item => {
                item.checked = this.checked;
            });
        });
    });
    
    // Row actions
    const rowActions = document.querySelectorAll('.row-actions a');
    
    rowActions.forEach(action => {
        if (action.classList.contains('delete')) {
            action.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this item?')) {
                    e.preventDefault();
                }
            });
        }
    });
}

// Form functionality
function initForms() {
    // Auto-save drafts
    const contentForms = document.querySelectorAll('form[data-autosave]');
    
    contentForms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        let saveTimeout;
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    autoSaveDraft(form);
                }, 2000);
            });
        });
    });
    
    // Slug generation
    const titleInputs = document.querySelectorAll('input[name="title"]');
    const slugInputs = document.querySelectorAll('input[name="slug"]');
    
    titleInputs.forEach((titleInput, index) => {
        const slugInput = slugInputs[index];
        if (slugInput) {
            titleInput.addEventListener('input', function() {
                if (slugInput.value === '' || slugInput.dataset.manual !== 'true') {
                    slugInput.value = generateSlug(this.value);
                }
            });
            
            slugInput.addEventListener('input', function() {
                this.dataset.manual = 'true';
            });
        }
    });
    
    // Rich text editor initialization
    initRichTextEditor();
}

// Auto-save draft
function autoSaveDraft(form) {
    const formData = new FormData(form);
    formData.append('action', 'autosave');
    
    fetch('autosave-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Draft saved', 'info', 2000);
        }
    })
    .catch(error => {
        console.error('Autosave error:', error);
    });
}

// Generate slug from title
function generateSlug(title) {
    return title
        .toLowerCase()
        .replace(/[^a-z0-9 -]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
}

// Rich text editor
function initRichTextEditor() {
    const textareas = document.querySelectorAll('.rich-editor');
    
    textareas.forEach(textarea => {
        // Create toolbar
        const toolbar = createEditorToolbar();
        textarea.parentNode.insertBefore(toolbar, textarea);
        
        // Make textarea content editable
        const editor = document.createElement('div');
        editor.className = 'editor-content';
        editor.contentEditable = true;
        editor.innerHTML = textarea.value;
        
        textarea.style.display = 'none';
        textarea.parentNode.insertBefore(editor, textarea.nextSibling);
        
        // Sync content
        editor.addEventListener('input', function() {
            textarea.value = this.innerHTML;
        });
        
        // Toolbar functionality
        setupEditorToolbar(toolbar, editor);
    });
}

// Create editor toolbar
function createEditorToolbar() {
    const toolbar = document.createElement('div');
    toolbar.className = 'editor-toolbar';
    toolbar.innerHTML = `
        <button type="button" data-command="bold"><i class="fas fa-bold"></i></button>
        <button type="button" data-command="italic"><i class="fas fa-italic"></i></button>
        <button type="button" data-command="underline"><i class="fas fa-underline"></i></button>
        <div class="toolbar-separator"></div>
        <button type="button" data-command="insertUnorderedList"><i class="fas fa-list-ul"></i></button>
        <button type="button" data-command="insertOrderedList"><i class="fas fa-list-ol"></i></button>
        <div class="toolbar-separator"></div>
        <button type="button" data-command="createLink"><i class="fas fa-link"></i></button>
        <button type="button" data-command="unlink"><i class="fas fa-unlink"></i></button>
    `;
    
    return toolbar;
}

// Setup editor toolbar
function setupEditorToolbar(toolbar, editor) {
    const buttons = toolbar.querySelectorAll('button[data-command]');
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const command = this.getAttribute('data-command');
            
            if (command === 'createLink') {
                const url = prompt('Enter URL:');
                if (url) {
                    document.execCommand(command, false, url);
                }
            } else {
                document.execCommand(command, false, null);
            }
            
            editor.focus();
        });
    });
}

// Notification system
function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to page
    let container = document.querySelector('.notification-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'notification-container';
        document.body.appendChild(container);
    }
    
    container.appendChild(notification);
    
    // Auto-hide
    if (duration > 0) {
        setTimeout(() => {
            notification.remove();
        }, duration);
    }
    
    // Close button
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        notification.remove();
    });
}

// Get notification icon
function getNotificationIcon(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    
    return icons[type] || 'info-circle';
}

// Perform bulk action
function performBulkAction(action, checkedItems) {
    const ids = Array.from(checkedItems).map(item => item.value);
    
    fetch('bulk-action-handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: action,
            ids: ids
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(`${action} completed successfully!`, 'success');
            location.reload(); // Refresh page to see changes
        } else {
            showNotification(`Error: ${data.message}`, 'error');
        }
    })
    .catch(error => {
        showNotification(`Error: ${error.message}`, 'error');
    });
}

// Refresh media grid
function refreshMediaGrid() {
    const mediaGrid = document.querySelector('.media-grid');
    if (!mediaGrid) return;
    
    fetch('get-media.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mediaGrid.innerHTML = data.html;
        }
    })
    .catch(error => {
        console.error('Error refreshing media grid:', error);
    });
}

// Search functionality
function initSearch() {
    const searchInputs = document.querySelectorAll('.search-input');
    
    searchInputs.forEach(input => {
        let searchTimeout;
        
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(this.value, this.dataset.target);
            }, 500);
        });
    });
}

// Perform search
function performSearch(query, target) {
    const targetElement = document.querySelector(target);
    if (!targetElement) return;
    
    fetch(`search.php?q=${encodeURIComponent(query)}&target=${target}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            targetElement.innerHTML = data.html;
        }
    })
    .catch(error => {
        console.error('Search error:', error);
    });
}

// Initialize search when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initSearch();
});

// Utility functions
function formatFileSize(bytes) {
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    if (bytes === 0) return '0 Bytes';
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Add CSS for notifications
const notificationCSS = `
<style>
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    max-width: 400px;
}

.notification {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    padding: 15px;
    animation: slideIn 0.3s ease;
}

.notification-success {
    border-left: 4px solid #00a32a;
}

.notification-error {
    border-left: 4px solid #d63638;
}

.notification-warning {
    border-left: 4px solid #dba617;
}

.notification-info {
    border-left: 4px solid #0073aa;
}

.notification-content {
    display: flex;
    align-items: center;
    flex: 1;
}

.notification-content i {
    margin-right: 10px;
    font-size: 16px;
}

.notification-success .notification-content i {
    color: #00a32a;
}

.notification-error .notification-content i {
    color: #d63638;
}

.notification-warning .notification-content i {
    color: #dba617;
}

.notification-info .notification-content i {
    color: #0073aa;
}

.notification-close {
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    padding: 5px;
    margin-left: 10px;
}

.notification-close:hover {
    color: #333;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.upload-progress {
    margin-top: 20px;
}

.progress-bar {
    background: #f1f1f1;
    border-radius: 4px;
    height: 8px;
    overflow: hidden;
    margin-bottom: 10px;
}

.progress-fill {
    background: #0073aa;
    height: 100%;
    width: 0%;
    animation: pulse 1.5s ease-in-out infinite;
}

.progress-text {
    text-align: center;
    color: #666;
    font-size: 14px;
}

@keyframes pulse {
    0% { width: 0%; }
    50% { width: 100%; }
    100% { width: 0%; }
}

.editor-toolbar {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-bottom: none;
    border-radius: 4px 4px 0 0;
    padding: 8px;
    display: flex;
    gap: 5px;
}

.editor-toolbar button {
    background: none;
    border: 1px solid transparent;
    border-radius: 3px;
    padding: 6px 8px;
    cursor: pointer;
    color: #666;
}

.editor-toolbar button:hover {
    background: #e1e1e1;
    border-color: #ccc;
}

.editor-content {
    border: 1px solid #ddd;
    border-radius: 0 0 4px 4px;
    padding: 12px;
    min-height: 200px;
    background: #fff;
}

.editor-content:focus {
    outline: none;
    border-color: #0073aa;
    box-shadow: 0 0 0 1px #0073aa;
}

.toolbar-separator {
    width: 1px;
    background: #ddd;
    margin: 0 5px;
}
</style>
`;

document.head.insertAdjacentHTML('beforeend', notificationCSS);