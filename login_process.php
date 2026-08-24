<?php
session_start();
include 'db.php';

if (isset($_POST['login_btn'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Query to check if the user exists
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Check if the user is deactivated (status = 0)
        if ($user['status'] == 0) {
            header("Location: login.php?error=deactivated");
            exit();
        }

        // Verify the encrypted password
        if (password_verify($password, $user['password'])) {
            // Set session variables (Including Department for routing)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['department'] = $user['department']; // Kept secure!

            // Log this activity (Audit Trail)
            $log_query = "INSERT INTO activity_logs (username, action) VALUES ('$username', 'Logged In')";
            mysqli_query($conn, $log_query);

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Redirect with password error parameter
            header("Location: login.php?error=invalid_password");
            exit();
        }
    } else {
        // Redirect with user not found error parameter
        header("Location: login.php?error=user_not_found");
        exit();
    }
}
?>