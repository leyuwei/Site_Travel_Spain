# Travel Information Portal

This is a simple LAMP-based website for storing and sharing travel information and documents.

## Features
- Upload and download travel documents
- Maintain a list of travel plans / itinerary notes
- Keep track of upcoming events
- Administration page for adding and deleting entries

## Setup
1. Import the database schema from `create_tables.sql` into your MySQL server:
   ```bash
   mysql -u root -p travel_db < create_tables.sql
   ```
   Create the user referenced in `config.php` and grant permissions.

2. Place the repository contents under your Apache document root.
3. Ensure the `uploads` directory is writable by the web server:
   ```bash
   chmod 777 uploads
   ```
4. Access `index.php` to view the site and `admin.php` to manage content.

## Security Note
This example omits authentication and advanced security for brevity. In production you should secure the administration interface and sanitize inputs further.
