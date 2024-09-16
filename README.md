
# Project Overview

This project is a web-based application built using PHP, HTML, CSS, and JavaScript. It allows users to login, query data, upload files, and manage downloads. The system features an SQL query editor with autocomplete functionality and responsive design.

## Features

1. **Login System**:
   - Users can log in via a form that submits data to `index.php`.
   - Error messages are displayed in case of incorrect login credentials.

2. **Main Menu**:
   - After logging in, users are directed to a menu (`menu.html`) where they can:
     - Query data via `query.php`
     - Upload data via `upload.php`

3. **Query Data**:
   - `query.php` provides an Ace Editor interface for users to input SQL queries.
   - The editor supports:
     - SQL syntax highlighting
     - Autocompletion for tables and columns
     - Snippet support
   - Queries are sanitized by removing SQL comments before submission.

4. **Upload Data**:
   - The upload page (`upload.php`) allows users to upload files.
   - Uploaded files are handled by `upload_handler.php`.

5. **Download Data**:
   - Users can download files using the `download.php` functionality.

## Files

- **index.php**: Main entry point and handles login/logout.
- **login.html**: Front-end for user login.
- **menu.html**: Main menu interface for navigation.
- **query.php**: SQL query input page with Ace Editor.
- **upload.php**: File upload page.
- **upload_handler.php**: Handles the uploaded files on the server.
- **download.php**: Manages file downloads.
- **results.php**: Likely handles and displays query results (based on the file name).
- **scripts.js**: Contains JavaScript code for managing Ace Editor and form submissions.
- **styles.css**: Styling for all the pages, ensuring a responsive design.

## Requirements

- **PHP**: Make sure the server supports PHP to run server-side scripts.
- **Web Server**: Can be deployed on any web server that supports PHP (e.g., Apache, Nginx).
- **Database**: Connect the application to an SQL database for query execution (modify connection details as necessary).
  
## Installation & Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/naikvaditya/SQL-Web-Interface.git
   cd SQL-Web-Interface
   ```

2. Move the files to your web server directory.

3. Ensure the server has write permissions for the directories where files are uploaded/downloaded.

4. Access the application via a web browser.

5. Login using the credentials updated in `config.py`.

## Configuration

The project contains a `demo_config.php` file, which holds the configuration details for database connections and other environment-specific settings.
1. Update this file with your own database credentials and configurations.
2. Rename `demo_config.php` to `config.php` before deploying.

## Usage

1. **Login**: 
   - Open the login page at `/login.html`.
   - Enter your username and password to access the system.

2. **Querying Data**: 
   - Navigate to the query section from the main menu.
   - Enter your SQL queries in the provided editor and click the submit button.

3. **Uploading Files**: 
   - Go to the upload section and select a file to upload.
   - After submission, the file will be processed by the server.

4. **Downloading Files**: 
   - Use the download section to retrieve files.

## Credits

- **CSS**: Responsive design using modern CSS techniques.
- **JavaScript**: Ace Editor for SQL query editing and form validation.
- **PHP**: Server-side scripting for login, file handling, and query execution.


## License

This project is licensed under the MIT License. See the LICENSE file for details.

