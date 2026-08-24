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
    $username = $_SESSION['username'];

    // Fetch document details to delete the physical file
    $doc_query = "SELECT title, file_name FROM documents WHERE id = '$id'";
    $doc_res = mysqli_query($conn, $doc_query);
    $doc = mysqli_fetch_assoc($doc_res);
    $title = $doc['title'];
    $file_name = $doc['file_name'];
    $file_path = "uploads/" . $file_name;

    // Permanent Delete from database
    $query = "DELETE FROM documents WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        // Delete the physical file from the local "uploads" folder
        if (file_exists($file_path)) {
            unlink($file_path); // Physical deletion
        }

        // Log this activity (Audit Trail)
        $action_text = "Permanently deleted file: " . $title;
        $log_query = "INSERT INTO activity_logs (username, action, file_info) 
                      VALUES ('$username', '$action_text', '$file_name')";
        mysqli_query($conn, $log_query);

        echo "<script>alert('Document deleted permanently from system and storage!'); window.location='trash.php';</script>";
    } else {
        echo "<script>alert('Failed to permanently delete document! Please try again.'); window.location='trash.php';</script>";
    }
}
?>