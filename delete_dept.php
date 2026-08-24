<?php
session_start();
include 'db.php';

// Check if user is logged in and is Admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $username_logged = $_SESSION['username'];

    // Fetch department name for activity logging
    $dept_query = "SELECT name FROM departments WHERE id = '$id'";
    $dept_res = mysqli_query($conn, $dept_query);
    $dept = mysqli_fetch_assoc($dept_res);
    $dept_name = $dept['name'];

    // Delete query from database
    $query = "DELETE FROM departments WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        // Log this activity (Audit Trail)
        $action_text = "Deleted department: " . $dept_name;
        $log_query = "INSERT INTO activity_logs (username, action) VALUES ('$username_logged', '$action_text')";
        mysqli_query($conn, $log_query);

        echo "<script>alert('Department deleted successfully!'); window.location='add_department.php';</script>";
    } else {
        echo "<script>alert('Failed to delete department! Please try again.'); window.location='add_department.php';</script>";
    }
}
?>