<?php
session_start();

// Include the configuration
$config = include('config.php');

// Redirect to login page if not logged in
if (!isset($_SESSION['loggedin'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check login credentials
        if ($username === $config['db_username'] && $password === $config['db_password']) {
            $_SESSION['loggedin'] = true;
            header("Location: index.php");
            exit;
        } else {
            $login_error = "Invalid username or password";
        }
    }

    // Display login form if not logged in
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="login-container">
            <h1>Login</h1>
            <form method="POST" action="">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <input type="submit" value="Login">
            </form>';
            if (isset($login_error)) {
                echo "<div class='error'>$login_error</div>";
            }
    echo '
        </div>
    </body>
    </html>';
    exit;
}

// Proceed with the main page if logged in
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Execute SQL Query</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="query-section">
            <h1><a href="" style="text-decoration:none; color:black;">Execute SQL Query</a></h1>
            <form method="POST" action="">
                <label for="query">Enter SQL Query:</label>
                <textarea name="query" id="query" placeholder="Enter your SQL queries here" required><?php echo isset($_POST['query']) ? htmlspecialchars($_POST['query']) : ''; ?></textarea>
                <input type="submit" value="Execute">
            </form>
        </div>

        <div class="results-section">
            <?php
            try {
                $conn = new mysqli($config['db_host'], $config['db_username'], $config['db_password'], $config['db_name']);

                if ($conn->connect_error) {
                    throw new mysqli_sql_exception("Connection failed: " . $conn->connect_error);
                }

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $query = $_POST['query'];
                    if ($conn->multi_query($query)) {
                        echo "<div class='success'>Query executed successfully.</div>";
                        do {
                            if ($result = $conn->store_result()) {
                                echo "<div class='results'><h2>Query Results:</h2>";
                                echo "<table border='1'><tr>";

                                $field_info = $result->fetch_fields();
                                foreach ($field_info as $field) {
                                    echo "<th>{$field->name}</th>";
                                }
                                echo "</tr>";

                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    foreach ($row as $data) {
                                        echo "<td>{$data}</td>";
                                    }
                                    echo "</tr>";
                                }
                                echo "</table>";
                                $result->free();
                            }
                        } while ($conn->more_results() && $conn->next_result());
                    } else {
                        throw new mysqli_sql_exception("Error executing query: " . $conn->error);
                    }
                }

                $conn->close();
            } catch (mysqli_sql_exception $e) {
                echo "<div class='error'>".$e->getMessage()."</div>";
                echo "<script>logErrorToConsole(" . json_encode($e->getMessage()) . ");</script>";
            }
            ?>
        </div>
    </div>
</body>
</html>