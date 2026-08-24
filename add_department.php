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

// Only Admin can manage departments (RBAC Requirement)
if ($user_role !== 'Admin') {
    echo "<script>alert('Access Denied!'); window.location='dashboard.php';</script>";
    exit();
}

// Process adding new department
if (isset($_POST['add_dept_btn'])) {
    $dept_name = mysqli_real_escape_string($conn, $_POST['dept_name']);

    $query = "INSERT INTO departments (name) VALUES ('$dept_name')";
    if (mysqli_query($conn, $query)) {
        // Log activity (Audit Trail)
        $action_text = "Created new department: " . $dept_name;
        mysqli_query($conn, "INSERT INTO activity_logs (username, action) VALUES ('$username_logged', '$action_text')");

        echo "<script>alert('New department created successfully!'); window.location='add_department.php';</script>";
    } else {
        echo "<script>alert('Failed! Department might already exist.'); window.location='add_department.php';</script>";
    }
}

// Process updating/editing an existing department (The Update Logic!)
if (isset($_POST['update_dept_btn'])) {
    $dept_id = mysqli_real_escape_string($conn, $_POST['dept_id']);
    $dept_name = mysqli_real_escape_string($conn, $_POST['dept_name']);

    // Fetch old name for activity logging
    $old_res = mysqli_query($conn, "SELECT name FROM departments WHERE id = '$dept_id'");
    $old_name = mysqli_fetch_assoc($old_res)['name'];

    $query = "UPDATE departments SET name = '$dept_name' WHERE id = '$dept_id'";
    if (mysqli_query($conn, $query)) {
        // Log activity (Audit Trail)
        $action_text = "Updated department: " . $old_name . " to " . $dept_name;
        mysqli_query($conn, "INSERT INTO activity_logs (username, action) VALUES ('$username_logged', '$action_text')");

        echo "<script>alert('Department updated successfully!'); window.location='add_department.php';</script>";
    } else {
        echo "<script>alert('Update failed! Please try again.'); window.location='add_department.php';</script>";
    }
}

// Check if "Edit" is clicked from table link to load specific department details (The Edit Logic!)
$edit_mode = false;
$edit_id = "";
$edit_name = "";

if (isset($_GET['edit_id'])) {
    $edit_id = mysqli_real_escape_string($conn, $_GET['edit_id']);
    $edit_query = "SELECT * FROM departments WHERE id = '$edit_id'";
    $edit_result = mysqli_query($conn, $edit_query);
    
    if (mysqli_num_rows($edit_result) > 0) {
        $edit_dept = mysqli_fetch_assoc($edit_result);
        $edit_name = $edit_dept['name'];
        $edit_mode = true; // Switch form to Update mode
    }
}

// Fetch all departments sorted by ID in ascending order (1, 2, 3, 4...)
$dept_query = "SELECT * FROM departments ORDER BY id ASC";
$dept_result = mysqli_query($conn, $dept_query);

// Count total departments in database dynamically
$count_query = "SELECT COUNT(*) AS total FROM departments";
$count_result = mysqli_query($conn, $count_query);
$dept_count = mysqli_fetch_assoc($count_result)['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office File Management System - Manage Departments</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f1f3f7; font-size: 1.15rem; }
        .navbar-custom { background-color: #1a365d; }
        .navbar-brand { font-size: 1.45rem; }
        .nav-link { font-size: 1.15rem; }
        .badge { font-size: 0.95rem; }
        .btn-sm { font-size: 0.95rem; }
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

<!-- Main Container -->
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <!-- Left Column: Add/Update Department Form (Switches dynamically based on Edit Mode!) -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold text-dark border-bottom d-flex justify-content-between align-items-center">
                    <?php if ($edit_mode): ?>
                        <!-- If Edit Mode is active, show Blue Edit Header and Cancel button -->
                        <h5 class="mb-0 text-primary"><i class="bi bi-pencil-square me-2"></i>Edit Department</h5>
                        <a href="add_department.php" class="btn btn-sm btn-outline-secondary fw-bold">Cancel</a>
                    <?php else: ?>
                        <!-- If normal mode, show green Add Header and Back button -->
                        <h5 class="mb-0"><i class="bi bi-building-plus text-success me-2"></i>Add Department</h5>
                        <a href="dashboard.php" class="btn btn-sm btn-outline-secondary fw-bold"><i class="bi bi-arrow-left-circle me-1"></i> Back</a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-4">
                    <form action="add_department.php" method="POST">
                        <?php if ($edit_mode): ?>
                            <!-- Hidden input field to carry the ID for updating -->
                            <input type="hidden" name="dept_id" value="<?php echo $edit_id; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="dept_name" class="form-label fw-semibold text-secondary">Department Name</label>
                            <input type="text" class="form-control py-2" id="dept_name" name="dept_name" required 
                                   value="<?php echo htmlspecialchars($edit_name); ?>" 
                                   placeholder="e.g., Computer Science" autocomplete="off">
                        </div>
                        <div class="d-grid gap-2 mt-4">
                            <?php if ($edit_mode): ?>
                                <!-- Blue Update Button during Edit Mode -->
                                <button type="submit" name="update_dept_btn" class="btn btn-primary py-2 fw-bold shadow-sm" style="background-color: #1a365d; border: none;">Update Department</button>
                            <?php else: ?>
                                <!-- Green Create Button during normal mode -->
                                <button type="submit" name="add_dept_btn" class="btn btn-success py-2 fw-bold shadow-sm" style="background-color: #198754; border: none;">Create Department</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Departments Table with working Edit and Delete Buttons -->
        <div class="col-md-7">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold text-dark border-bottom d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-building-fill text-primary me-2"></i>Active Departments Directory</span>
                    <span class="badge bg-success"><?php echo $dept_count; ?> Active Departments</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Department Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($dept_result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($dept_result)): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td class="fw-semibold text-dark"><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td>
                                                <!-- EDIT Button linking to edit_id -->
                                                <a href="add_department.php?edit_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary fw-bold">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </a>
                                                <!-- DELETE Button -->
                                                <a href="delete_dept.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this department?')" class="btn btn-sm btn-outline-danger fw-bold ms-1">
                                                    <i class="bi bi-trash-fill me-1"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-secondary py-4">No departments registered.</td>
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

</body>
</html>