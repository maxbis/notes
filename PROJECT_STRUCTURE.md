# Notes App Project Structure

```
notes/
├── .env.php                 # Configuration file (create from config.example.php)
├── .env.example.php         # Example configuration
├── .htaccess               # Apache configuration and security
├── index.php               # Main list page with search and infinite scroll
├── note.php                # View/edit note page
├── create.php              # Create new note
├── delete.php              # Delete note endpoint
├── notes_feed.php          # JSON API for infinite scroll
├── install.php             # Installation script
├── README.md               # Setup and usage instructions
├── TEST_PLAN.md            # Comprehensive testing guide
├── PROJECT_STRUCTURE.md    # This file
├── config.example.php      # Example configuration template
├── includes/
│   ├── config.php          # Configuration loader
│   ├── database.php        # Database connection and operations
│   ├── functions.php       # Utility functions
│   ├── header.php          # Common header with Tailwind CSS
│   └── footer.php          # Common footer
├── database/
│   └── schema.sql          # Database schema and sample data
└── assets/
    └── js/
        └── app.js          # JavaScript functionality
```

## File Descriptions

### Core Application Files
- **index.php**: Main page displaying notes with search, sorting, and infinite scroll
- **note.php**: Combined view/edit interface for individual notes
- **create.php**: Creates new notes and redirects to edit page
- **delete.php**: Handles note deletion with CSRF protection
- **notes_feed.php**: JSON API endpoint for infinite scroll pagination

### Configuration Files
- **.env.php**: Main configuration file (create from config.example.php)
- **.env.example.php**: Template configuration file
- **includes/config.php**: Configuration loader with defaults
- **.htaccess**: Apache configuration, security headers, and URL handling

### Database Files
- **database/schema.sql**: Complete database schema with indexes and sample data
- **includes/database.php**: Database connection class with all CRUD operations

### Utility Files
- **includes/functions.php**: Helper functions for title generation, formatting, security
- **includes/header.php**: Common HTML header with Tailwind CSS and navigation
- **includes/footer.php**: Common HTML footer

### Frontend Assets
- **assets/js/app.js**: JavaScript for infinite scroll, character counter, and UI enhancements

### Documentation
- **README.md**: Complete setup and usage guide
- **TEST_PLAN.md**: Comprehensive testing procedures
- **PROJECT_STRUCTURE.md**: This file

### Installation
- **install.php**: Web-based installation script for easy setup

## Key Features Implemented

✅ **CRUD Operations**: Create, read, update, delete notes
✅ **Search & Filtering**: Case-insensitive search with hashtag support
✅ **Sorting**: By last edited or creation date
✅ **Infinite Scroll**: Load more notes as user scrolls
✅ **Auto-title Generation**: Smart title creation from content
✅ **Character Limits**: Configurable content length validation
✅ **Security**: CSRF protection, SQL injection prevention, input sanitization
✅ **Responsive Design**: Mobile-friendly with Tailwind CSS
✅ **Hashtag Support**: Use #tags for organization
✅ **URL-based Access**: Notes accessible via hash_id in URL

## Technology Stack

- **Backend**: PHP 8.1+ with PDO
- **Database**: MariaDB/MySQL with utf8mb4 support
- **Frontend**: Tailwind CSS (CDN), vanilla JavaScript
- **Security**: CSRF tokens, prepared statements, input validation
- **Architecture**: Classic multi-page PHP with AJAX for infinite scroll

## Installation Steps

1. Copy project files to web server directory
2. Run `install.php` in browser for guided setup
3. Configure database credentials in `.env.php`
4. Import database schema from `database/schema.sql`
5. Access application via `index.php`

## Security Features

- CSRF protection on all forms
- SQL injection prevention with prepared statements
- Input validation and sanitization
- Security headers (XSS protection, content type options)
- File access restrictions via .htaccess

## Performance Optimizations

- Database indexes on frequently queried fields
- Infinite scroll with configurable page sizes
- Efficient search with LIKE queries
- Character limits to prevent excessive data storage
- Responsive design for mobile devices
