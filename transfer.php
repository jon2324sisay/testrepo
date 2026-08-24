<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];
$username_logged = $_SESSION['username'];

$doc_id = "";
$ref_number = "";
$title = "";
$department = "";
$is_direct = false;

// Case 1: If accessed directly from the Table Transfer button (with ID)
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Query to get specific document info
    $query = "SELECT * FROM documents WHERE id = '$id' AND is_deleted = 0";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $doc = mysqli_fetch_assoc($result);
        $doc_id = $doc['id'];
        $ref_number = $doc['ref_number'];
        $title = $doc['title'];
        $department = $doc['department'];
        $is_direct = true; 
    }
}

// Fetch all active departments dynamically from the database for the dropdowns
$dept_query = "SELECT * FROM departments ORDER BY id ASC";
$dept_result = mysqli_query($conn, $dept_query);

// Fetch all active documents for the general select dropdown (when accessed from Navbar)
$all_docs_query = "SELECT * FROM documents WHERE is_deleted = 0 ORDER BY id DESC";
$all_docs_result = mysqli_query($conn, $all_docs_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office File Management System - Transfer Document</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f1f3f7; font-size: 1.15rem; }
        .navbar-custom { background-color: #1a365d; }
        .navbar-brand { font-size: 1.45rem; }
        .nav-link { font-size: 1.15rem; }
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

<!-- Transfer Form -->
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold text-dark border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-send-fill text-success me-2"></i>Transfer & Assign Portal</h5>
                    <a href="dashboard.php" class="btn btn-sm btn-outline-secondary fw-bold">
                        <i class="bi bi-arrow-left-circle me-1"></i> Back
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="transfer_process.php" method="POST">
                        
                        <?php if ($is_direct): ?>
                            <!-- DIRECT TRANSFER (Fields are locked) -->
                            <input type="hidden" name="doc_id" value="<?php echo $doc_id; ?>">

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary">Selected Document (Ref - Title)</label>
                                <input type="text" class="form-control bg-light py-2" value="<?php echo htmlspecialchars($ref_number . ' - ' . $title); ?>" readonly>
                            </div>
                        <?php else: ?>
                            <!-- GENERAL TRANSFER (Dropdown select from Navbar) -->
                            <div class="mb-3">
                                <label for="doc_id" class="form-label fw-semibold text-secondary">Select Document to Transfer</label>
                                <select class="form-select py-2" id="doc_id" name="doc_id" required>
                                    <option value="" selected disabled>Choose document from archives...</option>
                                    <?php if (mysqli_num_rows($all_docs_result) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($all_docs_result)): ?>
                                            <option value="<?php echo $row['id']; ?>">
                                                <?php echo htmlspecialchars($row['ref_number'] . ' - ' . $row['title'] . ' (' . $row['department'] . ')'); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <!-- Assign / Change Department (DYNAMIC FROM DATABASE!) -->
                        <div class="mb-3">
                            <label for="new_department" class="form-label fw-semibold text-secondary">Assign / Change Department</label>
                            <select class="form-select py-2" id="new_department" name="new_department" required>
                                <option value="Keep" selected>Keep Current Department</option>
                                <?php 
                                mysqli_data_seek($dept_result, 0); // Reset pointer
                                if (mysqli_num_rows($dept_result) > 0): 
                                ?>
                                    <?php while ($dept = mysqli_fetch_assoc($dept_result)): ?>
                                        <option value="<?php echo htmlspecialchars($dept['name']); ?>">
                                            <?php echo htmlspecialchars($dept['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- SINGLE DROP-DOWN RECIPIENT SELECTION (100% Stable, No Javascript needed!) -->
                        <div class="mb-3">
                            <label for="transfer_to" class="form-label fw-semibold text-secondary">Transfer To</label>
                            <select class="form-select py-2" id="transfer_to" name="transfer_to" required>
                                <option value="" selected disabled>Select Recipient...</option>
                                <option value="Admin"> Admin Only </option>
                                <option value="Manager"> Manager Only </option>
                                <option value="All"> Staff - All Departments (Public)</option>
                                <option value=" Secretary Office"> Staff - Secretary Office</option>
                                <option value="System Development & Database Office"> Staff - System Development & Database Office</option>
                                <option value="IT Support & Maintenance Office"> Staff - IT Support & Maintenance Office</option>
                                <option value="Network Administration Office"> Staff - Network Administration Office</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" name="transfer_btn" class="btn btn-success py-2 fw-bold shadow-sm">Transfer </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
 <!-- Spacer to force the dropdown to open downwards -->
<div style="height: 300px;"></div>

</body>
</html>