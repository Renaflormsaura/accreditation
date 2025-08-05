# PUP Accreditation Website

A comprehensive accreditation management system for Polytechnic University of the Philippines (PUP) with a WordPress-style admin panel.

## Features

### Admin Panel
- **WordPress-style Navigation**: Familiar sidebar navigation with dropdown menus
- **Dashboard**: Statistics overview with recent activity
- **Posts Management**: Create, edit, and manage posts with categories
- **Media Library**: Secure file upload with drag-and-drop functionality
- **Pages Management**: Create and manage static pages
- **Comments System**: Moderate and manage user comments
- **User Management**: Admin and super admin role management
- **Responsive Design**: Mobile-friendly admin interface

### Security Features
- **Secure Authentication**: Password hashing and session management
- **Role-based Access Control**: Admin and Super Admin roles
- **File Upload Security**: Secure file handling outside web root
- **SQL Injection Protection**: Prepared statements throughout
- **Session Security**: Secure session handling with tokens

### Frontend
- **Modern Design**: Responsive website with PUP branding
- **Mobile Responsive**: Works on all device sizes
- **SEO Friendly**: Clean HTML structure and meta tags

## Installation Instructions

### Prerequisites
- XAMPP (Apache, MySQL, PHP 7.4+)
- Web browser
- Text editor (optional)

### Step 1: Setup XAMPP
1. Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Import the database schema:
   - Click "Import" tab
   - Choose file: `database_schema.sql`
   - Click "Go" to execute

### Step 3: File Setup
1. Copy all files to your XAMPP htdocs directory:
   ```
   C:\xampp\htdocs\pup-accreditation\
   ```
2. Create uploads directory with proper permissions:
   ```
   mkdir uploads
   mkdir uploads/2024
   mkdir uploads/2024/01
   ```

### Step 4: Configuration
1. Open `connection.php` and verify database settings:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'pup_accreditation');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Default XAMPP password is empty
   ```

### Step 5: Access the System
1. **Frontend**: http://localhost/pup-accreditation/
2. **Admin Panel**: http://localhost/pup-accreditation/admin/
3. **Default Login Credentials**:
   - Username: `superadmin`
   - Password: `admin123`

## File Structure

```
pup-accreditation/
├── admin/                      # Admin panel files
│   ├── assets/
│   │   ├── css/
│   │   │   └── admin-style.css # Admin panel styles
│   │   └── js/
│   │       └── admin-script.js # Admin panel JavaScript
│   ├── index.php              # Admin dashboard
│   ├── login.php              # Admin login page
│   ├── logout.php             # Logout functionality
│   ├── media-upload.php       # Media upload page
│   └── upload-handler.php     # File upload processor
├── uploads/                   # File upload directory (create manually)
├── connection.php             # Database connection & security functions
├── database_schema.sql        # Database structure
├── index.php                 # Frontend homepage
└── README.md                 # This file
```

## Admin Panel Navigation

### Dashboard
- **Home**: Add new page, open site, edit styles
- **Updates**: System updates and design changes

### Posts
- **All Posts**: View and manage all posts
- **Add Post**: Create new blog posts
- **Categories**: Manage post categories

### Media
- **Library**: 
  - All Media
  - Dates
  - Filter by type (Images, Audio, Video, Documents, Spreadsheets, Archives, Unattached)
- **Add Media File**: Drag-and-drop file upload

### Pages
- **All Pages**: Manage static pages
- **Add Page**: Create new pages

### Comments
- Comment moderation and management

### Users
- **All Users**: View all system users
- **Add User**: Create new user accounts
- **Profile**: Manage user profile

## Database Schema

The system includes the following main tables:

- **users**: User accounts with role-based access
- **posts**: Blog posts and articles
- **pages**: Static pages
- **media**: File uploads and media library
- **comments**: User comments on posts/pages
- **categories**: Post categorization
- **user_sessions**: Session management
- **settings**: System configuration

## Security Features

### File Upload Security
- Files are stored outside the web root
- File type validation
- Size limits (50MB per file)
- Secure filename generation
- Thumbnail generation for images

### Authentication
- Password hashing using PHP's `password_hash()`
- Secure session management
- Remember me functionality
- Session token validation

### Access Control
- **Super Admin**: Full system access
- **Admin**: Limited administrative access
- **User**: Basic user access

## Customization

### Styling
- Edit `admin/assets/css/admin-style.css` for admin panel styling
- Edit the `<style>` section in `index.php` for frontend styling

### Database Configuration
- Modify `connection.php` for different database settings
- Update database credentials as needed

### File Upload Settings
- Modify `upload-handler.php` to change file size limits
- Update allowed file types in the same file

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Ensure MySQL is running in XAMPP
   - Verify database name and credentials in `connection.php`

2. **File Upload Not Working**
   - Check that the `uploads` directory exists and is writable
   - Verify PHP file upload settings in php.ini

3. **Admin Panel Not Loading**
   - Ensure all files are in the correct directory
   - Check for PHP errors in XAMPP error logs

4. **Login Issues**
   - Verify the database was imported correctly
   - Check that the default user exists in the users table

### PHP Settings
Recommended PHP settings for optimal performance:
```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
memory_limit = 256M
```

## Support

For technical support or questions about this system:
- Email: accreditation@pup.edu.ph
- Phone: +63 (2) 8335-1PUP

## License

This system is developed for the Polytechnic University of the Philippines.
© 2024 Polytechnic University of the Philippines. All rights reserved.

## Changelog

### Version 1.0.0
- Initial release
- WordPress-style admin panel
- Secure file upload system
- User management with roles
- Responsive frontend design
- Complete database schema
- Security features implementation