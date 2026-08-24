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

// Fetch all active departments dynamically from the database
$dept_query = "SELECT * FROM departments ORDER BY name ASC";
$dept_result = mysqli_query($conn, $dept_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office File Management System - Upload Document</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Reduced font-size slightly for perfect mobile/screen fit without hiding buttons */
        body { 
            background-color: #f1f3f7; 
            font-size: 1.00rem; 
        }
        .navbar-custom { 
            background-color: #1a365d; 
        }
        .navbar-brand { font-size: 0.95rem; }
        .nav-link { font-size: 0.95rem; }
        
        /* Custom Corporate Button Style */
        .btn-custom {
            background-color: #1a365d;
            color: #ffffff;
            border: none;
        }
        .btn-custom:hover {
            background-color: #2a4d7c;
            color: #ffffff;
        }
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

<!-- Main Form Content (Upgraded: 2-Column Grid Layout for Perfect Height Fit!) -->
<div class="container mt-4 mb-4">
    <div class="row justify-content-center">
        <!-- Changed to col-md-8 for comfortable side-by-side columns alignment -->
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-3">
                <!-- White Premium Contrast Header -->
                <div class="card-header bg-white py-3 fw-bold text-dark border-bottom d-flex justify-content-between align-items-center px-4">
                    <h4 class="mb-0 fw-bold" style="color: #1a365d;"><i class="bi bi-cloud-arrow-up-fill me-2"></i>Upload Document</h4>
                    <a href="dashboard.php" class="btn btn-sm btn-outline-secondary fw-bold"><i class="bi bi-arrow-left-circle me-1"></i> Back</a>
                </div>
                <!-- card-body padding is spacious and clean (p-4) -->
                <div class="card-body p-4">
                    <form action="upload_process.php" method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            
                            <!-- Column 1: Reference Number -->
                            <div class="col-md-6">
                                <label for="ref_number" class="form-label fw-semibold text-secondary mb-1">Reference Number</label>
                                <input type="text" class="form-control py-1" id="ref_number" name="ref_number" required placeholder="e.g., REF-102-2026">
                            </div>

                            <!-- Column 2: Document Title -->
                            <div class="col-md-6">
                                <label for="title" class="form-label fw-semibold text-secondary mb-1">Document Title </label>
                                <input type="text" class="form-control py-1" id="title" name="title" required placeholder="e.g., Salary Sheet, Budget Request">
                            </div>

                            <!-- Column 3: Sender / Receiver -->
                            <div class="col-md-6">
                                <label for="sender_receiver" class="form-label fw-semibold text-secondary mb-1">Sender / Receiver Organization</label>
                                <input type="text" class="form-control py-1" id="sender_receiver" name="sender_receiver" required placeholder="e.g., Ministry of Finance">
                            </div>

                            <!-- Column 4: Department Selection -->
                            <div class="col-md-6">
                                <label for="department" class="form-label fw-semibold text-secondary mb-1">Department</label>
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

                            <!-- Column 5: Ethiopian Date Inputs -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary mb-1">Document Date (Ethiopian Calendar - E.C.)</label>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <input type="number" class="form-control py-1" name="eth_day" required min="1" max="30" value="1" placeholder="Day">
                                    </div>
                                    <div class="col-4">
                                        <input type="number" class="form-control py-1" name="eth_month" required min="1" max="13" value="1" placeholder="Month">
                                    </div>
                                    <div class="col-4">
                                        <input type="number" class="form-control py-1" name="eth_year" required min="1900" max="2100" value="2018" placeholder="Year">
                                    </div>
                                </div>
                            </div>

                            <!-- Column 6: Send To -->
                            <div class="col-md-6">
                                <label for="sent_to" class="form-label fw-semibold text-secondary mb-1">Send To (Recipient Role)</label>
                                <select class="form-select py-1" id="sent_to" name="sent_to" required>
                                    <option value="All" selected>All Users</option>
                                    <option value="Admin">Admin Only</option>
                                    <option value="Staff">Staff Only</option>
                                    <option value="Manager">Manager Only</option>
                                </select>
                            </div>

                            <!-- Column 7: File Input (Full Width) -->
                            <div class="col-md-12">
                                <label for="document_file" class="form-label fw-semibold text-secondary mb-1">Select Document File (PDF or Image only)</label>
                                <input type="file" class="form-control py-1" id="document_file" name="document_file" required accept=".pdf, image/*">
                                <small class="text-muted">Maximum file size: 10MB</small>
                            </div>

                            <!-- Column 8: Submit Button (Full Width) -->
                            <div class="col-md-12 d-grid mt-3">
     <button type="submit" name="upload_btn" class="btn btn-custom py-2 fw-bold shadow-sm" style="font-size: 1.15rem;">Upload </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Local Bootstrap Bundle with Popper (Offline Compatible!) -->
<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>