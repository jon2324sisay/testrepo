<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$host = "sql202.infinityfree.com";
$username = "if0_42712329";
$password = "8FzYyQMhFq85A";
$dbname = "if0_42712329_office";

// Create Connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check Connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>