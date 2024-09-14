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

// Check if a file and table have been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && isset($_POST['table'])) {
    $table = $_POST['table'];
    $csvFile = $_FILES['file']['tmp_name'];

    // Step 1: Get the schema of the selected table, ignoring the "ID" column
    $query = "SHOW COLUMNS FROM $table";
    $result = $conn->query($query);

    if (!$result) {
        echo "Error fetching table schema: " . $conn->error;
        exit;
    }

    // Get table columns, excluding the ID column
    $tableColumns = [];
    while ($row = $result->fetch_assoc()) {
        if (strtolower($row['Field']) !== 'id') { // Ignore the 'ID' column
            $tableColumns[] = $row['Field'];
        }
    }

    // Step 2: Open the CSV and get the header (first row)
    if (($handle = fopen($csvFile, 'r')) !== false) {
        $csvHeader = fgetcsv($handle);  // Get CSV header

        // Remove 'ID' if it's present in the CSV header
        $csvHeader = array_filter($csvHeader, function($column) {
            return strtolower($column) !== 'id';
        });

        // Step 3: Compare the CSV schema with the table schema (ignoring ID)
        if ($csvHeader !== $tableColumns) {
            // Display a detailed error message including both schemas
            echo "<div class='error'>CSV schema does not match the selected table schema (excluding 'ID').</div>";
            echo "<div><strong>Table Columns (from $table):</strong> " . implode(", ", $tableColumns) . "</div>";
            echo "<div><strong>CSV Columns:</strong> " . implode(", ", $csvHeader) . "</div>";
            fclose($handle);
            exit;
        }

        // Step 4: Insert data into the table (excluding ID)
        $insertSuccess = true;
        while (($data = fgetcsv($handle)) !== false) {
            // Align the data to the table columns (ignoring ID)
            if (count($data) > count($tableColumns)) {
                array_shift($data); // Ignore the first column assuming it's the 'ID' column
            }

            // Prepare the insert query (ignoring the ID column)
            $values = implode("','", array_map([$conn, 'real_escape_string'], $data));
            $query = "INSERT INTO $table (" . implode(',', $tableColumns) . ") VALUES ('$values')";

            if (!$conn->query($query)) {
                echo "<div class='error'>Error inserting data: " . $conn->error . "</div>";
                $insertSuccess = false;
                break;
            }
        }

        fclose($handle);

        if ($insertSuccess) {
            echo "<div class='success'>CSV data successfully inserted into the $table table, ignoring the 'ID' column.</div>";
        }
    } else {
        echo "<div class='error'>Failed to open CSV file.</div>";
    }
} else {
    echo "<div class='error'>No file or table selected.</div>";
}
?>