<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office File Management System - Login</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #eef2f7 0%, #d9e2ec 100%);
            min-height: 100vh;
        }
        .login-container {
            margin-top: 5%; /* Slightly adjusted margin */
        }
        /* Custom Corporate Navy Blue Style */
        .card-header-custom {
            background-color: #1a365d; /* Deep Navy Blue */
            color: #ffffff;
        }
        .btn-custom {
            background-color: #1a365d;
            color: #ffffff;
            border: none;
        }
        .btn-custom:hover {
            background-color: #2a4d7c;
            color: #ffffff;
        }
        .back-link {
            color: #1a365d;
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #2a4d7c;
        }
    </style>
</head>
<body>

<div class="container login-container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <!-- Clean and modern back link ABOVE the card (Prevents overlapping) -->
            <div class="mb-3 text-start">
                <a href="index.php" class="back-link fw-bold">
                    <i class="bi bi-arrow-left-circle-fill me-1"></i> Back to Homepage
                </a>
            </div>

            <div class="card shadow border-0 rounded-3">
                <div class="card-header card-header-custom text-center py-4 rounded-top-3">
                    <h2 class="fw-bold mb-1">Office File Management System</h2>
                    <span class="text-light opacity-75">User Secure Login</span>
                </div>
                <!-- card-body padding is spacious and luxurious (p-5) -->
                <div class="card-body p-5">

                    <!-- NEW DYNAMIC INLINE ALERT BOX (Enforces Premium Security Look) -->
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger py-2 mb-4 text-center fw-semibold shadow-sm d-flex align-items-center justify-content-center" style="font-size: 1rem;">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                            <?php 
                                if ($_GET['error'] == 'user_not_found') {
                                    echo "Username not found! Please try again.";
                                } elseif ($_GET['error'] == 'invalid_password') {
                                    echo "Incorrect password! Please try again.";
                                } elseif ($_GET['error'] == 'deactivated') {
                                    echo "Access Denied! Account has been deactivated.";
                                }
                            ?>
                        </div>
                    <?php endif; ?>

                    <form action="login_process.php" method="POST">
                        <!-- Username input -->
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold text-secondary">Username</label>
                            <input type="text" class="form-control py-2" id="username" name="username" required placeholder="Enter your username" autocomplete="off">
                        </div>
                        
                        <!-- Password input -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold text-secondary">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control py-2" id="password" name="password" required placeholder="Enter your password" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">Show</button>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" name="login_btn" class="btn btn-custom py-2 fw-bold shadow-sm">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to handle Show/Hide password toggle -->
<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
        // Toggle the input type between password and text
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Toggle the button text
        this.textContent = type === 'password' ? 'Show' : 'Hide';
    });
</script>

</body>
</html>