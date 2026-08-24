<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['transfer_btn'])) {
    $doc_id = mysqli_real_escape_string($conn, $_POST['doc_id']);
    $transfer_to = mysqli_real_escape_string($conn, $_POST['transfer_to']);
    $new_department = mysqli_real_escape_string($conn, $_POST['new_department']);
    $username_logged = $_SESSION['username'];
    
    // Read the redirect destination from hidden input (Defaults to 'dashboard' if not set)
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'dashboard';

    // Fetch document details for activity logging
    $doc_query = "SELECT title, file_name, department FROM documents WHERE id = '$doc_id'";
    $doc_res = mysqli_query($conn, $doc_query);
    $doc = mysqli_fetch_assoc($doc_res);
    $title = $doc['title'];

    // Determine the department (Keep current or update to new)
    $final_department = ($new_department == 'Keep') ? $doc['department'] : $new_department;

    // Update both recipient (sent_to) and department (Assign) in database
    $query = "UPDATE documents SET sent_to = '$transfer_to', department = '$final_department' WHERE id = '$doc_id'";
    
    if (mysqli_query($conn, $query)) {
        // Log this activity (Audit Trail)
        $action_text = "Transferred document: " . $title . " to " . $transfer_to . " and assigned to " . $final_department;
        $log_query = "INSERT INTO activity_logs (username, action, file_info) 
                      VALUES ('$username_logged', '$action_text', '{$doc['file_name']}')";
        mysqli_query($conn, $log_query);

        // Determine where to redirect the user dynamically (Solves your return issue!)
        $redirect_page = ($redirect == 'files') ? 'files.php' : 'dashboard.php';

        echo "<script>alert('Document transferred and assigned successfully!'); window.location='$redirect_page';</script>";
    } else {
        echo "<script>alert('Transfer and assignment failed! Please try again.'); window.location='dashboard.php';</script>";
    }
}
?>