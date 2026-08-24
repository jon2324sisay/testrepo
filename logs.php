<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// STRICTLY FOR ADMIN ONLY (RBAC Requirement)
if ($_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Access Denied! Only Admin can access the System Activity Log.'); window.location='dashboard.php';</script>";
    exit();
}

$username_logged = $_SESSION['username'];

// Fetch all activity logs ordered by newest first
$query = "SELECT * FROM activity_logs ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office File Management System - Activity Log</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f1f3f7; font-size: 1.15rem; }
        .navbar-custom { background-color: #1a365d; }
        
        /* New Table Responsiveness Styles (Prevents table cutting on the right) */
        .table-custom {
            table-layout: fixed; /* Forces the table to stay within screen width */
            width: 100%;
        }
        .table-custom th, .table-custom td {
            word-wrap: break-word; /* Forces long file names to wrap to next line */
            overflow-wrap: break-word;
            white-space: normal !important;
        }
        /* Specific column width allocations for perfect alignment */
        .table-custom th:nth-child(1), .table-custom td:nth-child(1) { width: 15%; } /* User */
        .table-custom th:nth-child(2), .table-custom td:nth-child(2) { width: 35%; } /* Action */
        .table-custom th:nth-child(3), .table-custom td:nth-child(3) { width: 30%; } /* Associated File */
        .table-custom th:nth-child(4), .table-custom td:nth-child(4) { width: 20%; } /* Timestamp */

        /* Force long badge text to wrap inside the column beautifully (Prevents overlapping) */
        .table-custom .badge {
            white-space: normal !important;
            text-align: left;
            display: inline-block;
            max-width: 100%;
            line-height: 1.4;
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

<!-- Main Container -->
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <!-- Activity Log Card (Upgraded with Collapse / Toggle Logs!) -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold text-dark border-bottom d-flex justify-content-between align-items-center">
                    <span class="mb-0"><i class="bi bi-journal-text text-success me-2 fs-5"></i> System Activity Log (Audit Trail)</span>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#logsTableCollapse" aria-expanded="true" aria-controls="logsTableCollapse">
                            <i class="bi bi-arrows-expand me-1"></i> Toggle Logs
                        </button>
                        <a href="dashboard.php" class="btn btn-sm btn-outline-secondary fw-bold ms-2">
                            <i class="bi bi-arrow-left-circle me-1"></i> Back
                        </a>
                    </div>
                </div>
                <!-- Collapsible Wrapper -->
                <div class="collapse show" id="logsTableCollapse">
                    <div class="card-body">
                        <div class="table-responsive">
                            <!-- Added "table-custom" class for preventing cutting on the right -->
                            <table class="table table-hover align-middle mb-0 table-custom">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Performed Action</th>
                                        <th>Associated File</th>
                                        <th>Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td class="fw-semibold text-dark"><i class="bi bi-person-fill me-1"></i><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td>
                                                    <span class="badge <?php 
                                                        if(strpos($row['action'], 'Logged In') !== false) echo 'bg-info';
                                                        elseif(strpos($row['action'], 'Uploaded') !== false) echo 'bg-success';
                                                        elseif(strpos($row['action'], 'Trash') !== false) echo 'bg-warning';
                                                        elseif(strpos($row['action'], 'Restored') !== false) echo 'bg-primary';
                                                        else echo 'bg-danger';
                                                    ?>">
                                                        <?php echo htmlspecialchars($row['action']); ?>
                                                    </span>
                                                </td>
                                                <td class="text-secondary small"><?php echo htmlspecialchars($row['file_info'] ? $row['file_info'] : 'N/A'); ?></td>
                                                <td class="text-muted small"><?php echo htmlspecialchars($row['timestamp']); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-secondary py-4">No system activities logged yet.</td>
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