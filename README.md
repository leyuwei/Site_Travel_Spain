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
4. Edit `config.php` and set the `$site_password` variable to the password you
   want to use for accessing the site.
5. Open `login.php` in your browser and enter the password. A cookie will be
   stored so you do not need to log in on every visit.
6. Once logged in you can access `index.php` and `admin.php` normally.

## Security Note
This project now includes a very basic password protection mechanism. All pages require the password specified in `config.php` and set a cookie once authenticated. For production use you should implement a more robust authentication system and sanitize inputs further.
