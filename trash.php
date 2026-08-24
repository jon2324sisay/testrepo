<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];
$username = $_SESSION['username'];

// Only Admin and Manager can access the Trash Bin (RBAC Requirement)
if ($user_role !== 'Admin' && $user_role !== 'Manager') {
    echo "<script>alert('Access Denied! Only Admin and Manager can access the Trash Bin.'); window.location='dashboard.php';</script>";
    exit();
}

// Fetch only soft-deleted documents (is_deleted = 1)
$query = "SELECT * FROM documents WHERE is_deleted = 1 ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office File Management System - Trash Bin</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f1f3f7; font-size: 1.15rem; }
        .navbar-custom { background-color: #1a365d; }
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
        <div class="col-md-10">
            <!-- Trash Bin Card (Upgraded with Collapse/Toggle View!) -->
            <div class="card shadow-sm border-0 rounded-3">
                <!-- Card Header with Collapse Button -->
                <div class="card-header bg-white py-3 fw-bold text-dark border-bottom d-flex justify-content-between align-items-center">
                    <span class="mb-0"><i class="bi bi-trash-fill text-danger me-2 fs-5"></i> Trash Bin - Deleted Documents</span>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#trashTableCollapse" aria-expanded="true" aria-controls="trashTableCollapse">
                            <i class="bi bi-arrows-expand me-1"></i> Toggle Trash
                        </button>
                        <a href="dashboard.php" class="btn btn-sm btn-outline-secondary fw-bold ms-2">
                            <i class="bi bi-arrow-left-circle me-1"></i> Back
                        </a>
                    </div>
                </div>
                <!-- Collapsible Wrapper -->
                <div class="collapse show" id="trashTableCollapse">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref Number</th>
                                        <th>Title / Subject</th>
                                        <th>Sender / Receiver</th>
                                        <th>Category</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td class="fw-semibold text-danger"><?php echo htmlspecialchars($row['ref_number']); ?></td>
                                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                                <td><?php echo htmlspecialchars($row['sender_receiver']); ?></td>
                                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['department']); ?></span></td>
                                                <td>
                                                    <!-- Restore Button -->
                                                    <a href="restore_process.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-success fw-bold">
                                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                                    </a>
                                                    
                                                    <!-- Permanent Delete Button -->
                                                    <?php if ($user_role == 'Admin'): ?>
                                                        <a href="perm_delete_process.php?id=<?php echo $row['id']; ?>" onclick="return confirm('WARNING: This will permanently delete this document and file forever. Are you sure?')" class="btn btn-sm btn-outline-danger fw-bold ms-1">
                                                            <i class="bi bi-x-circle-fill me-1"></i> Delete Forever
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-secondary py-4">Trash Bin is empty. No deleted documents found.</td>
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

<!-- Bootstrap Bundle with Popper (Required for Collapse to work!) -->
<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>