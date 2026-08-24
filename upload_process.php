<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['upload_btn'])) {
    $ref_number = mysqli_real_escape_string($conn, $_POST['ref_number']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $sender_receiver = mysqli_real_escape_string($conn, $_POST['sender_receiver']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    
    // Default sent_to is 'Archive' (Not transferred to any user yet)
    $sent_to = 'Archive';
    $username = $_SESSION['username'];

    // NEW: Read Ethiopian Date from form inputs and combine them safely
    $eth_day = mysqli_real_escape_string($conn, $_POST['eth_day']);
    $eth_month = mysqli_real_escape_string($conn, $_POST['eth_month']);
    $eth_year = mysqli_real_escape_string($conn, $_POST['eth_year']);
    $document_date = $eth_day . "/" . $eth_month . "/" . $eth_year; // e.g., "1/1/2018"

    // File upload directory
    $target_dir = "uploads/";
    $file_name = basename($_FILES["document_file"]["name"]);
    
    // Add unique timestamp to prevent duplicate file names
    $new_file_name = time() . "_" . $file_name;
    $target_file_path = $target_dir . $new_file_name;
    $file_type = pathinfo($target_file_path, PATHINFO_EXTENSION);

    // Allow only PDF and Image types
    $allow_types = array('pdf', 'jpg', 'png', 'jpeg', 'gif');
    
    if (in_array(strtolower($file_type), $allow_types)) {
        if (move_uploaded_file($_FILES["document_file"]["tmp_name"], $target_file_path)) {
            
            // Insert metadata with DYNAMIC document_date into the database
            $query = "INSERT INTO documents (ref_number, title, sender_receiver, file_name, department, sent_to, document_date) 
                      VALUES ('$ref_number', '$title', '$sender_receiver', '$new_file_name', '$department', '$sent_to', '$document_date')";
            
            if (mysqli_query($conn, $query)) {
                // Log this activity (Audit Trail)
                $action_text = "Archived file: " . $title;
                $log_query = "INSERT INTO activity_logs (username, action, file_info) 
                              VALUES ('$username', '$action_text', '$new_file_name')";
                mysqli_query($conn, $log_query);

                echo "<script>alert('Document archived successfully in storage!'); window.location='dashboard.php';</script>";
            } else {
                echo "<script>alert('Database insertion failed! Please try again.'); window.location='upload.php';</script>";
            }
        } else {
            echo "<script>alert('File upload failed! Please check folder permissions.'); window.location='upload.php';</script>";
        }
    } else {
        echo "<script>alert('Only PDF, JPG, JPEG, PNG, and GIF files are allowed.'); window.location='upload.php';</script>";
    }
}
?>