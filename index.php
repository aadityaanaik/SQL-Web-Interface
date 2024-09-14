<?php
session_start();

$config = include('config.php'); // Fetch database and user config

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.html");
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Verify username and password
        if ($username === $config['db_username'] && $password === $config['db_password']) {
            $_SESSION['loggedin'] = true;
            header("Location: menu.html"); // Redirect to the menu
            exit;
        } else {
            $login_error = "Invalid username or password";
            include 'login.html'; // Show login form again
            exit;
        }
    } else {
        header("Location: login.html"); // Redirect to login if not logged in
        exit;
    }
} else {
    header("Location: menu.html"); // Redirect to menu if already logged in
    exit;
}