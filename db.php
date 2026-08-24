<?php
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