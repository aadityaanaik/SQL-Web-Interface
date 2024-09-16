<?php
session_start();

$config = include('config.php'); // Fetch database and user config

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.html");
    exit;
}

// Check if user is already logged in
if (isset($_SESSION['loggedin'])) {
    header("Location: menu.html"); // Redirect to the menu if already logged in
    exit;
}

// Handle login form submission
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verify username and password
    if ($username === $config['db_username'] && $password === $config['db_password']) {
        $_SESSION['loggedin'] = true;
        header("Location: menu.html"); // Redirect to the menu
        exit;
    } else {
        $login_error = "Invalid username or password"; // Set the error message
    }
}

// Show the login form (this is reached if not logged in or if there's an error)
include 'login.html';
?>