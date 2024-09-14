<?php
session_start();
$config = include('config.php');

if (!isset($_SESSION['loggedin'])) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli($config['db_host'], $config['db_username'], $config['db_password'], $config['db_name']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['download_query']) && !empty($_POST['download_query'])) {
    $query = $_POST['download_query'];
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        // Set headers for the CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="query_results.csv"');

        // Output the column names as the first row
        $fields = $result->fetch_fields();
        $csv_output = fopen('php://output', 'w');
        $header = [];
        foreach ($fields as $field) {
            $header[] = $field->name;
        }
        fputcsv($csv_output, $header);

        // Output the rows
        while ($row = $result->fetch_assoc()) {
            fputcsv($csv_output, $row);
        }

        fclose($csv_output);
        exit;
    } else {
        echo "No results found for the query.";
    }
} else {
    echo "No results to download.";
}
?>