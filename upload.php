<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.html");
    exit;
}

$config = include('config.php');

// Establish the database connection
$conn = new mysqli($config['db_host'], $config['db_username'], $config['db_password'], $config['db_name']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all the table names to populate the dropdown
$tables = $conn->query("SHOW TABLES");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload CSV Data</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="logout-container">
        <a href="index.php?logout=true" class="logout-button">Logout</a>
    </div>
    <div class="back-container">
        <a href="menu.html" class="back-button">Back</a>
    </div>

<div class="container">

    <!-- Upload CSV File Form -->
    <div class="upload-section">
        <h2>Upload CSV File</h2>
        <form method="POST" action="upload_handler.php" enctype="multipart/form-data">
            <label for="table">Select Table:</label>
            <select id="table" name="table" required>
                <?php
                while ($table = $tables->fetch_array()) {
                    echo "<option value='{$table[0]}'>{$table[0]}</option>";
                }
                ?>
            </select>

            <label for="file">Select CSV File:</label>
            <input type="file" id="file" name="file" accept=".csv" required>
            <input type="submit" value="Upload">
        </form>
    </div>
</div>
</body>
</html>