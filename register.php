<?php
// Enable error reporting to show mistakes on screen
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role']; 
$username_logged = $_SESSION['username'];

// Only Admin can register new users (RBAC Requirement)
if ($user_role !== 'Admin') {
    echo "<script>alert('Access Denied! Only Admin can register new users.'); window.location='dashboard.php';</script>";
    exit();
}

// Process registration form (Now with 3 separate Name Inputs!)
if (isset($_POST['register_btn'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $new_user = mysqli_real_escape_string($conn, $_POST['new_username']);
    $password = $_POST['new_password'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);

    // Concatenate the 3 names into a single Full Name variable (Smart Coding!)
    $full_name = $first_name . ' ' . $middle_name . ' ' . $last_name;

    // Securely hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user into database including full_name
    $query = "INSERT INTO users (full_name, username, password, role, department) 
              VALUES ('$full_name', '$new_user', '$hashed_password', '$role', '$department')";
    
    if (mysqli_query($conn, $query)) {
        // Log this activity (Audit Trail)
        $action_text = "Registered new user: " . $full_name . " (" . $role . ") in " . $department;
        $log_query = "INSERT INTO activity_logs (username, action) VALUES ('$username_logged', '$action_text')";
        mysqli_query($conn, $log_query);

        echo "<script>alert('New user registered successfully!'); window.location='register.php';</script>";
    } else {
        echo "<script>alert('Registration failed! Username might already exist.'); window.location='register.php';</script>";
    }
}

// Fetch all registered users (except the currently logged-in Admin)
$users_query = "SELECT * FROM users WHERE id != " . $_SESSION['user_id'] . " ORDER BY id DESC";
$users_result = mysqli_query($conn, $users_query);

// Fetch all active departments dynamically from the database for the dropdown
$dept_query = "SELECT * FROM departments ORDER BY name ASC";
$dept_result = mysqli_query($conn, $dept_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office File Management System - Register User</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f1f3f7; font-size: 1.00rem; } /* Reduced global font size slightly for better screen fit */
        .navbar-custom { background-color: #1a365d; }
        .navbar-brand { font-size: 1.30rem; }
        .nav-link { font-size: 1.10rem; }
        .badge { font-size: 0.90rem; }
        .btn-sm { font-size: 0.90rem; }
    </style>
</head>
<body>

<!-- New Clean Universal Navigation Bar (Without search.php) -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="bi bi-folder-fill me-2"></i>Office File Management
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                </li>
                
                <!-- DROPDOWN 1: File Management (Cleaned up!) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="docDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-file-earmark-text-fill me-1"></i> File Management
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="docDropdown">
                        <li><a class="dropdown-item py-2 fw-semibold" href="upload.php"><i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i> Upload File</a></li>
                        <li><a class="dropdown-item py-2 fw-semibold" href="files.php"><i class="bi bi-file-earmark-text-fill text-primary me-2"></i> List of Files</a></li>
                        <li><a class="dropdown-item py-2 fw-semibold" href="transfer.php"><i class="bi bi-send-fill text-info me-2"></i> Transfer File</a></li>
                    </ul>
                </li>
                
                <!-- DROPDOWN 2: Administration -->
                <?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Manager'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear-fill me-1"></i> Administration
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="adminDropdown">
                        <li><a class="dropdown-item py-2 fw-semibold" href="trash.php"><i class="bi bi-trash-fill text-warning me-2"></i> Trash Bin</a></li>
                        
                        <!-- Only Manager can see Track Transfers -->
                        <?php if ($_SESSION['role'] == 'Manager'): ?>
                        <li><a class="dropdown-item py-2 fw-semibold" href="track_transfers.php"><i class="bi bi-send-check-fill text-success me-2"></i> Track Transfers</a></li>
                        <?php endif; ?>
                        
                        <!-- Only Admin can see Activity Log, User Registration, and Manage Departments -->
                        <?php if ($_SESSION['role'] == 'Admin'): ?>
                        <li><a class="dropdown-item py-2 fw-semibold" href="logs.php"><i class="bi bi-journal-text text-primary me-2"></i> Activity Log</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 fw-semibold" href="register.php"><i class="bi bi-person-plus-fill text-primary me-2"></i> Register User</a></li>
                        <li><a class="dropdown-item py-2 fw-semibold" href="add_department.php"><i class="bi bi-building-plus text-success me-2"></i> Manage Departments</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text text-white me-3 fw-semibold">
                <i class="bi bi-person-circle me-1"></i> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm fw-bold">Logout</a>
        </div>
    </div>
</nav>

<!-- Main Container (Upgraded: Compact mt-3 padding for better screen height fit) -->
<div class="container mt-3 mb-5">
    <div class="row justify-content-center">
        <!-- Left Column: Registration Form (Upgraded: Horizontal Names Layout & Compact Spacing!) -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold text-dark border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person-plus-fill text-success me-2"></i>Register New user </h5>
                    <a href="dashboard.php" class="btn btn-sm btn-outline-secondary fw-bold">Back</a>
                </div>
                <!-- card-body padding is compact and clean (p-4) -->
                <div class="card-body p-4">
                    <form action="register.php" method="POST">
                        
                        <!-- 3 Names placed horizontally in a single row! (Saves massive space) -->
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <label for="first_name" class="form-label fw-semibold text-secondary small mb-1">First Name</label>
                                <input type="text" class="form-control py-1" id="first_name" name="first_name" required placeholder="Abebe" autocomplete="off">
                            </div>
                            <div class="col-md-4">
                                <label for="middle_name" class="form-label fw-semibold text-secondary small mb-1">Middle Name</label>
                                <input type="text" class="form-control py-1" id="middle_name" name="middle_name" required placeholder="Kebede" autocomplete="off">
                            </div>
                            <div class="col-md-4">
                                <label for="last_name" class="form-label fw-semibold text-secondary small mb-1">Last Name</label>
                                <input type="text" class="form-control py-1" id="last_name" name="last_name" required placeholder="Almaz" autocomplete="off">
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="mb-2">
                            <label for="new_username" class="form-label fw-semibold text-secondary small mb-1">Username (Login ID)</label>
                            <input type="text" class="form-control py-1" id="new_username" name="new_username" required placeholder="Enter username" autocomplete="off">
                        </div>

                        <!-- Password -->
                        <div class="mb-2">
                            <label for="new_password" class="form-label fw-semibold text-secondary small mb-1">Password</label>
                            <input type="password" class="form-control py-1" id="new_password" name="new_password" required placeholder="Enter password">
                        </div>

                        <!-- Department Selection -->
                        <div class="mb-2">
                            <label for="department" class="form-label fw-semibold text-secondary small mb-1">Department / Office</label>
                            <select class="form-select py-1" id="department" name="department" required>
                                <option value="" selected disabled>Select Department</option>
                                <?php if (mysqli_num_rows($dept_result) > 0): ?>
                                    <?php while ($dept = mysqli_fetch_assoc($dept_result)): ?>
                                        <option value="<?php echo htmlspecialchars($dept['name']); ?>">
                                            <?php echo htmlspecialchars($dept['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Role Selection -->
                        <div class="mb-2">
                            <label for="role" class="form-label fw-semibold text-secondary small mb-1">System Role</label>
                            <select class="form-select py-1" id="role" name="role" required>
                                <option value="Staff" selected>Staff (Secretary/Officer)</option>
                                <option value="Manager">Manager </option>
                               
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" name="register_btn" class="btn btn-success py-1.5 fw-bold shadow-sm">Register User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Registered Users Table -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold text-dark border-bottom d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-people-fill text-primary me-2"></i>Registered Users Directory</span>
                    <button class="btn btn-sm btn-outline-secondary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#usersTableCollapse" aria-expanded="true" aria-controls="usersTableCollapse">
                        <i class="bi bi-arrows-expand me-1"></i> Toggle View
                    </button>
                </div>
                <div class="collapse show" id="usersTableCollapse">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Full Name</th>
                                        <th>Username</th>
                                        <th>Department / Office</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($users_result) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($users_result)): ?>
                                            <tr>
                                                <td class="fw-bold text-dark small"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                                <td class="small"><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td class="small text-secondary"><?php echo htmlspecialchars($row['department'] ? $row['department'] : 'N/A'); ?></td>
                                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['role']); ?></span></td>
                                                <td>
                                                    <?php if ($row['status'] == 1): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Deactivated</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($row['status'] == 1): ?>
                                                        <a href="user_action.php?action=toggle&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger fw-bold">Deactivate</a>
                                                    <?php else: ?>
                                                        <a href="user_action.php?action=toggle&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-success fw-bold">Activate</a>
                                                    <?php endif; ?>
                                                    <a href="user_action.php?action=reset&id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to reset password to 12345678?')" class="btn btn-sm btn-outline-primary fw-bold ms-1">Reset Pass</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-secondary py-4">No other users registered in the system.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>