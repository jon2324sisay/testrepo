<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$host = "localhost";
$username = "root";
$password = "";
$dbname = "office_db";

// Create Connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check Connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>