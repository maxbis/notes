# Simple PHP/MariaDB Note-Taking App

A single-user note-taking web application built with PHP 8.1+ and MariaDB, featuring plain text notes with hashtag support, search, and infinite scroll.

## Features

- **CRUD Operations**: Create, read, update, and delete notes
- **Search**: Case-insensitive search across titles and content
- **Hashtags**: Use #tag style within content for organization
- **Sorting**: Sort by last edited or creation date
- **Infinite Scroll**: Load more notes as you scroll
- **Auto-title**: Automatic title generation from content
- **Responsive Design**: Clean UI with Tailwind CSS

## Requirements

- PHP 8.1 or higher
- MariaDB 10.2+ or MySQL 5.7+
- Apache web server
- PDO extension enabled

## Setup Instructions

### For XAMPP (Local Development)

1. **Install XAMPP**
   - Download and install XAMPP from [apachefriends.org](https://www.apachefriends.org/)
   - Start Apache and MySQL services

2. **Clone/Download Project**
   - Place the project files in `htdocs/notes/` directory
   - Navigate to `http://localhost/notes/` in your browser

3. **Database Setup**
   - Open phpMyAdmin at `http://localhost/phpmyadmin`
   - Create a new database named `notes_app`
   - Import the `database/schema.sql` file

4. **Configuration**
   - Copy `.env.example.php` to `.env.php`
   - Update database credentials in `.env.php`:
     ```php
     DB_HOST = 'localhost'
     DB_NAME = 'notes_app'
     DB_USER = 'root'
     DB_PASS = ''  // XAMPP default is empty
     ```

### For VPS (Production)

1. **Server Setup**
   - Install Apache, PHP-FPM, and MariaDB
   - Ensure PDO extension is enabled
   - Set appropriate file permissions

2. **Deploy Files**
   - Upload project files to your web root
   - Set proper ownership (usually `www-data:www-data`)

3. **Database Setup**
   - Create a MariaDB database and user
   - Import `database/schema.sql`
   - Update `.env.php` with production credentials

4. **Apache Configuration**
   - Ensure mod_rewrite is enabled
   - Set proper document root permissions

## File Structure

```
notes/
├── .env.php                 # Configuration file
├── .env.example.php         # Example configuration
├── index.php               # Main list page with search and infinite scroll
├── note.php                # View/edit note page
├── create.php              # Create new note
├── delete.php              # Delete note endpoint
├── notes_feed.php          # JSON API for infinite scroll
├── includes/
│   ├── config.php          # Configuration loader
│   ├── database.php        # Database connection
│   ├── functions.php       # Utility functions
│   └── header.php          # Common header
├── database/
│   └── schema.sql          # Database schema
├── assets/
│   └── js/
│       └── app.js          # JavaScript for infinite scroll and UI
└── README.md               # This file
```

## Configuration

The `.env.php` file contains all configurable settings:

- Database connection details
- Content length limits
- Timezone settings
- Base URL configuration

## Usage

1. **Creating Notes**: Click "New Note" button on the main page
2. **Editing**: Click on any note title to open the edit interface
3. **Searching**: Use the search bar to find notes by title or content
4. **Hashtags**: Include #tag in your note content for organization
5. **Sorting**: Toggle between "Edited" and "Created" sorting
6. **Deleting**: Use the delete icon with confirmation modal

## Security Features

- Prepared statements for all database queries
- Input validation and sanitization
- Content length enforcement
- CSRF protection on forms
- HTML escaping for all output

## Testing

### Manual Test Plan

1. **Basic CRUD Operations**
   - Create a new note
   - Edit existing note
   - Delete note
   - Verify note appears/disappears from list

2. **Title Auto-fill**
   - Create note with empty title and content ≥5 chars
   - Create note with empty title and short content
   - Verify appropriate title generation

3. **Search Functionality**
   - Search for plain text words
   - Search for hashtags (#work, #personal)
   - Verify case-insensitive matching

4. **Sorting**
   - Toggle between "Edited" and "Created" sorting
   - Verify correct order of results

5. **Infinite Scroll**
   - Scroll to bottom of list
   - Verify additional notes load
   - Test with search filters applied

6. **Edge Cases**
   - Very long content (near 10,000 char limit)
   - Special characters in content
   - Empty search results

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify database credentials in `.env.php`
   - Ensure MariaDB service is running
   - Check PDO extension is enabled

2. **Permission Errors**
   - Ensure web server has read access to project files
   - Check file ownership on VPS deployments

3. **Infinite Scroll Not Working**
   - Check browser console for JavaScript errors
   - Verify `notes_feed.php` is accessible
   - Check PHP error logs for server-side issues

### Error Logs

- **XAMPP**: Check `xampp/apache/logs/error.log`
- **VPS**: Check `/var/log/apache2/error.log` or similar

## License

This project is open source and available under the MIT License.

## Support

For issues or questions, please check the error logs and ensure all requirements are met. The application is designed to be simple and self-contained.
