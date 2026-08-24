<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];
$username = $_SESSION['username'];

// Allow BOTH Admin and Manager to restore files
if ($user_role !== 'Admin' && $user_role !== 'Manager') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Fetch document details for activity logging
    $doc_query = "SELECT title, file_name FROM documents WHERE id = '$id'";
    $doc_res = mysqli_query($conn, $doc_query);
    $doc = mysqli_fetch_assoc($doc_res);
    $title = $doc['title'];

    // Restore: Set is_deleted back to 0
    $query = "UPDATE documents SET is_deleted = 0 WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        // Log this activity (Audit Trail)
        $action_text = "Restored file from Trash: " . $title;
        $log_query = "INSERT INTO activity_logs (username, action, file_info) 
                      VALUES ('$username', '$action_text', '{$doc['file_name']}')";
        mysqli_query($conn, $log_query);

        echo "<script>alert('Document restored successfully!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to restore document! Please try again.'); window.location='trash.php';</script>";
    }
}
?>