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
    $query = "SELECT TABLE_NAME, COLUMN_NAME
              FROM INFORMATION_SCHEMA.COLUMNS
              WHERE TABLE_SCHEMA = '" . $conn->real_escape_string($dbName) . "'";

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

// Store the last query in session after execution
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['query'])) {
    $_SESSION['last_query'] = $_POST['query'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Execute SQL Query</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ext-language_tools.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/snippets/sql.js"></script>
    <script>
        var schema = <?php echo $schemaJson; ?>;
    </script>
</head>
<body>
    <div class="container">
        <!-- Logout Button -->
        <div style="position: absolute; top: 10px; right: 10px;">
            <a href="?logout=true" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">Logout</a>
        </div>

        <div class="query-section">
            <h1><a href="" style="text-decoration:none; color:black;">Execute SQL Query</a></h1>
            <form method="POST" action="">
                <div id="editor" style="height: 300px; width: 100%;">-- Enter your SQL queries here:<br/></div>
                <input type="hidden" name="query" id="query">
                <input type="submit" value="Execute">
            </form>
        </div>

        <div class="results-section">
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['query'])) {
                try {
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
                } catch (mysqli_sql_exception $e) {
                    echo "<div class='error'>".$e->getMessage()."</div>";
                    echo "<script>logErrorToConsole(" . json_encode($e->getMessage()) . ");</script>";
                }
            }
            ?>
        </div>
    </div>

    <script>
        ace.require("ace/ext/language_tools");

        var editor = ace.edit("editor");
        editor.session.setMode("ace/mode/sql");
        editor.setOptions({
            enableBasicAutocompletion: true, // Enable basic autocompletion
            enableLiveAutocompletion: true,  // Enable live autocompletion
            enableSnippets: true             // Enable code snippets
        });

        var customCompleter = {
            getCompletions: function(editor, session, pos, prefix, callback) {
                var suggestions = [];

                for (var table in schema) {
                    if (schema.hasOwnProperty(table)) {
                        suggestions.push({
                            caption: table,
                            value: table,
                            meta: "table"
                        });

                        schema[table].forEach(function(column) {
                            suggestions.push({
                                caption: table + "." + column,
                                value: table + "." + column,
                                meta: "column"
                            });
                        });
                    }
                }

                callback(null, suggestions);
            }
        };

        editor.completers = [customCompleter,
            ace.require("ace/ext/language_tools").keyWordCompleter,
            ace.require("ace/ext/language_tools").textCompleter,
            ace.require("ace/ext/language_tools").snippetCompleter
        ];

        // Load the last query if it exists
        <?php if (isset($_SESSION['last_query'])): ?>
            editor.setValue(<?php echo json_encode($_SESSION['last_query']); ?>, 1);
        <?php endif; ?>

        // Sync Ace Editor content with the hidden input field
        function syncEditorContent() {
            document.getElementById("query").value = editor.getValue();
        }

        var form = document.querySelector("form");
        form.addEventListener("submit", function() {
            syncEditorContent();
        });

        // Add event listener for Command+Enter
        editor.commands.addCommand({
            name: "executeQuery",
            bindKey: {win: "Ctrl-Enter", mac: "Command-Enter"},
            exec: function() {
                syncEditorContent();  // Ensure the query field is updated
                form.submit();
            }
        });
    </script>
</body>
</html>