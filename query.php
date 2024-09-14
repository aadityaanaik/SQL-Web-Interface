<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.html");
    exit;
}

$config = include('config.php');
$conn = new mysqli($config['db_host'], $config['db_username'], $config['db_password'], $config['db_name']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Store the last query in session after execution
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['query'])) {
    $_SESSION['last_query'] = $_POST['query'];
}

// Get the last query from session
$lastQuery = isset($_SESSION['last_query']) ? json_encode($_SESSION['last_query']) : '""'; // Default to empty string
$schemaJson = json_encode($conn->query("SHOW TABLES")->fetch_all());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Query Database</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ext-language_tools.js"></script>
    <script src="scripts.js"></script>
</head>
<body>
    <div class="container">
        <div class="query-section">
            <h1>Execute SQL Query</h1>
            <form method="POST" action="">
                <div id="editor" style="height: 300px; width: 100%;">-- Enter your SQL queries here:</div>
                <input type="hidden" name="query" id="query">
                <input type="submit" value="Execute">
            </form>
        </div>

        <div class="results-section">
            <?php include 'results.php'; ?>
        </div>
    </div>

    <script>
        var lastQuery = <?php echo $lastQuery; ?>;
        var schema = <?php echo $schemaJson; ?>;
    </script>
</body>
</html>