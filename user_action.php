<?php
session_start();
include 'db.php';

// Check if user is logged in and is Admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $action = $_GET['action'];
    $username_logged = $_SESSION['username'];

    // Fetch user details
    $user_query = "SELECT username, status FROM users WHERE id = '$id'";
    $user_res = mysqli_query($conn, $user_query);
    $user = mysqli_fetch_assoc($user_res);
    $target_user = $user['username'];

    if ($action == 'toggle') {
        // Toggle status: 1 to 0, or 0 to 1
        $new_status = ($user['status'] == 1) ? 0 : 1;
        $update_query = "UPDATE users SET status = '$new_status' WHERE id = '$id'";
        
        if (mysqli_query($conn, $update_query)) {
            $status_text = ($new_status == 1) ? "Activated" : "Deactivated";
            // Log this activity (Audit Trail)
            $action_text = $status_text . " user account: " . $target_user;
            $log_query = "INSERT INTO activity_logs (username, action) VALUES ('$username_logged', '$action_text')";
            mysqli_query($conn, $log_query);

            echo "<script>alert('User account status updated successfully!'); window.location='register.php';</script>";
        }
    } elseif ($action == 'reset') {
        // Reset password to default '12345678'
        $default_pass = password_hash('12345678', PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET password = '$default_pass' WHERE id = '$id'";
        
        if (mysqli_query($conn, $update_query)) {
            // Log this activity (Audit Trail)
            $action_text = "Reset password for user: " . $target_user;
            $log_query = "INSERT INTO activity_logs (username, action) VALUES ('$username_logged', '$action_text')";
            mysqli_query($conn, $log_query);

            echo "<script>alert('Password reset successfully to: 12345678'); window.location='register.php';</script>";
        }
    }
}
?>