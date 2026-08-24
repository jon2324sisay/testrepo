<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $username = $_SESSION['username'];
    
    // Read the redirect destination (Defaults to 'dashboard' if not set)
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard';

    // Fetch document details for activity logging
    $doc_query = "SELECT title, file_name FROM documents WHERE id = '$id'";
    $doc_res = mysqli_query($conn, $doc_query);
    $doc = mysqli_fetch_assoc($doc_res);
    $title = $doc['title'];

    // Soft delete: Update is_deleted to 1 instead of deleting from DB
    $query = "UPDATE documents SET is_deleted = 1 WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        // Log this activity (Audit Trail)
        $action_text = "Moved to Trash Bin: " . $title;
        $log_query = "INSERT INTO activity_logs (username, action, file_info) 
                      VALUES ('$username', '$action_text', '{$doc['file_name']}')";
        mysqli_query($conn, $log_query);

        // Determine where to redirect the user dynamically (Solves your return issue!)
        $redirect_page = ($redirect == 'files') ? 'files.php' : 'dashboard.php';

        echo "<script>alert('Document moved to Trash Bin successfully!'); window.location='$redirect_page';</script>";
    } else {
        echo "<script>alert('Failed to delete document! Please try again.'); window.location='dashboard.php';</script>";
    }
}
?>