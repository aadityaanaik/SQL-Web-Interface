<?php
session_start();

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

$config = include('config.php');

// Establish the database connection
$conn = new mysqli($config['db_host'], $config['db_username'], $config['db_password'], $config['db_name']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get the database schema
function getDatabaseSchema($conn, $dbName) {
    $schema = [];
    $query = "SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . $conn->real_escape_string($dbName) . "'";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $table = $row['TABLE_NAME'];
        $column = $row['COLUMN_NAME'];
        if (!isset($schema[$table])) {
            $schema[$table] = [];
        }
        $schema[$table][] = $column;
    }

    return json_encode($schema);
}

// Retrieve and encode the schema as JSON
$schemaJson = getDatabaseSchema($conn, $config['db_name']);

if (!isset($_SESSION['loggedin'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($username === $config['db_username'] && $password === $config['db_password']) {
            $_SESSION['loggedin'] = true;
            header("Location: index.php");
            exit;
        } else {
            $login_error = "Invalid username or password";
        }
    }

    include 'login.html';
    exit;
}

// Store the last query in session after execution
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['query'])) {
    $_SESSION['last_query'] = $_POST['query'];
}

$lastQuery = isset($_SESSION['last_query']) ? json_encode($_SESSION['last_query']) : '';

?>
<script>
    var lastQuery = <?php echo $lastQuery; ?>;
</script>

<?php include 'query.html'; ?>