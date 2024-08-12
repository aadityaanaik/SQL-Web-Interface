<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Execute SQL Query</title>
    <style>
        /* Include the CSS for scrollable sections */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            display: flex;
            flex: 1;
            justify-content: space-between;
            align-items: stretch;
            width: 100%;
            overflow: hidden;
        }

        .query-section {
            width: 40%;
            padding: 20px;
            box-sizing: border-box;
            overflow: auto; /* Enable both vertical and horizontal scrolling */
        }

        .results-section {
            width: 60%;
            padding: 20px;
            box-sizing: border-box;
            overflow: auto; /* Enable both vertical and horizontal scrolling */
        }

        textarea {
            width: 100%;
            height: calc(100vh - 140px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .success, .error {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="query-section">
            <h1>Execute SQL Query</h1>
            <form method="POST" action="">
                <label for="query">Enter SQL Query:</label>
                <textarea name="query" id="query" placeholder="Enter your SQL queries here" required><?php echo isset($_POST['query']) ? htmlspecialchars($_POST['query']) : ''; ?></textarea>
                <input type="submit" value="Execute">
            </form>
        </div>

        <div class="results-section">
            <?php
            try {
                $config = include('config.php');
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