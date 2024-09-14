<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['query'])) {
    try {
        $query = $_POST['query'];
        $_SESSION['last_query'] = $query; // Store the last query
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